<?php

class RelationModel extends Model
{
    protected $_table = 'roc_relation';

    public function addRelation($res_id, $attachment_id, $uid, $type)
    {
        if (is_array($attachment_id) && !empty($attachment_id)) {
            foreach ($attachment_id as $a_id) {
                $this->addRelation($res_id, $a_id, $uid, $type);
            }
        } elseif (is_numeric($attachment_id) && $attachment_id > 0) {
            $attachment = Roc::model('attachment')->getAttachment($attachment_id, $uid);

            if (!empty($attachment)) {
                $this->_db->from($this->_table)
                        ->insert([
                            'attachment_id' => $attachment_id,
                            'res_id' => $res_id,
                            'type' => $type,
                            'valid' => 1
                        ])->execute();
            }
        }
    }

    public function getRelation($res_id, $type, $result = 'many')
    {
        return $this->_db->from($this->_table)
                ->where(['res_id'=>$res_id, 'type'=>$type, 'valid'=>1])
                ->select(['attachment_id'])
                ->$result();
    }
}
