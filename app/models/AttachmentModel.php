<?php

class AttachmentModel extends Model
{
    protected $_table = 'roc_attachment';

    public function getAttachment($id, $uid, $size = false)
    {
        $data = $this->_db->from($this->_table)
                        ->where(['id'=>$id, 'uid'=>$uid, 'valid'=>1])
                        ->select('path')
                        ->one();

        if ($size === false) {
            return $data;
        } else {
            if (!empty($data)) {
                return $this->convertPath($data['path'], $size);
            } else {
                return '';
            }
        }
    }

    public function postAttachment($data)
    {
        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    public function getAttachments(array $ids)
    {
        $return = [];

        if (is_array($ids) && !empty($ids)) {
            $data = $this->_db->from($this->_table)
                        ->where(['id'=>$ids, 'valid'=>1])
                        ->select('path')
                        ->many();

            if (!empty($data)) {
                foreach ($data as $attachment) {
                    array_push($return, $this->convertPath($attachment['path']));
                }
            }
        }

        return $return;
    }

    public function convertPath($path, $size = 800)
    {
        return (Roc::request()->secure ? 'https://' : 'http://').Roc::get('qiniu.domain').'/'.$path.'-'.$size.'.png';
    }
}
