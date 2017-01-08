<?php
namespace backend;

use Roc;
use UserModel;
use ClubModel;
use LinkModel;
use ScoreModel;
use TopicModel;
use ReplyModel;
use WhisperModel;
use AttachmentModel;
use WithdrawModel;

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

        if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false) {
            $server['software'] = 'Apache';
        } elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx') !== false) {
            $server['software'] = 'Nginx';
        } else {
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
        if (!empty($allSysData)) {
            foreach ($allSysData as $key => $value) {
                $system[$value['key']] = $value['value'];
            }
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

        $clubs = ClubModel::m()->getList();

        parent::renderBase(['active' => 'clubs']);
        Roc::render('admin/clubs', [
            'clubs' => $clubs
        ]);
    }

    /**
     * 文章
     * @method articles
     * @param  [type]   $page [description]
     * @return [type]         [description]
     */
    public static function articles($page)
    {
        self::__checkManagePrivate(true);

        $page = parent::getNumVal($page, 1, true);

        $articles = ArticleModel::m()->getList(($page - 1) * self::$per, self::$per, 0, true);
        foreach ($articles as &$article) {
            $article['title'] = Roc::filter()->topicOut($article['title'], true);
            $article['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($article['content']), 128);
            $article['post_time'] = parent::formatTime($article['post_time']);
            $article['poster'] = AttachmentModel::m()->getAttachment($article['poster_id'], $article['uid'], '90x68');
        }

        $total = ArticleModel::m()->getTotal(['valid' => 1]);

        parent::renderBase(['active' => 'articles']);
        Roc::render('admin/articles', [
            'articles' => [
                'rows' => $articles,
                'per' => self::$per,
                'page' => $page,
                'total' => $total,
            ],
            'count' => $total
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
            'count' => TopicModel::m()->getTotal(['valid' => 1])
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

        $replys = ReplyModel::m()->getList(self::$per*($page - 1), self::$per);

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

        parent::renderBase(['active' => 'replys']);
        Roc::render('admin/replys', [
            'replys' => $replys,
            'page' => $page,
            'per' => self::$per,
            'count' => ReplyModel::m()->getTotal(['roc_topic.valid' => 1, 'roc_reply.valid' => 1], false)
        ]);
    }

    /**
     * 提现申请
     * @method withdraw
     * @param  [type]   $page [description]
     * @return [type]         [description]
     */
    public static function withdraw($page)
    {
        self::__checkManagePrivate(true);
        $page = parent::getNumVal($page, 1, true);

        $withdraws = WithdrawModel::m()->getList(self::$per*($page - 1), self::$per);
        foreach ($withdraws as $key => &$row) {
            $row['avatar'] = Roc::controller('frontend\User')->getAvatar($row['uid']);
            $row['statusText'] = $row['status'] == 0 ? '待审核' : ($row['status'] == 1 ? '已通过' : '已拒绝');
            $row['add_time'] = date('Y-m-d H:i:s', $row['add_time']);
        }

        parent::renderBase(['active' => 'withdraws']);
        Roc::render('admin/withdraws', [
            'withdraws' => $withdraws,
            'page' => $page,
            'per' => self::$per,
            'count' => WithdrawModel::m()->getTotal()
        ]);
    }

    /**
     * 提现操作
     * @method doWithdraw
     * @return [type]     [description]
     */
    public static function doWithdraw()
    {
        self::__checkManagePrivate();
        parent::csrfCheck();

        $data = Roc::request()->data;
        $wd = WithdrawModel::m()->get('id', $data->id);
        if (!empty($wd)) {
            WithdrawModel::m()->update($wd['id'], [
                'status' => in_array($data->status, [0, 1, 2]) ? $data->status : 0,
                'remark' => $data->remark,
                'handle_uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                'handle_time' => time(),
            ]);
            if ($data->status == 1) {
                ScoreModel::m()->addRecord([
                    'tid' => 0,
                    'uid' => $wd['uid'],
                    'changed' => 0,
                    'remain' => UserModel::m()->getUserScore($wd['uid']),
                    'reason' => '申请提现【编号 '.$wd['id'].'】，审核通过，已转账',
                    'add_user' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                    'add_time' => time(),
                ]);
            } elseif ($data->status == 2) {
                $ret = UserModel::m()->updateInfo([
                    'score' => $wd['score'] + UserModel::m()->getUserScore($wd['uid'])
                ], $wd['uid']);
                if ($ret > 0) {
                    ScoreModel::m()->addRecord([
                        'tid' => 0,
                        'uid' => $wd['uid'],
                        'changed' => + $wd['score'],
                        'remain' => UserModel::m()->getUserScore($wd['uid']),
                        'reason' => '申请提现【编号 '.$wd['id'].'】，已拒绝，积分返还。理由【'.$data->remark.'】',
                        'add_user' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                        'add_time' => time(),
                    ]);
                }
            }

            $ret = WhisperModel::m()->addWhisper([
                'at_uid' => $wd['uid'],
                'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                'content' => '你的提现申请【编号 '.$wd['id'].'】已处理，请前往个人积分详情页查看（本消息由系统后台自动发送）',
                'post_time' => time()
            ]);

            echo json_encode(['status' => 'success', 'data' => '请求成功']);
        } else {
            echo json_encode(['status' => 'error', 'data' => '请求非法']);
        }
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
        if (!empty(Roc::request()->query->username)) {
            $condition = 'username LIKE \'%'.Roc::filter()->topicInWeb(Roc::request()->query->username).'%\' AND valid = 1';
        }

        $page = parent::getNumVal($page, 1, true);
        $users = UserModel::m()->getMany(self::$per*($page - 1), self::$per, $condition, ['sortDESC', 'last_time']);
        foreach ($users as &$user) {
            $user['group_name'] = Roc::controller('frontend\User')->getGroupName($user['groupid']);
            $user['avatar'] = Roc::controller('frontend\User')->getAvatar($user['uid']);
        }

        parent::renderBase(['active' => 'users']);
        Roc::render('admin/users', [
            'users' => $users,
            'page' => $page,
            'per' => self::$per,
            'count' => UserModel::m()->getTotal($condition)
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

        $links = LinkModel::m()->getList();

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

        parent::csrfCheck();

        $data = Roc::request()->data;

        if ($data->id > 0) {
            LinkModel::m()->update([
                'name' => $data->name,
                'url' => $data->url,
                'sort' => $data->sort,
            ], $data->id);
        } else {
            LinkModel::m()->insert([
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

        parent::csrfCheck();
        $data = Roc::request()->data;
        LinkModel::m()->delete($data->id);

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

        parent::csrfCheck();

        $data = Roc::request()->data;

        if ($data->cid > 0) {
            ClubModel::m()->update([
                'club_name' => $data->club_name,
                'sort' => $data->sort,
            ], $data->cid);
        } else {
            ClubModel::m()->insert([
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

        parent::csrfCheck();
        $data = Roc::request()->data;
        $count = TopicModel::m()->getTotal(['valid' => 1, 'cid' => $data->cid]);

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'msg' => '当前分类下存在主题，无法删除']);
        } else {
            ClubModel::m()->delete($data->cid);
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

        parent::csrfCheck();

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
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' Bytes';
        }

        return $filesize;
    }
}
