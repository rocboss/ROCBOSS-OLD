<?php

class LinkModel extends Model
{
    protected $_table = 'roc_link';

    // 获取链接列表
    public function getList()
    {
        return $this->_db->from($this->_table)
                        ->where(['valid' => 1])
                        ->sortDESC('sort')
                        ->sortASC('id')
                        ->select(['id', 'name', 'url', 'sort'])
                        ->many(__CLASS__.':links', 86400);
    }

    // 更新链接
    public function update($data, $id)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->where(['id' => $id])
            ->update($data)
            ->execute();

        return $this->_db->affected_rows;
    }

    // 删除分类
    public function delete($id)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->where(['id' => $id])
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
