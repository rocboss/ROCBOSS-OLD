<?php
namespace backend;

use \Controller;
use \Roc;

class AdminController extends BaseController
{
    public static $per = 12;

    /**
     * 管理首页
     * @return [type] [description]
     */
    public static function index()
    {
        self::__checkManagePrivate(true);

        $server = [];

        $server['time'] = date('Y-m-d H:i:s', time());

        $server['port'] = $_SERVER['SERVER_PORT'];

        $server['os'] = @PHP_OS;

        $server['version'] = @PHP_VERSION;

        $server['root'] = $_SERVER['DOCUMENT_ROOT'];

        $server['name'] = $_SERVER['SERVER_NAME'];

        $server['upload'] = @ini_get('upload_max_filesize');

        $session_timeout = @ini_get('session.gc_maxlifetime');

        $server['timeout'] = $session_timeout ? $session_timeout / 60 : '未知';

        $server['memory_usage'] = self::__formatSize(memory_get_usage());

        if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false)
        {
            $server['software'] = 'Apache';
        }
        elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx') !== false)
        {
            $server['software'] = 'Nginx';
        }
        else
        {
            $server['software'] = 'Other';
        }

        parent::renderBase(['active' => 'index']);

        Roc::render('admin/index', [
            'server' => $server
        ]);
    }

    /**
     * 系统设置
     * @method system
     * @return [type] [description]
     */
    public static function system()
    {
        self::__checkManagePrivate(true);

        $allSysData = Roc::db()->from('roc_config')->select()->many();

        if (!empty($allSysData))

            foreach ($allSysData as $key => $value) {
                $system[$value['key']] = $value['value'];
            }

        parent::renderBase(['active' => 'system']);

        Roc::render('admin/system', [
            'system' => $system
        ]);
    }

    /**
     * 分类
     * @method clubs
     * @return [type] [description]
     */
    public static function clubs()
    {
        self::__checkManagePrivate(true);

        $clubs = Roc::model('club')->getList();

        parent::renderBase(['active' => 'clubs']);

        Roc::render('admin/clubs', [
            'clubs' => $clubs
        ]);
    }

    /**
     * 帖子
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function topics($page)
    {
        self::__checkManagePrivate(true);

        $page = parent::getNumVal($page, 1, true);

        $topics = Roc::controller('frontend\Index')->getTopicList(0, $page, 'tid');

        parent::renderBase(['active' => 'topics']);

        Roc::render('admin/topics', [
            'topics' => $topics,
            'count' => Roc::model('topic')->getTotal(['valid' => 1])
        ]);
    }

    /**
     * 回复
     * @method replys
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function replys($page)
    {
        self::__checkManagePrivate(true);

        $page = parent::getNumVal($page, 1, true);

        $replys = Roc::model('reply')->getList(self::$per*($page - 1), self::$per);

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

            $reply['avatar'] = Roc::controller('api\User')->getAvatar($reply['uid']);

            $reply['add_time'] = $reply['post_time'];

            $reply['post_time'] = parent::formatTime($reply['post_time']);
        }

        parent::renderBase(['active' => 'replys']);

        Roc::render('admin/replys', [
            'replys' => $replys,
            'page' => $page,
            'per' => self::$per,
            'count' => Roc::model('reply')->getTotal(['valid' => 1])
        ]);
    }

    /**
     * 用户
     * @method users
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function users($page)
    {
        self::__checkManagePrivate(true);

        $condition = ['valid' => 1];

        if (Roc::request()->query->uid > 0) {
            $condition = ['valid' => 1, 'uid' => Roc::request()->query->uid];
        }

        $page = parent::getNumVal($page, 1, true);

        $users = Roc::model('user')->getMany(self::$per*($page - 1), self::$per, $condition, ['sortDESC', 'last_time']);

        foreach ($users as &$user)
        {
            $user['group_name'] = Roc::controller('frontend\User')->getGroupName($user['groupid']);

            $user['avatar'] = Roc::controller('frontend\User')->getAvatar($user['uid']);
        }

        parent::renderBase(['active' => 'users']);

        Roc::render('admin/users', [
            'users' => $users,
            'page' => $page,
            'per' => self::$per,
            'count' => Roc::model('user')->getTotal($condition)
        ]);
    }

    /**
     * 链接
     * @method links
     * @return [type] [description]
     */
    public static function links()
    {
        self::__checkManagePrivate(true);

        $links = Roc::model('link')->getList();

        parent::renderBase(['active' => 'links']);

        Roc::render('admin/links', [
            'links' => $links
        ]);
    }

    /**
     * 编辑修改链接
     * @method actionPostLink
     * @return [type]         [description]
     */
    public static function actionPostLink()
    {
        self::__checkManagePrivate();

        Roc::controller('frontend\Base')->csrfCheck();

        $data = Roc::request()->data;

        if ($data->id > 0) {
            Roc::model('link')->update([
                'name' => $data->name,
                'url' => $data->url,
                'sort' => $data->sort,
            ], $data->id);
        } else {
            Roc::model('link')->insert([
                'name' => $data->name,
                'url' => $data->url,
                'sort' => $data->sort,
                'valid' => 1
            ]);
        }

        echo json_encode(['status' => 'success']);
    }

    /**
     * 删除链接
     * @method actionDelLink
     * @return [type]        [description]
     */
    public static function actionDelLink()
    {
        self::__checkManagePrivate();

        Roc::controller('frontend\Base')->csrfCheck();

        $data = Roc::request()->data;

        Roc::model('link')->delete($data->id);

        echo json_encode(['status' => 'success', 'msg' => '删除成功']);
    }

    /**
     * 编辑修改分类
     * @method actionPostClub
     * @return [type]         [description]
     */
    public static function actionPostClub()
    {
        self::__checkManagePrivate();

        Roc::controller('frontend\Base')->csrfCheck();

        $data = Roc::request()->data;

        if ($data->cid > 0) {
            Roc::model('club')->update([
                'club_name' => $data->club_name,
                'sort' => $data->sort,
            ], $data->cid);
        } else {
            Roc::model('club')->insert([
                'club_name' => $data->club_name,
                'sort' => $data->sort,
                'valid' => 1
            ]);
        }

        echo json_encode(['status' => 'success']);
    }

    /**
     * 删除分类
     * @method actionDelClub
     * @return [type]        [description]
     */
    public static function actionDelClub()
    {
        self::__checkManagePrivate();

        Roc::controller('frontend\Base')->csrfCheck();

        $data = Roc::request()->data;

        $count = Roc::model('topic')->getTotal(['valid' => 1, 'cid' => $data->cid]);

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'msg' => '当前分类下存在主题，无法删除']);
        } else {
            Roc::model('club')->delete($data->cid);
            echo json_encode(['status' => 'success', 'msg' => '删除成功']);
        }
    }

    /**
     * 保存系统设置
     * @method actionSaveSystemSetting
     * @return [type]                  [description]
     */
    public static function actionSaveSystemSetting()
    {
        self::__checkManagePrivate();

        Roc::controller('frontend\Base')->csrfCheck();

        $data = Roc::request()->data;

        Roc::db()->from('roc_config')->where(['`key`' => 'sitename'])->update(['value' => $data->sitename])->execute();

        Roc::db()->from('roc_config')->where(['`key`' => 'keywords'])->update(['value' => $data->keywords])->execute();

        Roc::db()->from('roc_config')->where(['`key`' => 'description'])->update(['value' => $data->description])->execute();

        Roc::db()->from('roc_config')->where(['`key`' => 'rockey'])->update(['value' => $data->rockey])->execute();

        @unlink('_config.php');

        echo json_encode(['status' => 'success', 'msg' => '更新成功']);
    }

    /**
     * 清理缓存
     * @return [type] [description]
     */
    public static function actionDoClearCache()
    {
        Roc::view()->clean();

        echo json_encode(['status' => 'success']);
    }

    /**
     * 格式化大小
     * @param  [type] $filesize [description]
     * @return [type]           [description]
     */
    private static function __formatSize($filesize)
    {
        if ($filesize >= 1073741824)
        {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        }
        elseif ($filesize >= 1048576)
        {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        }
        elseif ($filesize >= 1024)
        {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        }
        else
        {
            $filesize = $filesize . ' Bytes';
        }

        return $filesize;
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
