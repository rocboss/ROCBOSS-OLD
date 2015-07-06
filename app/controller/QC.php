<?php

namespace app\controller;

class Recorder
{
    private static $data;
    private $inc;
    private $error;
    public function __construct($appid = "", $appkey = "", $root)
    {
        $this->inc = (object) array(
            "appid" => $appid,
            "appkey" => $appkey,
            "callback" => "http://" . $_SERVER['HTTP_HOST'] . $root . urlencode("user/QQCallback/"),
            "scope" => "get_user_info,add_t",
            "errorReport" => true
        );
        if (empty($_SESSION['QC_userData']))
        {
            self::$data = array();
        }
        else
        {
            self::$data = $_SESSION['QC_userData'];
        }
    }
    public function write($name, $value)
    {
        self::$data[$name] = $value;
    }
    public function read($name)
    {
        if (empty(self::$data[$name]))
        {
            return null;
        }
        else
        {
            return self::$data[$name];
        }
    }
    public function readInc($name)
    {
        if (empty($this->inc->$name))
        {
            return null;
        }
        else
        {
            return $this->inc->$name;
        }
    }
    public function delete($name)
    {
        unset(self::$data[$name]);
    }
    function __destruct()
    {
        $_SESSION['QC_userData'] = self::$data;
    }
}

namespace app\controller;

class ErrorCase
{
    private $errorMsg;
    public function __construct()
    {
        $this->errorMsg = array(
            "30001" => "<h2>The state does not match. You may be a victim of CSRF.</h2>",
            "50001" => "<h2>可能是服务器无法请求https协议</h2>可能未开启curl支持,请尝试开启curl支持，重启web服务器，如果问题仍未解决，请联系我们"
        );
    }
    public function showError($code, $description = '$')
    {
        $recorder = new Recorder();
        if ($recorder->readInc("errorReport"))
        {
            echo "<meta charset=\"UTF-8\">";
            if ($description == "$")
            {
                die($this->errorMsg[$code]);
            }
            else
            {
                echo "<h3>error:</h3>$code";
                echo "<h3>msg  :</h3>$description";
                exit();
            }
        }
    }
    public function showTips($code, $description = '$')
    {
    }
}

namespace app\controller;

class URL
{
    private $error;
    public function __construct()
    {
        $this->error = new ErrorCase();
    }
    public function combineURL($baseURL, $keysArr)
    {
        $combined = $baseURL . "?";
        $valueArr = array();
        foreach ($keysArr as $key => $val)
        {
            $valueArr[] = "$key=$val";
        }
        $keyStr = implode("&", $valueArr);
        $combined .= ($keyStr);
        return $combined;
    }
    public function get_contents($url)
    {
        if (ini_get("allow_url_fopen") == "1")
        {
            $response = file_get_contents($url);
        }
        else
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        if (empty($response))
        {
            $this->error->showError("50001");
        }
        return $response;
    }
    public function get($url, $keysArr)
    {
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }
    public function post($url, $keysArr, $flag = 0)
    {
        $ch = curl_init();
        if (!$flag)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}

namespace app\controller;

class Oauth
{
    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
    protected $recorder;
    public $urlUtils;
    protected $error;
    function __construct($appid, $appkey, $root)
    {
        $this->recorder = new Recorder($appid, $appkey, $root);
        $this->urlUtils = new URL();
        $this->error    = new ErrorCase();
    }
    public function qq_login()
    {
        $appid    = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        $scope    = $this->recorder->readInc("scope");
        $state    = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state', $state);
        $keysArr   = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );
        $login_url = $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        return $login_url;
    }
    public function qq_callback()
    {
        $state = $this->recorder->read("state");
        if (!isset($_GET['state']) || $_GET['state'] != $state)
        {
            $this->error->showError("30001");
        }
        $keysArr   = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->recorder->readInc("appid"),
            "redirect_uri" => $this->recorder->readInc("callback"),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code" => isset($_GET['code']) ? $_GET['code'] : ""
        );
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response  = $this->urlUtils->get_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos     = strpos($response, "(");
            $rpos     = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
            $msg      = json_decode($response);
            if (isset($msg->error))
            {
                $this->error->showError($msg->error, $msg->error_description);
            }
        }
        $params = array();
        parse_str($response, $params);
        if (isset($params["access_token"]))
        {
            $this->recorder->write("access_token", $params["access_token"]);
            return $params["access_token"];
        }
        return "";
    }
    public function get_openid()
    {
        $keysArr   = array(
            "access_token" => $this->recorder->read("access_token")
        );
        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response  = $this->urlUtils->get_contents($graph_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos     = strpos($response, "(");
            $rpos     = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $user = json_decode($response);
        if (isset($user->error))
        {
            $this->error->showError($user->error, $user->error_description);
        }
        if (isset($user->openid))
        {
            $this->recorder->write("openid", $user->openid);
            return $user->openid;
        }
        return "";
    }
}

namespace app\controller;

class QC extends Oauth
{
    private $kesArr, $APIMap;
    public function __construct($appid, $appkey, $root = "/", $access_token = "", $openid = "")
    {
        parent::__construct($appid, $appkey, $root);
        if ($access_token === "" || $openid === "")
        {
            $this->keysArr = array(
                "oauth_consumer_key" => (int) $this->recorder->readInc("appid"),
                "access_token" => $this->recorder->read("access_token"),
                "openid" => $this->recorder->read("openid")
            );
        }
        else
        {
            $this->keysArr = array(
                "oauth_consumer_key" => (int) $this->recorder->readInc("appid"),
                "access_token" => $access_token,
                "openid" => $openid
            );
        }
        $this->APIMap = array(
            "add_blog" => array(
                "https://graph.qq.com/blog/add_one_blog",
                array(
                    "title",
                    "format" => "json",
                    "content" => null
                ),
                "POST"
            ),
            "add_topic" => array(
                "https://graph.qq.com/shuoshuo/add_topic",
                array(
                    "richtype",
                    "richval",
                    "con",
                    "#lbs_nm",
                    "#lbs_x",
                    "#lbs_y",
                    "format" => "json",
                    "#third_source"
                ),
                "POST"
            ),
            "get_user_info" => array(
                "https://graph.qq.com/user/get_user_info",
                array(
                    "format" => "json"
                ),
                "GET"
            ),
            "add_one_blog" => array(
                "https://graph.qq.com/blog/add_one_blog",
                array(
                    "title",
                    "content",
                    "format" => "json"
                ),
                "GET"
            ),
            "add_album" => array(
                "https://graph.qq.com/photo/add_album",
                array(
                    "albumname",
                    "#albumdesc",
                    "#priv",
                    "format" => "json"
                ),
                "POST"
            ),
            "upload_pic" => array(
                "https://graph.qq.com/photo/upload_pic",
                array(
                    "picture",
                    "#photodesc",
                    "#title",
                    "#albumid",
                    "#mobile",
                    "#x",
                    "#y",
                    "#needfeed",
                    "#successnum",
                    "#picnum",
                    "format" => "json"
                ),
                "POST"
            ),
            "list_album" => array(
                "https://graph.qq.com/photo/list_album",
                array(
                    "format" => "json"
                )
            ),
            "add_share" => array(
                "https://graph.qq.com/share/add_share",
                array(
                    "title",
                    "url",
                    "#comment",
                    "#summary",
                    "#images",
                    "format" => "json",
                    "#type",
                    "#playurl",
                    "#nswb",
                    "site",
                    "fromurl"
                ),
                "POST"
            ),
            "check_page_fans" => array(
                "https://graph.qq.com/user/check_page_fans",
                array(
                    "page_id" => "314416946",
                    "format" => "json"
                )
            ),
            "add_t" => array(
                "https://graph.qq.com/t/add_t",
                array(
                    "format" => "json",
                    "content",
                    "#clientip",
                    "#longitude",
                    "#compatibleflag"
                ),
                "POST"
            ),
            "add_pic_t" => array(
                "https://graph.qq.com/t/add_pic_t",
                array(
                    "content",
                    "pic",
                    "format" => "json",
                    "#clientip",
                    "#longitude",
                    "#latitude",
                    "#syncflag",
                    "#compatiblefalg"
                ),
                "POST"
            ),
            "del_t" => array(
                "https://graph.qq.com/t/del_t",
                array(
                    "id",
                    "format" => "json"
                ),
                "POST"
            ),
            "get_repost_list" => array(
                "https://graph.qq.com/t/get_repost_list",
                array(
                    "flag",
                    "rootid",
                    "pageflag",
                    "pagetime",
                    "reqnum",
                    "twitterid",
                    "format" => "json"
                )
            ),
            "get_info" => array(
                "https://graph.qq.com/user/get_info",
                array(
                    "format" => "json"
                )
            ),
            "get_other_info" => array(
                "https://graph.qq.com/user/get_other_info",
                array(
                    "format" => "json",
                    "#name",
                    "fopenid"
                )
            ),
            "get_fanslist" => array(
                "https://graph.qq.com/relation/get_fanslist",
                array(
                    "format" => "json",
                    "reqnum",
                    "startindex",
                    "#mode",
                    "#install",
                    "#sex"
                )
            ),
            "get_idollist" => array(
                "https://graph.qq.com/relation/get_idollist",
                array(
                    "format" => "json",
                    "reqnum",
                    "startindex",
                    "#mode",
                    "#install"
                )
            ),
            "add_idol" => array(
                "https://graph.qq.com/relation/add_idol",
                array(
                    "format" => "json",
                    "#name-1",
                    "#fopenids-1"
                ),
                "POST"
            ),
            "del_idol" => array(
                "https://graph.qq.com/relation/del_idol",
                array(
                    "format" => "json",
                    "#name-1",
                    "#fopenid-1"
                ),
                "POST"
            ),
            "get_tenpay_addr" => array(
                "https://graph.qq.com/cft_info/get_tenpay_addr",
                array(
                    "ver" => 1,
                    "limit" => 5,
                    "offset" => 0,
                    "format" => "json"
                )
            )
        );
    }
    private function _applyAPI($arr, $argsList, $baseUrl, $method)
    {
        $pre           = "#";
        $keysArr       = $this->keysArr;
        $optionArgList = array();
        foreach ($argsList as $key => $val)
        {
            $tmpKey = $key;
            $tmpVal = $val;
            if (!is_string($key))
            {
                $tmpKey = $val;
                if (strpos($val, $pre) === 0)
                {
                    $tmpVal = $pre;
                    $tmpKey = substr($tmpKey, 1);
                    if (preg_match("/-(\d$)/", $tmpKey, $res))
                    {
                        $tmpKey                   = str_replace($res[0], "", $tmpKey);
                        $optionArgList[$res[1]][] = $tmpKey;
                    }
                }
                else
                {
                    $tmpVal = null;
                }
            }
            if (!isset($arr[$tmpKey]) || $arr[$tmpKey] === "")
            {
                if ($tmpVal == $pre)
                {
                    continue;
                }
                else if ($tmpVal)
                {
                    $arr[$tmpKey] = $tmpVal;
                }
                else
                {
                    if ($v = $_FILES[$tmpKey])
                    {
                        $filename = dirname($v['tmp_name']) . "/" . $v['name'];
                        move_uploaded_file($v['tmp_name'], $filename);
                        $arr[$tmpKey] = "@$filename";
                    }
                    else
                    {
                        $this->error->showError("api调用参数错误", "未传入参数$tmpKey");
                    }
                }
            }
            $keysArr[$tmpKey] = $arr[$tmpKey];
        }
        foreach ($optionArgList as $val)
        {
            $n = 0;
            foreach ($val as $v)
            {
                if (in_array($v, array_keys($keysArr)))
                {
                    $n++;
                }
            }
            if (!$n)
            {
                $str = implode(",", $val);
                $this->error->showError("api调用参数错误", $str . "必填一个");
            }
        }
        if ($method == "POST")
        {
            if ($baseUrl == "https://graph.qq.com/blog/add_one_blog")
                $response = $this->urlUtils->post($baseUrl, $keysArr, 1);
            else
                $response = $this->urlUtils->post($baseUrl, $keysArr, 0);
        }
        else if ($method == "GET")
        {
            $response = $this->urlUtils->get($baseUrl, $keysArr);
        }
        return $response;
    }
    public function __call($name, $arg)
    {
        if (empty($this->APIMap[$name]))
        {
            $this->error->showError("api调用名称错误", "不存在的API: <span style='color:red;'>$name</span>");
        }
        $baseUrl  = $this->APIMap[$name][0];
        $argsList = $this->APIMap[$name][1];
        $method   = isset($this->APIMap[$name][2]) ? $this->APIMap[$name][2] : "GET";
        if (empty($arg))
        {
            $arg[0] = null;
        }
        if ($name != "get_tenpay_addr")
        {
            $response    = json_decode($this->_applyAPI($arg[0], $argsList, $baseUrl, $method));
            $responseArr = $this->objToArr($response);
        }
        else
        {
            $responseArr = $this->simple_json_parser($this->_applyAPI($arg[0], $argsList, $baseUrl, $method));
        }
        if ($responseArr['ret'] == 0)
        {
            return $responseArr;
        }
        else
        {
            $this->error->showError($response->ret, $response->msg);
        }
    }
    private function objToArr($obj)
    {
        if (!is_object($obj) && !is_array($obj))
        {
            return $obj;
        }
        $arr = array();
        foreach ($obj as $k => $v)
        {
            $arr[$k] = $this->objToArr($v);
        }
        return $arr;
    }
    public function get_access_token()
    {
        return $this->recorder->read("access_token");
    }
    private function simple_json_parser($json)
    {
        $json      = str_replace("{", "", str_replace("}", "", $json));
        $jsonValue = explode(",", $json);
        $arr       = array();
        foreach ($jsonValue as $v)
        {
            $jValue                                = explode(":", $v);
            $arr[str_replace('"', "", $jValue[0])] = (str_replace('"', "", $jValue[1]));
        }
        return $arr;
    }
}
?>