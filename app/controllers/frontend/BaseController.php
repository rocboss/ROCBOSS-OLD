<?php
namespace frontend;

use Bootstrap;
use Roc;

class BaseController extends Bootstrap
{
    public static $_code = [
        '10000' => '请求成功',
        '10001' => '未知错误',
        '10002' => '您尚未登录',
        '10003' => '您已经点过赞了',
        '10004' => '不存在该Share',
        '10005' => '评论内容太少了',
        '10006' => 'CSRF攻击拦截',

        '10100' => '验证码错误',

        '10200' => '权限不足',
        '10201' => '不存在该评论',
        '10202' => '删除评论失败',

        '10400' => '请求参数不全',
        '10401' => '邮箱地址不合法',
        '10402' => '用户名不合法',
        '10403' => '密码格式不合法',
        '10404' => '邮箱已被占用',
        '10405' => '用户名已被占用',
        '10406' => '用户不存在',
        '10407' => '密码错误',
        '10408' => '尝试次数过多，请稍后再试',
        '10409' => '该账户已绑定QQ',
        '10410' => '该账户已绑定微博',
        '10411' => '不开放注册',
        '10412' => '该账户已绑定微信',
        '10413' => '微信授权已超时',
    ];

    /**
     * 传递基础视图
     */
    protected static function renderBase(array $params)
    {
        $asset = isset($params['asset']) ? $params['asset'] : (isset($params['active']) ? $params['active'] : 'index');
        $theme = in_array(Roc::request()->cookies->light, ['black', 'white']) ? Roc::request()->cookies->light : 'white';

        Roc::render('_header', [
            'pageTitle' => isset($params['pageTitle']) ? $params['pageTitle'] : '',
            'keywords' => isset($params['keywords']) ? $params['keywords'] : '',
            'description' => isset($params['description']) ? $params['description'] : '',
            'active' => $params['active'],
            'seo'=>[
                    'sitename' => Roc::get('sys.config')['sitename'],
                    'keywords' => Roc::get('sys.config')['keywords'],
                    'description' => Roc::get('sys.config')['description']
                ],
            'loginInfo' => Roc::controller('frontend\User')->getloginInfo(),
            'asset' => $asset,
            'theme' => $theme,
        ], 'headerLayout');

        Roc::render('_footer', [
            'asset' => $asset,
            'theme' => $theme
        ], 'footerLayout');
    }

    /**
     * 输出JSON
     * @param  [type] $status [description]
     * @param  [type] $data   [description]
     * @return [type]         [description]
     */
    public static function json($status, $data)
    {
        die(Roc::json(['status' => $status, 'data'=>$data]));
    }

    /**
     * 按Code输出JSON数据
     * @param  [type] $code [description]
     * @param  [type] $msg  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function renderJson($code, $msg = '', $data = '')
    {
        return Roc::json([
            'code' => $code,
            'msg' => empty($msg) ? self::$_code["$code"] : $msg,
            'data' => $data
        ]);
    }

    /**
     * 格式化时间
     * @param  [type] $unixTime [description]
     * @return [type]           [description]
     */
    public static function formatTime($unixTime)
    {
        $showTime = date('Y', $unixTime) . "年" . date('n', $unixTime) . "月" . date('j', $unixTime) . "日";

        if (date('Y', $unixTime) == date('Y')) {
            $showTime = date('n', $unixTime) . "月" . date('j', $unixTime) . "日 " . date('H:i', $unixTime);
            if (date('n.j', $unixTime) == date('n.j')) {
                $timeDifference = time() - $unixTime + 1;

                if ($timeDifference < 30) {
                    return "刚刚";
                }
                if ($timeDifference >= 30 && $timeDifference < 60) {
                    return $timeDifference . "秒前";
                }
                if ($timeDifference >= 60 && $timeDifference < 3600) {
                    return floor($timeDifference / 60) . "分钟前";
                }
                return date('H:i', $unixTime);
            }
            if (date('n.j', ($unixTime + 86400)) == date('n.j')) {
                return "昨天 " . date('H:i', $unixTime);
            }
        }

        return $showTime;
    }

    /**
     * 获取普通参数
     * @param  [type] $value   [description]
     * @param  string $default [description]
     * @return [type]          [description]
     */
    public static function getVal($value, $default = '')
    {
        return isset($value) ? $value : $default;
    }

    /**
     * 获取Int参数
     * @param  [type]  $value   [description]
     * @param  integer $default [description]
     * @param  boolean $force   [description]
     * @return [type]           [description]
     */
    public static function getNumVal($value, $default = 0, $force = false)
    {
        if ($force) {
            return is_numeric($value) && intval($value) > 0 ? intval($value) : $default;
        }

        return is_numeric($value) ? intval($value) : $default;
    }

    /**
     * 发送短信
     * @param  [type] $tplId   [description]
     * @param  [type] $phone   [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public static function sendSms($tplId, $phone, $content)
    {
        if (Roc::get('sms.switch')) {
            $sendUrl = 'http://v.juhe.cn/sms/send';
            $smsConf = [
                'key' => Roc::get('sms.appkey'),
                'mobile'    => $phone,
                'tpl_id'    => $tplId,
                'tpl_value' => $content
            ];

            $content = self::juheCurl($sendUrl, $smsConf, 1);
            if ($content) {
                $result = json_decode($content,true);

                $errorCode = $result['error_code'];
                if ($errorCode == 0) {
                    return $result['result']['sid'];
                } else {
                    return ['status' => 'error', 'data' => '短信发送失败('.$errorCode.')：'.$result['reason']];
                }
            } else {
                return ['status' => 'error', 'data' => '请求发送短信失败'];
            }
        }
    }

    /**
     * CSRF攻击拦截
     * @method csrfCheck
     * @return [type]    [description]
     */
    public static function csrfCheck()
    {
        $data = Roc::request()->data;

        if (!empty($data['_csrf']) && $data['_csrf'] == md5(Roc::request()->cookies->roc_secure)) {
            return true;
        } else {
            self::renderJson(10006);
        }
    }

    /**
     * 请求聚合API接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    private static function juheCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 30);
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);

        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST , true);
            curl_setopt($ch, CURLOPT_POSTFIELDS , $params);
            curl_setopt($ch, CURLOPT_URL , $url);
        } else {
            if($params) {
                curl_setopt($ch, CURLOPT_URL , $url.'?'.$params);
            } else {
                curl_setopt($ch, CURLOPT_URL , $url);
            }
        }

        $response = curl_exec($ch);
        if ($response === FALSE) {
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo , curl_getinfo($ch));
        curl_close($ch);

        return $response;
    }

    /**
     * 发送GET请求
     * @method httpGet
     * @param  [type]  $url [description]
     * @return [type]       [description]
     */
    protected static function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**
     * 获取GUID
     * @method getGuid
     * @return [type]  [description]
     */
    protected static function getGuid($sand)
    {
        $charId = md5(uniqid($sand.mt_rand(), true));

        $hyphen = chr(45); // "-"
        $uuid = substr($charId, 0, 8).$hyphen
            .substr($charId, 8, 4).$hyphen
            .substr($charId, 12, 4).$hyphen
            .substr($charId, 16, 4).$hyphen
            .substr($charId, 20, 12);

        return $uuid;
    }

    /**
     * 生成随机字符串
     * @method randomString
     * @param  integer        $length [description]
     * @return [type]                 [description]
     */
    protected static function randomString($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $string;
    }
}
