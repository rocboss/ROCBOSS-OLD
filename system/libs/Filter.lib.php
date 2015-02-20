<?php
class Filter
{
    /**
     * 字符串过滤
     * @param string|array $date
     * @param boolean $force
     * @return string|array
     */
    public static function in($data, $force = false)
    {
        if (is_string($data))
        {
            if ($force == true || !get_magic_quotes_gpc())
            {
                $data = addslashes(trim($data));
            }
            return trim($data);
        }
        else
        {
            if (is_array($data))
            {
                foreach ($data as $key => $value)
                {
                    $data[$key] = self::in($value, $force);
                }
                return $data;
            }
            else
            {
                return $data;
            }
        }
    }

    /**
     * 字符串还原
     * @param string|array $date
     * @return string|array
     */
    public static function out($data)
    {
        if (is_string($data))
        {            
            return $data = stripslashes($data);
        }
        else
        {
            if (is_array($data))
            {
                foreach ($data as $key => $value)
                {
                    $data[$key] = self::out($value);
                }
                return $data;
            }
            else
            {
                return $data;
            }
        }
    }

    /**
     * 帖子过滤处理
     * @param string $date
     * @return string
     */
    public static function topicIn($data)
    {
        $data = strip_tags(str_replace(array("\r","\n","\t"), "[p]", $data), '[p]');

        return self::in($data);
    }

    /**
     * 帖子输出处理
     * @param string $date
     * @return string
     */
    public static function topicOut($data)
    {
        $data = self::out($data);

        $data = htmlspecialchars($data);

        $data = str_replace("[p]", "</p><p>", $data);
        
        return trim($data);
    }
}
?>