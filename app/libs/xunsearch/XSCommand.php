<?php
namespace xunsearch;

/**
 * XSCommand 命令对象
 * 是与服务端交互的最基本单位, 命令对象可自动转换为通讯字符串,
 * 命令结构参见 C 代码中的 struct xs_cmd 定义, 头部长度为 8字节.
 *
 * @property int $arg 参数, 相当于 (arg1<<8)|arg2 的值
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS
 */
class XSCommand extends XSComponent
{
    /**
     * @var int 命令代码
     * 通常是预定义常量 XS_CMD_xxx, 取值范围 0~255
     */
    public $cmd = XS_CMD_NONE;

    /**
     * @var int 参数1
     * 取值范围 0~255, 具体含义根据不同的 CMD 而变化
     */
    public $arg1 = 0;

    /**
     * @var int 参数2
     * 取值范围 0~255, 常用于存储 value no, 具体参照不同 CMD 而确定
     */
    public $arg2 = 0;

    /**
     * @var string 主数据内容, 最长 2GB
     */
    public $buf = '';

    /**
     * @var string 辅数据内容, 最长 255字节
     */
    public $buf1 = '';

    /**
     * 构造函数
     * @param mixed $cmd 命令类型或命令数组
     *        当类型为 int 表示命令代码, 范围是 1~255, 参见 xs_cmd.inc.php 里的定义
     *        当类型为 array 时忽略其它参数, 可包含 cmd, arg1, arg2, buf, buf1 这些键值
     * @param int $arg1 参数1, 其值为 0~255, 具体含义视不同 CMD 而确定
     * @param int $arg2 参数2, 其值为 0~255, 具体含义视不同 CMD 而确定, 常用于存储 value no
     * @param string $buf 字符串内容, 最大长度为 2GB
     * @param string $buf1 字符串内容1, 最大长度为 255字节
     */
    public function __construct($cmd, $arg1 = 0, $arg2 = 0, $buf = '', $buf1 = '')
    {
        if (is_array($cmd)) {
            foreach ($cmd as $key => $value) {
                if ($key === 'arg' || property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $this->cmd = $cmd;
            $this->arg1 = $arg1;
            $this->arg2 = $arg2;
            $this->buf = $buf;
            $this->buf1 = $buf1;
        }
    }

    /**
     * 转换为封包字符串
     * @return string 用于服务端交互的字符串
     */
    public function __toString()
    {
        if (strlen($this->buf1) > 0xff) {
            $this->buf1 = substr($this->buf1, 0, 0xff);
        }
        return pack('CCCCI', $this->cmd, $this->arg1, $this->arg2, strlen($this->buf1), strlen($this->buf)) . $this->buf . $this->buf1;
    }

    /**
     * 获取属性 arg 的值
     * @return int 参数值
     */
    public function getArg()
    {
        return $this->arg2 | ($this->arg1 << 8);
    }

    /**
     * 设置属性 arg 的值
     * @param int $arg 参数值
     */
    public function setArg($arg)
    {
        $this->arg1 = ($arg >> 8) & 0xff;
        $this->arg2 = $arg & 0xff;
    }
}

?>
