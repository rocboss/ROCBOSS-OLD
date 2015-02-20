<?php
class Utils
{
    /**
     * 执行时间
     * @return string
     */
    public static function runtime()
    {
        global $sys_starttime;

        $mtime       = explode(' ', microtime());

        $sys_runtime = number_format(($mtime[1] + $mtime[0] - $sys_starttime), 4);

        unset($sys_starttime);

        return $sys_runtime . 's';
    }

    /**
     * 单向加密函数（三层盐值）
     * @param $str
     * @param string $salt
     * @return string
     */
    public static function encrypt($str, $salt = 'rocboss')
    {
        return md5(md5(md5($str . $salt) . $salt) . $salt);
    }
    
    /**
     * 生成指定长度的随机码
     * @param int $n
     * @return string
     */
    public static function getRandomCode($n = 32)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $n);
    }
    
    /**
     * 邮箱合法性判断
     * @param string $email
     * @return boolean
     */
    public static function checkEmailValidity($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        
        return preg_match($pattern, $email);
    }

    /**
     * 昵称合法性判断
     * @param string $nickname
     * @return boolean
     */
    public static function checkNickname($nickname)
    {
        if (strlen($nickname) < 3 || self::getStrlen($nickname) < 2)
        {
            return '昵称太短了';
        }
        if (self::getStrlen($nickname) > 10)
        {
            return '昵称太长了';
        }
        if (preg_match('/\s/', $nickname) || strpos($nickname,' '))
        {
            return '昵称不允许存在空格';
        }
        if (is_numeric(substr($nickname, 0, 1)) || substr($nickname, 0, 1) == "_")
        {
            return '昵称不能以数字和下划线开头';
        }
        if (substr($nickname, -1, 1) == "_")
        {
            return '昵称不能以下划线结尾';
        }
        if (!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $nickname))
        {
            return '昵称只能用汉字、英文、数字及下划线';
        }
        for ($i = 0, $l = self::getStrlen($nickname); $i < $l; $i++)
        {
            if (self::textCount($nickname, self::getSubstr($nickname, $i, 1)) > 3)
            {
                return '昵称内重复字符太多';
            }
        }
        return "";
    }

    /**
     * 获取当前分页
     * @return int
     */
    public static function getCurrentPage()
    {
        return isset($GLOBALS['Router']['params']['page']) && intval($GLOBALS['Router']['params']['page']) > 0 ? intval($GLOBALS['Router']['params']['page']) : 1;
    }
    
    /**
     * 裁剪字符串
     * @param string $str
     * @param int $start
     * @param int $len
     * @return string
     */
    public static function getSubstr($str, $start, $len)
    {
        return mb_substr($str, $start, $len, 'utf-8');
    }

    /**
     * 格式化时间
     * @param string $unixTime
     * @return string
     */
    public static function formatTime($unixTime)
    {
        $showTime = date('Y', $unixTime) . "年" . date('n', $unixTime) . "月" . date('j', $unixTime) . "日 " . date('H:i', $unixTime);
        
        if (date('Y', $unixTime) == date('Y'))
        {
            $showTime = date('n', $unixTime) . "月" . date('j', $unixTime) . "日 " . date('H:i', $unixTime);
            
            if (date('n.j', $unixTime) == date('n.j'))
            {
                $timeDifference = time() - $unixTime + 1;
                
                if ($timeDifference < 60)
                {
                    return $timeDifference . "秒前";
                }
                if ($timeDifference >= 60 && $timeDifference < 3600)
                {
                    return floor($timeDifference / 60) . "分钟前";
                }
                return date('H:i', $unixTime);
            }
            if (date('n.j', ($unixTime + 86400)) == date('n.j'))
            {
                return "昨天 " . date('H:i', $unixTime);
            }
        }
        return $showTime;
    }

    /**
     * 获取文段自动摘要
     * @param string $str_cut
     * @param int $length
     * @return string
     */
    public static function cutSubstr($str_cut, $length = 64)
    {
        $str_cut = preg_replace('/(\[\:[0-9]+\])/i', '图片', $str_cut);

        $str_cut = preg_replace('/\[p\]/i', ' ', $str_cut);

        if (mb_strlen(trim($str_cut), 'utf8') > $length)
        {
            return trim(mb_substr($str_cut, 0, $length, 'utf-8')) . '...';
        }
        else
        {
            return trim($str_cut);
        }
    }

    /**
     * 获取用户组名
     * @param int $groupid
     * @return string
     */
    public static function getGroupName($groupid)
    {
        switch ($groupid)
        {
            case 0:
                return '禁言用户';

            case 1:
                return '普通会员';

            case 9:
                return '管理员';

            default:
                return '禁言用户';
        }
    }

    /**
     * 获取用户积分事由
     * @param int $type
     * @return string
     */
    public static function getScoreAction($type)
    {
        switch ($type)
        {
            case 1:
                return '创建话题';

            case 2:
                return '回复话题';

            case 3:
                return '签到奖励';

            case 4:
                return '发送私信';

            case 5:
                return '话题被赞';

            case 6:
                return '话题被删';

            case 7:
                return '回复被删';

            case 8:
                return '赞被取消';

            default:
                return '未知操作';
        }
    }

    /**
     * 获取字符串的长度
     * @param string $str
     * @return int
     */
    public static function getStrlen($str)
    {
        return mb_strlen($str, "utf-8");
    }

    /**
     * 获取子串出现的次数
     * @param string $str
     * @param string $needle
     * @return int
     */
    public static function textCount($str, $needle)
    {
        return mb_substr_count($str, $needle, 'utf-8');
    }

    /**
     * 解析URL地址
     * @param string $str
     * @return string
     */
    public static function parseUrl($str)
    {
        $auto_arr = array(
            "/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp):\/\/)([a-z0-9\/\-_+=.~!%@?#%&;:$\\│]+)/i",
            "/(?<=[^\]a-z0-9\/\-_.~?=:.])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/i"
        );
        $auto_url = array(
            '<a href="\\1\\3" target="_blank" class="url">\\1\\3</a>',
            '<a href="mailto:\\0" class="url">\\0</a>'
        );
        return preg_replace($auto_arr, $auto_url, ' ' . $str);
    }
    
    /**
     * @用户解析
     * @param string $str
     * @return string
     */
    public static function parseUser($str)
    {
        return preg_replace_callback('/\@([^[:punct:]\s]{3,39})([\s]+)/', 'Utils::atName', $str . ' ');
    }

    /**
     * @用户解析 callback 方法
     * @param string $str
     * @return string
     */
    public static function atName($str)
    {
        if (in_array($str[1], array(
            "。",
            "？",
            "，",
            "！"
        ))) {
            return $str[0];
        }
        return '<span class=atname><a href="'.ROOT. 'user/t/' . urlencode($str[1]) . '">@' . $str[1] . '</a></span>' . $str[2];
    }

    /**
     * 获取来访Client
     * @return string
     */
    public static function getClient()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (stripos($agent, 'iphone') !== false) {
            $x_moblie = 'iPhone';
        } else {
            if (stripos($agent, 'ipad') !== false) {
                $x_moblie = 'iPad';
            } else {
                if (stripos($agent, 'ipod') !== false) {
                    $x_moblie = 'iPod';
                } else {
                    if (stripos($agent, 'android') !== false) {
                        $x_moblie = 'Android';
                    } else {
                        if (stripos($agent, 'blackberry') !== false) {
                            $x_moblie = 'BlackBerry';
                        } else {
                            if (stripos($agent, 'windows phone') !== false) {
                                $x_moblie = 'Windows Phone';
                            } else {
                                if (stripos($agent, 'windows mobile') !== false) {
                                    $x_moblie = 'Windows Mobile';
                                } else {
                                    if (stripos($agent, 'windows ce') !== false) {
                                        $x_moblie = 'Windows CE';
                                    } else {
                                        if (stripos($agent, 'symbian') !== false) {
                                            $x_moblie = 'Symbian';
                                        } else {
                                            if (stripos($agent, 'uc') !== false) {
                                                $x_moblie = 'UC浏览器';
                                            } else {
                                                $x_moblie = '';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $x_moblie;
    }
}
?>