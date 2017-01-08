<?php
namespace xunsearch;
/**
 * XSTokenizer 接口和内置分词器文件
 *
 * @author hightman
 * @link http://www.xunsearch.com/
 * @copyright Copyright &copy; 2011 HangZhou YunSheng Network Technology Co., Ltd.
 * @license http://www.xunsearch.com/license/
 * @version $Id$
 */

/**
 * 自定义字段词法分析器接口
 * 系统将按照 {@link getTokens} 返回的词汇列表对相应的字段建立索引
 *
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS.tokenizer
 */
interface XSTokenizer
{
    /**
     * 内置分词器定义(常量)
     */
    const DFL = 0;

    /**
     * 执行分词并返回词列表
     * @param string $value 待分词的字段值(UTF-8编码)
     * @param XSDocument $doc 当前相关的索引文档
     * @return array 切好的词组成的数组
     */
    public function getTokens($value, XSDocument $doc = null);
}
