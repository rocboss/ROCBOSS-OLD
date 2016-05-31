<?php

class WhisperModel extends Model
{
    protected $_table = 'roc_whisper';

    // 新增私信
    public function addWhisper($data)
    {
        $this->clearCache('WhisperUnreadNum');

        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    // 获取阅读私信
    public function getRead($at_uid, $offset = 0, $limit = 12)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_whisper.uid' => 'roc_user.uid'])
                ->where(['roc_whisper.at_uid' => $at_uid, 'roc_whisper.is_read' => 1, 'roc_whisper.valid' => 1, 'roc_whisper.del_flag <>' => $at_uid])
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('roc_whisper.id')
                ->select(['roc_whisper.*', 'roc_user.username'])
                ->many();
    }

    // 获取我发送的私信
    public function getMySending($uid, $offset = 0, $limit = 12)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_whisper.at_uid' => 'roc_user.uid'])
                ->where(['roc_whisper.uid' => $uid, 'roc_whisper.valid' => 1, 'roc_whisper.del_flag <>' => $uid])
                ->offset($offset)
                ->limit($limit)
                ->sortDESC('roc_whisper.id')
                ->select(['roc_whisper.*', 'roc_user.username'])
                ->many();
    }

    // 获取未读私信
    public function getUnread($at_uid)
    {
        return $this->_db->from($this->_table)
                ->leftJoin('roc_user', ['roc_whisper.uid' => 'roc_user.uid'])
                ->where(['roc_whisper.at_uid' => $at_uid, 'roc_whisper.is_read' => 0, 'roc_whisper.valid' => 1])
                ->where(['roc_whisper.del_flag <>' => $at_uid])
                ->sortDESC('roc_whisper.id')
                ->select(['roc_whisper.*', 'roc_user.username'])
                ->many();
    }

    // 获取未读提醒数量
    public function getUnreadTotal($at_uid)
    {
        $key = 'WhisperUnreadNum:'.$at_uid;

        return $this->_db->from($this->_table)
                ->where(['roc_whisper.at_uid' => $at_uid, 'roc_whisper.is_read' => 0, 'roc_whisper.valid' => 1])
                ->where(['roc_whisper.del_flag <>' => $at_uid])
                ->count('id', $key, parent::$_expire);
    }

    // 阅读私信
    public function read($uid, $id)
    {
        $this->_db->from($this->_table)
                ->where(['id' => $id, 'at_uid' => $uid, 'valid' => 1])
                ->update(['is_read' => 1])
                ->execute();

        parent::$redis->del('WhisperUnreadNum:'.$uid);

        return $this->_db->affected_rows;
    }

    // 删除私信
    public function delete($uid, $id)
    {
        $whisper = $this->_db->from($this->_table)
                ->where(['id' => $id, 'at_uid' => $uid, 'valid' => 1])
                ->select(['*'])
                ->one();

        if (!empty($whisper)) 
        {
            if ($whisper['del_flag'] == 0) 
            {
                $this->_db->from($this->_table)
                    ->where(['id' => $id])
                    ->update(['del_flag' => $uid])
                    ->execute();
            }
            else if ($whisper['del_flag'] != $uid)
            {
                $this->_db->from($this->_table)
                    ->where(['id' => $id])
                    ->update(['valid' => 0])
                    ->execute();
            }
            else
            {
                return 0;
            }
        }
        else
        {
            $whisper = $this->_db->from($this->_table)
                ->where(['id' => $id, 'uid' => $uid, 'valid' => 1])
                ->select(['*'])
                ->one();

            if (!empty($whisper)) 
            {
                if ($whisper['del_flag'] == 0) 
                {
                    $this->_db->from($this->_table)
                        ->where(['id' => $id])
                        ->update(['del_flag' => $uid])
                        ->execute();
                }
                else if ($whisper['del_flag'] != $uid)
                {
                    $this->_db->from($this->_table)
                        ->where(['id' => $id])
                        ->update(['valid' => 0])
                        ->execute();
                }
                else
                {
                    return 0;
                }
            }
        }

        parent::$redis->del('WhisperUnreadNum:'.$uid);

        return $this->_db->affected_rows;
    }
}