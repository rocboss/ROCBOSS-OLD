<?php
namespace frontend;

use Roc;
use UserModel;
use ClubModel;
use LinkModel;
use ScoreModel;
use TopicModel;
use ReplyModel;
use FollowModel;
use WhisperModel;
use RelationModel;
use ArticleModel;
use AttachmentModel;
use WithdrawModel;

class IndexController extends BaseController
{
    public static $per = 30;

    /**
     * 切换明暗主题
     * @method turnLight
     * @return [type]    [description]
     */
    public static function turnLight()
    {
        $status = in_array(Roc::request()->cookies->light, ['black', 'white']) ? Roc::request()->cookies->light : 'white';

        if ($status == 'black') {
            setcookie('light', 'white', time() + 86400 * 30, '/', null, Roc::request()->secure, true);
        } else {
            setcookie('light', 'black', time() + 86400 * 30, '/', null, Roc::request()->secure, true);
        }

        echo json_encode(['status' => 'success']);
    }

    public static function indexRedirect($cid = 0, $page = 1)
    {
        $cid = parent::getNumVal($cid, 0, true);
        $page = parent::getNumVal($page, 1, true);

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /category-$cid-$page.html");
        exit;
    }

    /**
     * 首页
     * @param  [type] $cid  [description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function index($cid = 0, $page = 1)
    {
        $cid = parent::getNumVal($cid, 0, true);
        $page = parent::getNumVal($page, 1, true);
        $sort = self::getSort(Roc::request()->cookies->topic_sort);
        $data = self::getTopicList($cid, $page, $sort);

        $clubs = ClubModel::m()->getList();
        $pageTitle = '';
        if ($cid > 0) {
            foreach ($clubs as $key => $club) {
                if ($club['cid'] == $cid) {
                    $pageTitle = $club['club_name'];
                }
            }
        }

        parent::renderBase([
            'asset' => 'index',
            'active' => 'index-'.$cid,
            'pageTitle' => $pageTitle
        ]);

        Roc::render('index', [
            'data' => $data,
            'clubs' => $clubs,
            'statistic' => [
                'user' => UserModel::m()->getTotal(['valid' => 1]),
                'topic' => TopicModel::m()->getTotal(['valid' => 1]),
                'article' => ArticleModel::m()->getTotal(['valid' => 1])
            ],
            'links' => LinkModel::m()->getList()
        ]);
    }

    /**
     * 帖子搜索
     * @method search
     * @return [type] [description]
     */
    public static function search()
    {
        $query = Roc::request()->query;
        $page = $query->page > 1 ? intval($query->page) : 1;
        $error = '';
        $data = [
            'rows' => [],
            'q' => Roc::filter()->topicInWeb($query->q),
            'page' => $page,
            'per' => self::$per,
            'total' => 0
        ];

        if (!is_string($query->q)) {
            $error = '搜索参数不合法';
        } else {
            if (strlen($query->q) >= 2) {
                $data = self::searchTopicList(Roc::filter()->topicInWeb($query->q), $page);
            } else {
                $error = '搜索关键字太短，要不少于两个字符哦~';
            }
        }

        parent::renderBase(['active' => 'search']);
        Roc::render('search', [
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * 帖子详情
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function read($tid)
    {
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];
        $topic = TopicModel::m()->getByTid($tid);

        if (!empty($topic)) {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);
            $topic['content'] = Roc::filter()->topicOut($topic['content']);
            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
            $topic['add_time'] = $topic['post_time'];
            $topic['post_time'] = parent::formatTime($topic['post_time']);
            $topic['edit_time'] = parent::formatTime($topic['edit_time']);
            $topic['last_time'] = parent::formatTime($topic['last_time']);

            $topic['owner_statistic'] = [
                'topic' => TopicModel::m()->getTotal(['uid' => $topic['uid'], 'valid' => 1]),
                'reply' => ReplyModel::m()->getTotal(['uid' => $topic['uid'], 'valid' => 1]),
                'fans' => FollowModel::m()->getFansCount(['fuid' => $topic['uid']]),
            ];

            $relation = array_map([__CLASS__, 'getAttachmentID'], RelationModel::m()->getRelation($topic['tid'], 1));

            // 主题所带图片(APP单独附图)
            $topic['images'] = [
                'rows' => AttachmentModel::m()->getAttachments($relation),
                'count' => RelationModel::m()->getRelation($topic['tid'], 1, 'count')
            ];

            // 主题下赞
            $topic['praise'] = [
                'rows' => TopicModel::m()->getPraiseList($topic['tid']),
                'hasPraise' => TopicModel::m()->getPraiseDetail($topic['tid'], $uid) > 0 ? true : false
            ];

            // 主题下打赏
            $topic['reward'] = [
                'rows' => TopicModel::m()->getRewardList($topic['tid']),
            ];

            // 主题是否收藏
            $topic['hasCollection'] = TopicModel::m()->getCollectionDetail($topic['tid'], $uid) > 0 ? true : false;

            // 主题下回复
            $topic['reply'] = [
                'rows' => ReplyModel::m()->getListByTid($topic['tid']),
                'count' => ReplyModel::m()->getListByTid($topic['tid'], 'count')
            ];

            if (!empty($topic['reward']['rows'])) {
                foreach ($topic['reward']['rows'] as &$reward) {
                    $reward['add_time'] = parent::formatTime($reward['add_time']);
                }
            }

            if (!empty($topic['reply']['rows'])) {
                foreach ($topic['reply']['rows'] as &$reply) {
                    $relation = array_map([__CLASS__, 'getAttachmentID'], RelationModel::m()->getRelation($reply['pid'], 2));
                    $reply['content'] = Roc::filter()->topicOut($reply['content']);
                    if ($reply['at_pid'] > 0) {
                        $reply['at_reply'] = ReplyModel::m()->getReply($reply['at_pid'], $reply['tid']);
                        if (!empty($reply['at_reply'])) {
                            $reply['at_reply']['content'] = self::cutSubstr(Roc::filter()->topicOut($reply['at_reply']['content']));
                            $reply['at_reply']['post_time'] = parent::formatTime($reply['at_reply']['post_time']);
                        }
                    }

                    $reply['images'] = [
                        'rows' => AttachmentModel::m()->getAttachments($relation),
                        'count' => RelationModel::m()->getRelation($reply['pid'], 2, 'count')
                    ];
                    $reply['avatar'] = Roc::controller('frontend\User')->getAvatar($reply['uid']);
                    $reply['add_time'] = $reply['post_time'];
                    $reply['post_time'] = parent::formatTime($reply['post_time']);
                }
            }

            if (!empty($topic['praise']['rows'])) {
                foreach ($topic['praise']['rows'] as &$praise) {
                    $praise['avatar'] = Roc::controller('frontend\User')->getAvatar($praise['uid']);
                }
            }

            parent::renderBase([
                'active' => 'read',
                'pageTitle' => (!empty($topic) ? $topic['title'] : ''),
                'keywords' =>  (!empty($topic) ? $topic['title'] : ''),
                'description' => (!empty($topic) ? strip_tags(Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($topic['content']), 128)) : ''),
            ]);
            Roc::render('read', [
                'data' => $topic,
                'clubs' => ClubModel::m()->getList(),
            ]);
        } else {
            Roc::redirect('/');
        }
    }

    /**
     * 发布新帖
     * @return [type] [description]
     */
    public static function newTopic()
    {
        if (!UserModel::m()->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid'])) {
            parent::renderBase(['asset' => 'new_topic', 'active' => 'newTopic', 'pageTitle' => '发布新主题']);

            Roc::render('new_topic', [
                'clubs' => ClubModel::m()->getList()
            ]);
        } else {
            Roc::redirect('/login');
        }
    }

    public static function editTopic($tid)
    {
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if (!UserModel::m()->checkIsBanned($uid)) {
            $topic = TopicModel::m()->getByTid($tid);

            if (!empty($topic)) {
                parent::renderBase(['asset' => 'edit_topic', 'active' => 'editTopic', 'pageTitle' => '编辑主题']);
                Roc::render('edit_topic', [
                    'topic' => $topic,
                    'clubs' => ClubModel::m()->getList()
                ]);
            } else {
                Roc::redirect('/');
            }
        } else {
            Roc::redirect('/login');
        }
    }

    /**
     * 更改排序
     * @param  [type] $cid  [description]
     * @param  [type] $page [description]
     * @param  [type] $sort [description]
     * @return [type]       [description]
     */
    public static function changeSort($cid, $page, $sort)
    {
        $cid = parent::getNumVal($cid, 0, true);
        $page = parent::getNumVal($page, 1, true);

        if (!in_array($sort, ['post_time', 'last_time', 'comment_num', 'essence'])) {
            $sort = 'post_time';
        }

        setcookie('topic_sort', $sort, time() + 86400, '/', null, Roc::request()->secure, true);
        $data = self::getTopicList($cid, $page, $sort);
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    /**
     * 获取附件ID
     * @param  [type] $v [description]
     * @return [type]    [description]
     */
    public static function getAttachmentID($v)
    {
        return $v['attachment_id'];
    }

    /**
     * 裁剪摘要
     * @param  [type]  $str_cut [description]
     * @param  integer $length  [description]
     * @return [type]           [description]
     */
    public static function cutSubstr($str_cut, $length = 64)
    {
        $str_cut = strip_tags($str_cut);

        if (mb_strlen(trim($str_cut), 'utf8') > $length) {
            return trim(mb_substr($str_cut, 0, $length, 'utf-8')) . '...';
        } else {
            return trim($str_cut);
        }
    }

    /**
     * 获取帖子列表
     * @param  [type] $cid  [description]
     * @param  [type] $page [description]
     * @param  [type] $sort [description]
     * @return [type]       [description]
     */
    public static function getTopicList($cid, $page, $sort)
    {
        // 返回数据格式
        $data = [
            'rows' => [],
            'sort' => $sort,
            'cid' => $cid,
            'page' => $page,
            'per' => self::$per,
            'total' => 0
        ];

        $condition = $cid > 0 ? ['roc_topic.valid' => 1, 'roc_topic.cid' => $cid] : ['roc_topic.valid' => 1];

        if ($sort == 'essence') {
            $condition = array_merge($condition, ['roc_topic.is_essence' => 1]);
            $sort = 'post_time';
        }

        $rsp = TopicModel::m()->getList(($page-1)*self::$per, self::$per, $condition, ['sortDESC', ['is_top', $sort]], true);
        $data['rows'] = isset($rsp['rows']) ? $rsp['rows'] : $rsp;
        $data['total'] = isset($rsp['total']) ? $rsp['total'] : TopicModel::m()->getTotal($condition);

        if (!empty($data['rows'])) {
            foreach ($data['rows'] as &$topic) {
                $topic['title'] = Roc::filter()->topicOut($topic['title']);
                $topic['imageCount'] = RelationModel::m()->getRelation($topic['tid'], 1, 'count');
                $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
                $topic['post_time'] = parent::formatTime($topic['post_time']);
                $topic['edit_time'] = parent::formatTime($topic['edit_time']);
                $topic['last_time'] = parent::formatTime($topic['last_time']);
                unset($topic['content']);
            }
        }

        return $data;
    }

    /**
     * 搜索帖子列表
     * @method searchTopicList
     * @param  [type]          $q    [description]
     * @param  [type]          $page [description]
     * @return [type]                [description]
     */
    public static function searchTopicList($q, $page)
    {
        // 返回数据格式
        $data = [
            'rows' => [],
            'q' => $q,
            'page' => $page,
            'per' => self::$per,
            'total' => 0
        ];

        $condition = ['roc_topic.valid' => 1, 'roc_topic.title %' => '%'.$q.'%'];
        $rsp = TopicModel::m()->getList(($page-1)*self::$per, self::$per, $condition, ['sortDESC', ['post_time']], true, true);
        $data['rows'] = isset($rsp['rows']) ? $rsp['rows'] : $rsp;
        $data['total'] = isset($rsp['total']) ? $rsp['total'] : TopicModel::m()->getTotal($condition);

        if (!empty($data['rows'])) {
            foreach ($data['rows'] as &$topic) {
                $topic['title'] = Roc::filter()->topicOut($topic['title']);
                $topic['imageCount'] = RelationModel::m()->getRelation($topic['tid'], 1, 'count');
                $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
                $topic['post_time'] = parent::formatTime($topic['post_time']);
                $topic['edit_time'] = parent::formatTime($topic['edit_time']);
                $topic['last_time'] = parent::formatTime($topic['last_time']);
            }
        }

        return $data;
    }

    /**
     * 获取排序类型
     * @param  [type] $sort [description]
     * @return [type]       [description]
     */
    private static function getSort($sort)
    {
        switch ($sort) {
            case 'post_time':
                return 'post_time';

            case 'last_time':
                return 'last_time';

            case 'comment_num':
                return 'comment_num';

            case 'essence':
                return 'essence';

            default:
                return 'post_time';
        }
    }
}
