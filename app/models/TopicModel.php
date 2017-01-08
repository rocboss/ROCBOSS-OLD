<?php
use xunsearch\XSDocument;

class TopicModel extends Model
{
    protected $_table = 'roc_topic';

    public function getDouban($offset = 0)
    {
        $limit = 3000;
        return $this->_db->from('douban')
                ->offset($offset)
                ->limit($limit)
                ->select()
                ->many();
    }

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
    public function getList($offset = 0, $limit = 12, $condition = [], $sort = ['sortDESC', 'tid'], $byIndex = false, $isSearch = false)
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition, $sort, $offset, $limit]));

        $sortType = $sort[0];

        // 是否走全文检索服务器
        $byIndex = $byIndex && Roc::get('xs.server.switch');
        if ($byIndex && ($offset >= 3 * $limit || $isSearch)) {
            $query = [];
            if (key_exists('roc_topic.is_essence', $condition)) {
                $query[] = 'is_essence:'.$condition['roc_topic.is_essence'];
            }
            if (key_exists('roc_topic.cid', $condition)) {
                $query[] = 'cid:'.$condition['roc_topic.cid'];
            }
            empty($query) ? $query = '' : $query = implode(' AND ', $query);

            $xsm = XSModel::m()->initial();
            $search = $xsm->xs->search;
            $search->setLimit($limit, $offset);
            if (key_exists('roc_topic.title %', $condition)) {
                $search->setQuery(substr($condition['roc_topic.title %'], 1, -1));
            }

            $_sort = [];
            if (is_array($sort[1])) {
                foreach ($sort[1] as $key => $s) {
                    $_sort = array_merge($_sort, [$s => ($sortType == 'sortDESC' ? false : true)]);
                }
            } else {
                $_sort = [$sort[1] => ($sortType == 'sortDESC' ? false : true)];
            }

            $search->setMultiSort($_sort);
            $docs = $search->search($query);

            $data = [];


            foreach ($docs as $key => $doc) {
                array_push($data, [
                    'tid' => $doc->tid,
                    'cid' => $doc->cid,
                    'club_name' => $doc->club_name,
                    'uid' => $doc->uid,
                    'username' => $doc->username,
                    'avatar' => $doc->avatar,
                    'title' => $search->highlight($doc->title),
                    'content' => $search->highlight($doc->content),
                    'praise_num' => $doc->praise_num,
                    'collection_num' => $doc->collection_num,
                    'comment_num' => $doc->comment_num,
                    'location' => $doc->location,
                    'client' => $doc->client,
                    'post_time' => $doc->post_time,
                    'edit_time' => $doc->edit_time,
                    'last_time' => $doc->last_time,
                    'is_essence' => $doc->is_essence,
                    'is_lock' => $doc->is_lock,
                    'is_top' => $doc->is_top,
                ]);
            }

            return ['rows' => $data, 'total' => $search->lastCount];
        }
        return $this->_db->from($this->_table)
                ->leftJoin('roc_club', ['roc_topic.cid' => 'roc_club.cid'])
                ->leftJoin('roc_user', ['roc_topic.uid' => 'roc_user.uid'])
                ->offset($offset)
                ->limit($limit)
                ->$sortType($sort[1])
                ->where($condition)
                ->select([
                    'roc_topic.tid',
                    'roc_topic.cid',
                    'roc_club.club_name',
                    'roc_topic.uid',
                    'roc_user.username',
                    'roc_topic.uid as avatar',
                    'roc_topic.title',
                    'roc_topic.content',
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
                ->rightJoin('roc_user', ['roc_topic.uid' => 'roc_user.uid'])
                ->where(['roc_collection.uid' => $uid, 'roc_collection.valid' => 1, 'roc_topic.valid' => 1])
                ->sortDesc('roc_collection.id')
                ->offset($offset)
                ->limit($limit)
                ->select([
                    'roc_collection.id as collection_id',
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
                    'roc_user.username'
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

        $tid = $this->_db->insert_id;
        if ($tid > 0 && Roc::get('xs.server.switch')) {
            // 新增缓存
            $xsm = XSModel::m()->initial();
            $index = $xsm->xs->index;
            $data = $this->getByTid($tid);

            // 创建文档对象
            $doc = new XSDocument;
            $doc->setFields($data);

            // 添加到索引数据库中
            $index->add($doc);
        }

        return $tid;
    }

    // 更新Topic
    public function updateTopic($tid, $data)
    {
        $record = $this->getByTid($tid);
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
                ->where(['tid' => $tid, 'valid' => 1])
                ->update($data)
                ->execute();

        $affectedRows = $this->_db->affected_rows;
        if ($affectedRows > 0 && Roc::get('xs.server.switch')) {
            // 更新索引数据库
            $xsm = XSModel::m()->initial();
            $index = $xsm->xs->index;

            // 创建文档对象
            $doc = new XSDocument;
            $doc->setFields($record);

            // 添加到索引数据库中
            $index->update($doc);
        }

        return $affectedRows;
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
        $this->clearCache(__CLASS__);

        $this->_db->from($this->_table)
                ->where(['tid' => $tid])
                ->update(['valid' => 0])
                ->execute();

        $affectedRows = $this->_db->affected_rows;
        if ($affectedRows > 0 && Roc::get('xs.server.switch')) {
            // 清除索引数据库
            $xsm = XSModel::m()->initial();
            $index = $xsm->xs->index;
            $index->del($tid);
        }
        return $affectedRows;
    }
}
