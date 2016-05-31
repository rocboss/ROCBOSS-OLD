<?php

namespace frontend;

use \Controller;
use \Roc;

class UserController extends BaseController
{
    public static $per = 12;

    // VIP2 升级价格(RMB)
    public static $v2Price = 38800;

    // VIP3 升级价格(RMB)
    public static $v3Price = 80000;

    /**
     * 登录
     * @return [type] [description]
     */
    public static function login($type)
    {
        if (self::getloginInfo()['uid'] > 0)
        {
            Roc::redirect('/');
        }

        switch ($type)
        {
            case 'qq':

                Roc::redirect(Roc::qq()->qq_login());

                break;

            case 'weibo':

                Roc::redirect(Roc::weibo()->getAuthorizeURL(Roc::get('weibo.callback')));

                break;

            default:

                if (Roc::request()->method == 'POST')
                {
                    $captcha = json_decode(Roc::request()->data->captcha, true);

                    $geetest = Roc::geetest();

                    if (Roc::get('geetest.switch') && !$geetest->validate($captcha['geetest_challenge'], $captcha['geetest_validate'], $captcha['geetest_seccode']))
                    {
                        parent::json('error', '行为验证码验证失败');
                    }

                    $data = Roc::request()->data;

                    if (isset($data['password'], $data['account']))
                    {
                        if (!Roc::model('user')->checkPassword($data['password']))
                        {
                            parent::json('error', '密码格式不合法');
                        }

                        if (!Roc::model('user')->checkEmailValidity($data['account']))
                        {
                            $user = Roc::model('user')->getByUsername($data['account']);
                        }
                        else
                        {
                            $user = Roc::model('user')->getByEmail($data['account']);
                        }

                        if (!empty($user))
                        {
                            // 检测Redis中UID是否被锁住
                            if (Roc::redis()->exists(md5($user['uid'])) && Roc::redis()->get(md5($user['uid'])) > 10)
                            {
                                if (Roc::redis()->ttl(md5($user['uid'])) < 0)
                                {
                                    Roc::redis()->setex(md5($user['uid']), 600, 11);
                                }

                                parent::json('error', '密码错误次数过多，账号锁定中');
                            }

                            if (md5($data['password']) === $user['password'])
                            {
                                Roc::redis()->delete(md5($user['uid']));

                                self::setLoginInfo($user['uid'], $user['username'], $user['groupid']);

                                parent::json('success', '登陆成功');
                            }
                            else
                            {
                                // 写入Redis
                                Roc::redis()->incr(md5($user['uid']));

                                parent::json('error', '密码错误');
                            }
                        }
                        else
                        {
                            parent::json('error', '用户不存在');
                        }
                    }
                    else
                    {
                        parent::json('error', '非法请求');
                    }
                }
                else
                {
                    $geetest = Roc::geetest();

                    if ($geetest->register())
                    {
                        $captcha = [
                            'success' => 1,
                            'geetest' => Roc::get('geetest.appid'),
                            'challenge' => $geetest->challenge
                        ];
                    }
                    else
                    {
                        $captcha = [
                            'success' => 0
                        ];
                    }

                    parent::renderBase(['pageTitle' => '用户登录', 'active' => 'login']);

                    Roc::render('login', ['captcha' => $captcha]);
                }

                break;
        }
    }

    /**
     * 注册
     * @return [type] [description]
     */
    public static function register($type)
    {
        if (self::getloginInfo()['uid'] > 0)
        {
            Roc::redirect('/');
        }

        switch ($type)
        {
            case 'qq':

                $accessToken = Roc::qq()->qq_callback();

                $openid = Roc::qq()->get_openid();

                if (strlen($openid) == 32)
                {
                    $qqInfo = Roc::qq()->get_user_info();

                    $return = Roc::model('user')->getByQQOpenID($openid);

                    if (!empty($return))
                    {
                        self::setLoginInfo($return['uid'], $return['username'], $return['groupid']);

                        Roc::app()->redirect('/');
                    }
                    else
                    {
                        $avatar = isset($qqInfo['figureurl_qq_2']) ? $qqInfo['figureurl_qq_2'] : '';

                        $username = isset($qqInfo['nickname']) ? $qqInfo['nickname'] : '';

                        if (Roc::request()->method == 'POST')
                        {
                            $data = Roc::request()->data;

                            $email = Roc::filter()->in($data->email);

                            $username = Roc::filter()->in($data->username);

                            $password = Roc::filter()->in($data->password);

                            $newAccount = ($data->newAccount ? true : false);

                            if ($newAccount)
                            {
                                self::_checkInput($email, $username, $password);

                                $return = Roc::model('user')->addUser([
                                    'username' => $username,
                                    'email' => $email,
                                    'password' => $password,
                                    'qq_openid' => $openid,
                                    'reg_time' => time(),
                                    'last_time' => time(),
                                ]);

                                if ($return > 0)
                                {
                                    if (!empty($avatar))
                                    {
                                        Roc::qiniu()->fetch($avatar, 'avatar/'.$return);
                                    }

                                    self::setLoginInfo($return, $username, 1);

                                    die(parent::renderJson(10000));
                                }
                                else
                                {
                                    if ($return == -1)
                                    {
                                        die(parent::renderJson(10404));
                                    }

                                    if ($return == -2)
                                    {
                                        die(parent::renderJson(10405));
                                    }

                                    die(parent::renderJson(10001));
                                }
                            }
                            else
                            {
                                $return = Roc::model('user')->getByEmail($email);

                                if (!empty($return))
                                {
                                    if ($return['password'] == md5($password))
                                    {
                                        if (empty($return['qq_openid']))
                                        {
                                            Roc::model('user')->updateInfo(['qq_openid' => $openid], $return['uid']);

                                            self::setLoginInfo($return['uid'], $return['username'], $return['groupid']);

                                            die(parent::renderJson(10000));
                                        }
                                        else
                                        {
                                            die(parent::renderJson(10409));
                                        }
                                    }
                                    else
                                    {
                                        die(parent::renderJson(10407));
                                    }
                                }
                                else
                                {
                                    die(parent::renderJson(10406));
                                }
                            }
                        }
                        else
                        {
                            self::renderBase(['page_title' => 'QQ互联登录', 'active' => 'qq-join']);

                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        }
                    }
                }
                else
                {
                    echo parent::renderJson(400, '授权失败');
                }

                break;

            case 'weibo':

                $token = Roc::weibo()->getAccessToken('code', ['code'=>Roc::request()->query->code, 'redirect_uri'=>Roc::get('weibo.callback')]);

                if ($token)
                {
                    $_SESSION['token'] = serialize($token);

                    $client = new \WeiboClient(Roc::get('weibo.akey') , Roc::get('weibo.skey'), $token['access_token']);

                    $uid = $client->get_uid()['uid'];

                    $userInfo = $client->show_user_by_id($uid);

                    $return = Roc::model('user')->getByWeiboID($userInfo['id']);

                    if (!empty($return))
                    {
                        self::setLoginInfo($return['uid'], $return['username'], $return['groupid']);

                        Roc::app()->redirect('/login');
                    }
                    else
                    {
                        $avatar = isset($userInfo['avatar_large']) ? $userInfo['avatar_large'] : $userInfo['profile_image_url'];

                        $username = isset($userInfo['name']) ? $userInfo['name'] : $userInfo['screen_name'];

                        if (Roc::request()->method == 'POST')
                        {
                            $data = Roc::request()->data;

                            $email = Roc::filter()->in($data->email);

                            $username = Roc::filter()->in($data->username);

                            $password = Roc::filter()->in($data->password);

                            $newAccount = ($data->newAccount == 1 ? true : false);

                            if ($newAccount)
                            {
                                self::_checkInput($email, $username, $password);

                                $return = Roc::model('user')->addUser([
                                    'username' => $username,
                                    'email' => $email,
                                    'password' => $password,
                                    'weibo_openid' => $userInfo['id'],
                                    'reg_time' => time(),
                                    'last_time' => time(),
                                ]);

                                if ($return > 0)
                                {
                                    if (!empty($avatar))
                                    {
                                        Roc::qiniu()->fetch($avatar, 'avatar/'.$return);
                                    }

                                    self::setLoginInfo($return, $username, 1);

                                    die(parent::renderJson(10000));
                                }
                                else
                                {
                                    if ($return == -1)
                                    {
                                        die(parent::renderJson(10404));
                                    }

                                    if ($return == -2)
                                    {
                                        die(parent::renderJson(10405));
                                    }

                                    die(parent::renderJson(10001));
                                }
                            }
                            else
                            {
                                if (!Roc::model('user')->checkEmailValidity($email))
                                {
                                    $return = Roc::model('user')->getByUsername($username);
                                }
                                else
                                {
                                    $return = Roc::model('user')->getByEmail($email);
                                }

                                if (!empty($return))
                                {
                                    if ($return['password'] == md5($password))
                                    {
                                        if ($return['weibo_openid'] == 0)
                                        {
                                            Roc::model('user')->updateInfo(['weibo_openid'=>$userInfo['id']], $return['uid']);

                                            self::setLoginInfo($return['uid'], $return['username'], $return['groupid']);

                                            die(parent::renderJson(10000));
                                        }
                                        else
                                        {
                                            die(parent::renderJson(10409));
                                        }
                                    }
                                    else
                                    {
                                        die(parent::renderJson(10407));
                                    }
                                }
                                else
                                {
                                    die(parent::renderJson(10406));
                                }
                            }
                        }
                        else
                        {
                            self::renderBase(['page_title' => '微博登录', 'active' => 'weibo-join']);

                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        }
                    }
                }
                else
                {
                    echo parent::renderJson(400, '授权失败');
                }

                break;

            default:

                if (Roc::request()->method == 'POST')
                {
                    $data = Roc::request()->data;

                    if (isset($data->username, $data->email, $data->password, $data->captcha))
                    {
                        $email = Roc::filter()->in($data->email);

                        $username = Roc::filter()->in($data->username);

                        $password = Roc::filter()->in($data->password);

                        $captcha = json_decode($data->captcha, true);

                        $geetest = Roc::geetest();

                        if (Roc::get('geetest.switch') === false || $geetest->validate($captcha['geetest_challenge'], $captcha['geetest_validate'], $captcha['geetest_seccode']))
                        {
                            self::_checkInput($email, $username, $password);

                            $return = Roc::model('user')->addUser([
                                'username' => $username,
                                'email' => $email,
                                'password' => $password,
                                'reg_time' => time(),
                                'last_time' => time(),
                            ]);

                            if ($return > 0)
                            {
                                Roc::qiniu()->fetch('https://dn-roc.qbox.me/avatar/0-avatar.png', 'avatar/'.$return);

                                self::setLoginInfo($return, $username, 1);

                                die(parent::renderJson(10000));
                            }
                            else
                            {
                                if ($return == -1)
                                {
                                    die(parent::renderJson(10404));
                                }

                                if ($return == -2)
                                {
                                    die(parent::renderJson(10405));
                                }

                                die(parent::renderJson(10001));
                            }
                        }
                        else
                        {
                            die(self::renderJson(10100));
                        }
                    }
                    else
                    {
                        die(parent::renderJson(10400));
                    }
                }
                else
                {
                    $geetest = Roc::geetest();

                    if ($geetest->register())
                    {
                        $captcha = [
                            'success' => 1,
                            'geetest' => Roc::get('geetest.appid'),
                            'challenge' => $geetest->challenge
                        ];
                    }
                    else
                    {
                        $captcha = [
                            'success' => 0
                        ];
                    }

                    parent::renderBase(['pageTitle' => '用户注册', 'active' => 'register']);

                    Roc::render('register', ['captcha' => $captcha]);
                }

                break;
        }
    }

    /**
     * 注销登录
     * @return [type] [description]
     */
    public static function logout($redirect = true)
    {
        session_destroy();

        setcookie('roc_secure', '', 0, '/');

        setcookie('roc_login', '', 0, '/');

        if ($redirect)

            Roc::redirect('/');
    }

    /**
     * 获取用户头像
     * @param  [type]  $uid  [description]
     * @param  integer $size [description]
     * @return [type]        [description]
     */
    public static function getAvatar($uid, $size = 100)
    {
        switch ($size) {
            case 100:
                $flag = 'avatar';
                break;

            default:
                $flag = 'avatar';
                break;
        }

        $redis = Roc::model('user')->redis();
        $data = $redis->get('avatar:'.$uid);

        if (!empty($data)) {
            return $data;
        } else {
            $data = (Roc::request()->secure ? 'https://' : 'http://').Roc::get('qiniu.domain').'/avatar/'.$uid.'-'.$flag.'.png?'.time();
            $redis->setex('avatar:'.$uid, 86400*30, $data);

            return $data;
        }
    }

    /**
     * 获取加密身份信息
     * @return [type] [description]
     */
    public static function getloginInfo()
    {
        $cookie = Roc::request()->cookies;

        $userInfo = [
            'uid' => 0,

            'username' => '',

            'groupid' => 0,

            'groupname' => '',

            'logintime' => 0,

            'notice_num' => 0,

            'whisper_num' => 0,

            'avatar' => ''
        ];

        if (isset($cookie['roc_login'], $cookie['roc_secure']))
        {
            $userArr = json_decode(Roc::secret()->decrypt($cookie['roc_secure'], Roc::get('sys.config')['rockey']), true);

            if (count($userArr) == 4 && (time() -$userArr[3]) < 604800)
            {
                if ($cookie['roc_login'] == $userArr[1])
                {
                    $userInfo['uid'] = $userArr[0];

                    $userInfo['username'] = $userArr[1];

                    $userInfo['groupid'] = $userArr[2];

                    $userInfo['logintime'] = $userArr[3];

                    $userInfo['avatar'] = self::getAvatar($userArr[0]);

                    $userInfo['groupname'] = self::getGroupName($userArr[2]);

                    $userInfo['notice_num'] = Roc::model('notification')->getUnreadTotal($userArr[0]);

                    $userInfo['whisper_num'] = Roc::model('whisper')->getUnreadTotal($userArr[0]);
                }
            }
        }

        return $userInfo;
    }

    /**
     * 注册加密身份信息
     * @param [type] $uid      [description]
     * @param [type] $username [description]
     * @param [type] $groupid  [description]
     */
    public static function setLoginInfo($uid, $username, $groupid)
    {
        Roc::model('user')->updateLastTime($uid);

        $loginTime = time();

        setcookie('roc_login', $username, $loginTime + 604800, '/', NULL, Roc::request()->secure, true);

        $loginEncode = Roc::secret()->encrypt(json_encode([$uid, $username, $groupid, $loginTime]), Roc::get('sys.config')['rockey']);

        setcookie('roc_secure', $loginEncode, $loginTime + 604800, '/', NULL, Roc::request()->secure, true);
    }

    /**
     * 获取用户组
     * @param  [type] $groupid [description]
     * @return [type]          [description]
     */
    public static function getGroupName($groupid)
    {
        switch ($groupid)
        {
            case 0:
                return '禁言用户';

            case 99:
                return '管理员';

            default:
                return 'V'.$groupid;
        }
    }

    /**
     * 数据检测
     * @param  [type] $email    [description]
     * @param  [type] $username [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    private static function _checkInput($email, $username, $password)
    {
        if (!empty($email) && !Roc::model('user')->checkEmailValidity($email))
        {
            die(self::renderJson(10401));
        }

        $_checkNickname = Roc::model('user')->checkNickname($username);

        if (!empty($username) && $_checkNickname !== true)
        {
            die(self::renderJson(10402, $_checkNickname));
        }

        if (substr_count($password, ' ') > 0)
        {
            die(self::renderJson(10403));
        }

        if (strlen($password) < 8 || strlen($password) > 26)
        {
            die(self::renderJson(10403));
        }

        return true;
    }

    /**
     * 个人主页
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public static function index($uid)
    {
        $uid = $uid > 0 ? $uid : self::getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        if (!empty($user))
        {
            parent::renderBase(['active' => 'user']);

            Roc::render('user', [
                'user' => $user,
                'avatar' => self::getAvatar($user['uid']),
                'topics' => self::__getTopics(1, $uid),
                'replys' => self::__getReplys(1, $uid),
                'collections' => self::__getCollections(1, $uid),
                'fans' => self::__getFans(1, $uid),
                'statistic' => [
                    'topic' => Roc::model('topic')->getTotal(['uid' => $uid, 'valid' => 1]),
                    'article' => Roc::model('article')->getTotal(['uid' => $uid, 'valid' => 1]),
                    'fans' => Roc::model('follow')->getFansCount(['fuid' => $uid])
                ],
                'is_fans' => self::getloginInfo()['uid'] > 0 ? Roc::model('follow')->isFans(self::getloginInfo()['uid'], $uid) : 0,
                'v2Price' => self::$v2Price,
                'v3Price' => self::$v3Price
            ]);
        }
        else
        {
            Roc::redirect('/');
        }
    }

    /**
     * 个人提醒页
     * @return [type] [description]
     */
    public static function notice()
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0)
        {
            $rows = ['notification' => [], 'whisper' => [], 'unread_notification' => [], 'unread_whisper' => []];

            $total = 0;

            $data = Roc::model('notification')->getUnread($uid);

            if (!empty($data))

            foreach ($data as $notification)
            {
                $rows['unread_notification'][] = [
                    'id' => $notification['id'],
                    'title' => $notification['username'].'在主题 “'.Roc::controller('api\Topic')->cutSubstr($notification['title'], 15).'” '.($notification['pid'] > 0 ? '下的回复' : '').'中提到了你',
                    'time' => parent::formatTime($notification['post_time']),
                    'tid' => $notification['tid'],
                    'pid' => $notification['pid']
                ];

                $total++;
            }

            $data = Roc::model('whisper')->getUnread($uid);

            if (!empty($data))

            foreach ($data as $whisper)
            {
                $rows['unread_whisper'][] = [
                    'id' => $whisper['id'],
                    'title' => $whisper['username'].'给你发了一条私信',
                    'content' => Roc::filter()->topicOut($whisper['content']),
                    'time' => parent::formatTime($whisper['post_time']),
                    'uid' => $whisper['uid']
                ];

                $total++;
            }

            // 获取已读提醒
            $data = Roc::model('notification')->getRead($uid, 0, self::$per);

            if (!empty($data))

            foreach ($data as $notification)
            {
                $rows['notification'][] = [
                    'id' => $notification['id'],
                    'title' => $notification['username'].'在'.($notification['pid'] > 0 ? '回复' : '主题').'中提到了你',
                    'time' => parent::formatTime($notification['post_time']),
                    'tid' => $notification['tid'],
                    'pid' => $notification['pid']
                ];
            }

            // 获取已读私信
            $data = Roc::model('whisper')->getRead($uid, 0, self::$per);

            if (!empty($data))

            foreach ($data as $whisper)
            {
                $rows['whisper'][] = [
                    'id' => $whisper['id'],
                    'title' => $whisper['username'].'给你发了一条私信',
                    'content' => Roc::filter()->topicOut($whisper['content']),
                    'time' => parent::formatTime($whisper['post_time']),
                    'uid' => $whisper['uid']
                ];
            }

            parent::renderBase(['active' => 'notice']);

            Roc::render('notice', [
                'unread' => $rows,
                'notification' => $rows['notification'],
                'whisper' => $rows['whisper'],
                'total' => $total
            ]);
        }
        else
        {
            Roc::redirect('/login');
        }
    }

    /**
     * 个人积分页面
     * @return [type] [description]
     */
    public static function scores()
    {
        $uid = self::getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        if (!empty($user))
        {
            $data = self::__getScores(1, $uid);

            parent::renderBase(['active' => 'scores']);

            Roc::render('scores', [
                'data' => $data,
                'user' => $user,
            ]);
        }
        else
        {
            Roc::redirect('/login');
        }
    }

    /**
     * 个人设置页
     * @return [type] [description]
     */
    public static function profile()
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0)
        {
            $user = Roc::model('user')->getByUid($uid);

            if (!empty($user))
            {
                $data = [
                    // 头像上传Token
                    'avatarUploadToken' => Roc::qiniu()->uploadToken([
                        'scope' => Roc::get('qiniu.bucket').':avatar/'.$uid,
                        'deadline' => time() + 3600,
                        'saveKey' => 'avatar/'.$uid
                    ]),
                    'saveKey' => 'avatar/'.$uid
                ];
                Roc::model('user')->redis()->delete('avatar:'.$uid);
                parent::renderBase(['active' => 'setting']);

                Roc::render('setting', [
                    'user' => $user,
                    'avatar' => self::getAvatar($user['uid']),
                    'data' => $data
                ]);
            }
            else
            {
                Roc::redirect('/login');
            }
        }
        else
        {
            Roc::redirect('/login');
        }
    }

    /**
     * 保存个人设置
     * @return [type] [description]
     */
    public static function saveProfile()
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0)
        {
            $user = Roc::model('user')->getByUid($uid);

            $data = Roc::request()->data;

            if (!empty($user))
            {
                if (Roc::model('user')->checkEmailValidity($data->email))
                {
                    $cEmail = Roc::model('user')->getByEmail($data->email);

                    if (empty($cEmail) || $cEmail['uid'] == $uid)
                    {
                        $cPhone = Roc::model('user')->getByPhone($data->phone);

                        if (empty($cPhone) || $cPhone['uid'] == $uid)
                        {
                            if (!empty($data->password) && strlen($data->password) >= 8) {
                                $iData = [
                                    'phone' => $data->phone,
                                    'email' => $data->email,
                                    'password' => md5($data->password)
                                ];
                                self::logout(false);
                            } else {
                                $iData = [
                                    'phone' => $data->phone,
                                    'email' => $data->email
                                ];
                            }

                            $ret = Roc::model('user')->updateInfo($iData, $uid);

                            parent::json('success', '保存成功');
                        }
                        else
                        {
                            parent::json('error', '手机号已存在');
                        }
                    }
                    else
                    {
                        parent::json('error', '邮箱已存在');
                    }
                }
                else
                {
                    parent::json('error', '邮箱不合法');
                }
            }
            else
            {
                parent::json('error', '用户不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 升级VIP
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function doUpgrade($type)
    {
        $type = in_array($type, [2, 3]) ? $type : 2;

        if ($type == 2)
        {
            $score = self::$v2Price;
        }
        else
        {
            $score = self::$v3Price;
        }

        $uid = self::getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        if (empty($user))
        {
            parent::json('error', '用户不存在');
        }

        $groupid = $user['groupid'];

        if ($groupid >= $type)
        {
            parent::json('error', '您已经是该级别或更高级别的VIP了');
        }

        if ($groupid == 2)
        {
            $score = self::$v3Price - self::$v2Price;
        }

        if ($uid > 0)
        {
            $userScore = Roc::model('user')->getUserScore($uid);

            if ($userScore < $score)
            {
                parent::json('error', '您的积分余额（'.$userScore.'）不足以升级VIP'.$type);
            }

            try
            {
                $db = Roc::model()->getDb();

                $db->beginTransaction();

                $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$score." WHERE `uid` = ".$uid);

                if ($ret > 0)
                {
                    Roc::model('score')->addRecord([
                        'tid' => 0,
                        'uid' => $uid,
                        'changed' => - $score,
                        'remain' => Roc::model('user')->getUserScore($uid),
                        'reason' => '升级VIP'.$type,
                        'add_user' => $uid,
                        'add_time' => time(),
                    ]);

                    $ret = $db->exec("UPDATE `roc_user` SET `groupid` = ".$type." WHERE `uid` = ".$user['uid']);

                    if ($ret == 0)
                    {
                        throw new \Exception("打赏失败，请重试");
                    }
                }
                else
                {
                    throw new \Exception("您的余额不足");
                }

                $db->commit();

                self::logout(false);

                parent::json('success', '升级VIP'.$type.'成功，请重新登录');
            }
            catch (\Exception $e)
            {
                $db->rollBack();

                parent::json('error', $e->getMessage());
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 用户充值
     * @param  [type] $money [description]
     * @return [type]        [description]
     */
    public static function recharge($money)
    {
        $uid = self::getloginInfo()['uid'];

        if ($money > 0 && $money <= 1000 && $uid > 0)
        {
            $parameter = [
                'service' => 'create_direct_pay_by_user',

                'partner' => Roc::get('alipay.pid'),

                'payment_type' => '1',

                'it_b_pay' => '1h',

                'seller_id' => Roc::get('alipay.pid'),

                'return_url' => (Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/recharge/return/',

                'notify_url' => (Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/recharge/notify/',

                'payment_type' => '1',

                'out_trade_no' => 'ROCBOSS000'.rand(100000, 999999).time().'U'.$uid,

                'subject' => '充值'.(100*$money).'积分',

                'price' => $money,

                'quantity' => '1',

                'logistics_fee' => '0.00',

                'logistics_type' => 'EXPRESS',

                'logistics_payment' => 'SELLER_PAY',

                'body' => 'ROCBOSS微社区积分充值',

                'show_url' => (Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'],

                '_input_charset' => 'utf-8'
            ];

            $requestForm = Roc::alipay()->buildRequestForm($parameter, 'get', 'confirm');

            die(json_encode(['code' => 10000, 'data' => $requestForm]));
        }
        else
        {
            die(json_encode(['code' => 400, 'data' => '参数非法']));
        }
    }

    /**
     * Alipay同步返回
     * @return [type] [description]
     */
    public static function alipayReturn()
    {
        $verifyResult = Roc::alipay()->verifyReturn();

        if($verifyResult)
        {
            $outTradeNo = $_GET['out_trade_no'];

            $tradeNo = $_GET['trade_no'];

            $tradeStatus = $_GET['trade_status'];

            switch ($tradeStatus)
            {
                case 'TRADE_FINISHED':
                    # 此处可扩展写日志
                    break;

                default:
                    # 此处可扩展写日志
                    break;
            }

            Roc::app()->redirect('/scores');
        }
        else
        {
            die('Illegal Request');
        }
    }

    /**
     * 支付宝异步返回
     * @return [type] [description]
     */
    public static function alipayNotify()
    {
        $verifyResult = Roc::alipay()->verifyNotify();

        if($verifyResult)
        {
            $outTradeNo = $_POST['out_trade_no'];

            $tradeNo = $_POST['trade_no'];

            $tradeStatus = $_POST['trade_status'];

            $price = $_POST['price'];

            switch ($tradeStatus)
            {
                case 'WAIT_BUYER_PAY':

                    echo 'success';

                    break;

                case 'TRADE_FINISHED':

                    $uid = substr($outTradeNo, strpos($outTradeNo, 'U')+1);

                    self::__doRecharge($uid, 100*$price, $tradeNo);

                    echo 'success';

                    break;

                case 'TRADE_SUCCESS':

                    $uid = substr($outTradeNo, strpos($outTradeNo, 'U')+1);

                    self::__doRecharge($uid, 100*$price, $tradeNo);

                    echo 'success';

                    break;

                case 'TRADE_CLOSED':

                    echo 'success';

                    break;

                default:

                    echo 'success';

                    break;
            }
        }
        else
        {
            echo 'fail';
        }
    }

    /**
     * 获取更多用户主题
     * @param  [type] $uid  [description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getMoreTopic($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        echo json_encode(['status' => 'success', 'data' => self::__getTopics($page, $uid)]);
    }

    /**
     * 获取更多用户回复
     * @param  [type] $uid  [description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getMoreReply($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        echo json_encode(['status' => 'success', 'data' => self::__getReplys($page, $uid)]);
    }

    /**
     * 获取更多用户收藏
     * @method getMoreCollection
     * @param  [type]            $uid  [description]
     * @param  [type]            $page [description]
     * @return [type]                  [description]
     */
    public static function getMoreCollection($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        echo json_encode(['status' => 'success', 'data' => self::__getCollections($page, $uid)]);
    }

    /**
     * 获取更多粉丝
     * @param  [type] $uid  [description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getMoreFans($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        echo json_encode(['status' => 'success', 'data' => self::__getFans($page, $uid)]);
    }

    /**
     * 获取更多私信
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function getMoreWhisper($type, $page)
    {
        $uid = self::getloginInfo()['uid'];

        $page = $page > 0 ? $page : 1;

        $type = $type == 0 ? 'getRead' : 'getMySending';

        echo json_encode(['status' => 'success', 'data' => self::__getWhisper($type, $page, $uid)]);
    }

    /**
     * 关注（取消关注）用户
     * @return [type] [description]
     */
    public static function doFollow()
    {
        $uid = self::getloginInfo()['uid'];

        $fuid = Roc::request()->data->fuid;

        if ($uid == $fuid)
        {
            parent::json('error', '不可以关注自己哦~');
        }

        if ($uid > 0)
        {
            $status = Roc::model('follow')->isFans($uid, $fuid);

            if ($status > 0)
            {
                Roc::model('follow')->cancelFollow($uid, $fuid);

                parent::json('success', 0);
            }
            else
            {
                Roc::model('follow')->addFollow($uid, $fuid);

                parent::json('success', 1);
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 传送私信
     * @return [type] [description]
     */
    public static function deliverWhisper()
    {
        $uid = self::getloginInfo()['uid'];

        $content = Roc::request()->data->content;

        $atUid = Roc::request()->data->at_uid;

        if ($uid > 0)
        {
            $atUser = Roc::model('user')->getByUid($atUid);

            if (empty($atUser))
            {
                parent::json('error', '目标用户不存在');
            }

            if ($atUid == $uid)
            {
                parent::json('error', '抱歉，不能私信自己');
            }

            $userScore = Roc::model('user')->getUserScore($uid);

            if ($userScore < Roc::get('system.score.whisper'))
            {
                parent::json('error', '您的积分余额（'.$userScore.'）不足以支付');
            }

            try
            {
                $db = Roc::model()->getDb();

                $db->beginTransaction();

                $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".Roc::get('system.score.whisper')." WHERE `uid` = ".$uid);

                if ($ret > 0)
                {
                    Roc::model('score')->addRecord([
                        'tid' => 0,
                        'uid' => $uid,
                        'changed' => - Roc::get('system.score.whisper'),
                        'remain' => Roc::model('user')->getUserScore($uid),
                        'reason' => '发送私信给'.$atUser['username'],
                        'add_user' => $uid,
                        'add_time' => time(),
                    ]);

                    $ret = Roc::model('whisper')->addWhisper([
                        'at_uid' => $atUid,
                        'uid' => $uid,
                        'content' => $content,
                        'post_time' => time()
                    ]);

                    if ($ret == 0)
                    {
                        throw new \Exception("私信发送失败");
                    }
                    else
                    {
                        if (!empty($atUser['phone']))
                        {
                            parent::sendSms(Roc::get('sms.whisper_tplid'), $atUser['phone'], '#username#='.self::getloginInfo()['username'].'&#content#='.$content);
                        }
                    }
                }
                else
                {
                    throw new \Exception("您的余额不足");
                }

                $db->commit();

                parent::json('success', '私信传送成功');
            }
            catch (\Exception $e)
            {
                $db->rollBack();

                parent::json('error', $e->getMessage());
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    private static function __getTopics($page, $uid)
    {
        $topics = Roc::model('topic')->getList(self::$per*($page - 1), self::$per, ['roc_topic.uid' => $uid, 'roc_topic.valid' => 1]);

        if (!empty($topics))

        foreach ($topics as &$topic)
        {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);

            $topic['imageCount'] = Roc::model('relation')->getRelation($topic['tid'], 1, 'count');

            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);

            $topic['post_time'] = parent::formatTime($topic['post_time']);

            $topic['edit_time'] = parent::formatTime($topic['edit_time']);

            $topic['last_time'] = parent::formatTime($topic['last_time']);
        }

        return $topics;
    }

    private static function __getCollections($page, $uid)
    {
        $topics = Roc::model('topic')->getCollectionList(self::$per*($page - 1), self::$per, $uid);

        if (!empty($topics))

        foreach ($topics as &$topic)
        {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);

            $topic['imageCount'] = Roc::model('relation')->getRelation($topic['tid'], 1, 'count');

            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);

            $topic['post_time'] = parent::formatTime($topic['post_time']);

            $topic['edit_time'] = parent::formatTime($topic['edit_time']);

            $topic['last_time'] = parent::formatTime($topic['last_time']);
        }

        return $topics;
    }

    private static function __getReplys($page, $uid)
    {
        $replys = Roc::model('reply')->getListByUid(self::$per*($page - 1), self::$per, $uid);

        foreach ($replys as &$reply)
        {
            $reply['content'] = Roc::filter()->topicOut($reply['content']);

            if ($reply['at_pid'] > 0)
            {
                $reply['at_reply'] = Roc::model('reply')->getReply($reply['at_pid'], $reply['tid']);

                if (!empty($reply['at_reply']))
                {
                    $reply['at_reply']['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($reply['at_reply']['content']));

                    $reply['at_reply']['post_time'] = parent::formatTime($reply['at_reply']['post_time']);
                }
            }

            $reply['avatar'] = Roc::controller('frontend\User')->getAvatar($reply['uid']);

            $reply['add_time'] = $reply['post_time'];

            $reply['post_time'] = parent::formatTime($reply['post_time']);
        }

        return $replys;
    }

    private static function __getWhisper($type, $page, $uid)
    {
        $data = Roc::model('whisper')->$type($uid, self::$per*($page - 1), self::$per);

        if (!empty($data))

        foreach ($data as &$whisper)
        {
            $whisper = [
                'id' => $whisper['id'],
                'title' => $type == 'getRead' ? $whisper['username'].'给你发了一条私信' : '你给'.$whisper['username'].'发了一条私信',
                'content' => Roc::filter()->topicOut($whisper['content']),
                'time' => parent::formatTime($whisper['post_time']),
                'is_read' => $type == 'getRead' ? '' : ($whisper['is_read'] ? '对方已读' : '对方未读'),
                'uid' => $type == 'getRead' ? $whisper['uid'] : $whisper['at_uid']
            ];
        }

        return $data;
    }

    private static function __getFans($page, $uid)
    {
        $fans = Roc::model('follow')->getFans(self::$per*($page - 1), self::$per, ['fuid' => $uid]);

        if (!empty($fans))
        {
            foreach ($fans as &$fan)
            {
                $fan['avatar'] = self::getAvatar($fan['uid']);
            }
        }

        return $fans;
    }

    private static function __getScores($page, $uid)
    {
        $scores = Roc::model('score')->getList($uid, 100*($page - 1), 100);

        if (!empty($scores))
        {
            foreach ($scores as &$score)
            {
                $score['add_time'] = parent::formatTime($score['add_time']);
            }
        }

        return $scores;
    }

    private static function __doRecharge($uid, $score, $tradeNo)
    {
        $record = Roc::model('score')->getRecord($tradeNo);

        if (!empty($record))
        {
            return false;
        }

        try
        {
            $db = Roc::model()->getDb();

            $db->beginTransaction();

            $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` + ".$score." WHERE `uid` = ".$uid);

            if ($ret > 0)
            {
                $return = Roc::model('score')->addRecord([
                    'tid' => 0,
                    'trade_no' => $tradeNo,
                    'uid' => $uid,
                    'changed' => $score,
                    'remain' => Roc::model('user')->getUserScore($uid),
                    'reason' => '积分充值',
                    'add_user' => $uid,
                    'add_time' => time(),
                ]);

                if ($return == 0)
                {
                    throw new \Exception("充值记录添加失败");
                }
            }
            else
            {
                throw new \Exception("充值失败");
            }

            $db->commit();
        }
        catch (\Exception $e)
        {
            $db->rollBack();

            // parent::json('error', $e->getMessage());
        }
    }
}
