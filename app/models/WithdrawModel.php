<?php

class WithdrawModel extends Model
{
    protected $_table = 'roc_withdraw';

    public function getList($offset = 0, $limit = 12, $condition = [])
    {
        return $this->_db->from($this->_table . ' as w')
                ->leftJoin('roc_user', ['w.uid' => 'roc_user.uid'])
                ->where(['w.valid' => 1, 'roc_user.valid' => 1])
                ->where($condition)
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('w.id')
                ->select(['w.*', 'roc_user.username', 'roc_user.groupid'])
                ->many();
    }

    public function getTotal()
    {
        return $this->_db->from($this->_table)
                ->where(['valid' => 1])
                ->count('id');
    }

    public function update($id, $data)
    {
        $this->_db->from($this->_table)
                ->where(['id' => $id])
                ->update($data)
                ->execute();

        return $this->_db->affected_rows;
    }
}
