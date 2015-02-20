<?php
!defined('ROC') && exit('REFUSED!');
Class userControl extends commonControl
{
    public $page;
    public $per = 30;
    public function index()
    {
        $requestUid = $this->getRquestUid();
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $datas = $this->db->select('roc_topic', array(
                '[>]roc_user' => 'uid'
            ), array(
                'tid',
                'title',
                'content',
                'comments',
                'client',
                'posttime',
                'roc_topic.lasttime',
                'roc_user.uid',
                'roc_user.username'
            ), array(
                'uid' => $requestUid,
                
                'ORDER' => 'roc_topic.tid DESC',
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = Filter::topicOut($datas[$key]['title']);
                
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
                
                $datas[$key]['content'] = Filter::topicOut($datas[$key]['content']);
                
                $datas[$key]['posttime'] = Utils::formatTime($datas[$key]['posttime']);
                
                $datas[$key]['lasttime'] = Utils::formatTime($datas[$key]['lasttime']);
                
                $datas[$key]['tagArray'] = $this->getTopicTag($datas[$key]['tid']);
            }
            
            $this->getFollowStatus($requestUid);
            
            $this->page = new Page($this->per, $this->db->count("roc_topic", array(
                'uid' => $requestUid
            )), Utils::getCurrentPage(), 8, ROOT . 'user/index/uid/' . $requestUid . '/page/');
            
            $userInfo = $this->getMemberInfo('uid', $requestUid);
            
            $this->tpls->assign('seo', $this->getSiteSEO($userInfo['username'] . '的主题', $userInfo['username'], $userInfo['username'] . '的主题'));
            
            $this->tpls->assign('topicArray', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $userInfo);
            
            $this->tpls->assign('RequestType', 'topic');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function reply()
    {
        $requestUid = $this->getRquestUid();
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $datas = $this->db->select('roc_reply', array(
                '[>]roc_user' => 'uid'
            ), array(
                'pid',
                'tid',
                'content',
                'client',
                'posttime',
                'roc_user.uid',
                'roc_user.username'
            ), array(
                'uid' => $requestUid,
                
                'ORDER' => 'roc_reply.pid DESC',
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
                
                $datas[$key]['content'] = Filter::topicOut($datas[$key]['content']);
                
                $datas[$key]['posttime'] = Utils::formatTime($datas[$key]['posttime']);
            }
            
            $this->getFollowStatus($requestUid);
            
            $this->page = new Page($this->per, $this->db->count("roc_reply", array(
                'uid' => $requestUid
            )), Utils::getCurrentPage(), 8, ROOT . 'user/reply/uid/' . $requestUid . '/page/');
            
            $userInfo = $this->getMemberInfo('uid', $requestUid);
            
            $this->tpls->assign('seo', $this->getSiteSEO($userInfo['username'] . '的回复', $userInfo['username'], $userInfo['username'] . '的回复'));
            
            $this->tpls->assign('replyArray', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $userInfo);
            
            $this->tpls->assign('RequestType', 'reply');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function follow()
    {
        $requestUid = $this->getRquestUid();
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $datas = $this->db->select('roc_follow', array(
                '[>]roc_user' => array(
                    'fuid' => 'uid'
                )
            ), array(
                'roc_follow.fuid(uid)',
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'roc_follow.uid' => $requestUid,
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
            }
            
            $this->getFollowStatus($requestUid);
            
            $this->page = new Page($this->per, $this->db->count("roc_follow", array(
                'uid' => $requestUid
            )), Utils::getCurrentPage(), 8, ROOT . 'user/follow/uid/' . $requestUid . '/page/');
            
            $userInfo = $this->getMemberInfo('uid', $requestUid);
            
            $this->tpls->assign('seo', $this->getSiteSEO($userInfo['username'] . '的关注', $userInfo['username'], $userInfo['username'] . '的关注'));
            
            $this->tpls->assign('followList', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $userInfo);
            
            $this->tpls->assign('RequestType', 'follow');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function fans()
    {
        $requestUid = $this->getRquestUid();
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $datas = $this->db->select('roc_follow', array(
                '[>]roc_user' => 'uid'
            ), array(
                'roc_follow.uid',
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'roc_follow.fuid' => $requestUid,
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
            }
            
            $this->getFollowStatus($requestUid);
            
            $this->page = new Page($this->per, $this->db->count("roc_follow", array(
                'fuid' => $requestUid
            )), Utils::getCurrentPage(), 8, ROOT . 'user/fans/uid/' . $requestUid . '/page/');
            
            $userInfo = $this->getMemberInfo('uid', $requestUid);
            
            $this->tpls->assign('seo', $this->getSiteSEO($userInfo['username'] . '的粉丝', $userInfo['username'], $userInfo['username'] . '的粉丝'));
            
            $this->tpls->assign('fansList', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $userInfo);
            
            $this->tpls->assign('RequestType', 'fans');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function favorite()
    {
        $this->checkPrivate(true);
        
        $requestUid = $this->loginInfo['uid'];
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $datas = $this->db->select('roc_favorite', array(
                '[>]roc_user' => 'uid',
                
                '[>]roc_topic' => 'tid'
            ), array(
                'roc_topic.tid',
                'roc_topic.title',
                'roc_topic.content',
                'roc_topic.comments',
                'roc_topic.client',
                'roc_topic.posttime',
                'roc_topic.lasttime',
                'roc_user.uid',
                'roc_user.username'
            ), array(
                'roc_favorite.uid' => $requestUid,
                
                'ORDER' => 'roc_favorite.fid DESC',
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = Filter::topicOut($datas[$key]['title']);
                
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
                
                $datas[$key]['content'] = Filter::topicOut($datas[$key]['content']);
                
                $datas[$key]['posttime'] = Utils::formatTime($datas[$key]['posttime']);
                
                $datas[$key]['lasttime'] = Utils::formatTime($datas[$key]['lasttime']);
            }
            
            $this->page = new Page($this->per, $this->db->count("roc_favorite", array(
                'uid' => $requestUid
            )), Utils::getCurrentPage(), 8, ROOT . 'user/favorite/page/');
            
            $this->tpls->assign('seo', $this->getSiteSEO('我的收藏', '我的收藏', '我的收藏'));
            
            $this->tpls->assign('topicArray', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $this->getMemberInfo('uid', $requestUid));
            
            $this->tpls->assign('RequestType', 'favorite');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function score()
    {
        $this->checkPrivate(true);
        
        $requestUid = $this->loginInfo['uid'];
        
        if ($this->db->has('roc_user', array(
            'uid' => $requestUid
        )))
        {
            $deadLineTime = time() - 2592000;
            
            $datas = $this->db->select('roc_score', array(
                'id',
                'uid',
                'changed',
                'remain',
                'type',
                'time'
            ), array(
                'AND' => array(
                    'uid' => $requestUid,
                    
                    'time[>]' => $deadLineTime
                ),
                
                'ORDER' => 'id DESC',
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['detail'] = Utils::getScoreAction($datas[$key]['type']);
                
                $datas[$key]['time'] = Utils::formatTime($datas[$key]['time']);
            }
            
            $this->page = new Page($this->per, $this->db->count("roc_score", array(
                'AND' => array(
                    'uid' => $requestUid,
                    'time[>]' => $deadLineTime
                )
            )), Utils::getCurrentPage(), 8, ROOT . 'user/score/page/');
            
            $this->tpls->assign('seo', $this->getSiteSEO('我的积分明细', '我的积分明细', '我的积分明细'));
            
            $this->tpls->assign('scoreList', $datas);
            
            $this->tpls->assign('page', $this->page->show());
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('userInfo', $this->getMemberInfo('uid', $requestUid));
            
            $this->tpls->assign('RequestType', 'score');
            
            $this->tpls->display('user');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function notification()
    {
        $this->checkPrivate(true);
        
        $notifyStatus = isset($GLOBALS['Router']['params']['status']) && in_array($GLOBALS['Router']['params']['status'], array(
            0,
            1
        )) ? intval($GLOBALS['Router']['params']['status']) : 0;
        
        $notificationList = $this->db->select('roc_notification', array(
            'nid',
            'uid',
            'tid',
            'pid',
            'fid',
            'isread'
        ), array(
            'AND' => array(
                'atuid' => $this->loginInfo['uid'],
                
                'isread' => $notifyStatus
            ),
            
            'ORDER' => array(
                'nid DESC'
            ),
            
            'LIMIT' => array(
                $this->per * (Utils::getCurrentPage() - 1),
                $this->per
            )
        ));
        
        foreach ($notificationList as $key => $value)
        {
            $notificationList[$key]['username'] = $this->db->get('roc_user', 'username', array(
                'uid' => $value['uid']
            ));
            
            $notificationList[$key]['avatar'] = Image::getAvatarURL($value['uid']);
            
            if ($value['fid'] != 0)
            {
                $NC                                = $this->db->get('roc_floor', array(
                    'content',
                    'posttime'
                ), array(
                    'id' => $value['fid']
                ));
                $notificationList[$key]['content'] = ROOT . 'home/read/' . $value['tid'] . '/#floor-' . $value['fid'];
            }
            else if ($value['pid'] != 0)
            {
                $NC                                = $this->db->get('roc_reply', array(
                    'content',
                    'posttime'
                ), array(
                    'pid' => $value['pid']
                ));
                $notificationList[$key]['content'] = ROOT . 'home/read/' . $value['tid'] . '/#reply-' . $value['tid'];
            }
            else
            {
                $NC = $this->db->get('roc_topic', array(
                    'content',
                    'posttime'
                ), array(
                    'tid' => $value['tid']
                ));
            }
            
            $notificationList[$key]['content'] = Filter::topicOut($NC['content']);
            
            $notificationList[$key]['posttime'] = Utils::formatTime($NC['posttime']);
        }
        
        if ($notifyStatus == 0)
        {
            $this->db->update('roc_notification', array(
                'isread' => 1
            ), array(
                'AND' => array(
                    'atuid' => $this->loginInfo['uid'],
                    'isread' => 0
                )
            ));
        }
        
        $this->page = new Page($this->per, $this->db->count("roc_notification", array(
            'AND' => array(
                'uid' => $this->loginInfo['uid'],
                'isread' => $notifyStatus
            )
        )), Utils::getCurrentPage(), 8, ROOT . 'user/notification/status/' . $notifyStatus . '/page/');
        
        $this->tpls->assign('page', $this->page->show());
        
        $this->tpls->assign('seo', $this->getSiteSEO('我的提醒', '我的提醒', '我的提醒'));
        
        $this->tpls->assign('notificationList', $notificationList);
        
        $this->tpls->assign('notifyStatus', $notifyStatus);
        
        $this->tpls->assign('loginInfo', $this->loginInfo);
        
        $this->tpls->display('notification');
    }
    
    public function whisper()
    {
        $this->checkPrivate(true);
        
        $whisperStatus = isset($GLOBALS['Router']['params']['status']) && in_array($GLOBALS['Router']['params']['status'], array(
            0,
            1,
            2
        )) ? intval($GLOBALS['Router']['params']['status']) : 0;
        
        if ($whisperStatus == 2)
        {
            $whisperList = $this->db->select('roc_whisper', array(
                '[>]roc_user' => array(
                    'atuid' => 'uid'
                )
            ), array(
                'id',
                'atuid',
                'roc_whisper.uid',
                'content',
                'posttime',
                'isread',
                'roc_user.username'
            ), array(
                'AND' => array(
                    'roc_whisper.uid' => $this->loginInfo['uid'],
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                ),
                
                'ORDER' => array(
                    'roc_whisper.id DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
                
            ));
            
            $this->page = new Page($this->per, $this->db->count('roc_whisper', array(
                'AND' => array(
                    'roc_whisper.uid' => $this->loginInfo['uid'],
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                )
            )), Utils::getCurrentPage(), 8, ROOT . 'user/whisper/status/' . $whisperStatus . '/page/');
        }
        else
        {
            $whisperList = $this->db->select('roc_whisper', array(
                '[>]roc_user' => 'uid'
            ), array(
                'id',
                'atuid',
                'uid',
                'content',
                'posttime',
                'isread',
                'roc_user.username'
            ), array(
                'AND' => array(
                    'roc_whisper.atuid' => $this->loginInfo['uid'],
                    
                    'roc_whisper.isread' => $whisperStatus,
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                ),
                
                'ORDER' => array(
                    'roc_whisper.id DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * (Utils::getCurrentPage() - 1),
                    $this->per
                )
                
            ));
            
            if ($whisperStatus == 0)
            {
                $this->db->update('roc_whisper', array(
                    'isread' => 1
                ), array(
                    'AND' => array(
                        'atuid' => $this->loginInfo['uid'],
                        'isread' => 0
                    )
                ));
            }
            
            $this->page = new Page($this->per, $this->db->count('roc_whisper', array(
                'AND' => array(
                    'roc_whisper.atuid' => $this->loginInfo['uid'],
                    
                    'roc_whisper.isread' => $whisperStatus,
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                )
            )), Utils::getCurrentPage(), 8, ROOT . 'user/whisper/status/' . $whisperStatus . '/page/');
        }
        
        foreach ($whisperList as $key => $WP)
        {
            $whisperList[$key]['avatar'] = Image::getAvatarURL($whisperStatus == 2 ? $WP['atuid'] : $WP['uid']);
            
            $whisperList[$key]['content'] = Filter::topicOut($WP['content']);
            
            $whisperList[$key]['posttime'] = Utils::formatTime($WP['posttime']);
        }
        
        $this->tpls->assign('seo', $this->getSiteSEO('我的私信', '我的私信', '我的私信'));
        
        $this->tpls->assign('page', $this->page->show());
        
        $this->tpls->assign('whisperList', $whisperList);
        
        $this->tpls->assign('whisperStatus', $whisperStatus);
        
        $this->tpls->assign('loginInfo', $this->loginInfo);
        
        $this->tpls->display('whisper');
    }
    
    public function login()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['email'], $_POST['password'], $_POST['do']) && $_POST['do'] == 'login')
            {
                $loginAccount = Filter::in($_POST['email']);
                
                $loginPassword = Filter::in($_POST['password']);
                
                if (strlen($loginAccount) < 2)
                {
                    $this->showMsg('账号无效', 'error', 1);
                }
                if ((strlen($loginPassword) < 6 || strlen($loginPassword) > 26) || substr_count($loginPassword, ' ') > 0)
                {
                    $this->showMsg('密码无效', 'error', 2);
                }
                if (Utils::checkEmailValidity($loginAccount))
                {
                    $loginType = 'email';
                }
                else if (Utils::checkNickname($loginAccount) != '')
                {
                    $this->showMsg('账号不合法', 'error', 1);
                }
                else
                {
                    $loginType = 'username';
                }
                
                $userInfo = $this->getMemberInfo($loginType, $loginAccount);
                
                if (empty($userInfo['uid']))
                {
                    $this->showMsg('账号不存在', 'error', 1);
                }
                else
                {
                    if (md5($loginPassword) == $userInfo['password'])
                    {
                        $this->loginCookie($GLOBALS['sys_config']['ROCKEY'], $userInfo['uid'], $userInfo['username'], $userInfo['groupid']);
                        
                        $this->updateLasttime($userInfo['uid']);
                        
                        $this->showMsg('登录成功');
                    }
                    else
                    {
                        $this->showMsg('账号与密码不匹配', 'error', 2);
                    }
                }
            }
            $this->tpls->assign('seo', $this->getSiteSEO('登录', '登录', '登录'));
            
            $this->tpls->assign('currentStatus', 'login');
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->display('login');
        }
    }
    
    public function qqlogin()
    {
        $this->checkPrivate();
        
        require('application/controller/_qq.php');
        
        $qc = new QC($GLOBALS['qq_config']['appid'], $GLOBALS['qq_config']['appkey']);
        
        $qc->qq_login();
    }
    
    public function qqjoin()
    {
        if ($this->checkPrivate() == true)
        {
            $username = trim(Filter::in($_POST['username']));
            
            $usernameError = Utils::checkNickname($username);
            
            if ($username == '')
            {
                $this->showMsg('抱歉，用户名不允许为空', 'error');
            }
            
            if ($usernameError != '')
            {
                $this->showMsg($usernameError, 'error', 2);
            }
            
            if ($this->db->has('roc_user', array(
                'username' => $username
            )))
            {
                $this->showMsg('昵称已被占用', 'error', 2);
            }
            
            $QQArr = json_decode(Secret::decrypt($_COOKIE['qqjoin'], $GLOBALS['sys_config']['ROCKEY']), true);
            
            if (strlen($QQArr['openid']) == 32)
            {
                $userID = $this->addMember($username, '', '', $QQArr['openid'], $GLOBALS['sys_config']['scores']['register'], 1);
                
                if ($userID > 0)
                {
                    Image::CreatQQAvatar($userID, $QQArr['avatar']);
                    
                    $this->loginCookie($GLOBALS['sys_config']['ROCKEY'], $userID, $username, 1);
                    
                    $this->showMsg('QQ登录注册成功');
                    
                }
                else
                {
                    $this->showMsg('QQ登录注册失败', 'error', 0);
                }
            }
            else
            {
                $this->showMsg('QQ登录注册失败', 'error', 0);
            }
        }
    }
    
    public function QQCallBack()
    {
        if ($this->checkPrivate() == true)
        {
            require('application/controller/_qq.php');
            
            $qc = new QC($GLOBALS['qq_config']['appid'], $GLOBALS['qq_config']['appkey']);
            
            $access_token = $qc->qq_callback();
            
            $openid = $qc->get_openid();
            
            $QQArray = array(
                'connect' => 'QQ',
                'access_token' => '',
                'openid' => '',
                'nickname' => '',
                'avatar' => '',
                'sAvatar' => ''
            );
            
            if (strlen($openid) == 32)
            {
                $qc = new QC($GLOBALS['qq_config']['appid'], $GLOBALS['qq_config']['appkey'], $access_token, $openid);
                
                $qqInfo = $qc->get_user_info();
                
                $QQArray['access_token'] = $access_token;
                
                $QQArray['openid'] = $openid;
                
                $QQArray['username'] = isset($qqInfo['nickname']) ? $qqInfo['nickname'] : '';
                
                $QQArray['avatar'] = isset($qqInfo['figureurl_qq_2']) ? $qqInfo['figureurl_qq_2'] : '';
            }
            
            if ($QQArray['openid'] != '')
            {
                $userArr = $this->getMemberInfo('qqid', $QQArray['openid']);
                
                if (empty($userArr['uid']))
                {
                    $qa = Secret::encrypt(json_encode($QQArray), $GLOBALS['sys_config']['ROCKEY']);
                    
                    setcookie("qqjoin", $qa, time() + 600, "/");
                    
                    $this->tpls->assign('title', 'QQ登录');
                    
                    $this->tpls->assign('QQArray', $QQArray);
                    
                    $this->tpls->assign('currentStatus', 'qqjoin');
                    
                    $this->tpls->assign('loginInfo', $this->loginInfo);
                    
                    $this->tpls->display('login');
                }
                else
                {
                    $this->loginCookie($GLOBALS['sys_config']['ROCKEY'], $userArr['uid'], $userArr['username'], $userArr['groupid']);
                    
                    $this->updateLasttime($userArr['uid']);
                    
                    header('location:' . ROOT);
                }
            }
        }
    }
    
    public function register()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['email'], $_POST['nickname'], $_POST['password'], $_POST['verify']) && $_POST['do'] == 'register')
            {
                if (!$GLOBALS['sys_config']['join_switch'])
                {
                    $this->showMsg('账号注册暂不开放，请使用QQ登录', 'error');
                }
                
                $email = strtolower(stripslashes(trim($_POST['email'])));
                
                $nickname = trim(Filter::in($_POST['nickname']));
                
                $password = stripslashes(trim($_POST['password']));
                
                $verify = trim($_POST['verify']);
                
                if ($email == '' || $nickname == '' || $password == '' || $verify == '')
                {
                    if ($verify == '')
                    {
                        $this->showMsg('验证码不能为空', 'error', 4);
                    }
                    if ($email == '')
                    {
                        $this->showMsg('邮箱不能为空', 'error', 1);
                    }
                    if ($nickname == '')
                    {
                        $this->showMsg('用户名不能为空', 'error', 2);
                    }
                    if ($password == '')
                    {
                        $this->showMsg('密码不能为空', 'error', 3);
                    }
                }
                if (md5(strtolower($verify)) != $_SESSION['identifying_code'])
                {
                    $this->showMsg('验证码错误', 'error', 4);
                }
                if (!Utils::checkEmailValidity($email))
                {
                    $this->showMsg('邮件地址不正确', 'error', 1);
                }
                
                $usernameError = Utils::checkNickname($nickname);
                
                if ($usernameError != '')
                {
                    $this->showMsg($usernameError, 'error', 2);
                }
                if (substr_count($password, ' ') > 0)
                {
                    $this->showMsg('密码不能使用空格', 'error', 3);
                }
                if (strlen($password) < 6 || strlen($password) > 26)
                {
                    $this->showMsg('密码长度不合法', 'error', 3);
                }
                if ($this->db->has('roc_user', array(
                    'email' => $email
                )))
                {
                    $this->showMsg('邮件地址已被占用', 'error', 1);
                }
                else
                {
                    if ($this->db->has('roc_user', array(
                        'username' => $nickname
                    )))
                    {
                        $this->showMsg('昵称已被占用', 'error', 2);
                    }
                    else
                    {
                        $userID = $this->addMember($nickname, $email, md5($password), '', $GLOBALS['sys_config']['scores']['register'], 1);
                        
                        if ($userID > 0)
                        {
                            Image::CreatDefaultAvatar($userID);
                            
                            $this->showMsg('注册成功');
                        }
                        else
                        {
                            $this->showMsg('注册失败', 'error', 0);
                        }
                    }
                }
            }
            
            $this->tpls->assign('seo', $this->getSiteSEO('注册', '注册', '注册'));
            
            $this->tpls->assign('currentStatus', 'register');
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->display('login');
        }
    }
    
    public function t()
    {
        if (isset($GLOBALS['Router']['params']))
        {
            $username = Filter::in($GLOBALS['Router']['params']);
            
            if ($this->db->has('roc_user', array(
                'username' => $username
            )))
            {
                $uid = $this->db->get('roc_user', 'uid', array(
                    'username' => $username
                ));
                
                header('location:' . ROOT . 'user/index/uid/' . $uid);
            }
            else
            {
                header('location:' . ROOT);
            }
        }
        else
        {
            header('location:' . ROOT);
        }
    }
    
    public function logout()
    {
        session_destroy();
        
        setcookie('roc_secure', '', 0, '/');
        
        setcookie('roc_login', '', 0, '/');
        
        header('location:' . ROOT);
    }
    
    public function identifyImage()
    {
        return Image::RandomCode();
    }
    
    private function getMemberInfo($key, $value)
    {
        $memberArray = array();
        
        $DBArray = $this->db->get('roc_user', array(
            'uid',
            'username',
            'email',
            'signature',
            'password',
            'regtime',
            'lasttime',
            'qqid',
            'scores',
            'money',
            'groupid'
        ), array(
            $key => $value
        ));
        
        if (!empty($DBArray['uid']))
        {
            $memberArray['uid'] = $DBArray['uid'];
            
            $memberArray['avatar'] = Image::getAvatarURL($DBArray['uid']);
            
            $memberArray['username'] = $DBArray['username'];
            
            $memberArray['email'] = $DBArray['email'];
            
            $memberArray['signature'] = $DBArray['signature'];
            
            $memberArray['password'] = $DBArray['password'];
            
            $memberArray['regtime'] = date('Y年n月j日 H:i', $DBArray['regtime']);
            
            $memberArray['lasttime'] = date('Y年n月j日 H:i', $DBArray['lasttime']);
            
            $memberArray['scores'] = $DBArray['scores'];
            
            $memberArray['money'] = $DBArray['money'];
            
            $memberArray['qqid'] = $DBArray['qqid'];
            
            $memberArray['groupid'] = $DBArray['groupid'];
            
            $memberArray['groupname'] = Utils::getGroupName($DBArray['groupid']);
        }
        
        return $memberArray;
    }
    
    private function addMember($username, $email, $password, $qqid, $scores, $groupid = 1)
    {
        $addDBArray = array(
            'username' => $username,
            
            'email' => $email,
            
            'password' => $password,
            
            'regtime' => time(),
            
            'lasttime' => time(),
            
            'qqid' => $qqid,
            
            'scores' => $scores,
            
            'money' => 0,
            
            'groupid' => $groupid
        );
        
        return $this->db->insert('roc_user', $addDBArray);
    }
    
    private function getFollowStatus($requestUid)
    {
        if ($requestUid != $this->loginInfo['uid'])
        {
            $isFollow = $this->db->has('roc_follow', array(
                'AND' => array(
                    'uid' => $this->loginInfo['uid'],
                    'fuid' => $requestUid
                )
            )) ? 1 : 0;
            
            $this->tpls->assign('isFollow', $isFollow);
        }
    }
    
    private function getRquestUid()
    {
        return (isset($GLOBALS['Router']['params']['uid']) && is_numeric($GLOBALS['Router']['params']['uid'])) ? intval($GLOBALS['Router']['params']['uid']) : $this->loginInfo['uid'];
    }
    
    private function checkPrivate($s = false)
    {
        if ($s && $this->loginInfo['uid'] == 0)
        {
            header('location:' . ROOT . 'user/login/');
        }
        
        if (!$s && $this->loginInfo['uid'] > 0)
        {
            header('location:' . ROOT);
        }
        else
        {
            return true;
        }
    }
}
?>