<?php
namespace xunsearch;

/**
 * XS 异常类定义, XS 所有操作过程发生异常均抛出该实例
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS
 */
class XSException extends \Exception
{

    /**
     * 将类对象转换成字符串
     * @return string 异常的简要描述信息
     */
    public function __toString()
    {
        $string = '[' . __CLASS__ . '] ' . $this->getRelPath($this->getFile()) . '(' . $this->getLine() . '): ';
        $string .= $this->getMessage() . ($this->getCode() > 0 ? '(S#' . $this->getCode() . ')' : '');
        return $string;
    }

    /**
     * 取得相对当前的文件路径
     * @param string $file 需要转换的绝对路径
     * @return string 转换后的相对路径
     */
    public static function getRelPath($file)
    {
        $from = getcwd();
        $file = realpath($file);
        if (is_dir($file)) {
            $pos = false;
            $to = $file;
        } else {
            $pos = strrpos($file, '/');
            $to = substr($file, 0, $pos);
        }
        for ($rel = '';; $rel .= '../') {
            if ($from === $to) {
                break;
            }
            if ($from === dirname($from)) {
                $rel .= substr($to, 1);
                break;
            }
            if (!strncmp($from . '/', $to, strlen($from) + 1)) {
                $rel .= substr($to, strlen($from) + 1);
                break;
            }
            $from = dirname($from);
        }
        if (substr($rel, -1, 1) === '/') {
            $rel = substr($rel, 0, -1);
        }
        if ($pos !== false) {
            $rel .= substr($file, $pos);
        }
        return $rel;
    }
}

?>
