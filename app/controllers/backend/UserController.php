<?php
namespace backend;

use \Controller;
use \Roc;

class UserController extends BaseController
{
    /**
     * 编辑用户
     * @method actionEditUser
     * @return [type]         [description]
     */
    public static function actionEditUser()
    {
        Roc::controller('frontend\Base')->csrfCheck();

        self::__checkManagePrivate();

        $data = Roc::request()->data;

        $change = [
            'username' => $data->username,
            'email' => $data->email,
            'phone' => $data->phone,
            'groupid' => $data->groupid
        ];

        if (!empty($data['password']))
        {
            array_merge($change, ['password' => md5($data['password'])]);
        }

        $uid = $data['uid'];

        $ret = Roc::model('user')->updateInfo($change, $uid);

        if ($ret > 0)
        {
            parent::json('success', '修改成功');
        }
        else
        {
            parent::json('error', '修改失败');
        }
    }

    /**
     * 获取用户积分详情
     * @method getUserScoreRecords
     * @param  [type]              $uid [description]
     * @return [type]                   [description]
     */
    public static function getUserScoreRecords($uid)
    {
        self::__checkManagePrivate();

        // 直接取最新300条记录
        $scores = Roc::model('score')->getList($uid, 0, 300);

        if (!empty($scores))
        {
            foreach ($scores as &$score)
            {
                $score['add_time'] = Roc::controller('frontend\Base')->formatTime($score['add_time']);
            }
        }

        parent::renderBase(['active' => 'users']);

        Roc::render('admin/scores', [
            'scores' => $scores,
            'uid' => $uid
        ]);
    }

    /**
     * 检测管理员权限
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    private static function __checkManagePrivate($force = false)
    {
        if (Roc::controller('frontend\User')->getloginInfo()['groupid'] != 99)
        {
            if ($force)
            {
                Roc::redirect('/login');
            }

            parent::json('error', '抱歉，权限不足！');
        }
        else
        {
            return true;
        }
    }
}
