<?php

namespace system\template;

use Roc;

class View
{
    public $path;

    protected $vars = [];

    private $template;

    private $system_replace = [
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
    ];

    public function __construct($path = '.')
    {
        $this->path = $path;
    }

    public function get($key)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    public function set($key, $value = null)
    {
        if (is_array($key) || is_object($key)) {
            foreach ($key as $k => $v) {
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$key] = $value;
        }
    }

    public function has($key)
    {
        return isset($this->vars[$key]);
    }

    public function clear($key = null)
    {
        if (is_null($key)) {
            $this->vars = [];
        } else {
            unset($this->vars[$key]);
        }
    }

    public function render($file, $data = null)
    {
        $this->template = $this->getTemplate($file);

        if (!file_exists($this->template)) {
            throw new \Exception("Template file not found: {$this->template}.");
        }

        if (is_array($data)) {
            $this->vars = array_merge($this->vars, $data);
        }

        extract($this->vars);

        $tmpPath = Roc::get('system.views.cache').'/'.str_replace('/', '_', $this->template);

        if (!$this->isCached($tmpPath)) {
            $tpl = preg_replace(array_keys($this->system_replace), $this->system_replace, @file_get_contents($this->template));

            @file_put_contents($tmpPath, $tpl, LOCK_EX);
        }

        include $tmpPath;
    }

    public function clean()
    {
        $dir = Roc::get('system.views.cache');

        $cachedir = opendir($dir);

        while ($file = @readdir($cachedir)) {
            if ($file != "." && $file != "..") {
                unlink($dir . '/' . $file);
            }
        }

        closedir($cachedir);
    }

    public function fetch($file, $data = null)
    {
        ob_start();

        $this->render($file, $data);

        $output = ob_get_clean();

        return $output;
    }

    public function exists($file)
    {
        return file_exists($this->getTemplate($file));
    }

    public function getTemplate($file)
    {
        if ((substr($file, -4) != '.php')) {
            $file .= '.php';
        }
        if ((substr($file, 0, 1) == '/')) {
            return $file;
        } else {
            return $this->path . '/' . $file;
        }
    }

    public function isCached($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $cacheTime = Roc::get('system.views.cacheTime');

        if ($cacheTime < 0) {
            return true;
        }

        if (time() - filemtime($path) > $cacheTime) {
            return false;
        }

        return true;
    }

    public function e($str)
    {
        echo htmlentities($str);
    }
}
