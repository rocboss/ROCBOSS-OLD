<?php

class ReplyModel extends Model
{
    protected $_table = 'roc_reply';

    // 根据PID和TID获取回复
    public function getReply($pid, $tid)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_reply.uid' => 'roc_user.uid'])
                ->where(['roc_reply.tid' => $tid, 'roc_reply.pid' => $pid, 'roc_reply.valid' => 1])
                ->select(['roc_reply.*', 'roc_user.username'])
                ->one();
    }

    // 根据PID获取回复
    public function getReplyByPid($pid)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_reply.uid' => 'roc_user.uid'])
                ->where(['roc_reply.pid' => $pid, 'roc_reply.valid' => 1])
                ->select(['roc_reply.*', 'roc_user.username'])
                ->one();
    }

    // 根据TID获取回复列表
    public function getListByTid($tid, $result = 'many')
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_reply.uid' => 'roc_user.uid'])
                ->where(['roc_reply.tid' => $tid, 'roc_reply.valid' => 1])
                ->select(['roc_reply.*', 'roc_user.username', 'roc_user.groupid'])
                ->$result();
    }

    // 根据UID获取回复列表
    public function getListByUid($offset = 0, $limit = 12, $uid)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_topic', ['roc_reply.tid' => 'roc_topic.tid'])
                ->leftJoin('roc_user', ['roc_reply.uid' => 'roc_user.uid'])
                ->where(['roc_reply.uid' => $uid, 'roc_topic.valid' => 1, 'roc_reply.valid' => 1])
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('pid')
                ->select(['roc_reply.*', 'roc_topic.title as topic_title', 'roc_user.username', 'roc_user.groupid'])
                ->many();
    }

    // 获取所有回复列表
    public function getList($offset = 0, $limit = 12)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_topic', ['roc_reply.tid' => 'roc_topic.tid'])
                ->leftJoin('roc_user', ['roc_reply.uid' => 'roc_user.uid'])
                ->where(['roc_topic.valid' => 1, 'roc_reply.valid' => 1])
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('pid')
                ->select(['roc_reply.*', 'roc_topic.title as topic_title', 'roc_user.username', 'roc_user.groupid'])
                ->many();
    }

    // 获取Reply数量
    public function getTotal($condition=[], $force = true)
    {
        if ($force) {
            return $this->_db->from($this->_table)
                    ->where($condition)
                    ->count('pid');
        }
        return $this->_db->from($this->_table)
                ->leftJoin('roc_topic', ['roc_reply.tid' => 'roc_topic.tid'])
                ->where($condition)
                ->count('roc_reply.pid');
    }

    // 发布回复
    public function postReply($data)
    {
        $this->clearCache(__CLASS__);

        $this->clearCache('NotificationUnreadNum');

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 更新Reply
    public function updateReply(array $condition, $data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
                ->where($condition)
                ->update($data)
                ->execute();

        return $this->_db->affected_rows;
    }
}
