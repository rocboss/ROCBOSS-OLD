<?php
/**
 * 索引控制
 */
class IndexCommand
{
    // 推送所有的数据 ./console index/push-all
    public static function actionPushAll()
    {
        set_time_limit(0);

        $xsm = XSModel::m()->initial();

        $total = TopicModel::m()->getTotal(['valid' => 1]);
        $limit = 200;

        $page = ceil($total/$limit);
        $all = [];
        $index = $xsm->xs->index;
        for ($i=0; $i < $page; $i++) {
            $data = TopicModel::m()->getList($i * $limit, $limit, ['roc_topic.valid' => 1]);
            foreach ($data as $key => &$row) {
                $row['content'] = strip_tags(Roc::filter()->topicOut($row['content']));
            }
            $xsm->addDocs($data, $i == 0 ? true : false);
        }
        unset($xsm);
        echo date('Y-m-d H:i:s')." success \n";
    }

    public static function actionGetCustomDict()
    {
        $xsm = XSModel::m()->initial();
        echo $xsm->xs->index->getCustomDict();
    }
}
