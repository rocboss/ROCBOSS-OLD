<?php
namespace frontend;

use Roc;
use UserModel;
use ScoreModel;
use TopicModel;
use ReplyModel;
use FollowModel;
use WhisperModel;
use ArticleModel;
use RelationModel;
use AttachmentModel;
use NotificationModel;
class UserController extends BaseController
{
    public static $expire = 604800;
    public static $per = 12;

    // VIP2 升级价格(积分)
    public static $v2Price = 38800;

    // VIP3 升级价格(积分)
    public static $v3Price = 80000;

    /**
     * 登录
     * @return [type] [description]
     */
    public static function login($type)
    {
        if (self::getloginInfo()['uid'] > 0) {
            Roc::redirect('/');
        }

        switch ($type) {
            case 'qq':
                Roc::redirect(Roc::qq()->qq_login());
                break;

            case 'weibo':
                Roc::redirect(Roc::weibo()->getAuthorizeURL(Roc::get('weibo.callback')));
                break;

            default:

                if (Roc::request()->method == 'POST') {
                    $captcha = json_decode(Roc::request()->data->captcha, true);
                    $geetest = Roc::geetest();
                    if (Roc::get('geetest.switch') && !$geetest->validate($captcha['geetest_challenge'], $captcha['geetest_validate'], $captcha['geetest_seccode'])) {
                        parent::json('error', '行为验证码验证失败');
                    }

                    $data = Roc::request()->data;

                    if (isset($data['password'], $data['account'])) {
                        if (!UserModel::m()->checkPassword($data['password'])) {
                            parent::json('error', '密码格式不合法');
                        }

                        if (!UserModel::m()->checkEmailValidity($data['account'])) {
                            $user = UserModel::m()->getByUsername($data['account']);
                        } else {
                            $user = UserModel::m()->getByEmail($data['account']);
                        }

                        if (!empty($user)) {
                            // 检测Redis中UID是否被锁住
                            if (Roc::redis()->exists(md5($user['uid'])) && Roc::redis()->get(md5($user['uid'])) > 10) {
                                if (Roc::redis()->ttl(md5($user['uid'])) < 0) {
                                    Roc::redis()->setex(md5($user['uid']), 600, 11);
                                }

                                parent::json('error', '密码错误次数过多，账号锁定中');
                            }

                            if (md5($data['password']) === $user['password']) {
                                Roc::redis()->delete(md5($user['uid']));

                                self::setLoginInfo($user['uid'], $user['username'], $user['groupid'], $user['salt']);

                                parent::json('success', '登陆成功');
                            } else {
                                // 写入Redis
                                Roc::redis()->incr(md5($user['uid']));

                                parent::json('error', '密码错误');
                            }
                        } else {
                            parent::json('error', '用户不存在');
                        }
                    } else {
                        parent::json('error', '非法请求');
                    }
                } else {
                    $geetest = Roc::geetest();

                    if ($geetest->register()) {
                        $captcha = [
                            'success' => 1,
                            'geetest' => Roc::get('geetest.appid'),
                            'challenge' => $geetest->challenge
                        ];
                    } else {
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
        if (self::getloginInfo()['uid'] > 0) {
            Roc::redirect('/');
        }

        switch ($type) {
            case 'qq':
                $accessToken = Roc::qq()->qq_callback();
                $openid = Roc::qq()->get_openid();

                if (strlen($openid) == 32) {
                    $qqInfo = Roc::qq()->get_user_info();
                    $return = UserModel::m()->getByQQOpenID($openid);

                    if (!empty($return)) {
                        self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                        Roc::app()->redirect('/');
                    } else {
                        $avatar = isset($qqInfo['figureurl_qq_2']) ? $qqInfo['figureurl_qq_2'] : '';
                        $username = isset($qqInfo['nickname']) ? $qqInfo['nickname'] : '';

                        if (Roc::request()->method == 'POST') {
                            $data = Roc::request()->data;
                            $email = Roc::filter()->in($data->email);
                            $username = Roc::filter()->in($data->username);
                            $password = Roc::filter()->in($data->password);
                            $newAccount = ($data->newAccount ? true : false);

                            if ($newAccount) {
                                self::_checkInput($email, $username, $password);

                                $return = UserModel::m()->addUser([
                                    'username' => $username,
                                    'email' => $email,
                                    'password' => $password,
                                    'qq_openid' => $openid,
                                ]);

                                if ($return > 0) {
                                    $user = UserModel::m()->getByUid($return);

                                    if (!empty($avatar)) {
                                        Roc::qiniu()->fetch($avatar, 'avatar/'.$return);
                                    }

                                    self::setLoginInfo($user['uid'], $user['username'], $user['groupid'], $user['salt']);

                                    die(parent::renderJson(10000));
                                } else {
                                    if ($return == -1) {
                                        die(parent::renderJson(10404));
                                    }
                                    if ($return == -2) {
                                        die(parent::renderJson(10405));
                                    }

                                    die(parent::renderJson(10001));
                                }
                            } else {
                                $return = UserModel::m()->getByUsername($username);
                                if (!empty($return)) {
                                    if ($return['password'] == md5($password)) {
                                        if (empty($return['qq_openid'])) {
                                            UserModel::m()->updateInfo(['qq_openid' => $openid], $return['uid']);
                                            self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                                            die(parent::renderJson(10000));
                                        } else {
                                            die(parent::renderJson(10409));
                                        }
                                    } else {
                                        die(parent::renderJson(10407));
                                    }
                                } else {
                                    die(parent::renderJson(10406));
                                }
                            }
                        } else {
                            self::renderBase(['page_title' => 'QQ互联登录', 'active' => 'qq-join', 'asset' => 'o_join']);

                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        }
                    }
                } else {
                    echo parent::renderJson(400, '授权失败');
                }

                break;

            case 'weibo':
                $token = Roc::weibo()->getAccessToken('code', ['code'=>Roc::request()->query->code, 'redirect_uri'=>Roc::get('weibo.callback')]);
                if ($token) {
                    $_SESSION['token'] = serialize($token);
                    $client = new \WeiboClient(Roc::get('weibo.akey') , Roc::get('weibo.skey'), $token['access_token']);
                    $uid = $client->get_uid()['uid'];
                    $userInfo = $client->show_user_by_id($uid);
                    $return = UserModel::m()->getByWeiboID($userInfo['id']);

                    if (!empty($return)) {
                        self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                        Roc::app()->redirect('/login');
                    } else {
                        $avatar = isset($userInfo['avatar_large']) ? $userInfo['avatar_large'] : $userInfo['profile_image_url'];
                        $username = isset($userInfo['name']) ? $userInfo['name'] : $userInfo['screen_name'];

                        if (Roc::request()->method == 'POST') {
                            $data = Roc::request()->data;
                            $email = Roc::filter()->in($data->email);
                            $username = Roc::filter()->in($data->username);
                            $password = Roc::filter()->in($data->password);
                            $newAccount = ($data->newAccount == 1 ? true : false);

                            if ($newAccount) {
                                self::_checkInput($email, $username, $password);

                                $return = UserModel::m()->addUser([
                                    'username' => $username,
                                    'email' => $email,
                                    'password' => $password,
                                    'weibo_openid' => $userInfo['id'],
                                ]);

                                if ($return > 0) {
                                    $user = UserModel::m()->getByUid($return);
                                    if (!empty($avatar)) {
                                        Roc::qiniu()->fetch($avatar, 'avatar/'.$return);
                                    }

                                    self::setLoginInfo($user['uid'], $user['username'], $user['groupid'], $user['salt']);

                                    die(parent::renderJson(10000));
                                } else {
                                    if ($return == -1) {
                                        die(parent::renderJson(10404));
                                    }
                                    if ($return == -2) {
                                        die(parent::renderJson(10405));
                                    }

                                    die(parent::renderJson(10001));
                                }
                            } else {
                                if (!UserModel::m()->checkEmailValidity($email)) {
                                    $return = UserModel::m()->getByUsername($username);
                                } else {
                                    $return = UserModel::m()->getByEmail($email);
                                }

                                if (!empty($return)) {
                                    if ($return['password'] == md5($password)) {
                                        if ($return['weibo_openid'] == 0) {
                                            UserModel::m()->updateInfo(['weibo_openid'=>$userInfo['id']], $return['uid']);
                                            self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                                            die(parent::renderJson(10000));
                                        } else {
                                            die(parent::renderJson(10409));
                                        }
                                    } else {
                                        die(parent::renderJson(10407));
                                    }
                                } else {
                                    die(parent::renderJson(10406));
                                }
                            }
                        } else {
                            self::renderBase(['page_title' => '微博登录', 'active' => 'weibo-join']);

                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        }
                    }
                } else {
                    echo parent::renderJson(400, '授权失败');
                }

                break;

            // 微信扫码登录回调
            case 'weixin':
                // 执行注册
                if (Roc::request()->method == 'POST') {
                    $userinfo = Roc::request()->cookies->wx_userinfo;
                    if (empty($userinfo)) {
                        // 超过10分钟，授权超时
                        die(parent::renderJson(10413));
                    }
                    $userinfo = json_decode($userinfo);
                    $avatar = $userinfo->headimgurl.'.png';

                    $data = Roc::request()->data;
                    $email = Roc::filter()->in($data->email);
                    $username = Roc::filter()->in($data->username);
                    $password = Roc::filter()->in($data->password);
                    $newAccount = ($data->newAccount == 1 ? true : false);

                    if ($newAccount) {
                        self::_checkInput($email, $username, $password);

                        $return = UserModel::m()->addUser([
                            'username' => $username,
                            'email' => $email,
                            'password' => $password,
                            'wx_openid' => $userinfo->openid,
                            'wx_unionid' => $userinfo->unionid,
                        ]);

                        if ($return > 0) {
                            $user = UserModel::m()->getByUid($return);
                            if (!empty($avatar)) {
                                Roc::qiniu()->fetch($avatar, 'avatar/'.$return);
                            }

                            self::setLoginInfo($user['uid'], $user['username'], $user['groupid'], $user['salt']);

                            die(parent::renderJson(10000));
                        } else {
                            if ($return == -1) {
                                die(parent::renderJson(10404));
                            }
                            if ($return == -2) {
                                die(parent::renderJson(10405));
                            }

                            die(parent::renderJson(10001));
                        }
                    } else {
                        if (!UserModel::m()->checkEmailValidity($email)) {
                            $return = UserModel::m()->getByUsername($username);
                        } else {
                            $return = UserModel::m()->getByEmail($email);
                        }

                        if (!empty($return)) {
                            if ($return['password'] == md5($password)) {
                                if ($return['wx_unionid'] == '') {
                                    UserModel::m()->updateInfo(['wx_openid' => $userinfo->openid, 'wx_unionid' => $userinfo->unionid], $return['uid']);
                                    self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                                    die(parent::renderJson(10000));
                                } else {
                                    die(parent::renderJson(10412));
                                }
                            } else {
                                die(parent::renderJson(10407));
                            }
                        } else {
                            die(parent::renderJson(10406));
                        }
                    }
                } else {
                    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Roc::get('wx.appId')."&secret=".Roc::get('wx.appSecret')."&code=".Roc::request()->query->code."&grant_type=authorization_code";
                    $result = static::httpGet($url);
                    $resToken= json_decode($result);

                    if (!empty($resToken->access_token)) {
                        // 通过access_token获取用户信息
                        $url2 = "https://api.weixin.qq.com/sns/userinfo?access_token=".$resToken->access_token."&openid=".$resToken->openid."&lang=zh_CN";
                        $result2 = static::httpGet($url2);
                        $userinfo = json_decode($result2);

                        $return = UserModel::m()->getByUnionID($userinfo->unionid);
                        if (!empty($return)) {
                            self::setLoginInfo($return['uid'], $return['username'], $return['groupid'], $return['salt']);

                            Roc::app()->redirect('/login');
                        } else {

                            // 暂存10分钟
                            setcookie('wx_userinfo', json_encode($userinfo), time() + 600, '/', NULL, Roc::request()->secure, true);

                            $avatar = $userinfo->headimgurl.'.png';
                            $username = $userinfo->nickname;

                            self::renderBase(['page_title' => '微信登录', 'active' => 'weixin-join', 'asset' => 'o_join']);
                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        }
                    } else {
                        // 读取缓存
                        $cache = Roc::request()->cookies->wx_userinfo;

                        if (!empty($cache)) {
                            $userinfo = json_decode($cache);
                            $avatar = $userinfo->headimgurl.'.png';
                            $username = $userinfo->nickname;

                            self::renderBase(['page_title' => '微信登录', 'active' => 'weixin-join', 'asset' => 'o_join']);
                            Roc::render('o_join', [
                                'avatar' => $avatar,
                                'username' => $username
                            ]);
                        } else {
                            Roc::redirect('/');
                        }
                    }
                }
                break;

            default:
                if (Roc::request()->method == 'POST') {
                    if (!Roc::get('system.register.switch')) {
                        die(parent::renderJson(10411));
                    }
                    $data = Roc::request()->data;

                    if (isset($data->username, $data->email, $data->password, $data->captcha)) {
                        $email = Roc::filter()->in($data->email);
                        $username = Roc::filter()->in($data->username);
                        $password = Roc::filter()->in($data->password);
                        $captcha = json_decode($data->captcha, true);
                        $geetest = Roc::geetest();

                        if (!Roc::get('geetest.switch') || $geetest->validate($captcha['geetest_challenge'], $captcha['geetest_validate'], $captcha['geetest_seccode'])) {
                            self::_checkInput($email, $username, $password);

                            $return = UserModel::m()->addUser([
                                'username' => $username,
                                'email' => $email,
                                'password' => $password,
                            ]);

                            if ($return > 0) {
                                $user = UserModel::m()->getByUid($return);
                                Roc::qiniu()->fetch('https://dn-roc.qbox.me/avatar/0-avatar.png', 'avatar/'.$return);
                                self::setLoginInfo($user['uid'], $user['username'], $user['groupid'], $user['salt']);

                                die(parent::renderJson(10000));
                            } else {
                                if ($return == -1) {
                                    die(parent::renderJson(10404));
                                }
                                if ($return == -2) {
                                    die(parent::renderJson(10405));
                                }

                                die(parent::renderJson(10001));
                            }
                        } else {
                            die(self::renderJson(10100));
                        }
                    } else {
                        die(parent::renderJson(10400));
                    }
                } else {
                    $geetest = Roc::geetest();
                    if ($geetest->register()) {
                        $captcha = [
                            'success' => 1,
                            'geetest' => Roc::get('geetest.appid'),
                            'challenge' => $geetest->challenge
                        ];
                    } else {
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

        if ($redirect) {
            Roc::redirect('/');
        }
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

        $redis = UserModel::m()->redis();
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
    public static function getLoginInfo()
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

        if (isset($cookie['roc_login'], $cookie['roc_secure'])) {
            $sid = $cookie['roc_secure'];

            $loginInfo = UserModel::m()->redis()->get('loginInfo:'.$sid);
            if (!empty($loginInfo)) {
                $userArr = json_decode($loginInfo, true);
                if (is_array($userArr) && count($userArr) == 5 && (time() -$userArr[3]) < self::$expire) {
                    if ($cookie['roc_login'] == $userArr[1]) {

                        $loginSalt = UserModel::m()->redis()->get('loginSalt:'.$userArr[0]);
                        if (!empty($loginSalt) && $loginSalt == Roc::get('sys.config')['rockey'].$userArr[4]) {
                            $userInfo['uid'] = $userArr[0];
                            $userInfo['username'] = $userArr[1];
                            $userInfo['groupid'] = $userArr[2];
                            $userInfo['logintime'] = $userArr[3];
                            $userInfo['avatar'] = self::getAvatar($userArr[0]);
                            $userInfo['groupname'] = self::getGroupName($userArr[2]);
                            $userInfo['notice_num'] = NotificationModel::m()->getUnreadTotal($userArr[0]);
                            $userInfo['whisper_num'] = 0;
                        } else {
                            UserModel::m()->redis()->del('loginInfo:'.$sid);
                            UserModel::m()->redis()->del('loginSalt:'.$userArr[0]);
                        }
                    }
                }
            }
        }

        return $userInfo;
    }

    /**
     * 注册加密身份信息
     * @method setLoginInfo
     * @param  [type]       $uid      [description]
     * @param  [type]       $username [description]
     * @param  [type]       $groupid  [description]
     * @param  [type]       $salt     [description]
     */
    public static function setLoginInfo($uid, $username, $groupid, $salt)
    {
        UserModel::m()->updateLastTime($uid);

        $sid = parent::getGuid($uid.parent::randomString());
        // 重复检测，以确保唯一（虽然几乎不可能出现重复）
        if (!empty(UserModel::m()->redis()->get('loginInfo:'.$sid))) {
            self::setLoginInfo($uid, $username, $groupid, $salt);
            exit;
        }

        $loginTime = time();
        $expire = self::$expire;

        UserModel::m()->redis()->setex('loginInfo:'.$sid, $expire, json_encode([$uid, $username, $groupid, $loginTime, md5($salt)]));
        UserModel::m()->redis()->setex('loginSalt:'.$uid, $expire, Roc::get('sys.config')['rockey'].md5($salt));

        setcookie('roc_login', $username, $loginTime + $expire, '/', null, Roc::request()->secure, true);
        setcookie('roc_secure', $sid, $loginTime + $expire, '/', null, Roc::request()->secure, true);
    }

    /**
     * 获取用户组
     * @param  [type] $groupid [description]
     * @return [type]          [description]
     */
    public static function getGroupName($groupid)
    {
        switch ($groupid) {
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
        if (!empty($email) && !UserModel::m()->checkEmailValidity($email)) {
            die(self::renderJson(10401));
        }

        $_checkNickname = UserModel::m()->checkNickname($username);
        if (!empty($username) && $_checkNickname !== true) {
            die(self::renderJson(10402, $_checkNickname));
        }
        if (substr_count($password, ' ') > 0) {
            die(self::renderJson(10403));
        }
        if (strlen($password) < 8 || strlen($password) > 26) {
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
        $user = UserModel::m()->getByUid($uid);
        if (!empty($user)) {
            parent::renderBase(['active' => 'user']);
            Roc::render('user', [
                'user' => $user,
                'avatar' => self::getAvatar($user['uid']),
                'topics' => self::__getTopics(1, $uid),
                'statistic' => [
                    'topic' => TopicModel::m()->getTotal(['uid' => $uid, 'valid' => 1]),
                    'reply' => ReplyModel::m()->getTotal(['uid' => $uid, 'valid' => 1]),
                    'article' => ArticleModel::m()->getTotal(['uid' => $uid, 'valid' => 1]),
                    'fans' => FollowModel::m()->getFansCount(['fuid' => $uid]),
                    'follows' => FollowModel::m()->getFansCount(['uid' => $uid])
                ],
                'is_fans' => self::getloginInfo()['uid'] > 0 ? FollowModel::m()->isFans(self::getloginInfo()['uid'], $uid) : 0,
                'v2Price' => self::$v2Price,
                'v3Price' => self::$v3Price
            ]);
        } else {
            Roc::redirect('/');
        }
    }

    /**
     * 更换用户页TAB
     * @method changeTabType
     * @param  [type]        $uid  [description]
     * @param  [type]        $type [description]
     * @return [type]              [description]
     */
    public static function changeTabType($uid, $type)
    {
        $uid = $uid > 0 ? $uid : self::getloginInfo()['uid'];
        $user = UserModel::m()->getByUid($uid);

        if (!empty($user) && in_array($type, ['topics', 'replys', 'articles', 'fans', 'follows', 'collections'])) {
            if (in_array($type, ['follows', 'collections']) && $uid != self::getloginInfo()['uid']) {
                parent::json('error', '请求不合法');
            }

            $func = '__get'.ucfirst($type);
            $rows = self::$func(1, $uid);

            parent::json('success', [
                'rows' => $rows
            ]);
        }

        parent::json('error', '请求不合法');
    }

    /**
     * 个人提醒页
     * @return [type] [description]
     */
    public static function notice()
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0) {
            $rows = self::__getAllUnread($uid);
            parent::renderBase(['active' => 'notice', 'pageTitle' => '消息提醒']);
            Roc::render('notice', [
                'unread' => $rows
            ]);
        } else {
            Roc::redirect('/login');
        }
    }

    /**
     * 更换提醒类型
     * @method changeNoticeType
     * @param  [type]           $type [description]
     * @return [type]                 [description]
     */
    public static function changeNoticeType($type)
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0 && in_array($type, ['unread', 'notice', 'whisper'])) {

            switch ($type) {
                case 'unread':
                    $rows = self::__getAllUnread($uid);
                    break;

                case 'notice':
                    // 获取已读提醒
                    $ret = self::__getNotice(1, $uid);
                    $rows = $ret['rows'];
                    break;

                case 'whisper':
                    // 获取私信
                    $ret = self::__getWhisper(1, $uid);
                    $rows = $ret['rows'];
                    break;

                default:
                    break;
            }

            parent::json('success', [
                'type' => $type,
                'rows' => $rows,
                'offset' => isset($ret['offset']) ? $ret['offset'] : 0,
                'limit' => isset($ret['limit']) ? $ret['limit'] : 0,
                'total' => isset($ret['total']) ? $ret['total'] : 0
            ]);
        }

        parent::json('error', '请求不合法');
    }

    /**
     * 个人积分页面
     * @return [type] [description]
     */
    public static function scores()
    {
        $uid = self::getloginInfo()['uid'];
        $user = UserModel::m()->getByUid($uid);

        if (!empty($user)) {
            $data = self::__getScores(1, $uid);

            parent::renderBase(['active' => 'scores', 'pageTitle' => '积分明细']);
            Roc::render('scores', [
                'data' => $data,
                'user' => $user,
            ]);
        } else {
            Roc::redirect('/login');
        }
    }

    /**
     * 私信页面
     * @method chatWithUser
     * @param  [type]       $uid [description]
     * @return [type]            [description]
     */
    public static function chatWithUser($uid)
    {
        $myUid = self::getloginInfo()['uid'];
        $me = UserModel::m()->getByUid($myUid);
        $user = UserModel::m()->getByUid($uid);

        if (!empty($me) && !empty($user)) {
            $user['avatar'] = self::getAvatar($user['uid']);

            $data = self::__getWhisperDialog($user['uid'], $me['uid'], 1);
            parent::renderBase(['asset' => 'chat', 'active' => 'user', 'pageTitle' => '与'.$user['username'].'的私信']);
            Roc::render('chat', [
                'data' => $data,
                'user' => $user,
            ]);
        } else {
            empty($me) ? Roc::redirect('/login') : Roc::redirect('/');
        }
    }

    /**
     * 个人设置页
     * @return [type] [description]
     */
    public static function setting()
    {
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0) {
            $user = UserModel::m()->getByUid($uid);
            if (!empty($user)) {
                $data = [
                    // 头像上传Token
                    'avatarUploadToken' => Roc::qiniu()->uploadToken([
                        'scope' => Roc::get('qiniu.bucket').':avatar/'.$uid,
                        'deadline' => time() + 3600,
                        'saveKey' => 'avatar/'.$uid
                    ]),
                    'saveKey' => 'avatar/'.$uid
                ];
                UserModel::m()->redis()->delete('avatar:'.$uid);

                parent::renderBase(['active' => 'setting', 'pageTitle' => '设置']);
                Roc::render('setting', [
                    'user' => $user,
                    'avatar' => self::getAvatar($user['uid']),
                    'data' => $data
                ]);
            } else {
                Roc::redirect('/login');
            }
        } else {
            Roc::redirect('/login');
        }
    }

    /**
     * 保存个人设置
     * @return [type] [description]
     */
    public static function saveProfile()
    {
        $data = Roc::request()->data;
        $uid = self::getloginInfo()['uid'];

        if ($uid > 0) {
            $user = UserModel::m()->getByUid($uid);
            if (!empty($user)){
                if (UserModel::m()->checkEmailValidity($data->email)) {
                    $cEmail = UserModel::m()->getByEmail($data->email);

                    if (empty($cEmail) || $cEmail['uid'] == $uid) {
                        $cPhone = UserModel::m()->getByPhone($data->phone);

                        if (empty($cPhone) || $cPhone['uid'] == $uid) {
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

                            $ret = UserModel::m()->updateInfo($iData, $uid);

                            parent::json('success', '保存成功');
                        } else {
                            if (empty($data->phone)) {
                                parent::json('error', '请填写手机号');
                            }

                            parent::json('error', '手机号已存在');
                        }
                    } else {
                        parent::json('error', '邮箱已存在');
                    }
                } else {
                    parent::json('error', '邮箱不合法');
                }
            } else {
                parent::json('error', '用户不存在');
            }
        } else {
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

        if ($type == 2) {
            $score = self::$v2Price;
        } else {
            $score = self::$v3Price;
        }

        $uid = self::getloginInfo()['uid'];
        $user = UserModel::m()->getByUid($uid);

        if (empty($user)) {
            parent::json('error', '用户不存在');
        }

        $groupid = $user['groupid'];

        if ($groupid >= $type) {
            parent::json('error', '您已经是该级别或更高级别的VIP了');
        }

        if ($groupid == 2) {
            $score = self::$v3Price - self::$v2Price;
        }

        if ($uid > 0) {
            $userScore = UserModel::m()->getUserScore($uid);
            if ($userScore < $score) {
                parent::json('error', '您的积分余额（'.$userScore.'）不足以升级VIP'.$type);
            }

            try {
                $db = Roc::model()->getDb();
                $db->beginTransaction();

                $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$score." WHERE `uid` = ".$uid);
                if ($ret > 0) {
                    ScoreModel::m()->addRecord([
                        'tid' => 0,
                        'uid' => $uid,
                        'changed' => - $score,
                        'remain' => UserModel::m()->getUserScore($uid),
                        'reason' => '升级VIP'.$type,
                        'add_user' => $uid,
                        'add_time' => time(),
                    ]);

                    $ret = $db->exec("UPDATE `roc_user` SET `groupid` = ".$type." WHERE `uid` = ".$user['uid']);
                    if ($ret == 0) {
                        throw new \Exception("打赏失败，请重试");
                    }
                } else {
                    throw new \Exception("您的余额不足");
                }
                $db->commit();

                // 退出登录
                self::logout(false);

                parent::json('success', '升级VIP'.$type.'成功，请重新登录');
            } catch (\Exception $e) {
                $db->rollBack();
                parent::json('error', $e->getMessage());
            }
        } else {
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

        if ($money > 0 && $money <= 1000 && $uid > 0) {
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
        } else {
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

        if($verifyResult) {
            $outTradeNo = $_GET['out_trade_no'];
            $tradeNo = $_GET['trade_no'];
            $tradeStatus = $_GET['trade_status'];

            switch ($tradeStatus) {
                case 'TRADE_FINISHED':
                    # 此处可扩展写日志
                    break;

                default:
                    # 此处可扩展写日志
                    break;
            }

            Roc::app()->redirect('/scores');
        } else {
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

        if($verifyResult) {
            $outTradeNo = $_POST['out_trade_no'];
            $tradeNo = $_POST['trade_no'];
            $tradeStatus = $_POST['trade_status'];
            $price = $_POST['price'];

            switch ($tradeStatus) {
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
        } else {
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

        parent::json('success', self::__getTopics($page, $uid));
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

        parent::json('success', self::__getReplys($page, $uid));
    }

    public static function getMoreArticle($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        parent::json('success', self::__getArticles($page, $uid));
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

        parent::json('success', self::__getCollections($page, $uid));
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

        parent::json('success', self::__getFans($page, $uid));
    }

    /**
     * 获取更多关注者
     * @method getMoreFollows
     * @param  [type]         $uid  [description]
     * @param  [type]         $page [description]
     * @return [type]               [description]
     */
    public static function getMoreFollows($uid, $page)
    {
        $page = $page > 0 ? $page : 1;

        parent::json('success', self::__getFollows($page, $uid));
    }

    public static function getMoreNotice($page)
    {
        $uid = self::getloginInfo()['uid'];

        $page = $page > 0 ? $page : 1;

        parent::json('success', self::__getNotice($page, $uid));
    }

    /**
     * 获取更多私信
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function getMoreWhisper($page)
    {
        $uid = self::getloginInfo()['uid'];

        $page = $page > 0 ? $page : 1;

        parent::json('success', self::__getWhisper($page, $uid));
    }

    /**
     * 获取更多私信对话
     * @method getMoreWhisperDialog
     * @param  [type]               $uid  [description]
     * @param  [type]               $page [description]
     * @return [type]                     [description]
     */
    public static function getMoreWhisperDialog($uid, $page)
    {
        $myUid = self::getloginInfo()['uid'];

        $page = $page > 0 ? $page : 1;

        parent::json('success', self::__getWhisperDialog($uid, $myUid, $page));
    }

    /**
     * 关注（取消关注）用户
     * @return [type] [description]
     */
    public static function doFollow()
    {
        $uid = self::getloginInfo()['uid'];
        $fuid = Roc::request()->data->fuid;

        if ($uid == $fuid) {
            parent::json('error', '不可以关注自己哦~');
        }

        if ($uid > 0) {
            $status = FollowModel::m()->isFans($uid, $fuid);
            if ($status > 0) {
                FollowModel::m()->cancelFollow($uid, $fuid);

                parent::json('success', 0);
            } else {
                FollowModel::m()->addFollow($uid, $fuid);

                parent::json('success', 1);
            }
        } else {
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

        if ($uid > 0) {
            $atUser = UserModel::m()->getByUid($atUid);
            if (empty($atUser)) {
                parent::json('error', '目标用户不存在');
            }
            if ($atUid == $uid) {
                parent::json('error', '抱歉，不能私信自己');
            }

            $userScore = UserModel::m()->getUserScore($uid);
            if ($userScore < Roc::get('system.score.whisper')) {
                parent::json('error', '您的积分余额（'.$userScore.'）不足以支付');
            }

            try {
                $db = Roc::model()->getDb();
                $db->beginTransaction();

                $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".Roc::get('system.score.whisper')." WHERE `uid` = ".$uid);
                if ($ret > 0) {
                    ScoreModel::m()->addRecord([
                        'tid' => 0,
                        'uid' => $uid,
                        'changed' => - Roc::get('system.score.whisper'),
                        'remain' => UserModel::m()->getUserScore($uid),
                        'reason' => '发送私信给'.$atUser['username'],
                        'add_user' => $uid,
                        'add_time' => time(),
                    ]);

                    $ret = WhisperModel::m()->addWhisper([
                        'at_uid' => $atUid,
                        'uid' => $uid,
                        'content' => $content,
                        'post_time' => time()
                    ]);

                    if ($ret == 0) {
                        throw new \Exception("私信发送失败");
                    } else {
                        if (!empty($atUser['phone'])) {
                            parent::sendSms(Roc::get('sms.whisper_tplid'), $atUser['phone'], '#username#='.self::getloginInfo()['username'].'&#content#='.$content);
                        }
                    }
                } else {
                    throw new \Exception("您的余额不足");
                }

                $db->commit();

                parent::json('success', '私信传送成功');
            } catch (\Exception $e) {
                $db->rollBack();

                parent::json('error', $e->getMessage());
            }
        } else {
            parent::json('error', '您尚未登录');
        }
    }

    private static function __getTopics($page, $uid)
    {
        $topics = TopicModel::m()->getList(self::$per*($page - 1), self::$per, ['roc_topic.uid' => $uid, 'roc_topic.valid' => 1]);

        foreach ($topics as &$topic) {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);
            $topic['imageCount'] = RelationModel::m()->getRelation($topic['tid'], 1, 'count');
            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
            $topic['post_time'] = parent::formatTime($topic['post_time']);
            $topic['edit_time'] = parent::formatTime($topic['edit_time']);
            $topic['last_time'] = parent::formatTime($topic['last_time']);
        }

        return $topics;
    }

    private static function __getArticles($page, $uid)
    {
        $articles = ArticleModel::m()->getList(self::$per*($page - 1), self::$per, $uid);

        foreach ($articles as &$article)  {
            $article['title'] = Roc::filter()->topicOut($article['title'], true);
            $article['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($article['content']), 128);
            $article['post_time'] = parent::formatTime($article['post_time']);
            $article['poster'] = AttachmentModel::m()->getAttachment($article['poster_id'], $article['uid'], '90x68');
        }

        return $articles;
    }

    private static function __getFollows($page, $uid)
    {
        $follows = FollowModel::m()->getFollows(self::$per*($page - 1), self::$per, ['roc_follow.uid' => $uid]);

        if (!empty($follows)) {
            foreach ($follows as &$follow) {
                $follow['avatar'] = self::getAvatar($follow['fuid']);
            }
        }

        return $follows;
    }

    private static function __getCollections($page, $uid)
    {
        $topics = TopicModel::m()->getCollectionList(self::$per*($page - 1), self::$per, $uid);

        foreach ($topics as &$topic) {
            $topic['type'] = 'topic';
            $topic['title'] = Roc::filter()->topicOut($topic['title']);
            $topic['imageCount'] = RelationModel::m()->getRelation($topic['tid'], 1, 'count');
            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
            $topic['post_time'] = parent::formatTime($topic['post_time']);
            $topic['edit_time'] = parent::formatTime($topic['edit_time']);
            $topic['last_time'] = parent::formatTime($topic['last_time']);
        }

        $articles = ArticleModel::m()->getCollectionList(self::$per*($page - 1), self::$per, $uid);

        foreach ($articles as &$article)  {
            $article['type'] = 'article';
            $article['title'] = Roc::filter()->topicOut($article['title'], true);
            $article['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($article['content']), 128);
            $article['post_time'] = parent::formatTime($article['post_time']);
            $article['poster'] = AttachmentModel::m()->getAttachment($article['poster_id'], $article['uid'], '90x68');
        }

        $data = array_merge($topics, $articles);

        usort($data, function($a, $b) {
            if ($a['collection_id'] > $b['collection_id']) {
                return -1;
            } else {
                return 1;
            }
        });

        return $data;
    }

    private static function __getReplys($page, $uid)
    {
        $replys = ReplyModel::m()->getListByUid(self::$per*($page - 1), self::$per, $uid);

        foreach ($replys as &$reply) {
            $reply['content'] = Roc::filter()->topicOut($reply['content']);

            if ($reply['at_pid'] > 0) {
                $reply['at_reply'] = ReplyModel::m()->getReply($reply['at_pid'], $reply['tid']);

                if (!empty($reply['at_reply'])) {
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

    private static function __getAllUnread($uid)
    {
        $rows = [];
        // 未读提醒
        $data = NotificationModel::m()->getUnread($uid);
        foreach ($data as $notification) {
            array_push($rows, [
                'id' => $notification['id'],
                'title' => '<a href="/user/'.$notification['uid'].'" target="_blank">'.$notification['username'].'</a> 在主题 “<a href="/read/'.$notification['tid'].'#reply-'.$notification['pid'].'" target="_blank">'.(Roc::controller('frontend\Index')->cutSubstr($notification['title'])).'</a>” '.($notification['pid'] > 0 ? '下的回复' : '').'中提到了你',
                'time' => parent::formatTime($notification['post_time']),
                'tid' => $notification['tid'],
                'pid' => $notification['pid'],
                'type' => 'notice',
                'unix_time' => $notification['post_time']
            ]);
        }

        // 未读私信
        $data = WhisperModel::m()->getUnread($uid);
        foreach ($data as $whisper) {
            array_push($rows, [
                'id' => $whisper['id'],
                'title' => '<a href="/user/'.$whisper['uid'].'" target="_blank">'.$whisper['username'].'</a> 给您发了一条私信',
                'content' => Roc::filter()->topicOut($whisper['content']),
                'time' => parent::formatTime($whisper['post_time']),
                'uid' => $whisper['uid'],
                'type' => 'whisper',
                'unix_time' => $whisper['post_time']
            ]);
        }

        usort($rows, function($a, $b) {
            if ($a['unix_time'] >= $b['unix_time']) {
                return -1;
            } else {
                return 1;
            }
        });

        return $rows;
    }

    private static function __getNotice($page, $uid)
    {
        $data = NotificationModel::m()->getRead($uid, self::$per*($page - 1), self::$per);

        foreach ($data['rows'] as &$notification) {
            $notification = [
                'id' => $notification['id'],
                'title' => '<a href="/user/'.$notification['uid'].'" target="_blank">'.$notification['username'].'</a> 在'.($notification['pid'] > 0 ? '回复' : '主题').'中提到了你',
                'time' => parent::formatTime($notification['post_time']),
                'tid' => $notification['tid'],
                'pid' => $notification['pid']
            ];
        }

        return $data;
    }

    private static function __getWhisper($page, $uid)
    {
        $data = WhisperModel::m()->getAll($uid, self::$per*($page - 1), self::$per);

        foreach ($data['rows'] as &$whisper) {
            $whisper = [
                'id' => $whisper['id'],
                'is_read' => $whisper['is_read'],
                'uid' => $whisper['uid'],
                'send_username' => $whisper['send_username'],
                'at_uid' => $whisper['at_uid'],
                'receive_username' => $whisper['receive_username'],
                'title' => $whisper['at_uid'] == $uid ? $whisper['send_username'].'给您发了一条私信' : '您给'.$whisper['receive_username'].'发了一条私信',
                'content' => Roc::filter()->topicOut($whisper['content']),
                'time' => parent::formatTime($whisper['post_time'])
            ];
        }

        return $data;
    }

    private static function __getWhisperDialog($uid, $myUid, $page = 1)
    {
        $data = WhisperModel::m()->getDialog($uid, $myUid, self::$per*($page - 1), self::$per);

        foreach ($data['rows'] as &$whisper) {
            $whisper = [
                'id' => $whisper['id'],
                'is_read' => $whisper['is_read'],
                'is_mine' => $whisper['uid'] == $myUid ? 1 : 0,
                'uid' => $whisper['uid'],
                'at_uid' => $whisper['at_uid'],
                'content' => Roc::filter()->topicOut($whisper['content']),
                'time' => parent::formatTime($whisper['post_time'])
            ];
        }

        usort($data['rows'], function($a, $b) {
            if ($a['id'] > $b['id']) {
                return 1;
            }
            return -1;
        });

        return $data;
    }

    private static function __getFans($page, $uid)
    {
        $fans = FollowModel::m()->getFans(self::$per*($page - 1), self::$per, ['fuid' => $uid]);

        foreach ($fans as &$fan) {
            $fan['avatar'] = self::getAvatar($fan['uid']);
        }

        return $fans;
    }

    private static function __getScores($page, $uid)
    {
        $scores = ScoreModel::m()->getList($uid, 100*($page - 1), 100);

        foreach ($scores as &$score) {
            $score['add_time'] = parent::formatTime($score['add_time']);
        }

        return $scores;
    }

    private static function __doRecharge($uid, $score, $tradeNo)
    {
        $record = ScoreModel::m()->getRecord($tradeNo);

        if (!empty($record)) {
            return false;
        }

        try {
            $db = Roc::model()->getDb();
            $db->beginTransaction();
            $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` + ".$score." WHERE `uid` = ".$uid);

            if ($ret > 0) {
                $return = ScoreModel::m()->addRecord([
                    'tid' => 0,
                    'trade_no' => $tradeNo,
                    'uid' => $uid,
                    'changed' => $score,
                    'remain' => UserModel::m()->getUserScore($uid),
                    'reason' => '积分充值',
                    'add_user' => $uid,
                    'add_time' => time(),
                ]);

                if ($return == 0) {
                    throw new \Exception("充值记录添加失败");
                }
            } else {
                throw new \Exception("充值失败");
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();

            // parent::json('error', $e->getMessage());
        }
    }
}
