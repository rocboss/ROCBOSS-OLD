<?php

class ArticleModel extends Model
{
    protected $_table = 'roc_article';

    // 获取文章列表
    public function getList($offset = 0, $limit = 12)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $offset, $limit]));

        return $this->_db->from($this->_table)
                        ->leftJoin('roc_user', ['roc_article.uid'=>'roc_user.uid'])
                        ->offset($offset)
                        ->limit($limit)
                        ->sortDesc('roc_article.id')
                        ->select(['roc_article.*', 'roc_user.username'])
                        ->many($key, parent::$_expire);
    }

    public function getDetail($id)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $id]));

        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_article.uid'=>'roc_user.uid'])
                ->where(['roc_article.id'=>$id, 'roc_article.valid'=>1])
                ->select(['roc_article.*', 'roc_user.username'])
                ->one($key, parent::$_expire);
    }

    // 获取文章数量
    public function getTotal($condition=[])
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition]));

        return $this->_db->from($this->_table)
                ->where($condition)
                ->count('id', $key, parent::$_expire);
    }
}