<?php

class ArticleModel extends Model
{
    protected $_table = 'roc_article';

    // 获取文章列表
    public function getList($offset = 0, $limit = 12, $uid = 0, $isAll = false)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $offset, $limit, $uid, $isAll]));

        $condition = 'roc_article.valid = 1';

        if ($uid > 0) {
            $condition = $condition. ' AND roc_article.uid = ' .$uid;
        }
        if (!$isAll) {
            $condition = $condition. ' AND roc_article.is_open = 1';
        }

        return $this->_db->from($this->_table)
                        ->leftJoin('roc_user', ['roc_article.uid' => 'roc_user.uid'])
                        ->where($condition)
                        ->offset($offset)
                        ->limit($limit)
                        ->sortDesc('roc_article.id')
                        ->select(['roc_article.*', 'roc_user.username'])
                        ->many($key, parent::$_expire);
    }

    // 获取详情
    public function getDetail($id)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $id]));

        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_article.uid'=>'roc_user.uid'])
                ->where(['roc_article.id'=>$id, 'roc_article.valid'=>1])
                ->select(['roc_article.*', 'roc_user.username'])
                ->one($key, parent::$_expire);
    }

    // 检测是否已赞
    public function getPraiseDetail($aid, $uid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$aid.'_'.$uid;

        return $this->_db->from('roc_praise')
                ->where(['article_id' => $aid, 'uid' => $uid, 'valid' => 1])
                ->count('id', $key, parent::$_expire);
    }

    // 新增点赞
    public function addPraise($aid, $uid)
    {
        parent::$redis->del(__CLASS__.':ArticleModel::getPraiseDetail:'.$aid.'_'.$uid);

        $this->_db->from('roc_praise')
            ->insert(['article_id' => $aid, 'uid' => $uid])
            ->execute();

        return $this->_db->insert_id;
    }

    // 更新Article赞的数量
    public function updatePraiseNum($aid, $num = 1)
    {
        $this->clearCache(__CLASS__);

        return $this->_db->sql("UPDATE roc_article SET praise_num = praise_num + ".$num." WHERE id = ".$aid)->execute();
    }

    // 获取Article赞列表
    public function getPraiseList($aid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$aid;

        return $this->_db->from('roc_praise')
                ->where(['article_id' => $aid, 'valid' => 1])
                ->many($key, parent::$_expire);
    }

    // 获取收藏详情
    public function getCollectionDetail($aid, $uid)
    {
        $key = __CLASS__.':'.__METHOD__.':'.$aid.'_'.$uid;

        return $this->_db->from('roc_collection')
                ->where(['article_id' => $aid, 'uid' => $uid, 'valid' => 1])
                ->count('id', $key, parent::$_expire);
    }

    // 取消收藏
    public function cancelCollection($aid, $uid)
    {
        parent::$redis->del(__CLASS__.':ArticleModel::getCollectionDetail:'.$aid.'_'.$uid);

        $this->_db->from('roc_collection')
                ->where(['article_id' => $aid, 'uid' => $uid, 'valid' => 1])
                ->update(['valid' => 0])
                ->execute();

        return $this->_db->affected_rows;
    }

    // 新增收藏
    public function addCollection($aid, $uid)
    {
        parent::$redis->del(__CLASS__.':ArticleModel::getCollectionDetail:'.$aid.'_'.$uid);

        $this->_db->from('roc_collection')
            ->insert(['article_id' => $aid, 'uid' => $uid])
            ->execute();

        return $this->_db->insert_id;
    }

    // 获取收藏列表
    public function getCollectionList($offset = 0, $limit = 12, $uid)
    {
        return $this->_db->from('roc_article')
                ->rightJoin('roc_collection', ['roc_article.id' => 'roc_collection.article_id'])
                ->rightJoin('roc_user', ['roc_article.uid' => 'roc_user.uid'])
                ->where(['roc_collection.uid' => $uid, 'roc_collection.valid' => 1, 'roc_article.valid' => 1])
                ->sortDesc('roc_collection.id')
                ->offset($offset)
                ->limit($limit)
                ->select([
                    'roc_collection.id as collection_id',
                    'roc_article.*',
                    'roc_article.uid as avatar',
                    'roc_user.username'
                ])
                ->many();
    }

    // 更新收藏数量
    public function updateCollectionNum($id, $num = 1)
    {
        $this->clearCache(__CLASS__);

        return $this->_db->sql("UPDATE roc_article SET collection_num = collection_num + ".$num." WHERE id = ".$id)->execute();
    }

    // 获取文章数量
    public function getTotal($condition=[])
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition]));

        return $this->_db->from($this->_table)
                ->where($condition)
                ->count('id', $key, parent::$_expire);
    }

    // 发布文章
    public function postArticle($data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 更新
    public function updateArticle($id, $data)
    {
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
                ->where(['id' => $id, 'valid' => 1])
                ->update($data)
                ->execute();

        return $this->_db->affected_rows;
    }
}
