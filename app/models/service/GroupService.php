<?php
namespace service;

use EndyJasmi\Cuid;
use service\UserService;

/**
 * GroupService
 * @author ROC <i@rocs.me>
 */
class GroupService extends Service
{
    // 最大 Group 数量
    const MAX_GROUP_COUNT = 20;

    /**
     * 创建 Group
     *
     * @param integer $userId
     * @param array $data
     * @return boolean
     */
    public function create($userId, array $data)
    {
        $model = $this->groupModel;
        $model->user_id = $userId;
        $model->name = $data['name'];
        $model->desc = $data['desc'];
        $model->cover = $data['cover'];
        $model->type = $data['type'];

        if ($model->save() === true) {
            app()->set('new.group.id', $model->getPrimaryKey()['id']);
            return true;
        }

        $this->_error = '创建失败，请重试';
        return false;
    }

    /**
     * 获取 Group 详情
     *
     * @param integer $id
     * @return array
     */
    public function detail($id)
    {
        $this->groupModel->load([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        $group = $this->groupModel->getData();
        return $group;
    }

    /**
     * 获取 Group 列表
     *
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function list($offset, $limit = 20)
    {
        $groups = (array) $this->groupModel->dump([
            'is_deleted' => 0,
            'ORDER' => ['id' => 'DESC'],
            'LIMIT' => [$offset, $limit],
        ]);

        return [
            'rows' => $groups,
            'total' => $this->groupModel->count([
                'is_deleted' => 0,
            ]),
        ];
    }

    /**
     * 检测 Group 名称是否合法
     *
     * @param string $name
     * @param boolean $repeatCheck
     * @return boolean
     */
    public function checkGroupName($name, $repeatCheck = false)
    {
        if (strlen($name) < 4 || mb_strlen($name, 'utf-8') < 3) {
            $this->_error = ' Group名太短了';
            return false;
        }
        if (mb_strlen($name, 'utf-8') > 16) {
            $this->_error = ' Group名太长了';
            return false;
        }
        if (preg_match('/\s/', $name) || strpos($name, ' ')) {
            $this->_error = ' Group名不允许存在空格';
            return false;
        }
        if (is_numeric(substr($name, 0, 1)) || substr($name, 0, 1) == "_") {
            $this->_error = ' Group名不能以数字和下划线开头';
            return false;
        }
        if (!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $name)) {
            $this->_error = ' Group名只能包含汉字、英文、数字及下划线';
            return false;
        }

        if ($repeatCheck) {
            // 重名 Group检测
            if ($this->groupModel->has([
                'name' => $name,
                'is_deleted' => 0,
            ])) {
                $this->_error = '该 Group名称已存在';
                return false;
            }
        }

        return true;
    }

    /**
     * 根据封面ID获取封面
     *
     * @param integer $coverId
     * @return string
     */
    public function getCoverByCoverId($coverId)
    {
        $this->attachmentModel->load([
            'id' => $coverId,
            'is_deleted' => 0,
        ]);
        $data = $this->attachmentModel->getData();

        return !empty($data) ? $data['content'] : '';
    }

    /**
     *  Group 是否存在
     * @param integer $groupId
     * @return boolean
     */
    public function isExisted($groupId)
    {
        $this->groupModel->load([
            'id' => $groupId,
            'is_deleted' => 0
        ]);
        if (!empty($this->groupModel->getData())) {
            return true;
        }
        return false;
    }

    /**
     * 创建权限检测
     *
     * @param integer $userId
     * @return boolean
     */
    public function createAccessCheck($userId)
    {
        // 角色限制 TODO
        
        // 已创建的 Group数限制
        if ($this->groupModel->count([
            'is_deleted' => 0,
        ]) >= self::MAX_GROUP_COUNT) {
            $this->_error = '创建的 Group 数量已达上限';
            return false;
        }

        return true;
    }
}
