<?php
/**
 * 极验行为式验证安全平台 [ROCPHP专用SDK]
 * @Author ROC
 */
class Geetest
{
    const GT_SDK_VERSION = 'php_2.15.7.6.1';

    public $challenge;

    private $appId;

    private $appKey;

    public function __construct($appId, $appKey)
    {
        $this->appId = $appId;

        $this->appKey = $appKey;
    }

    /**
     * 判断极验服务器是否down机
     *
     * @return
     */
    public function register()
    {
        $url = "http://api.geetest.com/register.php?gt=".$this->appId;

        $this->challenge = $this->sendRequest($url);

        if (strlen($this->challenge) != 32) {
            return 0;
        }
        return 1;
    }
    public function validate($challenge, $validate, $seccode)
    {
        if (!$this->checkValidate($challenge, $validate)) {
            return false;
        }

        $data = [
            "seccode" => $seccode,
            "sdk" => self::GT_SDK_VERSION
        ];

        $url = "http://api.geetest.com/validate.php";

        $codevalidate = $this->postRequest($url, $data);

        if (strlen($codevalidate) > 0 && $codevalidate == md5($seccode)) {
            return true;
        } elseif ($codevalidate == "false") {
            return false;
        } else {
            return $codevalidate;
        }
    }
    private function checkValidate($challenge, $validate)
    {
        if (strlen($validate) != 32) {
            return false;
        }
        if (md5($this->appKey.'geetest'.$challenge) != $validate) {
            return false;
        }
        return true;
    }

    private function sendRequest($url)
    {
        if (function_exists('curl_exec')) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($ch);

            curl_close($ch);
        } else {
            $opts  = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 2
                ]
            ];

            $context = stream_context_create($opts);

            $data = file_get_contents($url, false, $context);
        }
        return $data;
    }

    /**
     *解码随机参数
     *
     * @param $challenge
     * @param $string
     * @return
     */
    private function decode_response($challenge, $string)
    {
        if (strlen($string) > 100) {
            return 0;
        }
        $key = [];
        $chongfu = [];
        $shuzi = [
            "0" => 1,
            "1" => 2,
            "2" => 5,
            "3" => 10,
            "4" => 50
        ];
        $count = 0;
        $res = 0;
        $array_challenge = str_split($challenge);
        $array_value = str_split($string);
        for ($i = 0; $i < strlen($challenge); $i++) {
            $item = $array_challenge[$i];
            if (in_array($item, $chongfu)) {
                continue;
            } else {
                $value = $shuzi[$count % 5];
                array_push($chongfu, $item);
                $count++;
                $key[$item] = $value;
            }
        }

        for ($j = 0; $j < strlen($string); $j++) {
            $res += $key[$array_value[$j]];
        }
        $res = $res - $this->decodeRandBase($challenge);
        return $res;
    }


    /**
     *
     * @param $x_str
     * @return
     */
    private function get_x_pos_from_str($x_str)
    {
        if (strlen($x_str) != 5) {
            return 0;
        }
        $sum_val = 0;
        $x_pos_sup = 200;
        $sum_val = base_convert($x_str, 16, 10);
        $result = $sum_val % $x_pos_sup;
        $result = ($result < 40) ? 40 : $result;
        return $result;
    }

    /**
     *
     * @param full_bg_index
     * @param img_grp_index
     * @return
     */
    private function get_failback_pic_ans($full_bg_index, $img_grp_index)
    {
        $full_bg_name = substr(md5($full_bg_index), 0, 9);
        $bg_name = substr(md5($img_grp_index), 10, 9);

        $answer_decode = "";
        // 通过两个字符串奇数和偶数位拼接产生答案位
        for ($i = 0; $i < 9; $i++) {
            if ($i % 2 == 0) {
                $answer_decode = $answer_decode . $full_bg_name[$i];
            } elseif ($i % 2 == 1) {
                $answer_decode = $answer_decode . $bg_name[$i];
            }
        }
        $x_decode = substr($answer_decode, 4, 5);
        $x_pos = $this->get_x_pos_from_str($x_decode);
        return $x_pos;
    }

    /**
     * 输入的两位的随机数字,解码出偏移量
     *
     * @param challenge
     * @return
     */
    private function decodeRandBase($challenge)
    {
        $base = substr($challenge, 32, 2);
        $tempArray = array();
        for ($i = 0; $i < strlen($base); $i++) {
            $tempAscii = ord($base[$i]);
            $result    = ($tempAscii > 57) ? ($tempAscii - 87) : ($tempAscii - 48);
            array_push($tempArray, $result);
        }
        $decodeRes = $tempArray['0'] * 36 + $tempArray['1'];
        return $decodeRes;
    }

    /**
     * 得到答案
     *
     * @param validate
     * @return
     */
    public function getAnswer($validate)
    {
        if ($validate) {
            $value     = explode("_", $validate);
            $challenge = $_SESSION['challenge'];
            $ans       = $this->decode_response($challenge, $value['0']);
            $bg_idx    = $this->decode_response($challenge, $value['1']);
            $grp_idx   = $this->decode_response($challenge, $value['2']);
            $x_pos     = $this->get_failback_pic_ans($bg_idx, $grp_idx);
            $answer    = abs($ans - $x_pos);
            if ($answer < 4) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function postRequest($url, $postdata = null)
    {
        $data = http_build_query($postdata);
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if (!$postdata) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            if ($postdata) {
                $url = $url.'?'.$data;
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($data) . "\r\n",
                        'content' => $data
                    ]
                ];
                $context = stream_context_create($opts);
                $data = file_get_contents($url, false, $context);
            }
        }
        return $data;
    }
}
