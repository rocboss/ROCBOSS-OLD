<?php

class Model
{
    protected $_error;

    protected $_db;

    /**
     * 实例化当前Model
     * @method m
     * @return [type] [description]
     */
    public static function m()
    {
        $class = get_called_class();

        return Roc::model(substr($class, 0, (strlen($class) - 5)));
    }

    // 默认Cache缓存时间
    protected static $_expire = 3600;

    protected static $redis;

    public function getError()
    {
        return $this->_error;
    }

    public function setDb(DBEngine $db)
    {
        $this->_db = $db;

        self::$redis = Roc::redis();

        self::$redis->select(Roc::get('redis.db'));

        $this->_db->setCache(self::$redis);
    }

    /**
     * 调用Redis
     * @method redis
     * @return boolean [description]
     */
    public function redis()
    {
        return self::$redis;
    }

    /**
     * 批量清除Model缓存
     * @method clearCache
     * @param  [type]     $class [description]
     * @return [type]            [description]
     */
    public function clearCache($class)
    {
        $keys = self::$redis->keys($class.':*');

        if (!empty($keys))  {
            foreach ($keys as $key)  {
                self::$redis->del($key);
            }
        }
    }

    /**
     * 获取单行数据
     * @method get
     * @param  [type] $key [description]
     * @param  [type] $val [description]
     * @return [type]      [description]
     */
    public function get($key, $val)
    {
        return $this->_db->from($this->_table)
                ->where([$key => $val])
                ->one();
    }

    /**
     * 新增数据
     * @method add
     * @param  [type] $data [description]
     */
    public function add($data)
    {
        $this->_db->from($this->_table)
            ->insert($data)
            ->execute();

        return $this->_db->insert_id;
    }

    /**
     * 更新
     * @method update
     * @param  [type]        $id   [description]
     * @param  [type]        $data [description]
     * @return [type]              [description]
     */
    public function update($id, $data)
    {
        $this->_db->from($this->_table)
                ->where(['id' => $id])
                ->update($data)
                ->execute();

        return $this->_db->affected_rows;
    }
}
