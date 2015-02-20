<?php
class Page
{
    protected $EACHNUMS;
    
    protected $ALLNUMS;
    
    protected $CURRENTPAGE;
    
    protected $SUBPAGES;
    
    protected $PAGEALLNUMS;
    
    protected $PAGEARRAY = array();
    
    protected $SUBPAGELINK;
    
    public function __construct($EACHNUMS, $ALLNUMS, $CURRENTPAGE, $SUBPAGES, $SUBPAGELINK)
    {
        $this->EACHNUMS = intval($EACHNUMS);
        
        $this->ALLNUMS = intval($ALLNUMS);
        
        if (!$CURRENTPAGE)
        {
            $this->CURRENTPAGE = 1;
        }
        else
        {
            $this->CURRENTPAGE = intval($CURRENTPAGE);
        }
        
        $this->SUBPAGES = intval($SUBPAGES);
        
        $this->PAGEALLNUMS = ceil($ALLNUMS / $EACHNUMS);
        
        $this->SUBPAGELINK = $SUBPAGELINK;
    }
    public function initArray()
    {
        for ($i = 0; $i < $this->SUBPAGES; $i++)
        {
            $this->PAGEARRAY[$i] = $i;
        }
        
        return $this->PAGEARRAY;
    }
    public function CONSTRUCT_NUM_PAGE()
    {
        if ($this->PAGEALLNUMS < $this->SUBPAGES)
        {
            $CURRENTARRAY = array();
            
            for ($i = 0; $i < $this->PAGEALLNUMS; $i++)
            {
                $CURRENTARRAY[$i] = $i + 1;
            }
        }
        else
        {
            $CURRENTARRAY = $this->initArray();
            
            if ($this->CURRENTPAGE <= 3)
            {
                for ($i = 0; $i < count($CURRENTARRAY); $i++)
                {
                    $CURRENTARRAY[$i] = $i + 1;
                }
            }
            elseif ($this->CURRENTPAGE <= $this->PAGEALLNUMS && $this->CURRENTPAGE > $this->PAGEALLNUMS - $this->SUBPAGES + 1)
            {
                for ($i = 0; $i < count($CURRENTARRAY); $i++)
                {
                    $CURRENTARRAY[$i] = ($this->PAGEALLNUMS) - ($this->SUBPAGES) + 1 + $i;
                }
            }
            else
            {
                for ($i = 0; $i < count($CURRENTARRAY); $i++)
                {
                    $CURRENTARRAY[$i] = $this->CURRENTPAGE - 2 + $i;
                }
            }
        }
        
        return $CURRENTARRAY;
    }
    public function show()
    {
        $PAGESHOWSTRING = "";
        
        if ($this->CURRENTPAGE > 1)
        {
            $FIRSTPAGEURL = $this->SUBPAGELINK . "1";
            
            $PREWPAGEURL = $this->SUBPAGELINK . ($this->CURRENTPAGE - 1);
            
            $PAGESHOWSTRING .= "<a href='$FIRSTPAGEURL'>首页</a>";
            
            $PAGESHOWSTRING .= "<a href='$PREWPAGEURL'>上一页</a>";
        }
        else
        {
            $PAGESHOWSTRING .= "<a>首页</a>";
            
            $PAGESHOWSTRING .= "<a class='disabled'>上一页</a>";
        }
        
        $ALL = $this->CONSTRUCT_NUM_PAGE();
        
        for ($i = 0; $i < count($ALL); $i++)
        {
            $STR = $ALL[$i];
            
            if ($STR == $this->CURRENTPAGE)
            {
                $PAGESHOWSTRING .= "<a class='hover'>" . $STR . "</a>";
            }
            else
            {
                $url = $this->SUBPAGELINK . $STR;
                
                $PAGESHOWSTRING .= "<a href='$url'>" . $STR . "</a>";
            }
        }
        if ($this->CURRENTPAGE < $this->PAGEALLNUMS)
        {
            $lastPageUrl = $this->SUBPAGELINK . $this->PAGEALLNUMS;
            
            $nextPageUrl = $this->SUBPAGELINK . ($this->CURRENTPAGE + 1);
            
            $PAGESHOWSTRING .= "<a href='$nextPageUrl'>下一页</a>";
            
            $PAGESHOWSTRING .= "<a href='$lastPageUrl'>尾页</a>";
        }
        else
        {
            $PAGESHOWSTRING .= "<a class='disabled'>下一页</a>";
            
            $PAGESHOWSTRING .= "<a class='disabled'>尾页</a>";
        }
        
        return $PAGESHOWSTRING;
    }
    public function __destruct()
    {
        unset($EACHNUMS);
        
        unset($ALLNUMS);
        
        unset($CURRENTPAGE);
        
        unset($SUBPAGES);
        
        unset($PAGEALLNUMS);
        
        unset($PAGEARRAY);
        
        unset($SUBPAGELINK);
    }
}
?>