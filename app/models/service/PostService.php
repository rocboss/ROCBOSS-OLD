<?php
namespace service;

use EndyJasmi\Cuid;
use service\UserService;
use service\UserAssetService;
use service\BillService;
use service\GroupUserService;
use model\PostModel;
use model\PostContentModel;
use model\GroupBlacklistModel;
use model\PostUpvoteModel;

/**
 * PostService
 * @author ROC <i@rocs.me>
 */
class PostService extends Service
{
    /**
     * 获取POST列表
     *
     * @param array $condition
     * @param integer $offset
     * @param integer $limit
     * @param array $sort
     * @return array
     */
    public function list(array $condition, $offset = 0, $limit = 20, array $sort = ['created_at' => 'desc'])
    {
        $posts = app()->es()->search([
            'query' => $condition,
            'from' => $offset,
            'size' => $limit,
            'sort' => $sort,
        ], env('ES_POST_INDEX'), env('ES_POST_TYPE'));

        $total = 0;
        $rows = [];

        if (!empty($posts['hits'])) {
            $total = $posts['hits']['total'];

            foreach ($posts['hits']['hits'] as $hit) {
                array_push($rows, $hit['_source']);
            }

            $userIds = array_column($rows, 'user_id');
            $groupIds = array_column($rows, 'group_id');

            $users = $this->userModel->dump([
                'id' => $userIds,
                'is_deleted' => 0,
            ], [
                'id',
                'nickname',
                'username',
                'avatar',
            ]);

            $groups = $this->groupModel->dump([
                'id' => $groupIds,
                'is_deleted' => 0,
            ], [
                'id',
                'name',
                'desc',
                'cover',
            ]);

            foreach ($rows as &$row) {
                $row['user'] = $row['group'] = [];
                // 用户
                foreach ($users as $user) {
                    if ($user['id'] == $row['user_id']) {
                        $row['user'] = $user;
                    }
                }
                // 圈子
                foreach ($groups as $group) {
                    if ($group['id'] == $row['group_id']) {
                        $row['group'] = $group;
                    }
                }
            }
        }

        return [
            'rows' => $rows,
            'total' => $total,
        ];
    }

    /**
     * 管理POST权限检测
     *
     * @param integer $postId
     * @param integer $userId
     * @return boolean
     */
    public function manageAccessCheck($postId, $userId)
    {
        // 检测POST是否存在
        $this->postModel->load([
            'id' => $postId,
            'is_deleted' => 0,
        ]);

        $post = $this->postModel->getData();
        if (!empty($post)) {
            // 发布主check
            if ($post['user_id'] == $userId) {
                return true;
            }

            // 超级管理员身份CHECK
            // if ((new UserService())->isSuperManager($userId)) {
            //     return true;
            // }

            return false;
        }

        return true;
    }

    /**
     * 获取用户点赞状态
     *
     * @param integer $postId
     * @param integer $userId
     * @return boolean
     */
    public function getUserUpvotedStatus($postId, $userId)
    {
        $this->postUpvoteModel->load([
            'user_id' => $userId,
            'post_id' => $postId,
            'is_deleted' => 0,
        ]);

        return !empty($this->postUpvoteModel->getData()) ? true : false;
    }

    /**
     * 获取用户收藏状态
     *
     * @param integer $postId
     * @param integer $userId
     * @return boolean
     */
    public function getUserStarredStatus($postId, $userId)
    {
        $this->postStarModel->load([
            'user_id' => $userId,
            'post_id' => $postId,
            'is_deleted' => 0,
        ]);

        return !empty($this->postStarModel->getData()) ? true : false;
    }

    /**
     * 全量推送数据至ES服务器
     *
     * @return boolean
     */
    public function pushFullPostsToEs()
    {
        try {
            // 已有索引数据全部清除
            app()->es()->deleteIndex(env('ES_POST_INDEX'));
        } catch (\Exception $e) {
        }

        $posts = $this->postModel->dump([
            'is_deleted' => 0,
        ], [
            'id'
        ]);

        $postIds = array_column($posts, 'id');

        foreach ($postIds as $postId) {
            $this->pushToElasticSearch($postId);
        }

        return true;
    }

    /**
     * 推送到ES服务器
     *
     * @param integer $postId
     * @return boolean
     */
    public function pushToElasticSearch($postId)
    {
        $post = $this->getDetailFromDatabase($postId);

        // 推送至ES
        if (!empty($post)) {
            // 不存在则新建索引
            if (!app()->es()->existsIndex(env('ES_POST_INDEX'))) {
                // 创建索引
                app()->es()->createIndex(env('ES_POST_INDEX'));
                // 创建文档映射
                app()->es()->createMappings([
                    'alias_id' => [
                        'type' => 'text',
                    ],
                    'group_id' => [
                        'type' => 'integer',
                    ],
                    'user_id' => [
                        'type' => 'integer',
                    ],
                    'type' => [
                        'type' => 'integer',
                    ],
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'created_at_timestamp' => [
                        'type' => 'integer',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                    'updated_at_timestamp' => [
                        'type' => 'integer',
                    ],
                    'comment_count' => [
                        'type' => 'integer',
                    ],
                    'collection_count' => [
                        'type' => 'integer',
                    ],
                    'upvote_count' => [
                        'type' => 'integer',
                    ],
                    'contents' => [],
                    'contents_text' => [
                        'type' => 'text',
                        'analyzer' => 'ik_max_word',
                    ]
                ], env('ES_POST_INDEX'), env('ES_POST_TYPE'));
            }

            $contentsText = '';
            if (is_array($post['contents'])) {
                foreach ($post['contents'] as $content) {
                    // 标题或者内容纳入进全文检索
                    if ($content['type'] == 1 || $content['type'] == 2) {
                        $contentsText .= ' '.$content['content'];
                    }
                }
            }
            // 推送文档
            app()->es()->addDoc($post['id'], [
                'alias_id' => $post['alias_id'],
                'group_id' => $post['group_id'],
                'user_id' => $post['user_id'],
                'type' => $post['type'],
                'created_at' => date('c', strtotime($post['created_at'])),
                'created_at_timestamp' => strtotime($post['created_at']),
                'updated_at' => date('c', strtotime($post['updated_at'])),
                'updated_at_timestamp' => strtotime($post['updated_at']),
                'contents' => $post['contents'],
                'comment_count' => $post['comment_count'],
                'collection_count' => $post['collection_count'],
                'upvote_count' => $post['upvote_count'],
                'contents_text' => $contentsText,
            ], env('ES_POST_INDEX'), env('ES_POST_TYPE'));

            return true;
        }

        return false;
    }

    /**
     * 检测是否在圈子黑名单中
     *
     * @param integer $groupId
     * @param integer $userId
     * @return boolean
     */
    public function isInGroupBlacklist($groupId, $userId)
    {
        return (new GroupBlacklistModel())->count([
            'group_id' => $groupId,
            'user_id' => $userId,
            'is_deleted' => 0,
        ]) > 0 ? true : false;
    }

    /**
     * POST 收藏操作
     *
     * @param integer $postId
     * @param integer $userId
     * @return integer
     */
    public function star($postId, $userId)
    {
        $this->postStarModel->load([
            'user_id' => $userId,
            'post_id' => $postId,
            'is_deleted' => 0,
        ]);
        if (!empty($this->postStarModel->getData())) {
            if ($this->postStarModel->delete()) {
                $this->updateCollectionCount(-1, $postId);

                return -1;
            }
            return 0;
        } else {
            $postStarModel = $this->postStarModel;
            $postStarModel->post_id = $postId;
            $postStarModel->user_id = $userId;

            if ($postStarModel->save()) {
                $this->updateCollectionCount(1, $postId);
                return 1;
            }
            return 0;
        }
    }

    /**
     * POST 点赞操作
     *
     * @param integer $postId
     * @param integer $userId
     * @return integer
     */
    public function upvote($postId, $userId)
    {
        $this->postUpvoteModel->load([
            'user_id' => $userId,
            'post_id' => $postId,
            'is_deleted' => 0,
        ]);
        if (!empty($this->postUpvoteModel->getData())) {
            if ($this->postUpvoteModel->delete()) {
                $this->updateUpvoteCount(-1, $postId);

                return -1;
            }
            return 0;
        } else {
            $postUpvoteModel = $this->postUpvoteModel;
            $postUpvoteModel->post_id = $postId;
            $postUpvoteModel->user_id = $userId;

            if ($postUpvoteModel->save()) {
                $this->updateUpvoteCount(1, $postId);
                return 1;
            }
            return 0;
        }
    }

    /**
     * 修改 POST 收藏数
     *
     * @param integer $change
     * @param integer $postId
     * @return void
     */
    public function updateCollectionCount($change, $postId)
    {
        $this->postModel->getDatabase()->update(PostModel::TABLE, [
            'collection_count[+]' => $change
        ], [
            'id' => $postId
        ]);
    }

    /**
     * 修改 POST 点赞数
     *
     * @param integer $change
     * @param integer $postId
     * @return void
     */
    public function updateUpvoteCount($change, $postId)
    {
        $this->postModel->getDatabase()->update(PostModel::TABLE, [
            'upvote_count[+]' => $change
        ], [
            'id' => $postId
        ]);
    }

    /**
     * 修改 POST 评论数
     *
     * @param integer $change
     * @param integer $postId
     * @return void
     */
    public function updateCommentCount($change, $postId)
    {
        $this->postModel->getDatabase()->update(PostModel::TABLE, [
            'comment_count[+]' => $change
        ], [
            'id' => $postId
        ]);
    }

    /**
     * 重新计算POST的评论数
     *
     * @param integer $postId
     * @return void
     */
    public function reCalcCommentCount($postId)
    {
        $comments = $this->commentModel->dump([
            'post_id' => $postId,
            'is_deleted' => 0,
        ], [
            'id'
        ]);
        $commentIds = array_column($comments, 'id');

        $this->postModel->getDatabase()->update(PostModel::TABLE, [
            'comment_count' => $this->commentModel->count([
                'post_id' => $postId,
                'is_deleted' => 0,
            ]) + $this->commentReplyModel->count([
                'comment_id' => $commentIds,
                'is_deleted' => 0,
            ])
        ], [
            'id' => $postId
        ]);
    }

    /**
     * 获取POST ID
     *
     * @param array $condition
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getPostIds(array $condition, $offset = 0, $limit = 20)
    {
        $posts = (array) $this->postModel->dump(array_merge($condition, [
            'LIMIT' => [$offset, $limit]
        ]), [
            'id'
        ]);

        return array_column($posts, 'id');
    }
}
