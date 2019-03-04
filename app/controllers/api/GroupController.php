<?php
namespace api;

use BaseController;
use service\PostService;
use service\GroupService;

/**
 * GroupController
 * @author ROC <i@rocs.me>
 */
class GroupController extends BaseController
{
    protected static $_checkSign = true;

    /**
     * Group 列表
     *
     * @return array
     */
    protected function list()
    {
        $query = app()->request()->query;
        $this->checkParams($query, ['page', 'page_size']);
        $params = $query->getData();

        $page = $params['page'] > 0 ? intval($params['page']) : 1;
        $pageSize = $params['page_size'] > 0 && $params['page_size'] <= self::MAX_PAGESIZE ? intval($params['page_size']) : 20;

        $service = new GroupService();
        $groups = $service->list(($page - 1) * $pageSize, $pageSize);

        // 数据格式化处理
        if (is_array($groups['rows'])) {
            foreach ($groups['rows'] as &$row) {
                $row = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'desc' => $row['desc'],
                    'cover' => $row['cover'],
                ];
            }
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => array_merge($groups, [
                'page' => $page,
                'page_size' => $pageSize,
            ])
        ];
    }

    /**
     * 获取 Group 详情
     *
     * @param integer $id
     * @return array
     */
    protected function detail($id)
    {
        $service = new GroupService();
        $group = $service->detail($id);
        if (!empty($group)) {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => $group,
            ];
        }
        
        return [
            'code' => 404,
            'msg' => 'error',
        ];
    }

    /**
     * 获取POST列表
     *
     * @param integer $groupId
     * @return array
     */
    protected function posts($groupId)
    {
        $query = app()->request()->query;
        $this->checkParams($query, ['page', 'page_size']);
        $params = $query->getData();

        $page = $params['page'] > 0 ? intval($params['page']) : 1;
        $pageSize = $params['page_size'] > 0 && $params['page_size'] <= self::MAX_PAGESIZE ? intval($params['page_size']) : 20;

        $service = new PostService();
        $data = $service->list([
            'bool' => [
                'must' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'type' => '1',
                                ]
                            ],
                            [
                                'match' => [
                                    'type' => '2',
                                ]
                            ]
                        ],
                        'must' => [
                            [
                                'match' => [
                                    'group_id' => $groupId,
                                ]
                            ]
                        ],
                    ]
                ]
            ],
        ], ($page - 1) * $pageSize, $pageSize);

        // 数据处理
        foreach ($data['rows'] as &$row) {
            $row['created_at'] = formatTime($row['created_at_timestamp']);
            $row['updated_at'] = formatTime($row['updated_at_timestamp']);

            // 图片类型做聚合处理
            $imgs = [];
            foreach ($row['contents'] as $key => &$content) {
                if ($content['type'] === '3') {
                    array_push($imgs, $content['content']);
                    unset($row['contents'][$key]);
                }
            }
            if (!empty($imgs)) {
                array_push($row['contents'], [
                    'type' => '3',
                    'content' => $imgs,
                ]);
            }
            $row['contents'] = array_values($row['contents']);
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data,
        ];
    }
}
