<?php

class ClubModel extends Model
{
    protected $_table = 'roc_club';

    // 获取分类列表
    public function getList()
    {
        return $this->_db->from($this->_table)
                        ->where(['valid'=>1])
                        ->sortDESC('sort')
                        ->sortASC('cid')
                        ->select(['cid', 'sort', 'club_name'])
                        ->many(__CLASS__.':clubs', 86400*30);
    }

    // 更新分类
    public function update($data, $cid)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->where(['cid' => $cid])
            ->update($data)
            ->execute();

        return $this->_db->affected_rows;
    }

    // 删除分类
    public function delete($cid)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->where(['cid' => $cid])
            ->update(['valid' => 0])
            ->execute();

        return $this->_db->affected_rows;
    }

    // 新增分类
    public function insert($data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }
}
