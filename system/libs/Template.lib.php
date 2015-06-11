<?php
class Template
{
    public $leftTag = "<!--{";
    public $rightTag = "}-->";
    public $tplDir;
    public $tplExt;
    public $cacheDir;
    public $cacheTime;
    public $data = array();
    public $jsonArray = array();
    
    public function __construct($CONFIG = NULL)
    {
        if ($CONFIG)
        {
            $this->config($CONFIG);
        }
    }
    public function config($CONFIG)
    {
        if (is_string($CONFIG))
        {
            $CONFIG = require $CONFIG;
        }
        if (isset($CONFIG['tplDir']))
        {
            $this->tplDir = $CONFIG['tplDir'];
        }
        if (isset($CONFIG['tplExt']))
        {
            $this->tplExt = $CONFIG['tplExt'];
        }
        if (isset($CONFIG['cacheDir']))
        {
            $this->cacheDir = $CONFIG['cacheDir'];
        }
        if (isset($CONFIG['cacheTime']))
        {
            $this->cacheTime = $CONFIG['cacheTime'];
        }
        if (isset($CONFIG['my_rep']))
        {
            $this->my_rep = $CONFIG['my_rep'];
        }
        if (isset($CONFIG['data']))
        {
            $this->data = $CONFIG['data'];
        }
    }
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
            $this->data[$name] =& $value;
        }
    }
    public function display($tplFile)
    {
        $_cache_path = $this->cache_path($tplFile);
        
        if (!$this->is_cached($_cache_path))
        {
            $this->compile($this->tpl_path($tplFile), $_cache_path);
        }
        
        unset($tplFile);
        
        extract($this->data);
        
        include $_cache_path;
    }
    
    public function  displayJson($tplFile){
        
        $_cache_path = $this->cache_path($tplFile);
        
        if (!$this->is_cached($_cache_path))
        {
            $this->compile($this->tpl_path($tplFile), $_cache_path);
        }
        
        unset($tplFile);
        
        extract($this->data);
        
        include $_cache_path;
        
    }

        public function fetch($tplFile)
    {
        ob_start();
        
        ob_implicit_flush(0);
        
        $this->display($tplFile);
        
        return ob_get_clean();
    }
    public function tpl_path($tplFile)
    {
        return $this->tplDir . $tplFile . $this->tplExt;
    }
    private function cache_path($tplFile)
    {
        return $this->cacheDir . $tplFile . $this->tplExt . '.php';
    }
    public function is_cached($cache_path)
    {
        if (!file_exists($cache_path))
        {
            return false;
        }
        
        if ($this->cacheTime < 0)
        {
            return true;
        }
        
        $cacheTime = filemtime($cache_path);
        
        if (time() - $cacheTime > $this->cacheTime)
        {
            return false;
        }
        return true;
    }
    public function compile($tpl_path, $cache_path)
    {
        $tpl = @file_get_contents($tpl_path);
        
        if ($tpl === FALSE)
        {
            die("Template " . $tpl_path . " Does Not Exist");
        }
        
        $cache = $this->replace($tpl);
        
        @mkdir(dirname($cache_path), 0777, true);
        
        $tmp = @file_put_contents($cache_path, $cache, LOCK_EX);
        
        if ($tmp === FALSE)
        {
            die("Can Not Write Into The Compiled File " . $cache_path);
        }
    }
    private function replace($template)
    {
        $template = preg_replace('/' . $this->leftTag . 'loop\s+(\S+)\s+(\S+)' . $this->rightTag . '/', '<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'loop\s+(\S+)\s+(\S+)\s+(\S+)' . $this->rightTag . '/', '<?php if(is_array(\\1)) foreach (\\1 as \\2 => \\3) { ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . '\/loop' . $this->rightTag . '/', '<?php } ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'if\s+(.+?)' . $this->rightTag . '/', '<?php if (\\1) { ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'elseif\s+(.+?)' . $this->rightTag . '/', '<?php }elseif(\\1){ ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'else' . $this->rightTag . '/', '<?php }else{ ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . '\/if' . $this->rightTag . '/', '<?php } ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'for\s+(.+?)' . $this->rightTag . '/', '<?php for(\\1) { ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . '\/for' . $this->rightTag . '/', '<?php } ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . 'include\s(.+?)' . $this->rightTag . '/', '<?php require \$this->_include(\'$1\',__FILE__); ?>', $template);
        
        $template = preg_replace('/' . $this->leftTag . ':/', '<?php ', $template);
        
        $search = array(
            $this->leftTag,
            $this->rightTag
        );
        
        $replace = array(
            "<?php echo ",
            "; ?>"
        );
        
        $template = str_replace($search, $replace, $template);
        
        return $template;
    }
    public function _include($inc_file, $cache_path)
    {
        $inc_path = dirname($cache_path) . '/' . $inc_file . '.php';
        
        if (!$this->is_cached($inc_path))
        {
            $tpl_path = str_replace(realpath($this->cacheDir), realpath($this->tplDir), dirname($cache_path) . '/' . $inc_file);
            
            $this->compile($tpl_path, $inc_path);
        }
        
        return $inc_path;
    }
    public function Clean($dir)
    {
        $cachedir = opendir($dir);
        
        while ($filea = @readdir($cachedir))
        {
            if ($filea != "." && $filea != ".." && $filea != "Thumbs.db")
            {
                unlink($dir . '/' . $filea);
            }
        }
        
        closedir($cachedir);
    }
}
?>