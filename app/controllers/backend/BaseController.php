<?php
namespace backend;

use Roc;
use Bootstrap;

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
        '10410' => '该账户已绑定微博'
    ];

    /**
     * 传递基础视图
     */
    protected static function renderBase(array $params)
    {
        Roc::render('admin/_header', [
            'pageTitle' => isset($params['pageTitle']) ? $params['pageTitle'] : '',
            'active' => $params['active'],
            'seo'=>[
                    'sitename' => Roc::get('sys.config')['sitename'],
                    'keywords' => Roc::get('sys.config')['keywords'],
                    'description' => Roc::get('sys.config')['description']
                ],
            'loginInfo' => Roc::controller('frontend\User')->getloginInfo(),
        ], 'headerLayout');

        Roc::render('admin/_sidebar', [], 'sidebarLayout');

        Roc::render('admin/_footer', [], 'footerLayout');
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
    public static function renderJson($code, $msg = null, $data = null)
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
    protected static function formatTime($unixTime)
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
    protected static function getVal($value, $default = '')
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
    protected static function getNumVal($value, $default = 0, $force = false)
    {
        if ($force) {
            return is_numeric($value) && intval($value) > 0 ? intval($value) : $default;
        }

        return is_numeric($value) ? intval($value) : $default;
    }

    /**
     * 检测管理员权限
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    protected static function __checkManagePrivate($force = false)
    {
        if (Roc::controller('frontend\User')->getloginInfo()['groupid'] != 99) {
            if ($force) {
                Roc::redirect('/login');
            }

            parent::json('error', '抱歉，权限不足！');
        } else {
            return true;
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
}
