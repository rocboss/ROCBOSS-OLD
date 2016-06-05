<?php

class TopicModel extends Model
{
    protected $_table = 'roc_topic';

    // 根据TID获取主题详情
    public function getByTid($tid)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $tid]));

        return $this->_db->from($this->_table)
                ->leftJoin('roc_club', ['roc_topic.cid' => 'roc_club.cid'])
                ->leftJoin('roc_user', ['roc_topic.uid' => 'roc_user.uid'])
                ->where(['roc_topic.tid' => $tid, 'roc_topic.valid' => 1])
                ->select(['roc_topic.*', 'roc_user.username', 'roc_user.groupid', 'roc_club.club_name'])
                ->one($key, parent::$_expire);
    }

    // 获取Topic列表
    public function getList($offset = 0, $limit = 12, $condition = [], $sort = ['sortDESC', 'tid'])
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition, $sort, $offset, $limit]));

        return $this->_db->from($this->_table)
                ->leftJoin('roc_club', ['roc_topic.cid' => 'roc_club.cid'])
                ->leftJoin('roc_user', ['roc_topic.uid' => 'roc_user.uid'])
                ->offset($offset)
                ->limit($limit)
                ->$sort[0]($sort[1])
                ->where($condition)
                ->select([
                    'roc_topic.tid',
                    'roc_topic.cid',
                    'roc_club.club_name',
                    'roc_topic.uid',
                    'roc_user.username',
                    'roc_topic.uid as avatar',
                    'roc_topic.title',
                    'roc_topic.tid',
                    'roc_topic.praise_num',
                    'roc_topic.collection_num',
                    'roc_topic.comment_num',
                    'roc_topic.location',
                    'roc_topic.client',
                    'roc_topic.post_time',
                    'roc_topic.edit_time',
                    'roc_topic.last_time',
                    'roc_topic.is_essence',
                    'roc_topic.is_lock',
                    'roc_topic.is_top',
                ])
                ->many($key, parent::$_expire);
    }

    // 获取Topic数量
    public function getTotal($condition=[])
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition]));

        return $this->_db->from($this->_table)
                ->where($condition)
                ->count('tid', $key, parent::$_expire);
    }

    // 获取打赏记录
    public function getRewardList($tid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$tid;

        return $this->_db->from('roc_score')
                ->leftJoin('roc_user', ['roc_score.add_user' => 'roc_user.uid'])
                ->where(['roc_score.tid' => $tid, 'roc_score.changed >' => 0, 'roc_score.valid' => 1])
                ->sortASC('id')
                ->select(['roc_score.*', 'roc_user.username'])
                ->many($key, parent::$_expire);
    }

    // 获取Topic赞列表
    public function getPraiseList($tid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$tid;

        return $this->_db->from('roc_praise')
                ->where(['tid' => $tid, 'valid' => 1])
                ->many($key, parent::$_expire);
    }

    // 检测是否已赞
    public function getPraiseDetail($tid, $uid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$tid.'_'.$uid;

        return $this->_db->from('roc_praise')
                ->where(['tid' => $tid, 'uid' => $uid, 'valid' => 1])
                ->count('id', $key, parent::$_expire);
    }

    // 获取收藏列表
    public function getCollectionList($offset = 0, $limit = 12, $uid)
    {
        return $this->_db->from('roc_topic')
                ->rightJoin('roc_collection', ['roc_topic.tid' => 'roc_collection.tid'])
                ->where(['roc_collection.uid' => $uid, 'roc_collection.valid' => 1, 'roc_topic.valid' => 1])
                ->offset($offset)
                ->limit($limit)
                ->select([
                    'roc_topic.tid',
                    'roc_topic.cid',
                    'roc_topic.uid',
                    'roc_topic.uid as avatar',
                    'roc_topic.title',
                    'roc_topic.tid',
                    'roc_topic.praise_num',
                    'roc_topic.collection_num',
                    'roc_topic.comment_num',
                    'roc_topic.location',
                    'roc_topic.client',
                    'roc_topic.post_time',
                    'roc_topic.edit_time',
                    'roc_topic.last_time',
                    'roc_topic.is_essence',
                    'roc_topic.is_lock',
                    'roc_topic.is_top',
                ])
                ->many();
    }

    // 获取收藏详情
    public function getCollectionDetail($tid, $uid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$tid.'_'.$uid;

        return $this->_db->from('roc_collection')
                ->where(['tid' => $tid, 'uid' => $uid, 'valid' => 1])
                ->count('id', $key, parent::$_expire);
    }

    // 新增点赞
    public function addPraise($tid, $uid)
    {
        parent::$redis->del(__CLASS__.':TopicModel::getPraiseDetail:'.$tid.'_'.$uid);

        $this->_db->from('roc_praise')
            ->insert(['tid' => $tid, 'uid' => $uid])
            ->execute();

        return $this->_db->insert_id;
    }

    // 新增收藏
    public function addCollection($tid, $uid)
    {
        parent::$redis->del(__CLASS__.':TopicModel::getCollectionDetail:'.$tid.'_'.$uid);

        $this->_db->from('roc_collection')
            ->insert(['tid' => $tid, 'uid' => $uid])
            ->execute();

        return $this->_db->insert_id;
    }

    // 取消收藏
    public function cancelCollection($tid, $uid)
    {
        parent::$redis->del(__CLASS__.':TopicModel::getCollectionDetail:'.$tid.'_'.$uid);

        $this->_db->from('roc_collection')
                ->where(['tid' => $tid, 'uid' => $uid, 'valid' => 1])
                ->update(['valid' => 0])
                ->execute();

        return $this->_db->affected_rows;
    }

    // 发布Topic
    public function postTopic($data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 更新Topic
    public function updateTopic($tid, $data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
                ->where(['tid' => $tid, 'valid' => 1])
                ->update($data)
                ->execute();

        return $this->_db->affected_rows;
    }

    // 更新Topic评论数量
    public function updateCommentNum($tid, $num = 1)
    {
        $this->clearCache(__CLASS__);

        return $this->_db->sql("UPDATE roc_topic SET comment_num = comment_num + ".$num." WHERE tid = ".$tid)->execute();
    }

    // 更新Topic收藏数量
    public function updateCollectionNum($tid, $num = 1)
    {
        $this->clearCache(__CLASS__);

        return $this->_db->sql("UPDATE roc_topic SET collection_num = collection_num + ".$num." WHERE tid = ".$tid)->execute();
    }

    // 更新Topic赞的数量
    public function updatePraiseNum($tid, $num = 1)
    {
        $this->clearCache(__CLASS__);

        return $this->_db->sql("UPDATE roc_topic SET praise_num = praise_num + ".$num." WHERE tid = ".$tid)->execute();
    }

    // 删除Topic
    public function deleteTopic($tid)
    {
        $this->_db->from($this->_table)
                ->where(['tid' => $tid])
                ->update(['valid' => 0])
                ->execute();

        $this->clearCache(__CLASS__);

        return $this->_db->affected_rows;
    }

    // 裁剪字符串
    public static function cutSubstr($str_cut, $length = 64)
    {
        if (mb_strlen(trim($str_cut), 'utf8') > $length)
        {
            return trim(mb_substr($str_cut, 0, $length, 'utf-8')) . '...';
        }
        else
        {
            return trim($str_cut);
        }
    }
}
