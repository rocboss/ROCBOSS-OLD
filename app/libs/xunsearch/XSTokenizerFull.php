<?php
namespace xunsearch;

/**
 * 内置整值分词器
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS.tokenizer
 */
class XSTokenizerFull implements XSTokenizer
{

    public function getTokens($value, XSDocument $doc = null)
    {
        return array($value);
    }
}

?>
