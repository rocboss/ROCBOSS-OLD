<?php

class ScoreModel extends Model
{
    protected $_table = 'roc_score';

    // 获取积分记录
    public function getRecord($tradeNo)
    {
        return $this->_db->from($this->_table)
            ->where('trade_no = "'.$tradeNo.'"')
            ->where(['valid' => 1])
            ->one();
    }

    // 新增积分记录
    public function addRecord($data)
    {
        $this->clearCache('TopicModel:TopicModel::getRewardList');

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 获取积分列表
    public function getList($uid, $offset, $limit)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_topic', ['roc_score.tid' => 'roc_topic.tid'])
                ->where(['roc_score.uid' => $uid, 'roc_score.valid' => 1])
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('roc_score.id')
                ->select([
                    'roc_score.*',
                    'roc_topic.title'
                ])
                ->many();
    }
}
