<?php

namespace app\controller;

Class user extends base
{
    public function index($uid)
    {
        $requestUid = $this->getRquestUid($uid);

        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $userInfo = $this->getMemberInfo('uid', $requestUid);

            $this->getFollowStatus($requestUid);

            $this->app->view()->assign('userInfo', $userInfo);

            $this->app->view()->assign('RequestType', 'index');

            $this->setViewBase($userInfo['username'].'的主页 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }

    public function topic($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        $requestUid = $this->getRquestUid($uid);
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $datas = $this->app->db()->select('roc_topic', array(
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
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'uid' => $requestUid,
                
                'ORDER' => 'roc_topic.tid DESC',
                
                'LIMIT' => array(
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = $this->filter->topicOut($datas[$key]['title']);
                
                $datas[$key]['avatar'] = $this->getUserAvatar($datas[$key]['uid']);
                
                $datas[$key]['content'] = $this->filter->topicOut($datas[$key]['content']);
                
                $datas[$key]['posttime'] = $this->utils->formatTime($datas[$key]['posttime']);
                
                $datas[$key]['lasttime'] = $this->utils->formatTime($datas[$key]['lasttime']);
                
                $datas[$key]['tagArray'] = $this->getTopicTag($datas[$key]['tid']);

                $datas[$key]['praiseArray'] = $this->getTopicPraise($value['tid'], false);
            }
            
            $this->getFollowStatus($requestUid);

            $this->setPage($page, $this->app->db()->count('roc_topic', array('uid' => $requestUid)), 'user-'.$requestUid.'-topic-?');
            
            $userInfo = $this->getMemberInfo('uid', $requestUid);
                        
            $this->app->view()->assign('topicArray', $datas);
            
            $this->app->view()->assign('userInfo', $userInfo);
            
            $this->app->view()->assign('RequestType', 'topic');
            
            $this->setViewBase($userInfo['username'].'的主题 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function reply($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        $requestUid = $this->getRquestUid($uid);
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $datas = $this->app->db()->select('roc_reply', array(
                '[>]roc_user' => 'uid'
            ), array(
                'pid',
                'tid',
                'content',
                'client',
                'posttime',
                'roc_user.uid',
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'uid' => $requestUid,
                
                'ORDER' => 'roc_reply.pid DESC',
                
                'LIMIT' => array(
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = $this->getUserAvatar($datas[$key]['uid']);
                
                $datas[$key]['content'] = $this->filter->topicOut($datas[$key]['content']);
                
                $datas[$key]['posttime'] = $this->utils->formatTime($datas[$key]['posttime']);
            }
            
            $this->getFollowStatus($requestUid);

            $this->setPage($page, $this->app->db()->count('roc_reply', array('uid' => $requestUid)), 'user-'.$requestUid.'-reply-?');

            $userInfo = $this->getMemberInfo('uid', $requestUid);
                        
            $this->app->view()->assign('replyArray', $datas);

            $this->app->view()->assign('userInfo', $userInfo);
            
            $this->app->view()->assign('RequestType', 'reply');
            
            $this->setViewBase($userInfo['username'].'的回复 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function follow($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        $requestUid = $this->getRquestUid($uid);
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $datas = $this->app->db()->select('roc_follow', array(
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
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = $this->getUserAvatar($datas[$key]['uid']);
            }
            
            $this->getFollowStatus($requestUid);

            $this->setPage($page, $this->app->db()->count('roc_follow', array('uid' => $requestUid)), 'user-'.$requestUid.'-follow-?');

            $userInfo = $this->getMemberInfo('uid', $requestUid);
                        
            $this->app->view()->assign('followList', $datas);            
            
            $this->app->view()->assign('userInfo', $userInfo);
            
            $this->app->view()->assign('RequestType', 'follow');
            
            $this->setViewBase($userInfo['username'].'的关注 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function fans($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        $requestUid = $this->getRquestUid($uid);
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $datas = $this->app->db()->select('roc_follow', array(
                '[>]roc_user' => 'uid'
            ), array(
                'roc_follow.uid',
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'roc_follow.fuid' => $requestUid,
                
                'LIMIT' => array(
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = $this->getUserAvatar($datas[$key]['uid']);
            }
            
            $this->getFollowStatus($requestUid);

            $this->setPage($page, $this->app->db()->count('roc_follow', array('fuid' => $requestUid)), 'user-'.$requestUid.'-fans-?');

            $userInfo = $this->getMemberInfo('uid', $requestUid);
                        
            $this->app->view()->assign('fansList', $datas);            
            
            $this->app->view()->assign('userInfo', $userInfo);
            
            $this->app->view()->assign('RequestType', 'fans');
            
            $this->setViewBase($userInfo['username'].'的粉丝 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function favorite($page)
    {
        $this->checkPrivate(true);

        $page = $page > 0 ? $page : 1;
        
        $requestUid = $this->loginInfo['uid'];
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $datas = $this->app->db()->select('roc_topic', array(
                '[>]roc_user' => 'uid',
                
                '[>]roc_favorite' => 'tid'
            ), array(
                'roc_topic.tid',
                'roc_topic.title',
                'roc_topic.comments',
                'roc_topic.client',
                'roc_topic.istop',
                'roc_topic.posttime',
                'roc_topic.lasttime',
                'roc_user.uid',
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'roc_favorite.uid' => $requestUid,
                
                'ORDER' => 'roc_favorite.fid DESC',
                
                'LIMIT' => array(
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = $this->filter->topicOut($value['title']);
                
                $datas[$key]['avatar'] = $this->getUserAvatar($value['uid']);
                
                $datas[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
                
                $datas[$key]['lasttime'] = $this->utils->formatTime($value['lasttime']);

                $datas[$key]['pictures'] = $this->getPictureList($value['tid']);

                $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);

                $datas[$key]['praiseArray'] = $this->getTopicPraise($value['tid'], false);
            }

            $this->setPage($page, $this->app->db()->count('roc_favorite', array('uid' => $requestUid)), 'my/favorite/?');

            $this->app->view()->assign('topicArray', $datas);
            
            $this->app->view()->assign('userInfo', $this->getMemberInfo('uid', $requestUid));
            
            $this->app->view()->assign('RequestType', 'favorite');
            
            $this->setViewBase('我的收藏 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function score($page)
    {
        $this->checkPrivate(true);

        $page = $page > 0 ? $page : 1;
        
        $requestUid = $this->loginInfo['uid'];
        
        if ($this->app->db()->has('roc_user', array('uid' => $requestUid)))
        {
            $deadLineTime = time() - 2592000;
            
            $datas = $this->app->db()->select('roc_score', array(
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
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['detail'] = $this->getScoreAction($datas[$key]['type']);
                
                $datas[$key]['time'] = $this->utils->formatTime($datas[$key]['time']);
            }

            $this->setPage($page, $this->app->db()->count('roc_score', array('AND'=>array('uid' => $requestUid, 'time[>]' => $deadLineTime))), 'my/score/?');
            
            $this->app->view()->assign('scoreList', $datas);
                        
            $this->app->view()->assign('userInfo', $this->getMemberInfo('uid', $requestUid));
            
            $this->app->view()->assign('RequestType', 'score');
            
            $this->setViewBase('我的积分明细 - ', 'user');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function notification($status, $page)
    {
        $this->checkPrivate(true);

        $page = $page > 0 ? $page : 1;

        $notifyStatus = isset($status) && in_array($status, array(0,1)) ? $status : 0;
        
        $notificationList = $this->app->db()->select('roc_notification', array(
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
                $this->per * ($page - 1),
                $this->per
            )
        ));
        
        foreach ($notificationList as $key => $value)
        {
            $notificationList[$key]['username'] = $this->app->db()->get('roc_user', 'username', array('uid' => $value['uid']));
            
            $notificationList[$key]['avatar'] = $this->getUserAvatar($value['uid']);
            
            if ($value['fid'] != 0)
            {
                $NC = $this->app->db()->get('roc_floor', array(
                    'content',
                    'posttime'
                ), array(
                    'id' => $value['fid']
                ));

                $notificationList[$key]['content'] = $this->app->get('root').'read/' . $value['tid'] . '/#floor-' . $value['fid'];
            }
            else if ($value['pid'] != 0)
            {
                $NC = $this->app->db()->get('roc_reply', array(
                    'content',
                    'posttime'
                ), array(
                    'pid' => $value['pid']
                ));
                $notificationList[$key]['content'] = $this->app->get('root').'read/' . $value['tid'] . '/#reply-' . $value['tid'];
            }
            else
            {
                $NC = $this->app->db()->get('roc_topic', array(
                    'title(content)',
                    'posttime'
                ), array(
                    'tid' => $value['tid']
                ));
            }
            
            $notificationList[$key]['content'] = $this->filter->topicOut($NC['content']);
            
            $notificationList[$key]['posttime'] = $this->utils->formatTime($NC['posttime']);
        }

        $this->setPage($page, $this->app->db()->count('roc_notification', array(
            'AND' => array(
                'uid' => $this->loginInfo['uid'],
                'isread' => $notifyStatus
            )
        )), 'notification/'.$notifyStatus.'/?');
                
        $this->app->view()->assign('notificationList', $notificationList);
        
        $this->app->view()->assign('notifyStatus', $notifyStatus);
        
        $this->setViewBase('我的提醒 - ', 'notification');
    }
    
    public function whisper($status, $page)
    {
        $this->checkPrivate(true);

        $page = $page > 0 ? $page : 1;
        
        $whisperStatus = isset($status) && in_array($status, array(0,1,2)) ? $status : 0;
        
        if ($whisperStatus == 2)
        {
            $whisperList = $this->app->db()->select('roc_whisper', array(
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
                    $this->per * ($page - 1),
                    $this->per
                )
                
            ));
            
            $this->setPage($page, $this->app->db()->count('roc_whisper', array(
                'AND' => array(
                    'roc_whisper.uid' => $this->loginInfo['uid'],
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                )
            )), 'whisper/'.$whisperStatus.'/?');
        }
        else
        {
            $whisperList = $this->app->db()->select('roc_whisper', array(
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
                    $this->per * ($page - 1),
                    $this->per
                )
                
            ));

            $this->setPage($page, $this->app->db()->count('roc_whisper', array(
                'AND' => array(
                    'roc_whisper.atuid' => $this->loginInfo['uid'],

                    'roc_whisper.isread' => $whisperStatus,
                    
                    'roc_whisper.del_flag[!]' => $this->loginInfo['uid']
                )
            )), 'whisper/'.$whisperStatus.'/?');
        }
        
        foreach ($whisperList as $key => $WP)
        {
            $whisperList[$key]['avatar'] = $this->getUserAvatar($whisperStatus == 2 ? $WP['atuid'] : $WP['uid']);
            
            $whisperList[$key]['content'] = $this->topicOut($WP['content']);
            
            $whisperList[$key]['posttime'] = $this->utils->formatTime($WP['posttime']);
        }
        
        $this->app->view()->assign('whisperList', $whisperList);
        
        $this->app->view()->assign('whisperStatus', $whisperStatus);
        
        $this->setViewBase('我的私信 - ', 'whisper');
    }
    
    public function login()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['email'], $_POST['password'], $_POST['do']) && $_POST['do'] == 'login')
            {
                $loginAccount = $this->filter->in($_POST['email']);
                
                $loginPassword = $this->filter->in($_POST['password']);
                
                if (strlen($loginAccount) < 2)
                {
                    $this->showMsg('账号无效', 'error', 1);
                }
                if ((strlen($loginPassword) < 6 || strlen($loginPassword) > 26) || substr_count($loginPassword, ' ') > 0)
                {
                    $this->showMsg('密码无效', 'error', 2);
                }
                if ($this->utils->checkEmailValidity($loginAccount))
                {
                    $loginType = 'email';
                }
                else if ($this->utils->checkNickname($loginAccount) != '')
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
                        $this->loginCookie($this->sys['rockey'], $userInfo['uid'], $userInfo['username'], $userInfo['groupid']);
                        
                        $this->updateLasttime($userInfo['uid'], time() - 30);
                        
                        $this->showMsg('登录成功');
                    }
                    else
                    {
                        $this->showMsg('账号与密码不匹配', 'error', 2);
                    }
                }
            }
            
            $this->app->view()->assign('currentStatus', 'login');
                        
            $this->setViewBase('登录 - ', 'login');
        }
    }
    
    public function qqlogin()
    {
        $this->checkPrivate();
        
        $qc = new \app\controller\QC($this->sys['appid'], $this->sys['appkey'], $this->app->get('root'));
        
        $return_url = $qc->qq_login();

        $this->app->redirect($return_url);
    }
    
    public function qqjoin()
    {
        if ($this->checkPrivate() == true)
        {
            $username = trim($this->filter->in($_POST['username']));
            
            $usernameError = $this->utils->checkNickname($username);
            
            if ($username == '')
            {
                $this->showMsg('抱歉，用户名不允许为空', 'error');
            }
            
            if ($usernameError != '')
            {
                $this->showMsg($usernameError, 'error', 2);
            }
            
            if ($this->app->db()->has('roc_user', array('username' => $username)))
            {
                $this->showMsg('昵称已被占用', 'error', 2);
            }
            
            $QQArr = json_decode($this->secret->decrypt($_COOKIE['qqjoin'], $this->sys['rockey']), true);
            
            if (strlen($QQArr['openid']) == 32)
            {
                $userID = $this->addMember($username, '', '', $QQArr['openid'], $this->sys['scores_register'], 1);
                
                if ($userID > 0)
                {
                    $this->CreatQQAvatar($userID, $QQArr['avatar']);
                    
                    $this->loginCookie($this->sys['rockey'], $userID, $username, 1);
                    
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
            $qc = new \app\controller\QC($this->sys['appid'], $this->sys['appkey'], $this->app->get('root'));
            
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
                $qc = new \app\controller\QC($this->sys['appid'], $this->sys['appkey'], $this->app->get('root'), $access_token, $openid);
                
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
                    $qa = $this->secret->encrypt(json_encode($QQArray), $this->sys['rockey']);
                    
                    setcookie('qqjoin', $qa, time() + 300, '/');
                    
                    $this->app->view()->assign('title', 'QQ登录');
                    
                    $this->app->view()->assign('QQArray', $QQArray);
                    
                    $this->app->view()->assign('currentStatus', 'qqjoin');
                    
                    $this->setViewBase('QQ授权登录 - ', 'login');
                }
                else
                {
                    $this->loginCookie($this->sys['rockey'], $userArr['uid'], $userArr['username'], $userArr['groupid']);
                    
                    $this->updateLasttime($userArr['uid'], time() - 30);
                    
                    $this->app->redirect('/');
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
                if ($this->sys['join_switch'] == 0)
                {
                    $this->showMsg('账号注册暂不开放，请使用QQ一键登录', 'error');
                }
                
                $email = strtolower(stripslashes(trim($_POST['email'])));
                
                $nickname = trim($this->filter->in($_POST['nickname']));
                
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

                if (!$this->utils->checkEmailValidity($email))
                {
                    $this->showMsg('邮件地址不正确', 'error', 1);
                }
                
                $usernameError = $this->utils->checkNickname($nickname);
                
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

                if ($this->app->db()->has('roc_user', array('email' => $email)))
                {
                    $this->showMsg('邮件地址已被占用', 'error', 1);
                }
                else
                {
                    if ($this->app->db()->has('roc_user', array(
                        'username' => $nickname
                    )))
                    {
                        $this->showMsg('昵称已被占用', 'error', 2);
                    }
                    else
                    {
                        $userID = $this->addMember($nickname, $email, md5($password), '', $this->sys['scores_register'], 1);
                        
                        if ($userID > 0)
                        {
                            $this->CreatDefaultAvatar($userID);
                            
                            $this->showMsg('注册成功');
                        }
                        else
                        {
                            $this->showMsg('注册失败', 'error', 0);
                        }
                    }
                }
            }

            $this->app->view()->assign('join_switch', $this->sys['join_switch']);
                        
            $this->app->view()->assign('currentStatus', 'register');
            
            $this->setViewBase('用户注册 - ', 'login');
        }
    }

    public function resetPassword()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['do'], $_POST['email'], $_POST['verify']) && $_POST['do'] == 'resetPassword')
            {
                $verify = trim($_POST['verify']);

                $email = strtolower(stripslashes(trim($_POST['email'])));

                if (md5(strtolower($verify)) != $_SESSION['identifying_code'])
                {
                    $this->showMsg('验证码错误', 'error', 4);
                }

                if ($this->app->db()->has('roc_user', array('email'=>$email)))
                {
                    if (!isset($_COOKIE['roc_has_sendEmail']))
                    {
                        $uid = $this->app->db()->get('roc_user', 'uid', array('email'=>$email));

                        if ($this->app->db()->has('roc_user_reset', array('uid'=>$uid)))
                        {
                            $code_time = $this->app->db()->get('roc_user_reset', 'time', array('uid'=>$uid));

                            if ($code_time <= time())
                            {
                                $this->app->db()->update('roc_user_reset', array('code' => $this->utils->getRandomCode(16), 'time' => (time() + 3600)), array('uid'=>$uid));
                            }
                        }
                        else
                        {
                            $insertID = $this->app->db()->insert('roc_user_reset', array(
                                'uid' => $uid,
                                'code' => $this->utils->getRandomCode(16),
                                'time' => (time() + 3600)
                            ));

                            if ($insertID == 0)
                            {
                                $this->showMsg('系统发生错误，请重试', 'error');
                            }
                        }

                        $info = $this->app->db()->get('roc_user_reset', array('code', 'time'), array('uid'=>$uid));

                        $subject = $this->sys['sitename']."找回邮件(请勿回复)";

                        $body = '你好，你在'.$this->sys['sitename'].'的重置密码验证码： <b>'.$info['code'].'</b> ，请复制后到网站完成验证并重置密码。<br>该验证码有效期为1小时，将在 <b>'.date('Y年m月d日  H:i:s ', $info['time']).'</b> 过期，使用后立即失效。（系统邮件，请勿回复）';

                        $this->sendmailto($email,$subject,$body);

                        setcookie('roc_has_sendEmail', 1, time() + 60, '/');

                        $this->showMsg('验证码发送成功！', 'success');
                    }
                    else
                    {
                        $this->showMsg('邮件发送太频繁了，请1分钟后再试！', 'error');
                    }
                }
                else
                {
                    $this->showMsg('邮箱不存在！', 'error');
                }
            }

            $this->app->view()->assign('currentStatus', 'resetPassword');

            $this->setViewBase('找回密码 - ', 'login');
        }
    }

    public function doReset()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['email'], $_POST['code'], $_POST['password'], $_POST['repassword']))
            {
                $email = strtolower(stripslashes(trim($_POST['email'])));

                $code = stripslashes(trim($_POST['code']));
                
                $password = stripslashes(trim($_POST['password']));

                $repassword = stripslashes(trim($_POST['repassword']));

                if ($email == '' || $code == '' || $password == '' || $repassword == '')
                {
                    if ($email == '')
                    {
                        $this->showMsg('邮箱不能为空', 'error', 1);
                    }
                    if ($password == '')
                    {
                        $this->showMsg('密码不能为空', 'error', 3);
                    }
                    if ($repassword == '')
                    {
                        $this->showMsg('再次密码不能为空', 'error', 4);
                    }
                    if ($code == '')
                    {
                        $this->showMsg('验证码不能为空', 'error', 2);
                    }
                }

                if ($password !== $repassword)
                {
                    $this->showMsg('两次密码不一样', 'error', 3);
                }

                if (substr_count($password, ' ') > 0)
                {
                    $this->showMsg('密码不能使用空格', 'error', 3);
                }

                if (strlen($password) < 6 || strlen($password) > 26)
                {
                    $this->showMsg('密码长度不合法', 'error', 3);
                }

                if ($this->app->db()->has('roc_user', array('email'=>$email)))
                {
                    $uid = $this->app->db()->get('roc_user', 'uid', array('email'=>$email));

                    $info = $this->app->db()->get('roc_user_reset', array('code', 'time'), array('uid'=>$uid));

                    if ($info['code'] === $code && $info['time'] > time())
                    {
                        $this->app->db()->update('roc_user', array('password'=>md5($password)), array('uid'=>$uid));

                        $this->app->db()->update('roc_user_reset', array('time'=>0), array('uid'=>$uid));

                        $this->showMsg('密码重置成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('验证码错误或已过期', 'error');
                    }

                }
                else
                {
                    $this->showMsg('该Email不存在', 'error', 0);
                }
            }

            $this->app->view()->assign('currentStatus', 'doReset');

            $this->setViewBase('重置密码 - ', 'login');
        }
    }
    
    # 退出登录
    public function logout()
    {
        session_destroy();
        
        setcookie('roc_secure', '', 0, '/');
        
        setcookie('roc_login', '', 0, '/');
        
        $this->app->redirect('/');
    }

    # 转跳至用户主页
    public function transUser($username)
    {
        $username = $this->filter->in($username);
        
        if ($this->app->db()->has('roc_user', array('username' => $username)))
        {
            $uid = $this->app->db()->get('roc_user', 'uid', array('username' => $username));
            
            $this->app->redirect('/user/'.$uid);
        }
        else
        {
            $this->app->redirect('/');
        }
    }

    # 获取验证码
    public function identifyImage()
    {
        return $this->RandomCode();
    }

    private function RandomCode($width = 120, $height = 38, $verifyName = 'identifying_code')
    {
        $textArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'i', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        $keyindex  = count($textArray) - 1;

        $verifyNum = '';

        for ($i = 0; $i < 5; $i++)
        {
            $verifyNum .= $textArray[rand(0, $keyindex)];
        }

        $_SESSION[$verifyName] = md5(strtolower($verifyNum));

        $im = imagecreate($width, $height);

        imagecolorallocatealpha($im, 255, 255, 255, 100);

        $color = imagecolorallocate($im, rand(0, 230), rand(0, 230), rand(0, 230));

        imagettftext($im, 16, 0, 5, 30, $color, 'app/template/'.$this->sys['theme'].'/assets/img/OctemberScript.ttf', $verifyNum);

        $this->output($im, 'png');
    }

    private function output($im, $type = 'png', $filename = '')
    {
        header("Content-type: image/" . $type);

        $ImageFun = 'image' . $type;

        if (empty($filename))
        {
            $ImageFun($im);
        }
        else
        {
            $ImageFun($im, $filename);
        }
        
        imagedestroy($im);

        exit;
    }

    private function CreatDefaultAvatar($userId)
    {
        $avatar_dir = $this->avatarPath($userId);

        if (!is_dir($avatar_dir))
        {
            @mkdir($avatar_dir, 0777);
        }

        $defaultBigAvatar = 'app/uploads/avatars/0/0/avatar_b.png';

        $defaultSmallAvatar = 'app/uploads/avatars/0/0/avatar_s.png';

        $newBigAvatar = $avatar_dir . '100.png';

        $newSmallAvatar = $avatar_dir . '50.png';

        @copy($defaultBigAvatar, $newBigAvatar);

        @copy($defaultSmallAvatar, $newSmallAvatar);
    }

    private function CreatQQAvatar($userId, $avatar)
    {
        $avatar_dir = $this->avatarPath($userId);

        if (!is_dir($avatar_dir))
        {
            @mkdir($avatar_dir, 0777);
        }

        $defaultBigAvatar = 'app/uploads/avatars/0/0/avatar_b.png';

        $defaultSmallAvatar = 'app/uploads/avatars/0/0/avatar_s.png';

        $avatarFile = $avatar_dir . '100.png';

        $avatarFile_S = $avatar_dir . '50.png';

        $avatarData = @file_get_contents($avatar, false, stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 3
            )
        )));

        @file_put_contents($avatarFile, $avatarData);

        @file_put_contents($avatarFile_S, $avatarData);

        $imgInfo = @getimagesize($avatarFile);

        if (isset($imgInfo[0], $imgInfo[1], $imgInfo[2]) && in_array($imgInfo[2], array(1, 2, 3 )))
        {
            $this->createImg($avatarFile, $imgInfo, 100, $avatarFile);

            $this->createImg($avatarFile_S, $imgInfo, 50, $avatarFile_S);
        }
        else
        {
            @unlink($avatarFile);

            @copy($defaultBigAvatar, $avatarFile);

            @copy($defaultSmallAvatar, $avatarFile);
        }
    }

    private function createImg($source, $imgInfo, $wh, $destination)
    {
        $image_p = imagecreatetruecolor($wh, $wh);

        switch ($imgInfo[2])
        {
            case 1:
                $image = imagecreatefromgif($source);
                break;

            case 2:
                $image = imagecreatefromjpeg($source);
                break;

            case 3:
                $image = imagecreatefrompng($source);
                break;
        }

        if ($imgInfo[0] > $imgInfo[1])
        {
            $imgInfo[0] = $imgInfo[0] - ($imgInfo[0] - $imgInfo[1]);
        }

        if ($imgInfo[0] < $imgInfo[1])
        {
            $imgInfo[1] = $imgInfo[1] - ($imgInfo[1] - $imgInfo[0]);
        }

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $wh, $wh, $imgInfo[0], $imgInfo[1]);
        
        imagejpeg($image_p, $destination, 100);
        
        imagedestroy($image_p);
        
        imagedestroy($image);
    }

    private function avatarPath($uid)
    {
        if (!is_dir('app/uploads/avatars/' . intval($uid / 1000) . '/'))
        {
            @mkdir('app/uploads/avatars/' . intval($uid / 1000) . '/', 0777);
        }
        
        return 'app/uploads/avatars/' . intval($uid / 1000) . '/' . $uid . '/';
    }

    private function sendmailto($mailto, $mailsub, $mailbd)
    {
        $smtpserver = $this->sys['smtp_server'];

        $smtpserverport = $this->sys['smtp_port'];

        $smtpusermail = "ROCBOSS <".$this->sys['smtp_user'].">";

        $smtpemailto = $mailto;

        $smtpuser = $this->sys['smtp_user'];

        $smtppass = $this->sys['smtp_password'];

        # 邮件主题
        $mailsubject = $mailsub; 

        # 防止乱码
        $mailsubject = "=?UTF-8?B?" . base64_encode($mailsubject) . "?="; 

        # 邮件内容
        $mailbody = $mailbd;

        $mailtype = "HTML";

        $smtp = new \system\util\Smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);

        # 是否显示发送的调试信息
        $smtp->debug = FALSE;

        $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
    }
    
    private function getMemberInfo($key, $value)
    {
        $memberArray = array();
        
        $DBArray = $this->app->db()->get('roc_user', array(
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
            
            $memberArray['avatar'] = $this->getUserAvatar($DBArray['uid']);
            
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
            
            $memberArray['groupname'] = $this->getGroupName($DBArray['groupid']);
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
        
        return $this->app->db()->insert('roc_user', $addDBArray);
    }
    
    private function getFollowStatus($requestUid)
    {
        if ($requestUid != $this->loginInfo['uid'])
        {
            $isFollow = $this->app->db()->has('roc_follow', array(
                'AND' => array(
                    'uid' => $this->loginInfo['uid'],
                    'fuid' => $requestUid
                )
            )) ? 1 : 0;
            
            $this->app->view()->assign('isFollow', $isFollow);
        }
    }

    private function getPictureList($tid)
    {
        $pictureArray = $this->app->db()->select('roc_attachment', 'path', array('tid' => $tid));
        
        return $pictureArray;
    }

    private function getRquestUid($uid)
    {
        return (isset($uid) && is_numeric($uid)) && $uid > 0 ? $uid : $this->loginInfo['uid'];
    }
    
    private function checkPrivate($s = false)
    {
        if ($s && $this->loginInfo['uid'] == 0)
        {
            $this->app->redirect('/login');
        }
        
        if (!$s && $this->loginInfo['uid'] > 0)
        {
            $this->app->redirect('/');
        }
        else
        {
            return true;
        }
    }
}
?>