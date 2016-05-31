<?php

namespace frontend;

use \Controller;
use \Roc;

class IndexController extends BaseController
{
    public static $per = 20;

    /**
     * 首页
     * @param  [type] $cid  [description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function index($cid, $page)
    {
        $cid = parent::getNumVal($cid, 0, true);

        $page = parent::getNumVal($page, 1, true);

        $sort = self::getSort(Roc::request()->cookies->topic_sort);

        $data = self::getTopicList($cid, $page, $sort);

        parent::renderBase(['active' => 'index-'.$cid]);

        Roc::render('index', [
            'data' => $data,
            'clubs' => Roc::model('club')->getList(),
            'statistic' => [
                'user' => Roc::model('user')->getTotal(['valid' => 1]),
                'topic' => Roc::model('topic')->getTotal(['valid' => 1]),
                'article' => Roc::model('article')->getTotal(['valid' => 1])
            ],
            'links' => Roc::model('link')->getList()
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
        $page = $query->page > 1 ? $query->page : 1;
        $error = '';
        $data = [
            'rows' => [],
            'q' => $query->q,
            'page' => $page,
            'per' => self::$per,
            'total' => 0
        ];

        if (strlen($query->q) >= 2) {
            $data = self::searchTopicList($query->q, $page);
        } else {
            $error = '搜索关键字太短，要不少于两个字符哦~';
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

        $topic = Roc::model('topic')->getByTid($tid);

        if (!empty($topic))
        {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);

            $topic['content'] = Roc::filter()->topicOut($topic['content']);

            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);

            $topic['add_time'] = $topic['post_time'];

            $topic['post_time'] = parent::formatTime($topic['post_time']);

            $topic['edit_time'] = parent::formatTime($topic['edit_time']);

            $topic['last_time'] = parent::formatTime($topic['last_time']);

            $relation = array_map([__CLASS__, 'getAttachmentID'], Roc::model('relation')->getRelation($topic['tid'], 1));

            // 主题所带图片(APP单独附图)
            $topic['images'] = [
                'rows' => Roc::model('attachment')->getAttachments($relation),
                'count' => Roc::model('relation')->getRelation($topic['tid'], 1, 'count')
            ];

            // 主题下赞
            $topic['praise'] = [
                'rows' => Roc::model('topic')->getPraiseList($topic['tid']),
                'hasPraise' => Roc::model('topic')->getPraiseDetail($topic['tid'], $uid) > 0 ? true : false
            ];

            // 主题下打赏
            $topic['reward'] = [
                'rows' => Roc::model('topic')->getRewardList($topic['tid']),
            ];

            // 主题是否收藏
            $topic['hasCollection'] = Roc::model('topic')->getCollectionDetail($topic['tid'], $uid) > 0 ? true : false;

            // 主题下回复
            $topic['reply'] = [
                'rows' => Roc::model('reply')->getListByTid($topic['tid']),
                'count' => Roc::model('reply')->getListByTid($topic['tid'], 'count')
            ];

            if (!empty($topic['reward']['rows']))
            {
                foreach ($topic['reward']['rows'] as &$reward)
                {
                    $reward['add_time'] = parent::formatTime($reward['add_time']);
                }
            }

            if (!empty($topic['reply']['rows']))

            foreach ($topic['reply']['rows'] as &$reply)
            {
                $relation = array_map([__CLASS__, 'getAttachmentID'], Roc::model('relation')->getRelation($reply['pid'], 2));

                $reply['content'] = Roc::filter()->topicOut($reply['content']);

                if ($reply['at_pid'] > 0)
                {
                    $reply['at_reply'] = Roc::model('reply')->getReply($reply['at_pid'], $reply['tid']);

                    if (!empty($reply['at_reply']))
                    {
                        $reply['at_reply']['content'] = self::cutSubstr(Roc::filter()->topicOut($reply['at_reply']['content']));

                        $reply['at_reply']['post_time'] = parent::formatTime($reply['at_reply']['post_time']);
                    }
                }

                $reply['images'] = [
                    'rows' => Roc::model('attachment')->getAttachments($relation),
                    'count' => Roc::model('relation')->getRelation($reply['pid'], 2, 'count')
                ];

                $reply['avatar'] = Roc::controller('frontend\User')->getAvatar($reply['uid']);

                $reply['add_time'] = $reply['post_time'];

                $reply['post_time'] = parent::formatTime($reply['post_time']);
            }

            if (!empty($topic['praise']['rows']))

            foreach ($topic['praise']['rows'] as &$praise)
            {
                $praise['avatar'] = Roc::controller('frontend\User')->getAvatar($praise['uid']);
            }

            parent::renderBase(['active' => 'read', 'pageTitle' => (!empty($topic) ? $topic['title'] : '')]);

            Roc::render('read', [
                'data' => $topic,
                'clubs' => Roc::model('club')->getList(),
            ]);
        }
        else
        {
            Roc::redirect('/');
        }
    }

    /**
     * 发布新帖
     * @return [type] [description]
     */
    public static function newTopic()
    {
        if (!Roc::model('User')->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid']))
        {
            parent::renderBase(['active' => 'newTopic']);

            Roc::render('new_topic', [
                'clubs' => Roc::model('club')->getList()
            ]);
        }
        else
        {
            Roc::redirect('/login');
        }
    }

    public static function editTopic($tid)
    {
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if (!Roc::model('User')->checkIsBanned($uid))
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                parent::renderBase(['active' => 'editTopic']);

                Roc::render('edit_topic', [
                    'topic' => $topic,
                    'clubs' => Roc::model('club')->getList()
                ]);
            }
            else
            {
                Roc::redirect('/');
            }
        }
        else
        {
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

        if (!in_array($sort, ['tid', 'last_time', 'essence']))
        {
            $sort = 'tid';
        }

        setcookie('topic_sort', $sort, time() + 86400, '/', NULL, Roc::request()->secure, true);

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

        if (mb_strlen(trim($str_cut), 'utf8') > $length)
        {
            return trim(mb_substr($str_cut, 0, $length, 'utf-8')) . '...';
        }
        else
        {
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

        if ($sort == 'essence')
        {
            $condition = array_merge($condition, ['roc_topic.is_essence' => 1]);

            $sort = 'tid';
        }

        $data['rows'] = Roc::model('topic')->getList(($page-1)*self::$per, self::$per, $condition, ['sortDESC', ['is_top', $sort]]);

        if (!empty($data['rows']))

        foreach ($data['rows'] as &$topic)
        {
            $topic['title'] = Roc::filter()->topicOut($topic['title']);

            $topic['imageCount'] = Roc::model('relation')->getRelation($topic['tid'], 1, 'count');

            $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);

            $topic['post_time'] = parent::formatTime($topic['post_time']);

            $topic['edit_time'] = parent::formatTime($topic['edit_time']);

            $topic['last_time'] = parent::formatTime($topic['last_time']);
        }

        $data['total'] = Roc::model('topic')->getTotal($condition);

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
        $data['rows'] = Roc::model('topic')->getList(($page-1)*self::$per, self::$per, $condition, ['sortDESC', ['tid']]);

        if (!empty($data['rows']))
            foreach ($data['rows'] as &$topic) {
                $topic['title'] = Roc::filter()->topicOut($topic['title']);
                $topic['imageCount'] = Roc::model('relation')->getRelation($topic['tid'], 1, 'count');
                $topic['avatar'] = Roc::controller('frontend\User')->getAvatar($topic['uid']);
                $topic['post_time'] = parent::formatTime($topic['post_time']);
                $topic['edit_time'] = parent::formatTime($topic['edit_time']);
                $topic['last_time'] = parent::formatTime($topic['last_time']);
            }

        $data['total'] = Roc::model('topic')->getTotal($condition);

        return $data;
    }

    /**
     * ROCBOSS源码下载
     * @method download
     * @return [type]   [description]
     */
    public static function download()
    {
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($uid > 0)
        {
            $user = Roc::model('user')->getByUid($uid);

            if (empty($user))
            {
                die('<script>alert(\'Access Denied. Only For VIP3\');</script>');
            }

            $groupid = $user['groupid'];

            if ($groupid >= 2)
            {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=rocboss_v2.2.0_u_".$uid.".zip");
                readfile('https://dn-rocboss.qbox.me/rocboss.zxc.zip?'.time());
            }
            else
            {
                die('<script>alert(\'Access Denied. Only For VIP3\');</script>');
            }
        }
        else
        {
            die('<script>alert(\'Access Denied. Only For VIP3\');</script>');
        }
    }

    /**
     * ROCBOSS OLD数据迁移同步
     * @method actionSynchronize
     * @return [type]            [description]
     */
    public static function actionSynchronize()
    {
        // 临时关闭
        exit;
        $url = 'https://dn-roc.qbox.me/';

        $attachments = Roc::db()->from('roc_attachment')->where(['id <= ' => 995])->select(['id', 'path'])->many();

        if (!empty($attachments)) {
            foreach ($attachments as $key => $attachment) {
                Roc::db()->from('roc_topic')->where(['tid >' => 0])->update("content = replace(content, '[:".$attachment['id']."]', '<img src=\"".$url.str_replace('app/uploads/pictures/', '', $attachment['path'])."-800\"/><br />')")->execute();
                Roc::db()->from('roc_reply')->where(['pid >' => 0])->update("content = replace(content, '[:".$attachment['id']."]', '<img src=\"".$url.str_replace('app/uploads/pictures/', '', $attachment['path'])."-800\"/><br />')")->execute();
            }
        }
    }

    /**
     * 获取排序类型
     * @param  [type] $sort [description]
     * @return [type]       [description]
     */
    private static function getSort($sort)
    {
        switch ($sort)
        {
            case 'tid':
                return 'tid';

            case 'last_time':
                return 'last_time';

            case 'essence':
                return 'essence';

            default:
                return 'tid';
        }
    }
}
