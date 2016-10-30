<?php

class FollowModel extends Model
{
    protected $_table = 'roc_follow';

    // 获取粉丝列表
    public function getFans($offset = 0, $limit = 12, $condition)
    {
        $data = $this->_db->from($this->_table)
            ->leftJoin('roc_user', ['roc_follow.uid' => 'roc_user.uid'])
            ->where($condition)
            ->offset($offset)
            ->limit($limit)
            ->select(['roc_follow.*', 'roc_user.username'])
            ->many();

        return $data;
    }

    // 获取关注列表
    public function getFollows($offset = 0, $limit = 12, $condition)
    {
        $data = $this->_db->from($this->_table)
            ->leftJoin('roc_user', ['roc_follow.fuid' => 'roc_user.uid'])
            ->where($condition)
            ->offset($offset)
            ->limit($limit)
            ->select(['roc_follow.*', 'roc_user.username'])
            ->many();

        return $data;
    }

    // 获取粉丝数量
    public function getFansCount($condition)
    {
        $data = $this->_db->from($this->_table)
            ->where($condition)
            ->count();

        return $data;
    }

    // 检测是否已关注
    public function isFans($uid, $fuid)
    {
        return $this->_db->from($this->_table)
            ->where(['uid' => $uid, 'fuid' => $fuid])
            ->count();
    }

    // 关注
    public function addFollow($uid, $fuid)
    {
        $this->_db->from($this->_table)
            ->insert([
                'uid' => $uid,
                'fuid' => $fuid
            ])
            ->execute();

        return $this->_db->insert_id;
    }

    // 取消关注
    public function cancelFollow($uid, $fuid)
    {
        $this->_db->from($this->_table)
            ->where(['uid' => $uid, 'fuid' => $fuid])
            ->delete()
            ->execute();

        return $this->_db->affected_rows;
    }
}
