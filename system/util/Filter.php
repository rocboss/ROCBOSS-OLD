<?php
# 安全过滤类
namespace system\util;

class Filter
{
    /**
     * 字符串过滤
     * @param string|array $date
     * @param boolean $force
     * @return string|array
     */
    public function in($data, $force = false)
    {
        if (is_string($data))
        {
            if ($force == true || !get_magic_quotes_gpc())
            {
                $data = addslashes(trim($data));
            }
            return trim($data);
        }
        else
        {
            if (is_array($data))
            {
                foreach ($data as $key => $value)
                {
                    $data[$key] = $this->in($value, $force);
                }
                return $data;
            }
            else
            {
                return $data;
            }
        }
    }

    /**
     * 字符串还原
     * @param string|array $date
     * @return string|array
     */
    public function out($data)
    {
        if (is_string($data))
        {            
            return $data = stripslashes($data);
        }
        else
        {
            if (is_array($data))
            {
                foreach ($data as $key => $value)
                {
                    $data[$key] = $this->out($value);
                }
                return $data;
            }
            else
            {
                return $data;
            }
        }
    }

    /**
    * 去除XSS（跨站脚本攻击）
    * @param string $val
    * @return string
    **/
    public function RemoveXSS($val)
    { 
       $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

       $search = 'abcdefghijklmnopqrstuvwxyz'; 

       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  

       $search .= '1234567890!@#$%^&*()'; 

       $search .= '~`";:?+/={}[]-_|\'\\'; 

       for ($i = 0; $i < strlen($search); $i++)
       { 
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); 

          $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); 
       } 

       $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base', 'style'); 

       $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
       
       $ra = array_merge($ra1, $ra2); 
     
       $found = true;

       while ($found == true)
       { 
          $val_before = $val; 

          for ($i = 0; $i < sizeof($ra); $i++)
          { 
             $pattern = '/'; 

             for ($j = 0; $j < strlen($ra[$i]); $j++)
             { 
                if ($j > 0)
                { 
                   $pattern .= '(';  
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
                   $pattern .= '|';  
                   $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
                   $pattern .= ')*'; 
                } 

                $pattern .= $ra[$i][$j]; 
             } 

             $pattern .= '/i';  

             $replacement = substr($ra[$i], 0, 2).'<xss>'.substr($ra[$i], 2); 

             $val = preg_replace($pattern, $replacement, $val); 

             if ($val_before == $val)
             {
                $found = false;  
             }  
          }  
       }  

       return $val;  
    }

    /**
     * 帖子过滤处理
     * @param string $date
     * @return string
     */
    public function topicIn($data)
    {
        $data = $this->RemoveXSS($data);

        return $this->in($data);
    }

    /**
     * 帖子输出处理
     * @param string $date
     * @return string
     */
    public function topicOut($data)
    {
        $data = $this->out($data);

        // $data = htmlspecialchars($data);
        
        return trim($data);
    }
}
?>