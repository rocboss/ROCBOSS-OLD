<?php

namespace app\controller;

class base
{
    protected $app;

    protected $sys;

    protected $filter;

    protected $utils;

    protected $secret;

    protected $page;

    protected $loginInfo;

    protected $per = 30;

    public function __construct($app, $db_config)
	{
        $this->app = $app;

        # 是否开启调试
        $this->app->set('handle_errors', false);

        # 初始化安全过滤库
        $this->filter = new \system\util\Filter();

        # 初始化加密库
        $this->secret = new \system\util\Secret();

        # 初始化工具库
        $this->utils = new \system\util\Utils();

        # 初始化数据库配置
        $app->db()->set_connection($db_config);

        # 自动生成系统配置
        if (!file_exists('app/cache/sys_config_cache.php'))
        {
            $allSysData = $this->app->db()->select('roc_system', '*');

            $fileContent = '<?php'."\n".'$sys_config = array('."\n";

            foreach ($allSysData as $key => $value)
            {
                $fileContent .= "'".$value['name']."' => '".$value['value']."', \n";

                $this->sys[$value['name']] = $value['value'];
            }

            $fileContent .= ');'."\n ?>";

            file_put_contents('app/cache/sys_config_cache.php', $fileContent);
        }
        else
        {
            require 'app/cache/sys_config_cache.php';

            $this->sys = $sys_config;
        }

        # 初始化模板引擎配置
        $this->app->view()->tpl_dir = 'app/template/'.$this->sys['theme'].'/';

        # 模板引擎后缀
        $this->app->view()->tpl_ext = '.tpl.php';

        # 模板所在目录
        $this->app->view()->cache_dir = 'app/cache/template/'.$this->sys['theme'].'/';

        # 模板缓存时间
        $this->app->view()->cache_time = 30;

        # 赋值tpl变量
        $this->app->set('tpl', ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/') .$this->app->view()->tpl_dir);

        # 模板赋值app所在根目录
        $this->app->view()->assign('root', ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/'));

        # 模板赋值app模板所在目录
        $this->app->view()->assign('tpl', $this->app->get('tpl'));
        
        # 模板赋值app模板css所在目录
        $this->app->view()->assign('css', $this->app->get('tpl').'assets/css/');

        # 模板赋值app模板img所在目录
        $this->app->view()->assign('img', $this->app->get('tpl').'assets/img/');

        # 模板赋值app模板js所在目录
        $this->app->view()->assign('js', $this->app->get('tpl').'assets/js/');

        # 获取用户登录信息
        $this->loginInfo = $this->isLogin($this->sys['rockey'], $_COOKIE);

        if ($this->loginInfo['uid'] > 0)
        {
            if (!isset($_COOKIE['today_sign']))
            {
                setcookie('today_sign', '1', strtotime(date('Y-m-d',time())) + 86400, '/');
                
                $this->updateLasttime($this->loginInfo['uid']);
            }

            $this->app->view()->assign('signStatus', $this->getSignStatus());
            
            $this->app->view()->assign('mine', $this->getMineInfo());
        }

        $this->app->view()->assign('sitename', $this->sys['sitename']);

        $this->app->view()->assign('keywords', $this->sys['keywords']);

        $this->app->view()->assign('description', $this->sys['description']);

        $this->app->view()->assign('ad', $this->filter->out($this->sys['ad']));

        $this->app->view()->assign('loginInfo', $this->loginInfo);
	}

    # 获取用户组
    protected function getGroupName($groupid)
    {
        switch ($groupid)
        {
            case '0':
                return '禁言用户';

            case '1':
                return '普通会员';

            case '2':
                return '金牌会员';

            case '3':
                return '钻石会员';
            
            case '9':
                return '管理员';

            default:
                return '禁言用户';
        }
    }

    # 获取用户积分事由
    public function getScoreAction($type)
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

    # 获取用户头像
    protected function getUserAvatar($uid, $size = 100)
    {
        return ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/').'app/uploads/avatars/'.intval($uid/1000).'/'.$uid.'/'.$size.'.png?'.time();
    }

    # 获取帖子标签
    protected function getTopicTag($tid)
    {
        return $this->app->db()->select('roc_topic_tag_connection', array(
            '[>]roc_tag' => 'tagid'
        ), 'roc_tag.tagname', array(
            'roc_topic_tag_connection.tid' => $tid
        ));
    }

    # 获取加密身份信息
    protected function isLogin($sKey, $cookie)
    {
        $userInfo = array(
            'uid' => 0,
            
            'username' => '',

            'signature' => '',
                        
            'groupid' => 0,
            
            'groupname' => '',
            
            'logintime' => 0,
            
            'avatar' => ''
        );
        
        if (isset($cookie['roc_login'], $cookie['roc_secure']))
        {
            $userArr = json_decode($this->secret->decrypt($cookie['roc_secure'], $sKey), true);
            
            if (count($userArr) == 4)
            {
                if ($cookie['roc_login'] == $userArr[1])
                {
                    $userInfo['uid'] = $userArr[0];
                    
                    $userInfo['username'] = $userArr[1];
                    
                    $userInfo['groupid'] = $userArr[2];
                    
                    $userInfo['logintime'] = $userArr[3];
                    
                    $userInfo['avatar'] = $this->getUserAvatar($userArr[0]);
                    
                    $userInfo['groupname'] = $this->getGroupName($userArr[2]);
                }
            }
        }
        return $userInfo;
    }

    # 注册加密身份信息
    protected function loginCookie($sKey, $uid, $username, $groupid)
    {
        $loginTime = time();
        
        setcookie('roc_login', $username, $loginTime + 604800, '/');
        
        $loginEncode = $this->secret->encrypt(json_encode(array(
            $uid,

            $username,

            $groupid,
            
            $loginTime
        )), $sKey);
        
        setcookie('roc_secure', $loginEncode, $loginTime + 604800, '/');        
    }

    # 过滤输入帖子
    protected function topicIn($content)
    {
        return $this->filter->topicIn($content);
    }

    # 过滤输出帖子
    protected function topicOut($content)
    {
        return $this->filter->topicOut($content);
    }

    # 提取图片
    protected function getPictures($str, $uid)
    {
        preg_match_all('/\[:([0-9]+)\]/i', $str, $attachment);
        
        foreach ($attachment[1] as $key => $value)
        {
            $res = $this->app->db()->get('roc_attachment', array(
                'uid',

                'path'
            ), array(
                'id' => $value
            ));
            
            if (!empty($res['path']) && $uid == $res['uid'])
            {
                $str = preg_replace('/\[:' . $value . '\]/i', '<a href="' . ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/') . $res['path'] . '" class="picPre"><img src="' . ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/') . $res['path'] . '.thumb.png" alt=""/></a>', $str);
            }
            else
            {
                $str = preg_replace('/\[:' . $value . '\]/i', '[此处非法引用 OR 图片已不存在]', $str);
            }
        }
        
        return $str;
    }
    
    # 获取用户通知、私信、积分
    protected function getMineInfo()
    {
        return array(
            'notification' => $this->app->db()->count('roc_notification', array(
                'AND' => array(
                    'atuid' => $this->loginInfo['uid'],
                    'isread' => 0
                )
            )),
            'whisper' => $this->app->db()->count('roc_whisper', array(
                'AND' => array(
                    'atuid' => $this->loginInfo['uid'],
                    'isread' => 0
                )
            ))
        );
    }

    # 获取签到状态
    protected function getSignStatus()
    {
        if ($this->app->db()->has('roc_score', array(
            'AND' => array(
                'uid' => $this->loginInfo['uid'],
                'type' => 3,
                'time[>]' => strtotime(date('Y-m-d', time()))
            )
        )))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    # 更新用户活跃时间
    protected function updateLasttime($uid, $time = 0)
    {
        $this->app->db()->update('roc_user', array(
            'lasttime' => $time > 0 ? $time : time()
        ), array(
            'uid' => $uid
        ));
    }

    # 更新用户积分
    protected function updateUserScore($uid, $changed, $type)
    {
        $ori = $this->app->db()->get('roc_user', 'scores', array(
            'uid' => $uid
        ));
        
        $this->app->db()->beginTransaction();

        if ($changed > 0)
        {
            $this->app->db()->update('roc_user', array(
                'scores[+]' => $changed
            ), array(
                'uid' => $uid
            ));
        }
        else
        {
            $this->app->db()->update('roc_user', array(
                'scores[-]' => abs($changed)
            ), array(
                'uid' => $uid
            ));
        }
        
        $scoreArray = array(
            'uid' => $uid,
            
            'changed' => $changed,
            
            'remain' => ($changed > 0) ? ($changed + $ori) : $ori - abs($changed),
            
            'type' => $type,
            
            'time' => time()
        );
        
        $insertID = $this->app->db()->insert('roc_score', $scoreArray);

        $this->app->db()->checkResult($insertID);
    }

    # 获取帖子赞的详情
    protected function getTopicPraise($tid, $flag = true)
    {
        if($flag)
        {
            $praiseArray = $this->app->db()->select('roc_praise', array(
                '[>]roc_user' => 'uid'
            ), array(
                'roc_user.username(praiseUsername)',
                
                'roc_user.uid(praiseUid)'
            ), array(
                'roc_praise.tid' => $tid
            ));
            
            foreach ($praiseArray as $key => $value)
            {
                $praiseArray[$key]['praiseAvatar'] = $this->getUserAvatar($value['praiseUid'], 50);
            }
            
            return $praiseArray;
        }
        else
        {
            $praiseArray['count'] = $this->app->db()->count('roc_praise', 'id', array('roc_praise.tid' => $tid));

            $praiseArray['myPraise'] = $this->app->db()->has('roc_praise', array('AND'=>array('roc_praise.tid' => $tid, 'roc_praise.uid'=>$this->loginInfo['uid'])));

            return $praiseArray;
        }
    }
    
    # 返回json数据
    protected function showMsg($message, $type = 'success', $position = 0)
    {
        header("Content-type:text/html;charset=utf-8");
        
        die(json_encode(array(
            "result" => $type,
            "message" => $message,
            "position" => $position
        )));
    }

    # 设置分页信息，参数：int $current 当前页, int $total 总数, string $url 页面URL格式
    protected function setPage($current, $total, $url)
    {
        $params = array(
            'total_rows' => $total,

            'method' => 'html',

            'parameter' => ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/') . $url,
            
            'now_page' => $current,
            
            'list_rows' => $this->per,
        );

        $this->page = new \system\util\Page($params);

        $this->app->view()->assign('page', $this->page->show(2));
    }

    # 设置模板文件已经页面标题
	protected function setViewBase($title, $tpl)
	{
        $this->app->view()->assign('title', $title);

        $this->app->view()->assign('tpl_status', $tpl);

        $this->app->view()->display($tpl);
	}
}

?>