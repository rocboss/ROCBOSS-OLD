<?php
!defined('ROC') && exit('REFUSED!');
Class mobileUserControl extends commonControl
{
    public $page;
    public $per = 15;
    
    public function userDetailInfo(){
        
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $loginUid = $_POST['loginUserId'];
        
        $userInfo = $this->getMemberInfo('uid', $requestUid);
        
        $userInfo['homeThemeBack'] = Image::getHomeThemeBackURL($loginUid);
        
        $topicsCount = $this->db->count("roc_topic", array(
                'uid' => $requestUid
            ));
        
        $replyCount = $this->db->count("roc_reply", array(
                'uid' => $requestUid
            ));
        
        $fansCount = $this->db->count("roc_follow", array(
                'uid' => $requestUid
            ));
        
        if($requestUid != $loginUid){
            
            $isFollow = $this->getFollowStatus($requestUid, $loginUid);
            
            $finalUserInfo = array('userInfo' => $userInfo,
                                   'isFollow' => $isFollow,
                                   'topicsCount' => $topicsCount,
                                   'replyCount' => $replyCount,
                                   'fansCount' => $fansCount
                                   );
                    
            $this->echoAppJsonResult('用户详情',$finalUserInfo,0);

        }else{
            
            $whisperCount = $this->db->count("roc_whisper", array(
                'roc_whisper.uid' => $loginUid,
            ));
            
            $whisperUnreadCount = $this->db->count('roc_whisper', array(
                'AND' => array(
                    'atuid' => $loginUid,
                    'isread' => 0
                )
            ));
            
            $notificationCount = $this->db->count("roc_notification", array(
                'uid' => $loginUid,
            ));
            
            $notificationUnreadCount = $this->db->count('roc_notification', array(
                'AND' => array(
                    'atuid' => $loginUid,
                    'isread' => 0
                )
            ));
                        
            $finalUserInfo = array('userInfo' => $userInfo,
                                   'topicsCount' => $topicsCount,
                                   'replyCount' => $replyCount,
                                   'fansCount' => $fansCount,
                                   'whisperCount' => $whisperCount,
                                   'whisperUnreadCount' => $whisperUnreadCount,
                                   'notificationCount' => $notificationCount,
                                   'notificationUnreadCount' => $notificationUnreadCount
                                   );
                    
            $this->echoAppJsonResult('用户详情',$finalUserInfo,0);
        }
    }

    public function topics()
    {
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $pageIndex = $_POST['pageIndex'];
        
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
                    $this->per * ($pageIndex - 1),
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
            
            $this->echoAppJsonResult('我的帖子列表', $datas,0);
        }
        else
        {
            $this->echoAppJsonResult('用户不存在', array(),0);
        }
    }
    
    public function reply()
    {
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $pageIndex = $_POST['pageIndex'];
        
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
                    $this->per * ($pageIndex - 1),
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
            
            $this->echoAppJsonResult('我的回复',$datas,0);
        }
        else
        {
            $this->echoAppJsonResult('用户不存在',array(),1);
        }
    }
    
    public function follow()
    {
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $pageIndex = $_POST['pageIndex'];
        
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
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
            }
            
            $this->echoAppJsonResult('某用户的关注',$datas,0);    
        }
        else
        {
            $this->echoAppJsonResult('用户不存在',$datas,1);
        }
    }
    
    public function fans()
    {
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $pageIndex = $_POST['pageIndex'];
        
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
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
            }
                        
            $this->echoAppJsonResult('用户粉丝列表',$datas,0);
        }
        else
        {
            $this->echoAppJsonResult('用户不存在',array(),1);
        }
    }
    
    public function favorite()
    {        
        $this->validateToken();
        
        $requestUid = $_POST['userId'];
        $pageIndex = $_POST['pageIndex'];
               
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
                    $this->per * ($pageIndex - 1),
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
            
            $this->echoAppJsonResult('某用户的收藏',$datas,0);
        }
        else
        {
            $this->echoAppJsonResult('用户不存在',array(),1);
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
        $this->validateToken();
        
        $notifyStatus = $_POST['status'];
        $loginUid = $_POST['loginUserId'];
        $pageIndex = $_POST['pageIndex'];
        
        $notificationList = $this->db->select('roc_notification', array(
            'nid',
            'uid',
            'tid',
            'pid',
            'fid',
            'isread'
        ), array(
            'AND' => array(
                'atuid' => $loginUid,
                
                'isread' => $notifyStatus
            ),
            
            'ORDER' => array(
                'nid DESC'
            ),
            
            'LIMIT' => array(
                $this->per * ($pageIndex - 1),
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
                    'atuid' => $loginUid,
                    'isread' => 0
                )
            ));
        }
        
        if($notifyStatus == 0){
            
           $this->echoAppJsonResult('未读提醒列表',$notificationList,0);
           
        }else{
            
           $this->echoAppJsonResult('已读提醒列表',$notificationList,0);
        }
    }
    
    public function whisper()
    {        
        $this->validateToken();
        
        /* 0: 未读 1:已读 2:已发 */
        $whisperStatus = $_POST['status'];
        $loginUid = $_POST['loginUserId'];
        $pageIndex = $_POST['pageIndex'];
        
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
                    'roc_whisper.uid' => $loginUid,
                    
                    'roc_whisper.del_flag[!]' => $loginUid
                ),
                
                'ORDER' => array(
                    'roc_whisper.id DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
                
            ));            
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
                    'roc_whisper.atuid' => $loginUid,
                    
                    'roc_whisper.isread' => $whisperStatus,
                    
                    'roc_whisper.del_flag[!]' => $loginUid
                ),
                
                'ORDER' => array(
                    'roc_whisper.id DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
                
            ));
            
            if ($whisperStatus == 0)
            {
                $this->db->update('roc_whisper', array(
                    'isread' => 1
                ), array(
                    'AND' => array(
                        'atuid' => $loginUid,
                        'isread' => 0
                    )
                ));
            }
        }
        
        foreach ($whisperList as $key => $WP)
        {
            $whisperList[$key]['avatar'] = Image::getAvatarURL($whisperStatus == 2 ? $WP['atuid'] : $WP['uid']);
            
            $whisperList[$key]['content'] = Filter::topicOut($WP['content']);
            
            $whisperList[$key]['posttime'] = Utils::formatTime($WP['posttime']);
        }
        
        if($whisperStatus == 2){
            
            $this->echoAppJsonResult('已发私信', $whisperList,0);
        }
        
        if($whisperStatus == 0){
            
            $this->echoAppJsonResult('未读私信', $whisperList,0);
        }
        
        if($whisperStatus == 1){
            
            $this->echoAppJsonResult('已读私信', $whisperList,0);
        }
    }
    
    public function login()
    {
        if (isset($_POST['email'], $_POST['password']))
            {
                $loginAccount = Filter::in($_POST['email']);
                
                $loginPassword = Filter::in($_POST['password']);
                
                if (strlen($loginAccount) < 2)
                {
                    $this->echoAppJsonResult('账号无效', array(), 1);
                }
                if ((strlen($loginPassword) < 6 || strlen($loginPassword) > 26) || substr_count($loginPassword, ' ') > 0)
                {
                    $this->echoAppJsonResult('密码无效', array(), 2);
                }
                if (Utils::checkEmailValidity($loginAccount))
                {
                    $loginType = 'email';
                }
                else if (Utils::checkNickname($loginAccount) != '')
                {
                    $this->echoAppJsonResult('账号不合法', array(), 1);
                }
                else
                {
                    $loginType = 'username';
                }
                
                $userInfo = $this->getMemberInfo($loginType, $loginAccount);
                
                if (empty($userInfo['uid']))
                {
                    $this->echoAppJsonResult('账号不存在', array(), 1);
                }
                else
                {
                    if (md5($loginPassword) == $userInfo['password'])
                    {
                        $this->updateLasttime($userInfo['uid']);
                        
                        //创建令牌
                        $userInfo['token'] = Secret::createMobileLoginToken();
                        
                        $this->echoAppJsonResult('登录成功',$userInfo,0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('账号与密码不匹配', array(), 2);
                    }
                }
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
            
            if ($nicknameError != '')
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
        if (isset($_POST['email'], $_POST['nickname'], $_POST['password']))
            { 
                $email = strtolower(stripslashes(trim($_POST['email'])));
                
                $nickname = trim(Filter::in($_POST['nickname']));
                
                $password = stripslashes(trim($_POST['password']));
                                
                if ($email == '' || $nickname == '' || $password == '')
                {
                    if ($email == '')
                    {
                        $this->echoAppJsonResult('邮箱不能为空',array(),1);
                    }
                    if ($nickname == '')
                    {
                        $this->echoAppJsonResult('用户名不能为空',array(),2);
                    }
                    if ($password == '')
                    {
                        $this->echoAppJsonResult('密码不能为空',array(),3);
                    }
                }
                
                if (!Utils::checkEmailValidity($email))
                {
                    $this->echoAppJsonResult('邮件地址不正确',array(),4);
                }
                
                $usernameError = Utils::checkNickname($nickname);
                
                if ($usernameError != '')
                {
                    $this->echoAppJsonResult($usernameError,array(),5);
                }
                if (substr_count($password, ' ') > 0)
                {
                    $this->echoAppJsonResult('密码不能使用空格',array(),6);
                }
                if (strlen($password) < 6 || strlen($password) > 26)
                {
                    $this->echoAppJsonResult('密码长度不合法',array(),7);
                }
                if ($this->db->has('roc_user', array(
                    'email' => $email
                )))
                {
                    $this->echoAppJsonResult('邮件地址已被占用',array(),8);
                }
                else
                {
                    if ($this->db->has('roc_user', array(
                        'username' => $nickname
                    )))
                    {
                        $this->echoAppJsonResult('昵称已被占用',array(),9);
                    }
                    else
                    {
                        $userID = $this->addMember($nickname, $email, md5($password), '', $GLOBALS['sys_config']['scores']['register'], 1);
                        
                        if ($userID > 0)
                        {
                            Image::CreatDefaultAvatar($userID);
                            
                            $this->echoAppJsonResult('注册成功',array('userId'=>$userID),0);
                        }
                        else
                        {
                            $this->echoAppJsonResult('注册失败',array(),10);
                        }
                    }
                }
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
    
    public function registAppleDevice() {
                
        $appname = $_POST['appname'];
        $appversion = $_POST['appversion'];
        $deviceuid = $_POST['deviceuid'];
        $devicetoken = $_POST['devicetoken'];
        $devicename = $_POST['devicename'];
        $devicemodel = $_POST['devicemodel'];
        $deviceversion = $_POST['deviceversion'];
        $pushbadge = $_POST['pushbadge'];
        $pushalert = $_POST['pushalert'];
        $pushsound = $_POST['pushsound'];
        $loginUid = $_POST['loginUserId'];
        $environment = $_POST['environment'];

        $this->registDevice(0, $deviceuid, $loginUid);

        $this->pushService->registAppleDevice($appname, $appversion, $deviceuid, $devicetoken, $devicename, $devicemodel, $deviceversion, $pushbadge, $pushalert, $pushsound,$loginUid,$environment);
        
    }
    
    public function registAndroidDevice(){
        
        
    }


    public function registDevice($deviceType,$deviceToken,$loginUid){
        
        if ($this->db->has('roc_device', array(
            'user_id' => $loginUid
        )))
        {           
           $addDBArray = array(
               
            'last_used_device' => $deviceType,
             
            );
           
           if($deviceType == 0){
                
               $addDBArray['ios_token'] = $deviceToken;
               
            }else{
                
               $addDBArray['android_token'] = $deviceToken;

            }
            
            $result =  $this->db->update('roc_device',$addDBArray,array('user_id'=>$loginUid));
           
            $this->echoAppJsonResult('更新设备操作',array('deviceId'=>$result),0);
            
        }else{
            
            $addDBArray = array(
                
            'last_used_device' => $deviceType,
            
            'user_id' => $loginUid,
   
            );
            
            if($deviceType == 0){
                
               $addDBArray['ios_token'] = $deviceToken;
               
            }else{
                
               $addDBArray['android_token'] = $deviceToken;

            }
            
            $result = $this->db->insert('roc_device',$addDBArray);
            
            return $result;

        }
                
    }
    
    public function pushTest() {
        
        $loginUid = $_POST['loginUserId'];
        
        $this->pushService->pushMessageToMobile('推送测试','iOS推送描述',1,array('status'=>1),'',$loginUid);
     
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
    
    private function getFollowStatus($requestUid,$loginUserId)
    {
        if ($requestUid != $loginUserId)
        {
            $isFollow = $this->db->has('roc_follow', array(
                'AND' => array(
                    'uid' => $loginUserId,
                    'fuid' => $requestUid
                )
            )) ? 1 : 0;
            
            return $isFollow;
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
    
    private function echoAppJsonResult($msg,$resultDictionary = array(),$status){
                        
        $resultArray = array('status'=>$status,'data'=>$resultDictionary,'msg'=>$msg);
        
        echo json_encode($resultArray);
        
    }
    
    protected function validateToken(){
        
        if (isset($_POST['token'])){
           
            $validate = Secret::validateLoginToken($_POST['token']);
        
        if($validate){
            
            return $validate;
            
        }  else {
           
            $this->echoAppJsonResult('token非法', array(),1);
                        
            exit();
        }
        
       }else{
           
           $this->echoAppJsonResult('非法请求', array(),1);
                        
            exit();
       }
    } 
}
?>