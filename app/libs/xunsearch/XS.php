<?php
namespace xunsearch;
/**
 * XS 主类定义文件
 *
 * @author hightman
 * @link http://www.xunsearch.com/
 * @copyright Copyright &copy; 2011 HangZhou YunSheng Network Technology Co., Ltd.
 * @license http://www.xunsearch.com/license/
 * @version $Id$
 *
 * <ul>
 * <li>XS 是 XunSearch 的统一缩写, XS 是解决方案而不仅仅针对搜索, 还包括索引管理等</li>
 * <li>XS 运行环境要求 PHP 5.2.0 及以上版本, 带有 SPL 扩展</li>
 * <li>如果您的数据包含 utf-8 以外的编码(如: gbk), 则要求安装 mbstring 或 iconv 以便转换编码</li>
 * <li>对于 bool 类型函数/方法若无特别说明, 均表示成功返回 true, 失败返回 false</li>
 * <li>对于致命的异常情况均抛出类型为 XSException 的异常, 应将 xs 所有操作放入 try/catch 区块</li>
 * <li>这只是 XunSearch 项目客户端的 PHP 实现, 需要配合 xunsearch 服务端协同工作</li>
 * </ul>
 *
 * 用法简例:
 * <pre>
 * try {
 *   // 创建 xs 实例 (包含3个字段 id, title, content)
 *   $xs = new XS('etc/sample.ini');
 *
 *   // 索引管理
 *   $doc = new XSDocument('gbk');
 *
 *   // 新增/根据主键更新数据
 *   $doc->id = 123;
 *   $doc->title = '您好, 世界!';
 *   $doc->setFields(array('content' => '英文说法是: Hello, the world!'));
 *   $xs->index->add($doc);
 *
 *   $doc->title = '世界, 你好!';
 *   $xs->index->update($doc);
 *
 *   $xs->index->del(124); // 删除单条主键为 124 的数据
 *   $xs->index->del(array(125, 126, 129)); // 批量删除 3条数据
 *
 *   // 正常检索
 *   // 快速检索取得结果
 *   // 快速检索匹配数量(估算)
 *
 * } catch (XSException $e) {
 *   echo $e . "<br />\n";
 * }
 * </pre>
 */
 /* Automatically generated at 2016/08/25 13:27 */
define('XS_CMD_NONE', 0);
define('XS_CMD_DEFAULT', XS_CMD_NONE);
define('XS_CMD_PROTOCOL', 20110707);
define('XS_CMD_USE', 1);
define('XS_CMD_HELLO', 1);
define('XS_CMD_DEBUG', 2);
define('XS_CMD_TIMEOUT', 3);
define('XS_CMD_QUIT', 4);
define('XS_CMD_INDEX_SET_DB', 32);
define('XS_CMD_INDEX_GET_DB', 33);
define('XS_CMD_INDEX_SUBMIT', 34);
define('XS_CMD_INDEX_REMOVE', 35);
define('XS_CMD_INDEX_EXDATA', 36);
define('XS_CMD_INDEX_CLEAN_DB', 37);
define('XS_CMD_DELETE_PROJECT', 38);
define('XS_CMD_INDEX_COMMIT', 39);
define('XS_CMD_INDEX_REBUILD', 40);
define('XS_CMD_FLUSH_LOGGING', 41);
define('XS_CMD_INDEX_SYNONYMS', 42);
define('XS_CMD_INDEX_USER_DICT', 43);
define('XS_CMD_SEARCH_DB_TOTAL', 64);
define('XS_CMD_SEARCH_GET_TOTAL', 65);
define('XS_CMD_SEARCH_GET_RESULT', 66);
define('XS_CMD_SEARCH_SET_DB', XS_CMD_INDEX_SET_DB);
define('XS_CMD_SEARCH_GET_DB', XS_CMD_INDEX_GET_DB);
define('XS_CMD_SEARCH_ADD_DB', 68);
define('XS_CMD_SEARCH_FINISH', 69);
define('XS_CMD_SEARCH_DRAW_TPOOL', 70);
define('XS_CMD_SEARCH_ADD_LOG', 71);
define('XS_CMD_SEARCH_GET_SYNONYMS', 72);
define('XS_CMD_SEARCH_SCWS_GET', 73);
define('XS_CMD_QUERY_GET_STRING', 96);
define('XS_CMD_QUERY_GET_TERMS', 97);
define('XS_CMD_QUERY_GET_CORRECTED', 98);
define('XS_CMD_QUERY_GET_EXPANDED', 99);
define('XS_CMD_OK', 128);
define('XS_CMD_ERR', 129);
define('XS_CMD_SEARCH_RESULT_DOC', 140);
define('XS_CMD_SEARCH_RESULT_FIELD', 141);
define('XS_CMD_SEARCH_RESULT_FACETS', 142);
define('XS_CMD_SEARCH_RESULT_MATCHED', 143);
define('XS_CMD_DOC_TERM', 160);
define('XS_CMD_DOC_VALUE', 161);
define('XS_CMD_DOC_INDEX', 162);
define('XS_CMD_INDEX_REQUEST', 163);
define('XS_CMD_IMPORT_HEADER', 191);
define('XS_CMD_SEARCH_SET_SORT', 192);
define('XS_CMD_SEARCH_SET_CUT', 193);
define('XS_CMD_SEARCH_SET_NUMERIC', 194);
define('XS_CMD_SEARCH_SET_COLLAPSE', 195);
define('XS_CMD_SEARCH_KEEPALIVE', 196);
define('XS_CMD_SEARCH_SET_FACETS', 197);
define('XS_CMD_SEARCH_SCWS_SET', 198);
define('XS_CMD_SEARCH_SET_CUTOFF', 199);
define('XS_CMD_SEARCH_SET_MISC', 200);
define('XS_CMD_QUERY_INIT', 224);
define('XS_CMD_QUERY_PARSE', 225);
define('XS_CMD_QUERY_TERM', 226);
define('XS_CMD_QUERY_TERMS', 232);
define('XS_CMD_QUERY_RANGEPROC', 227);
define('XS_CMD_QUERY_RANGE', 228);
define('XS_CMD_QUERY_VALCMP', 229);
define('XS_CMD_QUERY_PREFIX', 230);
define('XS_CMD_QUERY_PARSEFLAG', 231);
define('XS_CMD_SORT_TYPE_RELEVANCE', 0);
define('XS_CMD_SORT_TYPE_DOCID', 1);
define('XS_CMD_SORT_TYPE_VALUE', 2);
define('XS_CMD_SORT_TYPE_MULTI', 3);
define('XS_CMD_SORT_TYPE_GEODIST', 4);
define('XS_CMD_SORT_TYPE_MASK', 0x3f);
define('XS_CMD_SORT_FLAG_RELEVANCE', 0x40);
define('XS_CMD_SORT_FLAG_ASCENDING', 0x80);
define('XS_CMD_QUERY_OP_AND', 0);
define('XS_CMD_QUERY_OP_OR', 1);
define('XS_CMD_QUERY_OP_AND_NOT', 2);
define('XS_CMD_QUERY_OP_XOR', 3);
define('XS_CMD_QUERY_OP_AND_MAYBE', 4);
define('XS_CMD_QUERY_OP_FILTER', 5);
define('XS_CMD_RANGE_PROC_STRING', 0);
define('XS_CMD_RANGE_PROC_DATE', 1);
define('XS_CMD_RANGE_PROC_NUMBER', 2);
define('XS_CMD_VALCMP_LE', 0);
define('XS_CMD_VALCMP_GE', 1);
define('XS_CMD_PARSE_FLAG_BOOLEAN', 1);
define('XS_CMD_PARSE_FLAG_PHRASE', 2);
define('XS_CMD_PARSE_FLAG_LOVEHATE', 4);
define('XS_CMD_PARSE_FLAG_BOOLEAN_ANY_CASE', 8);
define('XS_CMD_PARSE_FLAG_WILDCARD', 16);
define('XS_CMD_PARSE_FLAG_PURE_NOT', 32);
define('XS_CMD_PARSE_FLAG_PARTIAL', 64);
define('XS_CMD_PARSE_FLAG_SPELLING_CORRECTION', 128);
define('XS_CMD_PARSE_FLAG_SYNONYM', 256);
define('XS_CMD_PARSE_FLAG_AUTO_SYNONYMS', 512);
define('XS_CMD_PARSE_FLAG_AUTO_MULTIWORD_SYNONYMS', 1536);
define('XS_CMD_PREFIX_NORMAL', 0);
define('XS_CMD_PREFIX_BOOLEAN', 1);
define('XS_CMD_INDEX_WEIGHT_MASK', 0x3f);
define('XS_CMD_INDEX_FLAG_WITHPOS', 0x40);
define('XS_CMD_INDEX_FLAG_SAVEVALUE', 0x80);
define('XS_CMD_INDEX_FLAG_CHECKSTEM', 0x80);
define('XS_CMD_VALUE_FLAG_NUMERIC', 0x80);
define('XS_CMD_INDEX_REQUEST_ADD', 0);
define('XS_CMD_INDEX_REQUEST_UPDATE', 1);
define('XS_CMD_INDEX_SYNONYMS_ADD', 0);
define('XS_CMD_INDEX_SYNONYMS_DEL', 1);
define('XS_CMD_SEARCH_MISC_SYN_SCALE', 1);
define('XS_CMD_SEARCH_MISC_MATCHED_TERM', 2);
define('XS_CMD_SCWS_GET_VERSION', 1);
define('XS_CMD_SCWS_GET_RESULT', 2);
define('XS_CMD_SCWS_GET_TOPS', 3);
define('XS_CMD_SCWS_HAS_WORD', 4);
define('XS_CMD_SCWS_GET_MULTI', 5);
define('XS_CMD_SCWS_SET_IGNORE', 50);
define('XS_CMD_SCWS_SET_MULTI', 51);
define('XS_CMD_SCWS_SET_DUALITY', 52);
define('XS_CMD_SCWS_SET_DICT', 53);
define('XS_CMD_SCWS_ADD_DICT', 54);
define('XS_CMD_ERR_UNKNOWN', 600);
define('XS_CMD_ERR_NOPROJECT', 401);
define('XS_CMD_ERR_TOOLONG', 402);
define('XS_CMD_ERR_INVALIDCHAR', 403);
define('XS_CMD_ERR_EMPTY', 404);
define('XS_CMD_ERR_NOACTION', 405);
define('XS_CMD_ERR_RUNNING', 406);
define('XS_CMD_ERR_REBUILDING', 407);
define('XS_CMD_ERR_WRONGPLACE', 450);
define('XS_CMD_ERR_WRONGFORMAT', 451);
define('XS_CMD_ERR_EMPTYQUERY', 452);
define('XS_CMD_ERR_TIMEOUT', 501);
define('XS_CMD_ERR_IOERR', 502);
define('XS_CMD_ERR_NOMEM', 503);
define('XS_CMD_ERR_BUSY', 504);
define('XS_CMD_ERR_UNIMP', 505);
define('XS_CMD_ERR_NODB', 506);
define('XS_CMD_ERR_DBLOCKED', 507);
define('XS_CMD_ERR_CREATE_HOME', 508);
define('XS_CMD_ERR_INVALID_HOME', 509);
define('XS_CMD_ERR_REMOVE_HOME', 510);
define('XS_CMD_ERR_REMOVE_DB', 511);
define('XS_CMD_ERR_STAT', 512);
define('XS_CMD_ERR_OPEN_FILE', 513);
define('XS_CMD_ERR_TASK_CANCELED', 514);
define('XS_CMD_ERR_XAPIAN', 515);
define('XS_CMD_OK_INFO', 200);
define('XS_CMD_OK_PROJECT', 201);
define('XS_CMD_OK_QUERY_STRING', 202);
define('XS_CMD_OK_DB_TOTAL', 203);
define('XS_CMD_OK_QUERY_TERMS', 204);
define('XS_CMD_OK_QUERY_CORRECTED', 205);
define('XS_CMD_OK_SEARCH_TOTAL', 206);
define('XS_CMD_OK_RESULT_BEGIN', XS_CMD_OK_SEARCH_TOTAL);
define('XS_CMD_OK_RESULT_END', 207);
define('XS_CMD_OK_TIMEOUT_SET', 208);
define('XS_CMD_OK_FINISHED', 209);
define('XS_CMD_OK_LOGGED', 210);
define('XS_CMD_OK_RQST_FINISHED', 250);
define('XS_CMD_OK_DB_CHANGED', 251);
define('XS_CMD_OK_DB_INFO', 252);
define('XS_CMD_OK_DB_CLEAN', 253);
define('XS_CMD_OK_PROJECT_ADD', 254);
define('XS_CMD_OK_PROJECT_DEL', 255);
define('XS_CMD_OK_DB_COMMITED', 256);
define('XS_CMD_OK_DB_REBUILD', 257);
define('XS_CMD_OK_LOG_FLUSHED', 258);
define('XS_CMD_OK_DICT_SAVED', 259);
define('XS_CMD_OK_RESULT_SYNONYMS', 280);
define('XS_CMD_OK_SCWS_RESULT', 290);
define('XS_CMD_OK_SCWS_TOPS', 291);
define('XS_PACKAGE_BUGREPORT', "http://www.xunsearch.com/bugs");
define('XS_PACKAGE_NAME', "xunsearch");
define('XS_PACKAGE_TARNAME', "xunsearch");
define('XS_PACKAGE_URL', "");
define('XS_PACKAGE_VERSION', "1.4.10");
define('XS_LIB_ROOT', dirname(__FILE__));
 /* end the cmd defination */

/**
 * XS 搜索项目主类
 *
 * @property XSFieldScheme $scheme 当前在用的字段方案
 * @property string $defaultCharset 默认字符集编码
 * @property-read string $name 项目名称
 * @property-read XSIndex $index 索引操作对象
 * @property-read XSSearch $search 搜索操作对象
 * @property-read XSFieldMeta $idField 主键字段
 * @author hightman <hightman@twomice.net>
 * @version 1.0.0
 * @package XS
 */
class XS extends XSComponent
{
    /**
     * @var XSIndex 索引操作对象
     */
    private $_index;

    /**
     * @var XSSearch 搜索操作对象
     */
    private $_search;

    /**
     * @var XSServer scws 分词服务器
     */
    private $_scws;

    /**
     * @var XSFieldScheme 当前字段方案
     */
    private $_scheme, $_bindScheme;
    private $_config;

    /**
     * @var XS 最近创建的 XS 对象
     */
    private static $_lastXS;

    /**
     * 构造函数
     * 特别说明一个小技巧, 参数 $file 可以直接是配置文件的内容, 还可以是仅仅是文件名,
     * 如果只是文件名会自动查找 XS_LIB_ROOT/../app/$file.ini
     * @param string $file 要加载的项目配置文件
     */
    public function __construct($file)
    {
        if (strlen($file) < 255 && !is_file($file)) {
            $appRoot = getenv('XS_APP_ROOT');
            if ($appRoot === false) {
                $appRoot = defined('XS_APP_ROOT') ? XS_APP_ROOT : XS_LIB_ROOT . '/../app';
            }
            $file2 = $appRoot . '/' . $file . '.ini';
            if (is_file($file2)) {
                $file = $file2;
            }
        }
        $this->loadIniFile($file);
        self::$_lastXS = $this;
    }

    /**
     * 析构函数
     * 由于对象交叉引用, 如需提前销毁对象, 请强制调用该函数
     */
    public function __destruct()
    {
        $this->_index = null;
        $this->_search = null;
    }

    /**
     * 获取最新的 XS 实例
     * @return XS 最近创建的 XS　对象
     */
    public static function getLastXS()
    {
        return self::$_lastXS;
    }

    /**
     * 获取当前在用的字段方案
     * 通用于搜索结果文档和修改、添加的索引文档
     * @return XSFieldScheme 当前字段方案
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * 设置当前在用的字段方案
     * @param XSFieldScheme $fs 一个有效的字段方案对象
     * @throw XSException 无效方案则直接抛出异常
     */
    public function setScheme(XSFieldScheme $fs)
    {
        $fs->checkValid(true);
        $this->_scheme = $fs;
        if ($this->_search !== null) {
            $this->_search->markResetScheme();
        }
    }

    /**
     * 还原字段方案为项目绑定方案
     */
    public function restoreScheme()
    {
        if ($this->_scheme !== $this->_bindScheme) {
            $this->_scheme = $this->_bindScheme;
            if ($this->_search !== null) {
                $this->_search->markResetScheme(true);
            }
        }
    }

    /**
     * @return array 获取配置原始数据
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 获取当前项目名称
     * @return string 当前项目名称
     */
    public function getName()
    {
        return $this->_config['project.name'];
    }

    /**
     * 修改当前项目名称
     * 注意，必须在 {@link getSearch} 和 {@link getIndex} 前调用才能起作用
     * @param string $name 项目名称
     */
    public function setName($name)
    {
        $this->_config['project.name'] = $name;
    }

    /**
     * 获取项目的默认字符集
     * @return string 默认字符集(已大写)
     */
    public function getDefaultCharset()
    {
        return isset($this->_config['project.default_charset']) ?
            strtoupper($this->_config['project.default_charset']) : 'UTF-8';
    }

    /**
     * 改变项目的默认字符集
     * @param string $charset 修改后的字符集
     */
    public function setDefaultCharset($charset)
    {
        $this->_config['project.default_charset'] = strtoupper($charset);
    }

    /**
     * 获取索引操作对象
     * @return XSIndex 索引操作对象
     */
    public function getIndex()
    {
        if ($this->_index === null) {
            $adds = array();
            $conn = isset($this->_config['server.index']) ? $this->_config['server.index'] : 8383;
            if (($pos = strpos($conn, ';')) !== false) {
                $adds = explode(';', substr($conn, $pos + 1));
                $conn = substr($conn, 0, $pos);
            }
            $this->_index = new XSIndex($conn, $this);
            $this->_index->setTimeout(0);
            foreach ($adds as $conn) {
                $conn = trim($conn);
                if ($conn !== '') {
                    $this->_index->addServer($conn)->setTimeout(0);
                }
            }
        }
        return $this->_index;
    }

    /**
     * 获取搜索操作对象
     * @return XSSearch 搜索操作对象
     */
    public function getSearch()
    {
        if ($this->_search === null) {
            $conns = array();
            if (!isset($this->_config['server.search'])) {
                $conns[] = 8384;
            } else {
                foreach (explode(';', $this->_config['server.search']) as $conn) {
                    $conn = trim($conn);
                    if ($conn !== '') {
                        $conns[] = $conn;
                    }
                }
            }
            if (count($conns) > 1) {
                shuffle($conns);
            }
            for ($i = 0; $i < count($conns); $i++) {
                try {
                    $this->_search = new XSSearch($conns[$i], $this);
                    $this->_search->setCharset($this->getDefaultCharset());
                    return $this->_search;
                } catch (XSException $e) {
                    if (($i + 1) === count($conns)) {
                        throw $e;
                    }
                }
            }
        }
        return $this->_search;
    }

    /**
     * 创建 scws 分词连接
     * @return XSServer 分词服务器
     */
    public function getScwsServer()
    {
        if ($this->_scws === null) {
            $conn = isset($this->_config['server.search']) ? $this->_config['server.search'] : 8384;
            $this->_scws = new XSServer($conn, $this);
        }
        return $this->_scws;
    }

    /**
     * 获取当前主键字段
     * @return XSFieldMeta 类型为 ID 的字段
     * @see XSFieldScheme::getFieldId
     */
    public function getFieldId()
    {
        return $this->_scheme->getFieldId();
    }

    /**
     * 获取当前标题字段
     * @return XSFieldMeta 类型为 TITLE 的字段
     * @see XSFieldScheme::getFieldTitle
     */
    public function getFieldTitle()
    {
        return $this->_scheme->getFieldTitle();
    }

    /**
     * 获取当前内容字段
     * @return XSFieldMeta 类型为 BODY 的字段
     * @see XSFieldScheme::getFieldBody
     */
    public function getFieldBody()
    {
        return $this->_scheme->getFieldBody();
    }

    /**
     * 获取项目字段元数据
     * @param mixed $name 字段名称(string) 或字段序号(vno, int)
     * @param bool $throw 当字段不存在时是否抛出异常, 默认为 true
     * @return XSFieldMeta 字段元数据对象
     * @throw XSException 当字段不存在并且参数 throw 为 true 时抛出异常
     * @see XSFieldScheme::getField
     */
    public function getField($name, $throw = true)
    {
        return $this->_scheme->getField($name, $throw);
    }

    /**
     * 获取项目所有字段结构设置
     * @return XSFieldMeta[]
     */
    public function getAllFields()
    {
        return $this->_scheme->getAllFields();
    }

    /**
     * 智能加载类库文件
     * 要求以 Name.class.php 命名并与本文件存放在同一目录, 如: XSTokenizerXxx.class.php
     * @param string $name 类的名称
     */
    public static function autoload($name)
    {
        $file = XS_LIB_ROOT . '/' . $name . '.class.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * 字符集转换
     * 要求安装有 mbstring, iconv 中的一种
     * @param mixed $data 需要转换的数据, 支持 string 和 array, 数组会自动递归转换
     * @param string $to 转换后的字符集
     * @param string $from 转换前的字符集
     * @return mixed 转换后的数据
     * @throw XSEXception 如果没有合适的转换函数抛出异常
     */
    public static function convert($data, $to, $from)
    {
        // need not convert
        if ($to == $from) {
            return $data;
        }
        // array traverse
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::convert($value, $to, $from);
            }
            return $data;
        }
        // string contain 8bit characters
        if (is_string($data) && preg_match('/[\x81-\xfe]/', $data)) {
            // mbstring, iconv, throw ...
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($data, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to . '//TRANSLIT', $data);
            } else {
                throw new XSException('Cann\'t find the mbstring or iconv extension to convert encoding');
            }
        }
        return $data;
    }

    /**
     * 计算经纬度距离
     * @param float $lon1 原点经度
     * @param float $lat1 原点纬度
     * @param float $lon2 目标点经度
     * @param float $lat2 目标点纬度
     * @return float 两点大致距离，单位：米
     */
    public static function geoDistance($lon1, $lat1, $lon2, $lat2)
    {
        $dx = $lon1 - $lon2;
        $dy = $lat1 - $lat2;
        $b = ($lat1 + $lat2) / 2;
        $lx = 6367000.0 * deg2rad($dx) * cos(deg2rad($b));
        $ly = 6367000.0 * deg2rad($dy);
        return sqrt($lx * $lx + $ly * $ly);
    }

    /**
     * 解析INI配置文件
     * 由于 PHP 自带的 parse_ini_file 存在一些不兼容，故自行简易实现
     * @param string $data 文件内容
     * @return array 解析后的结果
     */
    private function parseIniData($data)
    {
        $ret = array();
        $cur = &$ret;
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            if ($line === '' || $line[0] == ';' || $line[0] == '#') {
                continue;
            }
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if ($line[0] === '[' && substr($line, -1, 1) === ']') {
                $sec = substr($line, 1, -1);
                $ret[$sec] = array();
                $cur = &$ret[$sec];
                continue;
            }
            if (($pos = strpos($line, '=')) === false) {
                continue;
            }
            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1), " '\t\"");
            $cur[$key] = $value;
        }
        return $ret;
    }

    /**
     * 加载项目配置文件
     * @param string $file 配置文件路径
     * @throw XSException 出错时抛出异常
     * @see XSFieldMeta::fromConfig
     */
    private function loadIniFile($file)
    {
        // check cache
        $cache = false;
        $cache_write = '';
        if (strlen($file) < 255 && file_exists($file)) {
            $cache_key = md5(__CLASS__ . '::ini::' . realpath($file));
            if (function_exists('apc_fetch')) {
                $cache = apc_fetch($cache_key);
                $cache_write = 'apc_store';
            } elseif (function_exists('xcache_get') && php_sapi_name() !== 'cli') {
                $cache = xcache_get($cache_key);
                $cache_write = 'xcache_set';
            } elseif (function_exists('eaccelerator_get')) {
                $cache = eaccelerator_get($cache_key);
                $cache_write = 'eaccelerator_put';
            }
            if ($cache && isset($cache['mtime']) && isset($cache['scheme'])
                && filemtime($file) <= $cache['mtime']) {
                // cache HIT
                $this->_scheme = $this->_bindScheme = unserialize($cache['scheme']);
                $this->_config = $cache['config'];
                return;
            }
            $data = file_get_contents($file);
        } else {
            // parse ini string
            $data = $file;
            $file = substr(md5($file), 8, 8) . '.ini';
        }

        // parse ini file
        $this->_config = $this->parseIniData($data);
        if ($this->_config === false) {
            throw new XSException('Failed to parse project config file/string: \'' . substr($file, 0, 10) . '...\'');
        }

        // create the scheme object
        $scheme = new XSFieldScheme;
        foreach ($this->_config as $key => $value) {
            if (is_array($value)) {
                $scheme->addField($key, $value);
            }
        }
        $scheme->checkValid(true);

        // load default config
        if (!isset($this->_config['project.name'])) {
            $this->_config['project.name'] = basename($file, '.ini');
        }

        // save to cache
        $this->_scheme = $this->_bindScheme = $scheme;
        if ($cache_write != '') {
            $cache['mtime'] = filemtime($file);
            $cache['scheme'] = serialize($this->_scheme);
            $cache['config'] = $this->_config;
            call_user_func($cache_write, $cache_key, $cache);
        }
    }
}

/**
 * Add autoload handler to search classes on current directory
 * Class file should be named as Name.class.php
 */
// spl_autoload_register('XS::autoload', true, true);

/**
 * 修改默认的错误处理函数
 * 把发生的错误修改为抛出异常, 方便统一处理
 */
function xsErrorHandler($errno, $error, $file, $line)
{
    if (($errno & ini_get('error_reporting')) && !strncmp($file, XS_LIB_ROOT, strlen(XS_LIB_ROOT))) {
        throw new XSErrorException($errno, $error, $file, $line);
    }
    return false;
}
set_error_handler('xunsearch\xsErrorHandler');
