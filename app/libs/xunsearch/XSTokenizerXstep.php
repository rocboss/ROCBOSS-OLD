<?php
namespace xunsearch;


/**
 * 内置的步长分词器
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS.tokenizer
 */
class XSTokenizerXstep implements XSTokenizer
{
    private $arg = 2;

    public function __construct($arg = null)
    {
        if ($arg !== null && $arg !== '') {
            $this->arg = intval($arg);
            if ($this->arg < 1 || $this->arg > 255) {
                throw new XSException('Invalid argument for ' . __CLASS__ . ': ' . $arg);
            }
        }
    }

    public function getTokens($value, XSDocument $doc = null)
    {
        $terms = array();
        $i = $this->arg;
        while (true) {
            $terms[] = substr($value, 0, $i);
            if ($i >= strlen($value)) {
                break;
            }
            $i += $this->arg;
        }
        return $terms;
    }
}
?>
