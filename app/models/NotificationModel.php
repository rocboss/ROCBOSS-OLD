<?php

class NotificationModel extends Model
{
    protected $_table = 'roc_notification';

    // 新增提醒
    public function addNotification($data)
    {
        $this->clearCache(__CLASS__);

        $this->clearCache('NotificationUnreadNum');

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 获取阅读提醒
    public function getRead($at_uid, $offset = 0, $limit = 12)
    {
        $orm = $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_notification.uid' => 'roc_user.uid'])
                ->where(['roc_notification.at_uid' => $at_uid, 'roc_notification.is_read' => 1, 'roc_notification.valid' => 1]);

        $rows = $orm->offset($offset)
                ->limit($limit)
                ->sortDESC('roc_notification.id')
                ->select(['roc_notification.*', 'roc_user.username'])
                ->many();
        $total = $orm->count();

        return [
            'rows' => $rows,
            'offset' => $offset,
            'limit' => $limit,
            'total' => $total
        ];
    }

    // 获取未读提醒
    public function getUnread($at_uid)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_notification.uid' => 'roc_user.uid'])
                ->leftJoin('roc_topic', ['roc_notification.tid' => 'roc_topic.tid'])
                ->where(['roc_notification.at_uid' => $at_uid, 'roc_notification.is_read' => 0, 'roc_notification.valid' => 1])
                ->sortDESC('roc_notification.id')
                ->select(['roc_notification.*', 'roc_user.username', 'roc_topic.title'])
                ->many();
    }

    // 获取未读提醒数量
    public function getUnreadTotal($at_uid)
    {
        $key = 'NotificationUnreadNum:'.$at_uid;

        return $this->_db->from($this->_table)
                ->where(['roc_notification.at_uid' => $at_uid, 'roc_notification.is_read' => 0, 'roc_notification.valid' => 1])
                ->count('id', $key, parent::$_expire);
    }

    // 阅读提醒
    public function read($uid, $id)
    {
        $this->_db->from($this->_table)
                ->where(['id' => $id, 'at_uid' => $uid, 'valid' => 1])
                ->update(['is_read' => 1])
                ->execute();

        parent::$redis->del('NotificationUnreadNum:'.$uid);

        return $this->_db->affected_rows;
    }

    // 删除提醒
    public function delete($uid, $id)
    {
        $this->_db->from($this->_table)
                ->where(['id' => $id, 'at_uid' => $uid, 'valid' => 1])
                ->update(['valid' => 0])
                ->execute();

        return $this->_db->affected_rows;
    }
}
