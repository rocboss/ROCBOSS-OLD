<?php
/**
 * BaseController
 * @author ROC <i@rocs.me>
 */
class BaseController
{
    const ROCBOSS_VERSION = '3.0.0';

    const MAX_PAGESIZE = 50;

    protected static $_name;
    protected static $_class;
    protected static $_method;

    protected static $_checkSign = true;
    protected static $_signError = '';

    /**
     * __construct
     * @return void
     */
    public function __construct()
    {
        if (app()->router()->current()) {
            // Middleware intercept
            Middleware::check(app()->router()->current()->callback);
        }
    }

    /**
     * __callStatic
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public static function __callStatic($method, $arguments)
    {
        self::$_name = $method;
        self::$_class = get_called_class();
        self::$_method = $method;

        // check request sign
        if (static::$_checkSign) {
            if (app()->request()->method == 'GET') {
                $data = app()->request()->query->getData();
            } elseif (app()->request()->method == 'POST') {
                $data = app()->request()->data->getData();
                if (!isset($data['request_id'])) {
                    // Record Log.
                    app()->log()->error('400 Sign check error.', json_decode(json_encode(app()->request()), true));

                    app()->halt([
                        'code' => 400,
                        'msg'  => 'Request_id of request can\'t be empty.'
                    ]);
                    app()->stop();
                    exit;
                }
                // Reentrant attack check.
                // TODO
            }
            // if failure, abort
            if (!empty($data) && !self::checkSign($data)) {
                // Record Log.
                app()->log()->error('400 Sign check error.', json_decode(json_encode(app()->request()), true));

                app()->halt([
                    'code' => 400,
                    'msg'  => self::$_signError
                ]);
                app()->stop();
                exit;
            }
        }

        $return = call_user_func_array([(new self::$_class()), self::$_method], $arguments);

        // TODO => Redis
        
        return app()->get('isJob') ? $return : app()->json($return);
    }

    /**
     * Check request sign
     *
     * @param array $data
     * @return boolean
     */
    public static function checkSign(array $data)
    {
        if (!isset($data['sign'])) {
            self::$_signError = 'Signature of request can\'t be empty.';
            return false;
        }
        if (!isset($data['timestamp'])) {
            self::$_signError = 'Timestamp of request can\'t be empty.';
            return false;
        }
        if (abs(intval($data['timestamp']/1000) - time()) > 60) {
            self::$_signError = 'Request out of timestamp. Please check your computer time.';
            return false;
        }

        $clientSign = $data['sign'];
        unset($data['sign']);
        ksort($data);

        $str = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v);
            }
            $str .= strtolower($k).'='.$v;
        }
        $sign = md5(md5($str).env('SIGN_TOKEN', ''));

        if ($sign != $clientSign) {
            self::$_signError = 'Signature check error.';
            return false;
        }

        return true;
    }

    /**
     * Check Params
     *
     * @param array/object $data
     * @param array $params
     * @return void
     */
    public function checkParams($data, array $params)
    {
        $lossParams = [];
        foreach ($params as $param) {
            if (is_object($data)) {
                if (!isset($data->$param)) {
                    array_push($lossParams, $param);
                }
            } elseif (is_array($data)) {
                if (!isset($data[$param])) {
                    array_push($lossParams, $param);
                }
            }
        }
        if (!empty($lossParams)) {
            app()->halt([
                'code' => 500,
                'msg'  => 'The following parameter is losed. ('.implode(',', $lossParams).')',
            ]);
            app()->stop();
            exit;
        }
    }
}
