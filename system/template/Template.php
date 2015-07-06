<?php
# 模板引擎

namespace system\template;

class Template
{
    public $tpl_dir;
    # 模板文件所在目录 注意以斜杠结束

    public $tpl_ext;
    # 模板文件扩展名

    public $cache_dir;
    # 模板编译后的缓存目录 一样以斜杠结束 无则自动创建

    public $cache_time;
    # 编译后模板的缓存时间
    
    # 自定义的正则替换
    public $my_replace = array();
    
    # 内置的正则替换
    private $system_replace = array(
        '~\{(\$[a-z0-9_]+)\}~i' => '<?php echo $1 ?>', 
        # {$name}

        '~\{(\$[a-z0-9_]+)\.([a-z0-9_]+)\}~i' => '<?php echo $1[\'$2\'] ?>', 
        # {$arr.key}

        '~\{(\$[a-z0-9_]+)\.([a-z0-9_]+)\.([a-z0-9_]+)\}~i' => '<?php echo $1[\'$2\'][\'$3\'] ?>', 
        # {$arr.key.key2}

        '~\{(include_once|require_once|include|require)\s*\(\s*(.+?)\s*\)\s*\s*\}~i' => '<?php include \$this->_include($2, __FILE__) ?>', 
        # {include('inc/top.php')}

        '~\{:(.+?)\}~' => '<?php echo $1 ?>', 
        # {:strip_tags($a)}

        '~\{\~(.+?)\}~' => '<?php $1 ?>', 
        # {~var_dump($a)}

        '~<\?=\s*~' => '<?php echo ',
        # <?=

        '~\{loop\s+(\S+)\s+(\S+)\}~' => '<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>',
        # {loop $array $vaule}

        '~\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}~' => '<?php if(is_array(\\1)) foreach (\\1 as \\2 => \\3) { ?>',
        # {loop $array $key $value}

        '~\{\/loop\}~' => '<?php } ?>',
        # {/loop}

        '~\{if\s+(.+?)\}~' => '<?php if (\\1) { ?>',
        # {if condition}

        '~\{elseif\s+(.+?)\}~' => '<?php }elseif(\\1){ ?>',
        # {elseif condition}

        '~\{else\}~' => '<?php }else{ ?>',
        # {else}

        '~\{\/if\}~' => '<?php } ?>',
        # {/if}

        '~<\?php\s+die\(\'Access Denied\'\);\?>~' => ''
        # 防止模板文件单独被访问
    );

    # 用于存储模板变量
    public $data = array();
    
    public function __construct($config = array())
    {
        if (!empty($config))
        {
            $this->config($config);
        }
    }

    public function config($config)
    {
        if (is_array($config))
        {
            if (isset($config['tpl_dir']))
            {
                $this->tpl_dir = $config['tpl_dir'];
            }
            if (isset($config['tpl_ext']))
            {
                $this->tpl_ext = $config['tpl_ext'];
            }
            if (isset($config['cache_dir']))
            {
                $this->cache_dir = $config['cache_dir'];
            }
            if (isset($config['cache_time']))
            {
                $this->cache_time = $config['cache_time'];
            }
            if (isset($config['my_replace']))
            {
                $this->my_replace = $config['my_replace'];
            }
            if (isset($config['data']))
            {
                $this->data = $config['data'];
            }
        }
    }

    # 赋值
    public function assign($name, $value = NULL)
    {
        if (is_array($name))
        {
            foreach ($name as $k => $v)
            {
                $this->data[$k] = $v;
            }
        }
        else
        {
            $this->data[$name] = &$value;
        }
    }
    
    # 输出页面
    public function display($tpl_file)
    {
        $_cache_path = $this->cache_path($tpl_file);

        if (!$this->is_cached($_cache_path))
        {
            $this->compile($this->tpl_path($tpl_file), $_cache_path);
        }

        unset($tpl_file);

        extract($this->data);

        include $_cache_path;
    }

    # 获取模板文件路径
    private function tpl_path($tpl_file)
    {
        return $this->tpl_dir . $tpl_file . $this->tpl_ext;
    }

    # 获取模板缓存路径
    private function cache_path($tpl_file)
    {
        return $this->cache_dir . $tpl_file . $this->tpl_ext;
    }

    # 模板缓存是否有效
    private function is_cached($cache_path)
    {
        if (!file_exists($cache_path))
        {
            return false;
        }

        if ($this->cache_time < 0)
        {
            return true;
        }
        
        if (time() - filemtime($cache_path) > $this->cache_time)
        {
            return false;
        }

        return true;
    }

    # 编译模板
    private function compile($tpl_path, $cache_path)
    {
        $tpl = @file_get_contents($tpl_path);

        if ($tpl === FALSE)
        {
            die('Template "'.$tpl_path.'" does not exist');
        }
        
        $tmp   = array_merge($this->system_replace, $this->my_replace);

        $cache = preg_replace(array_keys($tmp), $tmp, $tpl);
        
        @mkdir(dirname($cache_path), 0777, true);
        
        $tmp = @file_put_contents($cache_path, $cache, LOCK_EX);

        if ($tmp === FALSE)
        {
            die('Can Not Write Into The Compiled File "'.$cache_path.'"');
        }
    }

    # 页面include
    private function _include($inc_file, $cache_path)
    {
        $inc_path = dirname($cache_path) . '/' . $inc_file;

        if (!$this->is_cached($inc_path))
        {
            $tpl_path = str_replace(realpath($this->cache_dir), realpath($this->tpl_dir), $inc_path);

            $this->compile($tpl_path, $inc_path);
        }

        return $inc_path;
    }

    # 清理缓存
    public function Clean($dir)
    {
        $cachedir = opendir($dir);
        
        while ($file = @readdir($cachedir))
        {
            if ($file != "." && $file != "..")
            {
                unlink($dir . '/' . $file);
            }
        }
        
        closedir($cachedir);
    }
}

?>