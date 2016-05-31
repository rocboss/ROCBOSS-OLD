<?php

namespace frontend;

use \Controller;
use \Roc;

class PostController extends BaseController
{
    /**
     * 发表主题
     */
    public static function addTopic()
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if (!Roc::model('User')->checkIsBanned($uid))
        {
            $data = Roc::request()->data;

            if (!empty($data['cid']) && !empty($data['title']) && !empty($data['content']))
            {
                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false')
                {
                    $location = '';
                }
                else
                {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);

                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $content = self::doAtUser(Roc::filter()->topicInWeb($data->content));

                $tid = Roc::model('topic')->postTopic([
                    'cid' => intval($data->cid),
                    'uid' => $uid,
                    'title' => Roc::filter()->topicInWeb($data->title),
                    'content' => $content['content'],
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'post_time' => time(),
                    'last_time' => time(),
                ]);

                if ($tid > 0)
                {
                    if (!empty($content['atUidArray']))

                    foreach (array_unique($content['atUidArray']) as $atUid)
                    {
                        if ($atUid == $uid)
                        {
                            continue;
                        }

                        Roc::model('notification')->addNotification([
                            'at_uid' => $atUid,
                            'uid' => $uid,
                            'tid' => $tid,
                            'pid' => 0,
                            'post_time' => time(),
                            'is_read' => 0
                        ]);
                    }
                    Roc::model('user')->updateLastTime($uid);

                    parent::json('success', $tid);
                }
                else
                {
                    parent::json('error', '发布失败，请重试');
                }
            }
            else
            {
                parent::json('error', '请求非法');
            }
        }
        else
        {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言' : '您尚未登录');
        }
    }

    /**
     * 编辑主题
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function editTopic($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $topic = Roc::model('topic')->getByTid($tid);

        if (empty($topic))
        {
            parent::json('error', '主题不存在');
        }

        if (!Roc::model('User')->checkIsBanned($uid) || empty($user))
        {
            if ($uid != $topic['uid'] && $user['groupid'] != 99)
            {
                parent::json('error', '无权修改该主题');
            }

            if ($topic['post_time'] < (time() - 3600) && $user['groupid'] != 99)
            {
                parent::json('error', '已超过可修改时间范围');
            }

            $data = Roc::request()->data;

            if (!empty($data['cid']) && !empty($data['title']) && !empty($data['content']))
            {
                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false')
                {
                    $location = '';
                }
                else
                {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);

                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $ret = Roc::model('topic')->updateTopic($tid, [
                    'cid' => intval($data->cid),
                    'title' => Roc::filter()->topicInWeb($data->title),
                    'content' => Roc::filter()->topicInWeb($data->content),
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'edit_time' => time(),
                ]);

                if ($ret > 0)
                {
                    Roc::model('user')->updateLastTime($uid);

                    parent::json('success', $tid);
                }
                else
                {
                    parent::json('error', '修改失败，请重试');
                }
            }
            else
            {
                parent::json('error', '请求非法');
            }
        }
        else
        {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言或无权操作' : '您尚未登录');
        }
    }

    /**
     * 发表回复
     * @param [type] $tid [description]
     */
    public static function addReply($tid)
    {
        parent::csrfCheck();

        if (!Roc::model('User')->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid']))
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                if ($topic['is_lock'] == 1)
                {
                    parent::json('error', '主题被锁，不支持回复');
                }

                $data = Roc::request()->data;

                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false')
                {
                    $location = '';
                }
                else
                {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);

                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $atReply = [];

                if ($data->at_pid > 0)
                {
                    $atReply = Roc::model('reply')->getReply($data->at_pid, $tid);
                }

                $content = self::doAtUser(Roc::filter()->topicInWeb($data->content));

                $pid = Roc::model('reply')->postReply([
                    'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                    'at_pid' => !empty($atReply) ? $atReply['pid'] : 0,
                    'tid' => $tid,
                    'content' => $content['content'],
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'post_time' => time()
                ]);

                if ($pid > 0)
                {
                    // 更新相关主题
                    Roc::model('topic')->updateTopic($tid, ['last_time' => time()]);

                    Roc::model('topic')->updateCommentNum($tid);

                    if (!empty($atReply))
                    {
                        // 通知宿主
                        if ($atReply['uid'] != $topic['uid'] && $atReply['uid'] != Roc::controller('frontend\User')->getloginInfo()['uid'])
                        {
                            Roc::model('notification')->addNotification([
                                'at_uid' => $atReply['uid'],
                                'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                                'tid' => $tid,
                                'pid' => $pid,
                                'post_time' => time()
                            ]);
                        }
                    }

                    // 通知楼主
                    if ($topic['uid'] != Roc::controller('frontend\User')->getloginInfo()['uid'])
                    {
                        Roc::model('notification')->addNotification([
                            'at_uid' => $topic['uid'],
                            'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                            'tid' => $tid,
                            'pid' => $pid,
                            'post_time' => time()
                        ]);
                    }

                    if (!empty($content['atUidArray']))

                    foreach (array_unique($content['atUidArray']) as $atUid)
                    {
                        if ($atUid == Roc::controller('frontend\User')->getloginInfo()['uid'] || (!empty($atReply) && $atReply['uid'] == $atUid) || $topic['uid'] == $atUid)
                        {
                            continue;
                        }

                        Roc::model('notification')->addNotification([
                            'at_uid' => $atUid,
                            'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                            'tid' => $tid,
                            'pid' => $pid,
                            'post_time' => time(),
                            'is_read' => 0
                        ]);
                    }

                    Roc::model('user')->updateLastTime(Roc::controller('frontend\User')->getloginInfo()['uid']);

                    parent::json('success', '发布成功');
                }
                else
                {
                    parent::json('error', '发布失败，请重试');
                }
            }
            else
            {
                parent::json('error', '主题不存在');
            }
        }
        else
        {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言' : '您尚未登录');
        }
    }

    /**
     * 标记消息已读
     * @method doRead
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function doRead($type)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($uid == 0)
        {
            parent::json('error', '您尚未登录');
        }

        switch ($type)
        {
            case 'notification':

                Roc::model('notification')->read($uid, Roc::request()->data->id);

                parent::json('success',  Roc::request()->data->id);

                break;

            case 'whisper':
                Roc::model('whisper')->read($uid, Roc::request()->data->id);

                parent::json('success',  Roc::request()->data->id);

                break;

            default:
                break;
        }

        Roc::model('user')->updateLastTime($uid);
    }

    /**
     * 点赞
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function doPraise($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($uid > 0)
        {
            if (Roc::model('topic')->getPraiseDetail($tid, $uid))
            {
                parent::json('error', '您已经点赞了哦~');
            }
            else
            {
                $ret = Roc::model('topic')->addPraise($tid, $uid);

                if ($ret > 0)
                {
                    Roc::model('topic')->updatePraiseNum($tid);

                    Roc::model('user')->updateLastTime($uid);

                    parent::json('success', '点赞成功');
                }
                else
                {
                     parent::json('error', '点赞失败，请重试');
                }
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 收藏
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function doCollection($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($uid > 0)
        {
            if (Roc::model('topic')->getCollectionDetail($tid, $uid))
            {
                $ret = Roc::model('topic')->cancelCollection($tid, $uid);

                if ($ret > 0)
                {
                    Roc::model('topic')->updateCollectionNum($tid, -1);

                    parent::json('success', '<i class="fa fa-star-o "></i> 收藏');
                }
                else
                {
                     parent::json('error', '取消收藏失败，请重试');
                }
            }
            else
            {
                $ret = Roc::model('topic')->addCollection($tid, $uid);

                if ($ret > 0)
                {
                    Roc::model('topic')->updateCollectionNum($tid);

                    parent::json('success', '<i class="fa fa-star "></i> 已收藏');
                }
                else
                {
                     parent::json('error', '收藏失败，请重试');
                }
            }

            Roc::model('user')->updateLastTime($uid);
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 打赏主题
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function doReward($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $score = Roc::request()->data->score;

        if ($uid > 0)
        {
            if ($score >= 1 && $score <= 1000)
            {
                $topic = Roc::model('topic')->getByTid($tid);

                if (!empty($topic))
                {
                    if ($topic['uid'] == $uid)
                    {
                        parent::json('error', '抱歉，不能打赏自己的主题');
                    }

                    $userScore = Roc::model('user')->getUserScore($uid);

                    if ($userScore < $score)
                    {
                        parent::json('error', '您的积分余额（'.$userScore.'）不足以支付');
                    }

                    try
                    {
                        $db = Roc::model()->getDb();

                        $db->beginTransaction();

                        $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$score." WHERE `uid` = ".$uid);

                        if ($ret > 0)
                        {
                            Roc::model('score')->addRecord([
                                'tid' => $tid,
                                'uid' => $uid,
                                'changed' => - $score,
                                'remain' => Roc::model('user')->getUserScore($uid),
                                'reason' => '打赏主题',
                                'add_user' => $uid,
                                'add_time' => time(),
                            ]);

                            $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` + ".$score." WHERE `uid` = ".$topic['uid']);

                            if ($ret > 0)
                            {
                                Roc::model('score')->addRecord([
                                    'tid' => $tid,
                                    'uid' => $topic['uid'],
                                    'changed' => $score,
                                    'remain' => Roc::model('user')->getUserScore($topic['uid']),
                                    'reason' => '主题被打赏',
                                    'add_user' => $uid,
                                    'add_time' => time(),
                                ]);
                            }
                            else
                            {
                                throw new \Exception("打赏失败，请重试");
                            }
                        }
                        else
                        {
                            throw new \Exception("您的余额不足");
                        }

                        $db->commit();

                        Roc::model('user')->updateLastTime($uid);

                        parent::json('success', '打赏成功');
                    }
                    catch (\Exception $e)
                    {
                        $db->rollBack();

                        parent::json('error', $e->getMessage());
                    }
                }
                else
                {
                    parent::json('error', '打赏主题不存在');
                }
            }
            else
            {
                parent::json('error', '单次打赏积分范围1~1000');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 更改主题分类
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function changeClub($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $groupid = !empty($user) ? $user['groupid'] : 0;

        if ($uid > 0)
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                if ($groupid == 99)
                {
                    self::__updateTopicClub($tid, Roc::request()->data->cid);

                    parent::json('success', '分类修改成功');
                }
                else
                {
                    if ($topic['uid'] == $uid && $topic['post_time'] >= time() - 3600)
                    {
                        self::__updateTopicClub($tid, Roc::request()->data->cid);

                        parent::json('success', '分类修改成功');
                    }
                    else
                    {
                        parent::json('error', '无权限或已超过1小时可编辑时间');
                    }
                }
            }
            else
            {
                parent::json('error', '主题不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 置顶主题
     * @method topTopic
     * @param  [type]   $tid [description]
     * @return [type]        [description]
     */
    public static function topTopic($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $groupid = !empty($user) ? $user['groupid'] : 0;

        if ($uid > 0)
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                if ($groupid == 99)
                {
                    Roc::model('topic')->updateTopic($tid, ['is_top' => 1 - $topic['is_top']]);

                    parent::json('success', (1 - $topic['is_top'] > 0 ? '置顶' : '取消置顶').'成功');
                }
                else
                {
                    parent::json('error', '无权限操作');
                }
            }
            else
            {
                parent::json('error', '主题不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 主题锁帖
     * @method lockTopic
     * @param  [type]    $tid [description]
     * @return [type]         [description]
     */
    public static function lockTopic($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $groupid = !empty($user) ? $user['groupid'] : 0;

        if ($uid > 0)
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                if ($groupid == 99)
                {
                    Roc::model('topic')->updateTopic($tid, ['is_lock' => 1 - $topic['is_lock']]);

                    parent::json('success', (1 - $topic['is_lock'] > 0 ? '锁帖' : '取消锁帖').'成功');
                }
                else
                {
                    parent::json('error', '无权限操作');
                }
            }
            else
            {
                parent::json('error', '主题不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 删除话题
     * @param  [type] $tid [description]
     * @return [type]      [description]
     */
    public static function deleteTopic($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $groupid = !empty($user) ? $user['groupid'] : 0;

        if ($uid > 0)
        {
            $topic = Roc::model('topic')->getByTid($tid);

            if (!empty($topic))
            {
                if ($groupid == 99)
                {
                    Roc::model('topic')->updateTopic($tid, ['valid' => 0]);

                    Roc::model('user')->updateLastTime($uid);

                    parent::json('success', '删除成功');
                }
                else
                {
                    if ($topic['uid'] == $uid && $topic['post_time'] >= time() - 3600)
                    {
                        Roc::model('topic')->updateTopic($tid, ['valid' => 0]);

                        parent::json('success', '删除成功');
                    }
                    else
                    {
                        parent::json('error', '无权限或已超过1小时可删除时间');
                    }
                }
            }
            else
            {
                parent::json('error', '主题不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 删除评论
     * @param  [type] $pid [description]
     * @return [type]      [description]
     */
    public static function deleteReply($pid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        $groupid = !empty($user) ? $user['groupid'] : 0;

        if ($uid > 0)
        {
            $reply = Roc::model('reply')->getReplyByPid($pid);

            if (!empty($reply))
            {
                if ($groupid == 99)
                {
                    $ret = Roc::model('reply')->updateReply(['pid' => $pid, 'valid' => 1], ['valid' => 0]);

                    if ($ret > 0)
                    {
                        Roc::model('reply')->updateReply(['at_pid' => $pid, 'valid' => 1], ['at_pid' => 0]);

                        Roc::model('topic')->updateCommentNum($reply['tid'], -1);
                    }

                    parent::json('success', '删除成功');
                }
                else
                {
                    if ($reply['uid'] == $uid && $reply['post_time'] >= time() - 3600)
                    {
                        $ret = Roc::model('reply')->updateReply(['pid' => $pid, 'valid' => 1], ['valid' => 0]);

                        if ($ret > 0)
                        {
                            Roc::model('reply')->updateReply(['at_pid' => $pid, 'valid' => 1], ['at_pid' => 0]);

                            Roc::model('topic')->updateCommentNum($reply['tid'], -1);
                        }

                        parent::json('success', '删除成功');
                    }
                    else
                    {
                        parent::json('error', '无权限或已超过1小时可删除时间');
                    }
                }
            }
            else
            {
                parent::json('error', '回复不存在');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 删除私信
     * @return [type] [description]
     */
    public static function deleteWhisper()
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $user = Roc::model('user')->getByUid($uid);

        if (!empty($user))
        {
            $ret = Roc::model('whisper')->delete($uid, intval(Roc::request()->data->id));

            if ($ret > 0)
            {
                parent::json('success', '删除成功');
            }
            else
            {
                parent::json('error', '删除失败');
            }
        }
        else
        {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 上传图片
     * @return [type] [description]
     */
    public static function upload()
    {
        if (!Roc::model('User')->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid']))
        {
            if (in_array($_FILES["file"]["type"], [
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/pjpeg',
                'image/bmp',
            ]) && $_FILES["file"]["size"] < 10000000)
            {
                if ($_FILES["file"]["error"] > 0)
                {
                    echo json_encode(['status' => 'error', 'data' => $_FILES['file']['error']]);
                }
                else
                {
                    $fileName = 'images/'.date('Y/m/d/').md5(Roc::controller('frontend\User')->getloginInfo()['uid'].'_'.uniqid(time(), false).rand(100, 999));

                    $ret = Roc::qiniu()->upload($_FILES["file"]["tmp_name"], $fileName);

                    if (isset($ret['key']))
                    {
                        echo json_encode(['status' => 'success', 'data' => Roc::model('attachment')->convertPath($ret['key'])]);

                        Roc::model('attachment')->postAttachment([
                            'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                            'path' => $ret['key'],
                            'mime_type' => $_FILES["file"]["type"],
                            'type' => 1
                        ]);
                    }
                    else
                    {
                        echo json_encode(['status' => 'error', 'data' => '上传失败，请重试']);
                    }
                }
            }
            else
            {
                echo json_encode(['status' => 'error', 'data' => '文件非法或过大']);
            }
        }
        else
        {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言' : '您尚未登录');
        }
    }

    /**
     * 更改分类
     * @param  [type] $tid [description]
     * @param  [type] $cid [description]
     * @return [type]      [description]
     */
    private static function __updateTopicClub($tid, $cid)
    {
        return Roc::model('topic')->updateTopic($tid, ['cid' => $cid]);
    }

    /**
     * @用户
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private static function doAtUser($content)
    {
        $atUidArray = [];

        preg_match_all("@\@(.*?)([\s]+)@is", $content . " ", $nameArray);

        if (isset($nameArray[1]))
        {
            $writeName = [];

            foreach ($nameArray[1] as $name)
            {
                if (in_array(strtolower($name), $writeName))
                {
                    continue;
                }

                array_push($writeName, strtolower($name));

                $userInfo = Roc::model('user')->getByUsername($name);

                if (empty($userInfo['username']))
                {
                    $content = str_ireplace('@' . $name . ' ', '@' . $name . ' ', $content . ' ');
                }
                else
                {
                    $content = str_ireplace('@' . $name . ' ', '@' . $userInfo['username'] . ' ', $content . ' ');

                    array_push($atUidArray, $userInfo['uid']);
                }
            }
        }

        return [
            'content' => $content,
            'atUidArray' => $atUidArray
        ];
    }
}
