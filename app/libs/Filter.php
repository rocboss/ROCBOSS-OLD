<?php

class Filter
{
    private $mDom;
    private $mXss;
    private $mOk;
    private $mAllowAttr = array('title', 'src', 'href', 'id', 'class', 'style', 'width', 'height', 'alt', 'target', 'align');
    private $mAllowTag = array('a', 'img', 'br', 'strong', 'b', 'code', 'pre', 'p', 'div', 'em', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'ul', 'ol', 'tr', 'th', 'td', 'hr', 'li', 'u');

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
     * 帖子过滤处理
     * @param string $date
     * @return string
     */
    public function topicIn($data)
    {
        $data = str_replace(["\n", " "], ["##br/##", "##nbsp;"], $data);

        $data = htmlspecialchars($data);

        $data = str_replace(["##br/##", "##nbsp;"], ["<br/>", "&nbsp;"], $data);

        return $this->in($data);
    }

    /**
     * 帖子过滤处理(WEB)
     * @param string $date
     * @return string
     */
    public function topicInWeb($data)
    {
        $this->doFilter($data);

        $data = $this->getHtml();

        return $this->in($data);
    }

    /**
     * 帖子输出处理
     * @param string $date
     * @return string
     */
    public function topicOut($data, $changeEmoji = false)
    {
        $data = $this->out($data);

        if ($changeEmoji)
        {
            $data = preg_replace("/<img src=\"\/app\/views\/emoji\/(\d+\.gif)\".+?>/i", '<img class="emoji" src="images/emoji/${1}"/>', $data);
        }

        return trim($data);
    }

    private function doFilter($html, $charset = 'utf-8', $AllowTag = [])
    {
        $this->mAllowTag = empty($AllowTag) ? $this->mAllowTag : $AllowTag;

        if (is_array($html) && !empty($html)) {
            static::doFilter(implode(' ', $html), $charset, $AllowTag);
        } else {
            $this->mXss = strip_tags($html, '<' . implode('><', $this->mAllowTag) . '>');

            if (empty($this->mXss)) {
                $this->mOk = FALSE;
                return ;
            }

            $this->mXss = "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$charset}\"><nouse>" . $this->mXss . "</nouse>";
            $this->mDom = new DOMDocument();
            $this->mDom->strictErrorChecking = FALSE;
            $this->mOk = @$this->mDom->loadHTML($this->mXss);
        }
    }

    /**
     * 获得过滤后的内容
     */
    public function getHtml()
    {
        if (!$this->mOk)
        {
            return '';
        }

        $nodeList = $this->mDom->getElementsByTagName('*');

        for ($i = 0; $i < $nodeList->length; $i++)
        {
            $node = $nodeList->item($i);

            if (in_array($node->nodeName, $this->mAllowTag))
            {
                if (method_exists($this, "__node_{$node->nodeName}"))
                {
                    call_user_func(array($this, "__node_{$node->nodeName}"), $node);
                }
                else
                {
                    call_user_func(array($this, '__node_default'), $node);
                }
            }
        }

        $html = strip_tags($this->mDom->saveHTML(), '<' . implode('><', $this->mAllowTag) . '>');

        $html = preg_replace('/^\n(.*)\n$/s', '$1', $html);

        return $html;
    }

    private function __true_url($url)
    {
        if (preg_match('#^https?://.+#is', $url))
        {
            return $url;
        }
        else
        {
            return 'http://' . $url;
        }
    }

    private function __get_style($node)
    {
        if ($node->attributes->getNamedItem('style'))
        {
            $style = $node->attributes->getNamedItem('style')->nodeValue;

            $style = str_replace('\\', ' ', $style);

            $style = str_replace(array('&#', '/*', '*/'), ' ', $style);

            $style = preg_replace('#e.*x.*p.*r.*e.*s.*s.*i.*o.*n#Uis', ' ', $style);

            return $style;
        }
        else
        {
            return '';
        }
    }

    private function __get_link($node, $att)
    {
        $link = $node->attributes->getNamedItem($att);

        if ($link)
        {
            return $this->__true_url($link->nodeValue);
        }
        else
        {
            return '';
        }
    }

    private function __setAttr($dom, $attr, $val)
    {
        if (!empty($val))
        {
            $dom->setAttribute($attr, $val);
        }
    }

    private function __set_default_attr($node, $attr, $default = '')
    {
        $o = $node->attributes->getNamedItem($attr);

        if ($o)
        {
            $this->__setAttr($node, $attr, $o->nodeValue);
        }
        else
        {
            $this->__setAttr($node, $attr, $default);
        }
    }

    private function __common_attr($node)
    {
        $list = array();
        foreach ($node->attributes as $attr)
        {
            if (!in_array($attr->nodeName, $this->mAllowAttr))
            {
                $list[] = $attr->nodeName;
            }
        }
        foreach ($list as $attr)
        {
            $node->removeAttribute($attr);
        }

        $style = $this->__get_style($node);

        $this->__setAttr($node, 'style', $style);

        $this->__set_default_attr($node, 'title');

        $this->__set_default_attr($node, 'id');

        $this->__set_default_attr($node, 'class');
    }

    private function __node_img($node)
    {
        $this->__common_attr($node);

        $this->__set_default_attr($node, 'src');

        $this->__set_default_attr($node, 'width');

        $this->__set_default_attr($node, 'height');

        $this->__set_default_attr($node, 'alt');

        $this->__set_default_attr($node, 'align');

    }

    private function __node_a($node)
    {
        $this->__common_attr($node);

        $href = $this->__get_link($node, 'href');

        $this->__setAttr($node, 'href', $href);

        $this->__set_default_attr($node, 'target', '_blank');
    }

    private function __node_embed($node)
    {
        $this->__common_attr($node);

        $link = $this->__get_link($node, 'src');

        $this->__setAttr($node, 'src', $link);

        $this->__setAttr($node, 'allowscriptaccess', 'never');

        $this->__set_default_attr($node, 'width');

        $this->__set_default_attr($node, 'height');
    }

    private function __node_default($node)
    {
        $this->__common_attr($node);
    }
}
?>
