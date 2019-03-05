<?php
namespace api;

use BaseController;
use service\PostService;

/**
 * PostController
 * @author ROC <i@rocs.me>
 */
class PostController extends BaseController
{
    protected static $_checkSign = true;

    /**
     * 获取POST列表
     *
     * @return array
     */
    protected function list()
    {
        $query = app()->request()->query;
        $this->checkParams($query, ['page', 'page_size']);
        $params = $query->getData();

        // 获取当前登录用户信息
        $currentUserId = app()->get('uid');

        $page = $params['page'] > 0 ? intval($params['page']) : 1;
        $pageSize = $params['page_size'] > 0 && $params['page_size'] <= self::MAX_PAGESIZE ? intval($params['page_size']) : 20;
        $userId = !empty($params['user_id']) ? intval($params['user_id']) : 0;
        $type = !empty($params['type']) && in_array($params['type'], [1, 2]) ? $params['type'] : 0;
        $groupId= !empty($params['group_id']) && $params['group_id'] > 0 ? intval($params['group_id']) : 0;

        $condition = [
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

                ]
            ],
        ];

        // 类型限制
        if ($type > 0) {
            unset($condition['bool']['should']);
            $condition['bool']['must'][] = [
                'match' => [
                    'type' => $type
                ]
            ];
        }

        // Group限制
        if ($groupId > 0) {
            $condition['bool']['must'][] = [
                'match' => [
                    'group_id' => $groupId
                ]
            ];
        }

        // 排序类型
        $sort = ['created_at' => 'desc'];
        if (!empty($params['filter_type']) && in_array($params['filter_type'], ['latest_post', 'latest_comment', 'hot_comment', 'cream_post'])) {
            switch ($params['filter_type']) {
                case 'latest_post':
                    $sort = ['created_at' => 'desc'];
                    break;

                case 'latest_comment':
                    $sort = ['updated_at' => 'desc'];
                    break;

                case 'hot_comment':
                    $sort = ['comment_count' => 'desc'];
                    break;

                case 'cream_post':
                    $sort = ['updated_at' => 'desc'];
                    break;

                default:
                    $sort = ['created_at' => 'desc'];
                    break;
            }
        }

        $service = new PostService();
        $data = $service->list($condition, ($page - 1) * $pageSize, $pageSize, $sort);

        // 数据处理
        foreach ($data['rows'] as &$row) {
            $row['title'] = '';
            $row['created_at'] = formatTime($row['created_at_timestamp']);
            $row['updated_at'] = formatTime($row['updated_at_timestamp']);
            $row['image_count'] = 0;
            $row['video_count'] = 0;
            $row['attachment_count'] = 0;

            // 标题&图片类型做聚合处理
            $imgs = [];
            foreach ($row['contents'] as $key => &$content) {
                // 图片类型
                if ($content['type'] === '3') {
                    array_push($imgs, $content['content']);
                    unset($row['contents'][$key]);
                    $row['image_count']++;
                }
                // 视频类型
                if ($content['type'] === '4') {
                    $row['video_count']++;
                }
                // 附件类型
                if ($content['type'] === '7') {
                    $row['attachment_count']++;
                }
                // 文章标题摘要
                if ($content['type'] === '1') {
                    $row['title'] = $content['content'];
                }
            }
            if (!empty($imgs)) {
                array_push($row['contents'], [
                    'type' => '3',
                    'content' => $imgs,
                ]);
            }
            $row['contents'] = array_values($row['contents']);

            unset($row['content_text']);
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data,
        ];
    }

    /**
     * POST 收藏操作
     *
     * @return array
     */
    protected function star()
    {
        $data = app()->request()->data;
        $this->checkParams($data, ['post_id']);
        $params = $data->getData();

        $userId = app()->get('uid');

        $service = new PostService();
        $return = $service->star($params['post_id'], $userId);
        if ($return === 0) {
            return [
                'code' => 500,
                'msg' => 'error',
            ];
        }

        // 重新推送ES
        $service->pushToElasticSearch($params['post_id']);

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $return
        ];
    }

    /**
     * POST 点赞操作
     *
     * @return array
     */
    protected function upvote()
    {
        $data = app()->request()->data;
        $this->checkParams($data, ['post_id']);
        $params = $data->getData();

        $userId = app()->get('uid');

        $service = new PostService();
        $return = $service->upvote($params['post_id'], $userId);
        if ($return === 0) {
            return [
                'code' => 500,
                'msg' => 'error',
            ];
        }

        // 重新推送ES
        $service->pushToElasticSearch($params['post_id']);

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $return
        ];
    }

    /**
     * POST 删除操作
     *
     * @return array
     */
    protected function delete($postId)
    {
        $service = new PostService();
        // 权限检测
        $accessCheck = $service->manageAccessCheck($postId, app()->get('uid'));

        if ($accessCheck) {
            // 删除POST
            $service->delete($postId);

            return [
                'code' => 0,
                'msg' => 'success',
            ];
        }

        return [
            'code' => 403,
            'msg' => '无权删除',
        ];
    }
}
