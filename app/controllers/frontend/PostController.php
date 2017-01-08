<?php
namespace frontend;

use Roc;
use UserModel;
use ScoreModel;
use TopicModel;
use ReplyModel;
use ArticleModel;
use WhisperModel;
use WithdrawModel;
use AttachmentModel;
use NotificationModel;

class PostController extends BaseController
{
    /**
     * 发表主题
     */
    public static function addTopic()
    {
        parent::csrfCheck();
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if (!UserModel::m()->checkIsBanned($uid)) {
            $data = Roc::request()->data;

            if (!empty($data['cid']) && !empty($data['title']) && !empty($data['content'])) {
                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false') {
                    $location = '';
                } else {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);
                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $content = self::doAtUser(Roc::filter()->topicInWeb($data->content));

                $tid = TopicModel::m()->postTopic([
                    'cid' => intval($data->cid),
                    'uid' => $uid,
                    'title' => Roc::filter()->topicInWeb($data->title),
                    'content' => $content['content'],
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'post_time' => time(),
                    'last_time' => time(),
                ]);

                if ($tid > 0) {
                    if (!empty($content['atUidArray'])) {
                        foreach (array_unique($content['atUidArray']) as $atUid) {
                            if ($atUid == $uid) {
                                continue;
                            }
                            NotificationModel::m()->addNotification([
                                'at_uid' => $atUid,
                                'uid' => $uid,
                                'tid' => $tid,
                                'pid' => 0,
                                'post_time' => time(),
                                'is_read' => 0
                            ]);
                        }
                    }
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', $tid);
                } else {
                    parent::json('error', '发布失败，请重试');
                }
            } else {
                parent::json('error', '请求非法');
            }
        } else {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言' : '您尚未登录');
        }
    }

    /**
     * 文章投稿
     * @method addArticle
     */
    public static function addArticle()
    {
        parent::csrfCheck();
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if (!UserModel::m()->checkIsBanned($uid)) {
            $data = Roc::request()->data;

            if (!empty($data['poster']) && !empty($data['title']) && !empty($data['content'])) {
                $retAttachment = AttachmentModel::m()->postAttachment([
                    'uid' => $uid,
                    'path' => $data['poster'],
                    'mime_type' => 'image/png',
                ]);
                $aid = ArticleModel::m()->postArticle([
                    'uid' => $uid,
                    'poster_id' => $retAttachment,
                    'title' => Roc::filter()->topicInWeb($data->title),
                    'content' => Roc::filter()->topicInWeb($data->content),
                    'post_time' => time(),
                    'is_open' => 0,
                ]);

                if ($aid > 0) {
                    UserModel::m()->updateLastTime($uid);
                    parent::json('success', $aid);
                } else {
                    parent::json('error', '发布失败，请重试');
                }
            } else {
                parent::json('error', '请求非法');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $topic = TopicModel::m()->getByTid($tid);
        if (empty($topic)) {
            parent::json('error', '主题不存在');
        }

        if (!UserModel::m()->checkIsBanned($uid) || empty($user)) {
            if ($uid != $topic['uid'] && $user['groupid'] != 99) {
                parent::json('error', '无权修改该主题');
            }
            if ($topic['post_time'] < (time() - 3600) && $user['groupid'] != 99) {
                parent::json('error', '已超过可修改时间范围');
            }

            $data = Roc::request()->data;
            if (!empty($data['cid']) && !empty($data['title']) && !empty($data['content'])) {
                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false') {
                    $location = '';
                } else {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);
                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $ret = TopicModel::m()->updateTopic($tid, [
                    'cid' => intval($data->cid),
                    'title' => Roc::filter()->topicInWeb($data->title),
                    'content' => Roc::filter()->topicInWeb($data->content),
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'edit_time' => time(),
                ]);

                if ($ret > 0) {
                    UserModel::m()->updateLastTime($uid);
                    parent::json('success', $tid);
                } else {
                    parent::json('error', '修改失败，请重试');
                }
            } else {
                parent::json('error', '请求非法');
            }
        } else {
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

        if (!UserModel::m()->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid'])) {
            $topic = TopicModel::m()->getByTid($tid);

            if (!empty($topic)) {
                if ($topic['is_lock'] == 1) {
                    parent::json('error', '主题被锁，不支持回复');
                }

                $data = Roc::request()->data;
                $client = Roc::client()->GetUserAgent();

                if ($data->ip == 'false') {
                    $location = '';
                } else {
                    $location = Roc::client()->Get_Ip_From(Roc::request()->ip);
                    $location = !empty($location) ? $location['region'].$location['city'] : (!empty($location['country']) ? $location['country'] : '');
                }

                $atReply = [];

                if ($data->at_pid > 0) {
                    $atReply = ReplyModel::m()->getReply($data->at_pid, $tid);
                }

                $content = self::doAtUser(Roc::filter()->topicInWeb($data->content));
                $pid = ReplyModel::m()->postReply([
                    'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                    'at_pid' => !empty($atReply) ? $atReply['pid'] : 0,
                    'tid' => $tid,
                    'content' => $content['content'],
                    'location' => $location,
                    'client' => substr($client[3].' '.$client[5], 0, 20),
                    'post_time' => time()
                ]);

                if ($pid > 0) {
                    // 更新相关主题
                    TopicModel::m()->updateTopic($tid, ['last_time' => time()]);
                    TopicModel::m()->updateCommentNum($tid);

                    if (!empty($atReply)) {
                        // 通知宿主
                        if ($atReply['uid'] != $topic['uid'] && $atReply['uid'] != Roc::controller('frontend\User')->getloginInfo()['uid']) {
                            NotificationModel::m()->addNotification([
                                'at_uid' => $atReply['uid'],
                                'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                                'tid' => $tid,
                                'pid' => $pid,
                                'post_time' => time()
                            ]);
                        }
                    }
                    // 通知楼主
                    if ($topic['uid'] != Roc::controller('frontend\User')->getloginInfo()['uid']) {
                        NotificationModel::m()->addNotification([
                            'at_uid' => $topic['uid'],
                            'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                            'tid' => $tid,
                            'pid' => $pid,
                            'post_time' => time()
                        ]);
                    }

                    if (!empty($content['atUidArray'])) {
                        foreach (array_unique($content['atUidArray']) as $atUid) {
                            if ($atUid == Roc::controller('frontend\User')->getloginInfo()['uid'] || (!empty($atReply) && $atReply['uid'] == $atUid) || $topic['uid'] == $atUid) {
                                continue;
                            }
                            NotificationModel::m()->addNotification([
                                'at_uid' => $atUid,
                                'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                                'tid' => $tid,
                                'pid' => $pid,
                                'post_time' => time(),
                                'is_read' => 0
                            ]);
                        }
                    }

                    UserModel::m()->updateLastTime(Roc::controller('frontend\User')->getloginInfo()['uid']);
                    parent::json('success', '发布成功');
                } else {
                    parent::json('error', '发布失败，请重试');
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
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
        if ($uid == 0) {
            parent::json('error', '您尚未登录');
        }

        switch ($type) {
            case 'notice':
                NotificationModel::m()->read($uid, Roc::request()->data->id);
                parent::json('success', Roc::request()->data->id);
                break;

            case 'whisper':
                WhisperModel::m()->read($uid, Roc::request()->data->id);
                parent::json('success', Roc::request()->data->id);
                break;

            default:
                break;
        }

        UserModel::m()->updateLastTime($uid);
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
        if ($uid > 0) {
            if (TopicModel::m()->getPraiseDetail($tid, $uid)) {
                parent::json('error', '您已经点赞了哦~');
            } else {
                $ret = TopicModel::m()->addPraise($tid, $uid);

                if ($ret > 0) {
                    TopicModel::m()->updatePraiseNum($tid);
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', '点赞成功');
                } else {
                    parent::json('error', '点赞失败，请重试');
                }
            }
        } else {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 文章点赞
     * @method doArticlePraise
     * @param  [type]          $aid [description]
     * @return [type]               [description]
     */
    public static function doArticlePraise($aid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];
        if ($uid > 0) {
            if (ArticleModel::m()->getPraiseDetail($aid, $uid)) {
                parent::json('error', '您已经点赞了哦~');
            } else {
                $ret = ArticleModel::m()->addPraise($aid, $uid);

                if ($ret > 0) {
                    ArticleModel::m()->updatePraiseNum($aid);
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', '点赞成功');
                } else {
                    parent::json('error', '点赞失败，请重试');
                }
            }
        } else {
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

        if ($uid > 0) {
            if (TopicModel::m()->getCollectionDetail($tid, $uid)) {
                $ret = TopicModel::m()->cancelCollection($tid, $uid);

                if ($ret > 0) {
                    TopicModel::m()->updateCollectionNum($tid, -1);

                    parent::json('success', '<i class="fa fa-star-o "></i> 收藏');
                } else {
                    parent::json('error', '取消收藏失败，请重试');
                }
            } else {
                $ret = TopicModel::m()->addCollection($tid, $uid);

                if ($ret > 0) {
                    TopicModel::m()->updateCollectionNum($tid);

                    parent::json('success', '<i class="fa fa-star "></i> 已收藏');
                } else {
                    parent::json('error', '收藏失败，请重试');
                }
            }

            UserModel::m()->updateLastTime($uid);
        } else {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 文章收藏
     * @method doArticleCollection
     * @param  [type]              $aid [description]
     * @return [type]                   [description]
     */
    public static function doArticleCollection($aid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($uid > 0) {
            if (ArticleModel::m()->getCollectionDetail($aid, $uid)) {
                $ret = ArticleModel::m()->cancelCollection($aid, $uid);

                if ($ret > 0) {
                    ArticleModel::m()->updateCollectionNum($aid, -1);

                    parent::json('success', '<i class="fa fa-star-o "></i> 收藏');
                } else {
                    parent::json('error', '取消收藏失败，请重试');
                }
            } else {
                $ret = ArticleModel::m()->addCollection($aid, $uid);

                if ($ret > 0) {
                    ArticleModel::m()->updateCollectionNum($aid);

                    parent::json('success', '<i class="fa fa-star "></i> 已收藏');
                } else {
                    parent::json('error', '收藏失败，请重试');
                }
            }

            UserModel::m()->updateLastTime($uid);
        } else {
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
        if ($uid > 0) {
            if ($score >= 1 && $score <= 1000) {
                $topic = TopicModel::m()->getByTid($tid);
                if (!empty($topic)) {
                    if ($topic['uid'] == $uid) {
                        parent::json('error', '抱歉，不能打赏自己的主题');
                    }

                    $userScore = UserModel::m()->getUserScore($uid);
                    if ($userScore < $score) {
                        parent::json('error', '您的积分余额（'.$userScore.'）不足以支付');
                    }

                    try {
                        $db = Roc::model()->getDb();
                        $db->beginTransaction();
                        $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$score." WHERE `uid` = ".$uid);
                        if ($ret > 0) {
                            ScoreModel::m()->addRecord([
                                'tid' => $tid,
                                'uid' => $uid,
                                'changed' => - $score,
                                'remain' => UserModel::m()->getUserScore($uid),
                                'reason' => '打赏主题',
                                'add_user' => $uid,
                                'add_time' => time(),
                            ]);

                            $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` + ".$score." WHERE `uid` = ".$topic['uid']);

                            if ($ret > 0) {
                                ScoreModel::m()->addRecord([
                                    'tid' => $tid,
                                    'uid' => $topic['uid'],
                                    'changed' => $score,
                                    'remain' => UserModel::m()->getUserScore($topic['uid']),
                                    'reason' => '主题被打赏',
                                    'add_user' => $uid,
                                    'add_time' => time(),
                                ]);
                            } else {
                                throw new \Exception("打赏失败，请重试");
                            }
                        } else {
                            throw new \Exception("您的余额不足");
                        }

                        $db->commit();
                        UserModel::m()->updateLastTime($uid);

                        parent::json('success', '打赏成功');
                    } catch (\Exception $e) {
                        $db->rollBack();
                        parent::json('error', $e->getMessage());
                    }
                } else {
                    parent::json('error', '打赏主题不存在');
                }
            } else {
                parent::json('error', '单次打赏积分范围1~1000');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $topic = TopicModel::m()->getByTid($tid);
            if (!empty($topic)) {
                if ($groupid == 99) {
                    self::__updateTopicClub($tid, Roc::request()->data->cid);
                    parent::json('success', '分类修改成功');
                } else {
                    if ($topic['uid'] == $uid && $topic['post_time'] >= time() - 3600) {
                        self::__updateTopicClub($tid, Roc::request()->data->cid);
                        parent::json('success', '分类修改成功');
                    } else {
                        parent::json('error', '无权限或已超过1小时可编辑时间');
                    }
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $topic = TopicModel::m()->getByTid($tid);
            if (!empty($topic)) {
                if ($groupid == 99) {
                    TopicModel::m()->updateTopic($tid, ['is_top' => 1 - $topic['is_top']]);

                    parent::json('success', (1 - $topic['is_top'] > 0 ? '置顶' : '取消置顶').'成功');
                } else {
                    parent::json('error', '无权限操作');
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 设置主题精华
     * @method essenceTopic
     * @param  [type]       $tid [description]
     * @return [type]            [description]
     */
    public static function essenceTopic($tid)
    {
        parent::csrfCheck();

        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $topic = TopicModel::m()->getByTid($tid);
            if (!empty($topic)) {
                if ($groupid == 99) {
                    TopicModel::m()->updateTopic($tid, ['is_essence' => 1 - $topic['is_essence']]);

                    parent::json('success', (1 - $topic['is_essence'] > 0 ? '设置精华' : '取消精华').'成功');
                } else {
                    parent::json('error', '无权限操作');
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $topic = TopicModel::m()->getByTid($tid);

            if (!empty($topic)) {
                if ($groupid == 99) {
                    TopicModel::m()->updateTopic($tid, ['is_lock' => 1 - $topic['is_lock']]);
                    parent::json('success', (1 - $topic['is_lock'] > 0 ? '锁帖' : '取消锁帖').'成功');
                } else {
                    parent::json('error', '无权限操作');
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $topic = TopicModel::m()->getByTid($tid);
            if (!empty($topic)) {
                if ($groupid == 99) {
                    TopicModel::m()->deleteTopic($tid);
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', '删除成功');
                } else {
                    if ($topic['uid'] == $uid && $topic['post_time'] >= time() - 3600) {
                        TopicModel::m()->deleteTopic($tid);

                        parent::json('success', '删除成功');
                    } else {
                        parent::json('error', '无权限或已超过1小时可删除时间');
                    }
                }
            } else {
                parent::json('error', '主题不存在');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        $groupid = !empty($user) ? $user['groupid'] : 0;
        if ($uid > 0) {
            $reply = ReplyModel::m()->getReplyByPid($pid);
            if (!empty($reply)) {
                if ($groupid == 99) {
                    $ret = ReplyModel::m()->updateReply(['pid' => $pid, 'valid' => 1], ['valid' => 0]);

                    if ($ret > 0) {
                        ReplyModel::m()->updateReply(['at_pid' => $pid, 'valid' => 1], ['at_pid' => 0]);
                        TopicModel::m()->updateCommentNum($reply['tid'], -1);
                    }

                    parent::json('success', '删除成功');
                } else {
                    if ($reply['uid'] == $uid && $reply['post_time'] >= time() - 3600) {
                        $ret = ReplyModel::m()->updateReply(['pid' => $pid, 'valid' => 1], ['valid' => 0]);

                        if ($ret > 0) {
                            ReplyModel::m()->updateReply(['at_pid' => $pid, 'valid' => 1], ['at_pid' => 0]);
                            TopicModel::m()->updateCommentNum($reply['tid'], -1);
                        }

                        parent::json('success', '删除成功');
                    } else {
                        parent::json('error', '无权限或已超过1小时可删除时间');
                    }
                }
            } else {
                parent::json('error', '回复不存在');
            }
        } else {
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
        $user = UserModel::m()->getByUid($uid);
        if (!empty($user)) {
            $ret = WhisperModel::m()->delete($uid, intval(Roc::request()->data->id));

            if ($ret > 0) {
                parent::json('success', '删除成功');
            } else {
                parent::json('error', '删除失败');
            }
        } else {
            parent::json('error', '您尚未登录');
        }
    }

    /**
     * 上传图片
     * @return [type] [description]
     */
    public static function upload()
    {
        if (!UserModel::m()->checkIsBanned(Roc::controller('frontend\User')->getloginInfo()['uid'])) {
            if (in_array($_FILES["file"]["type"], [
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/pjpeg',
                'image/bmp',
            ]) && $_FILES["file"]["size"] < 10000000) {
                if ($_FILES["file"]["error"] > 0) {
                    echo json_encode(['status' => 'error', 'data' => $_FILES['file']['error']]);
                } else {
                    $fileName = 'images/'.date('Y/m/d/').md5(Roc::controller('frontend\User')->getloginInfo()['uid'].'_'.uniqid(time(), false).rand(100, 999));

                    $ret = Roc::qiniu()->upload($_FILES["file"]["tmp_name"], $fileName);

                    if (isset($ret['key'])) {
                        echo json_encode(['status' => 'success', 'data' => AttachmentModel::m()->convertPath($ret['key'])]);

                        AttachmentModel::m()->postAttachment([
                            'uid' => Roc::controller('frontend\User')->getloginInfo()['uid'],
                            'path' => $ret['key'],
                            'mime_type' => $_FILES["file"]["type"],
                            'type' => 1
                        ]);
                    } else {
                        echo json_encode(['status' => 'error', 'data' => '上传失败，请重试']);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'data' => '文件非法或过大']);
            }
        } else {
            parent::json('error', Roc::controller('frontend\User')->getloginInfo()['uid'] > 0 ? '您已被禁言' : '您尚未登录');
        }
    }

    /**
     * 提现
     * @method withdraw
     * @return [type]   [description]
     */
    public static function withdraw()
    {
        parent::csrfCheck();

        $data = Roc::request()->data;
        $num = intval($data->num);
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($num >= 1000 && $num <= 10000) {
            $user = UserModel::m()->getByUid($uid);
            if (!empty($user) && $user['score'] >= $num) {
                try {
                    $db = Roc::model()->getDb();
                    $db->beginTransaction();
                    $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$num." WHERE `uid` = ".$uid);
                    if ($ret > 0) {
                        $ret = WithdrawModel::m()->add([
                            'uid' => $uid,
                            'pay_account' => $user['phone'],
                            'score' => $num,
                            'should_pay' => sprintf("%.2f", ($num -200)/100),
                            'add_time' => time(),
                        ]);
                        if ($ret == 0) {
                            throw new \Exception("提现申请失败，请重试");
                        } else {
                            ScoreModel::m()->addRecord([
                                'tid' => 0,
                                'uid' => $uid,
                                'changed' => - $num,
                                'remain' => UserModel::m()->getUserScore($uid),
                                'reason' => '申请提现【编号 '.$ret.'】，待审核中...',
                                'add_user' => $uid,
                                'add_time' => time(),
                            ]);
                        }
                    } else {
                        throw new \Exception("您的余额不足");
                    }
                    $db->commit();
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', '提现申请成功，等待审核');
                } catch (Exception $e) {
                    $db->rollBack();
                    parent::json('error', $e->getMessage());
                }
            } else {
                parent::json('error', '积分余额不足，当前余额'.$user['score']);
            }
        } else {
            parent::json('error', '请求非法');
        }
    }

    /**
     * 积分转账
     * @method transfer
     * @return [type]   [description]
     */
    public static function transfer()
    {
        parent::csrfCheck();

        $data = Roc::request()->data;
        $score = intval($data->score);
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        if ($score >= 1 && $score <= 1000) {
            // 转账者
            $user = UserModel::m()->getByUid($uid);
            $aimUser = UserModel::m()->getByUid($data->uid);
            if (!empty($user) && !empty($aimUser) && $user['score'] >= $score) {
                try {
                    $db = Roc::model()->getDb();
                    $db->beginTransaction();
                    // 扣积分
                    $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` - ".$score." WHERE `uid` = ".$uid);
                    if ($ret > 0) {
                        // 目标增积分
                        $ret = $db->exec("UPDATE `roc_user` SET `score` = `score` + ".$score." WHERE `uid` = ".$aimUser['uid']);
                        if ($ret > 0) {
                            // 写记录
                            $ret1 = ScoreModel::m()->addRecord([
                                'tid' => 0,
                                'uid' => $uid,
                                'changed' => - $score,
                                'remain' => UserModel::m()->getUserScore($uid),
                                'reason' => '转账给'.$aimUser['username'],
                                'add_user' => $uid,
                                'add_time' => time(),
                            ]);
                            $ret2 = ScoreModel::m()->addRecord([
                                'tid' => 0,
                                'uid' => $aimUser['uid'],
                                'changed' => $score,
                                'remain' => UserModel::m()->getUserScore($aimUser['uid']),
                                'reason' => '获得'.$user['username'].'的转账',
                                'add_user' => $uid,
                                'add_time' => time(),
                            ]);
                            if ($ret1 > 0 && $ret2 > 0) {
                                // 发送通知
                                WhisperModel::m()->addWhisper([
                                    'at_uid' => $aimUser['uid'],
                                    'uid' => $uid,
                                    'content' => '我向你转了一笔积分哦，快到积分详情页查看吧~（本消息由系统后台自动发送）',
                                    'post_time' => time()
                                ]);
                            } else {
                                throw new \Exception("转账失败");
                            }
                        } else {
                            throw new \Exception("转账失败");
                        }
                    } else {
                        throw new \Exception("您的积分余额不足");
                    }
                    $db->commit();
                    UserModel::m()->updateLastTime($uid);

                    parent::json('success', '积分转账成功');
                } catch (Exception $e) {
                    $db->rollBack();
                    parent::json('error', $e->getMessage());
                }
            } else {
                parent::json('error', '积分余额不足或对方不存在，当前余额'.$user['score']);
            }
        } else {
            parent::json('error', '请求非法');
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
        return TopicModel::m()->updateTopic($tid, ['cid' => $cid]);
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

        if (isset($nameArray[1])) {
            $writeName = [];
            foreach ($nameArray[1] as $name) {
                if (in_array(strtolower($name), $writeName)) {
                    continue;
                }

                array_push($writeName, strtolower($name));
                $userInfo = UserModel::m()->getByUsername($name);
                if (empty($userInfo['username'])) {
                    $content = str_ireplace('@' . $name . ' ', '@' . $name . ' ', $content . ' ');
                } else {
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
