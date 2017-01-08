<?php

class UserModel extends Model
{
    protected $_table = 'roc_user';

    // 获取用户数量
    public function getTotal($condition = [])
    {
        $key = __CLASS__.':'.md5(json_encode([__METHOD__, $condition]));

        return $this->_db->from($this->_table)
                ->where($condition)
                ->count('uid', $key, parent::$_expire);
    }

    // 根据ID获取用户信息
    public function getByUid($uid)
    {
        return $this->_db->from($this->_table)
                ->where(['uid' => $uid, 'valid' => 1])
                ->one();
    }

    // 根据Email获取用户信息
    public function getByEmail($email)
    {
        return $this->_db->from($this->_table)
                ->where(['email' => $email, 'valid' => 1])
                ->one();
    }

    // 根据Phone获取用户信息
    public function getByPhone($phone)
    {
        return $this->_db->from($this->_table)
                ->where(['phone' => $phone, 'valid' => 1])
                ->one();
    }

    // 根据Username获取用户信息
    public function getByUsername($username)
    {
        return $this->_db->from($this->_table)
                ->where(['username' => $username, 'valid' => 1])
                ->one();
    }

    // 根据qq_openid获取用户信息
    public function getByQQOpenID($openID)
    {
        return $this->_db->from($this->_table)
                ->where(['qq_openid' => $openID, 'qq_openid <>' => ''])
                ->one();
    }

    // 根据weibo_openid获取用户信息
    public function getByWeiboID($WeiboID)
    {
        return $this->_db->from($this->_table)
                ->where(['weibo_openid' => $WeiboID, 'weibo_openid <>' => 0])
                ->one();
    }

    // 根据unionid获取用户信息
    public function getByUnionID($unionId)
    {
        return $this->_db->from($this->_table)
                ->where(['wx_unionid' => $unionId, 'wx_unionid <>' => ''])
                ->one();
    }

    // 获取用户列表
    public function getMany($offset, $limit, $condition = ['valid' => 1], $sort = ['sortASC', 'uid'])
    {
        return $this->_db->from($this->_table)
                ->where($condition)
                ->offset($offset)
                ->limit($limit)
                ->$sort[0]($sort[1])
                ->many();
    }

    // 新增用户
    public function addUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = $this->_buildPassword($data['password']);
        }
        if (!isset($data['salt'])) {
            $data['salt'] = $this->buildSalt();
        }
        if (!isset($data['reg_time'])) {
            $data['reg_time'] = time();
        }
        $data['last_time'] = time();

        $result = $this->checkPassConflict($data['email'], $data['username']);

        if ($result === 0) {
            $this->_db->from($this->_table)->insert($data)->execute();

            return $this->_db->insert_id;
        } else {
            return $result;
        }
    }

    // 更新用户信息
    public function updateInfo($data, $uid)
    {
        $this->_db->from($this->_table)
            ->where(['uid' => $uid])
            ->update($data)
            ->execute();

        return $this->_db->affected_rows;
    }

    // 更新用户最后活跃时间
    public function updateLastTime($uid)
    {
        $this->_db->from($this->_table)
            ->where(['uid' => $uid])
            ->update([
                'last_time' => time()
            ])->execute();
    }

    // 检测新增用户是否存在冲突
    public function checkPassConflict($email, $username)
    {
        $ret0 = $this->_db->from($this->_table)
            ->where('email', $email)
            ->one();

        $ret1 = $this->_db->from($this->_table)
            ->where('username', $username)
            ->one();

        if (empty($ret0) && empty($ret1)) {
            return 0;
        } else {
            return !empty($ret0) ? -1 : -2;
        }
    }

    // 检测邮箱是否合法
    public function checkEmailValidity($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";

        return preg_match($pattern, $email) == 0 ? false : true;
    }

    // 检测用户名是否合法
    public function checkNickname($nickname)
    {
        if (strlen($nickname) < 3 || mb_strlen($nickname, 'utf-8') < 2) {
            return '用户名太短了';
        }
        if (mb_strlen($nickname, 'utf-8') > 12) {
            return '用户名太长了';
        }
        if (preg_match('/\s/', $nickname) || strpos($nickname, ' ')) {
            return '用户名不允许存在空格';
        }
        if (is_numeric(substr($nickname, 0, 1)) || substr($nickname, 0, 1) == "_") {
            return '用户名不能以数字和下划线开头';
        }
        if (substr($nickname, -1, 1) == "_") {
            return '用户名不能以下划线结尾';
        }
        if (!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $nickname)) {
            return '用户名只能用汉字、英文、数字及下划线';
        }

        return true;
    }

    // 检测密码是否合法
    public function checkPassword($password)
    {
        return strlen($password) >= 6 && strlen($password) <= 16;
    }

    // 检测用户是否被禁言
    public function checkIsBanned($uid)
    {
        $user = $this->_db->from($this->_table)
                    ->where(['uid' => $uid, 'valid' => 1])
                    ->where('groupid >', 0)
                    ->one();

        if (!empty($user)) {
            return false;
        } else {
            return true;
        }
    }

    // 检测用户余额
    public function getUserScore($uid)
    {
        $user = $this->_db->from($this->_table)
                    ->where(['uid' => $uid, 'valid' => 1])
                    ->select(['score'])
                    ->one();

        if (!empty($user)) {
            return $user['score'];
        } else {
            return 0;
        }
    }

    // 构建密码MD5密文
    private function _buildPassword($password)
    {
        return md5($password);
    }

    /**
     * 构建盐值
     * @method buildSalt
     * @param  integer        $length [description]
     * @return [type]                 [description]
     */
    private function buildSalt($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $string;
    }
}
