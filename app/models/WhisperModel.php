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

    // 获取用户所有私信
    public function getAll($uid, $offset = 0, $limit = 12)
    {
        $orm = $this->_db->from($this->_table.' as w')
                ->leftJoin('roc_user as u1', ['w.uid' => 'u1.uid'])
                ->leftJoin('roc_user as u2', ['w.at_uid' => 'u2.uid'])
                ->where('(w.at_uid = '.$uid.' OR w.uid = '.$uid. ') AND w.valid = 1 AND w.del_flag <> '.$uid);

        $rows = $orm->offset($offset)
                ->limit($limit)
                ->sortDESC('w.id')
                ->select(['w.*', 'u1.username as send_username', 'u2.username as receive_username'])
                ->many();
        $total = $orm->count();

        return [
            'rows' => $rows,
            'offset' => $offset,
            'limit' => $limit,
            'total' => $total
        ];
    }

    // 获取对话列表
    public function getDialog($uid, $myUid, $offset = 0, $limit = 12)
    {
        $orm = $this->_db->from($this->_table)
                ->where('((at_uid = '.$uid.' AND uid = '.$myUid.') OR (uid = '.$uid.' AND at_uid = '.$myUid.')) AND valid = 1 AND del_flag <> '.$myUid);

        $rows = $orm->offset($offset)
                ->limit($limit)
                ->sortDESC('id')
                ->many();
        $total = $orm->count();

        return [
            'rows' => $rows,
            'offset' => $offset,
            'limit' => $limit,
            'total' => $total
        ];
    }

    // 获取已读
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
                ->where([
                    'roc_whisper.at_uid' => $at_uid,
                    'roc_whisper.is_read' => 0,
                    'roc_whisper.valid' => 1,
                    'roc_whisper.del_flag <>' => $at_uid
                ])
                ->sortDESC('roc_whisper.id')
                ->select(['roc_whisper.*', 'roc_user.username'])
                ->many();
    }

    // 获取未读提醒数量
    public function getUnreadTotal($at_uid)
    {
        $key = 'WhisperUnreadNum:'.$at_uid;

        return $this->_db->from($this->_table)
                ->where([
                    'roc_whisper.at_uid' => $at_uid,
                    'roc_whisper.is_read' => 0,
                    'roc_whisper.valid' => 1,
                    'roc_whisper.del_flag <>' => $at_uid
                ])
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

        if (!empty($whisper)) {
            if ($whisper['del_flag'] == 0) {
                $this->_db->from($this->_table)
                    ->where(['id' => $id])
                    ->update(['del_flag' => $uid])
                    ->execute();
            } else if ($whisper['del_flag'] != $uid) {
                $this->_db->from($this->_table)
                    ->where(['id' => $id])
                    ->update(['valid' => 0])
                    ->execute();
            } else {
                return 0;
            }
        } else {
            $whisper = $this->_db->from($this->_table)
                ->where(['id' => $id, 'uid' => $uid, 'valid' => 1])
                ->select(['*'])
                ->one();

            if (!empty($whisper)) {
                if ($whisper['del_flag'] == 0) {
                    $this->_db->from($this->_table)
                        ->where(['id' => $id])
                        ->update(['del_flag' => $uid])
                        ->execute();
                } else if ($whisper['del_flag'] != $uid) {
                    $this->_db->from($this->_table)
                        ->where(['id' => $id])
                        ->update(['valid' => 0])
                        ->execute();
                } else {
                    return 0;
                }
            }
        }

        parent::$redis->del('WhisperUnreadNum:'.$uid);

        return $this->_db->affected_rows;
    }
}
