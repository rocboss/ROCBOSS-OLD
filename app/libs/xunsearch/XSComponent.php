<?php
namespace xunsearch;

/**
 * XS 组件基类
 * 封装一些魔术方法, 以实现支持模拟属性
 *
 * 模拟属性通过定义读取函数, 写入函数来实现, 允许两者缺少其中一个
 * 这类属性可以跟正常定义的属性一样存取, 但是这类属性名称不区分大小写. 例:
 * <pre>
 * $a = $obj->text; // $a 值等于 $obj->getText() 的返回值
 * $obj->text = $a; // 等同事调用 $obj->setText($a)
 * </pre>
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS
 */
class XSComponent
{

    /**
     * 魔术方法 __get
     * 取得模拟属性的值, 内部实际调用 getXxx 方法的返回值
     * @param string $name 属性名称
     * @return mixed 属性值
     * @throw XSException 属性不存在或不可读时抛出异常
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        // throw exception
        $msg = method_exists($this, 'set' . $name) ? 'Write-only' : 'Undefined';
        $msg .= ' property: ' . get_class($this) . '::$' . $name;
        throw new XSException($msg);
    }

    /**
     * 魔术方法 __set
     * 设置模拟属性的值, 内部实际是调用 setXxx 方法
     * @param string $name 属性名称
     * @param mixed $value 属性值
     * @throw XSException 属性不存在或不可写入时抛出异常
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        }

        // throw exception
        $msg = method_exists($this, 'get' . $name) ? 'Read-only' : 'Undefined';
        $msg .= ' property: ' . get_class($this) . '::$' . $name;
        throw new XSException($msg);
    }

    /**
     * 魔术方法 __isset
     * 判断模拟属性是否存在并可读取
     * @param string $name 属性名称
     * @return bool 若存在为 true, 反之为 false
     */
    public function __isset($name)
    {
        return method_exists($this, 'get' . $name);
    }

    /**
     * 魔术方法 __unset
     * 删除、取消模拟属性, 相当于设置属性值为 null
     * @param string $name 属性名称
     */
    public function __unset($name)
    {
        $this->__set($name, null);
    }
}

?>
