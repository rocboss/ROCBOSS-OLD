<?php

class Model
{
    protected $_error;

    protected $_db;

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
}
