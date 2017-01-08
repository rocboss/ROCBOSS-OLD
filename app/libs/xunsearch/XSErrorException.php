<?php
namespace xunsearch;

/**
 * XS 错误异常类定义, XS 所有操作过程发生错误均抛出该实例
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS
 */
class XSErrorException extends XSException
{
    private $_file, $_line;

    /**
     * 构造函数
     * 将 $file, $line 记录到私有属性在 __toString 中使用
     * @param int $code 出错代码
     * @param string $message 出错信息
     * @param string $file 出错所在文件
     * @param int $line 出错所在的行数
     * @param Exception $previous
     */
    public function __construct($code, $message, $file, $line, $previous = null)
    {
        $this->_file = $file;
        $this->_line = $line;
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }

    /**
     * 将类对象转换成字符串
     * @return string 异常的简要描述信息
     */
    public function __toString()
    {
        $string = '[' . __CLASS__ . '] ' . $this->getRelPath($this->_file) . '(' . $this->_line . '): ';
        $string .= $this->getMessage() . '(' . $this->getCode() . ')';
        return $string;
    }
}
?>
