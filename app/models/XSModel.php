<?php
use xunsearch\XS;
use xunsearch\XSDocument;

class XSModel extends Model
{
    public $xs;
    public $config;

    public function initial()
    {
        if (Roc::get('xs.server.switch')) {
            $this->xs = new XS($this->getConfig('topic'));
        }
        return $this;
    }

    public function getConfig($project = 'topic')
    {
        return '
            project.name = '.$project.'
            project.default_charset = utf-8
            server.index = '.Roc::get('xs.server.index').'
            server.search = '.Roc::get('xs.server.search').'

            [tid]
            type = id

            [cid]
            index = self

            [club_name]

            [uid]
            index = self

            [username]

            [avatar]

            [title]
            type = title

            [content]
            type = body

            [praise_num]

            [collection_num]

            [comment_num]

            [location]

            [client]

            [post_time]

            [edit_time]

            [last_time]

            [is_essence]
            index = self

            [is_lock]
            index = self

            [is_top]
            index = self
        ';
    }

    // 推送数据
    public function addDocs(array $data, $isRebuild = true)
    {
        if (Roc::get('xs.server.switch')) {
            // 索引
            $index = $this->xs->index;
            // 宣布开始重建索引
            if ($isRebuild) {
                $index->stopRebuild();
                $index->beginRebuild();
            }
            // 创建文档对象
            $doc = new XSDocument;
            // 开启缓冲区
            $index->openBuffer(8);
            foreach ($data as $key => $row) {
                $doc->setFields($row);
                // 添加到索引数据库中
                $index->add($doc);
            }
            // 结束缓冲区
            $index->closeBuffer();
            // 重建完成
            $isRebuild && $index->endRebuild();

            return true;
        }
        return false;
    }

    // 清空索引
    public function clean()
    {
        if (Roc::get('xs.server.switch')) {
            $this->xs->index->clean();
        }
    }
}
