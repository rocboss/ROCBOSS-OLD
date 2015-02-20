<?php
!defined('ROC') && exit('REFUSED!');
Class adminControl extends commonControl
{
    public $page;
    public $per = 30;
    public function index()
    {
        if ($this->checkManagePrivate(true))
        {
            $adminType = isset($GLOBALS['Router']['params']['type']) && in_array($GLOBALS['Router']['params']['type'], array(
                'system',
                'common',
                'user',
                'topic',
                'reply',
                'tag',
                'link',
                'edit_link',
                'add_link',
                'clear'
            )) ? $GLOBALS['Router']['params']['type'] : 'system';
            
            switch ($adminType)
            {
                case 'system':
                    $server = array();
                    
                    $server['port'] = $_SERVER['SERVER_PORT'];
                    
                    $server['os'] = @PHP_OS;
                    
                    $server['version'] = @PHP_VERSION;
                    
                    $server['root'] = $_SERVER['DOCUMENT_ROOT'];
                    
                    $server['name'] = $_SERVER['SERVER_NAME'];
                    
                    $server['upload'] = @ini_get('upload_max_filesize');
                    
                    $session_timeout = @ini_get('session.gc_maxlifetime');
                    
                    $server['timeout'] = $session_timeout ? $session_timeout / 60 : '未知';
                    
                    $server['memory_usage'] = $this->format_size(memory_get_usage());
                    
                    $server['disable_functions'] = @ini_get('disable_functions');
                    
                    $server['user_count'] = $this->db->count('roc_user');
                    
                    $server['sign_count'] = $this->db->count('roc_score', array(
                        'AND' => array(
                            'time[>]' => strtotime(date('Y-m-d', time())),
                            'type' => 3
                        )
                    ));
                    
                    if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false)
                    {
                        $server['software'] = 'Apache';
                    }
                    elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx') !== false)
                    {
                        $server['software'] = 'Nginx';
                    }
                    else
                    {
                        $server['software'] = 'Other';
                    }
                    
                    $this->tpls->assign('server', $server);
                    
                    break;
                
                case 'common':
                    $this->tpls->assign('sys_config', $GLOBALS['sys_config']);
                    $this->tpls->assign('qq_config', $GLOBALS['qq_config']);
                    
                    break;
                
                case 'user':
                    $userArray  = $this->db->select('roc_user', '*', array(
                        'ORDER' => 'lasttime DESC',
                        'LIMIT' => array(
                            $this->per * (Utils::getCurrentPage() - 1),
                            $this->per
                        )
                    ));
                    $this->page = new Page($this->per, $this->db->count('roc_user'), Utils::getCurrentPage(), 10, ROOT . 'admin/index/type/user/page/');
                    $this->tpls->assign('page', $this->page->show());
                    $this->tpls->assign('userArray', $userArray);
                    break;
                
                case 'topic':
                    $topicArray = $this->db->select('roc_topic', array(
                        '[>]roc_user' => 'uid'
                    ), array(
                        'tid',
                        'uid',
                        'title',
                        'posttime',
                        'roc_user.username'
                    ), array(
                        'ORDER' => 'tid DESC',
                        'LIMIT' => array(
                            $this->per * (Utils::getCurrentPage() - 1),
                            $this->per
                        )
                    ));
                    $this->page = new Page($this->per, $this->db->count('roc_topic'), Utils::getCurrentPage(), 10, ROOT . 'admin/index/type/topic/page/');
                    $this->tpls->assign('page', $this->page->show());
                    $this->tpls->assign('topicArray', $topicArray);
                    
                    break;
                
                case 'reply':
                    $replyArray = $this->db->select('roc_reply', array(
                        '[>]roc_user' => 'uid'
                    ), array(
                        'pid',
                        'tid',
                        'uid',
                        'content',
                        'posttime',
                        'roc_user.username'
                    ), array(
                        'ORDER' => 'pid DESC',
                        'LIMIT' => array(
                            $this->per * (Utils::getCurrentPage() - 1),
                            $this->per
                        )
                    ));
                    $this->page = new Page($this->per, $this->db->count('roc_reply'), Utils::getCurrentPage(), 10, ROOT . 'admin/index/type/reply/page/');
                    $this->tpls->assign('page', $this->page->show());
                    $this->tpls->assign('replyArray', $replyArray);
                    break;

                case 'tag':
                    $tagArray = $this->db->select('roc_tag', '*', array(
                        'ORDER' => 'used DESC',
                        'LIMIT' => array(
                            50 * (Utils::getCurrentPage() - 1),
                            50
                        )
                    ));
                    $this->page = new Page(50, $this->db->count('roc_tag'), Utils::getCurrentPage(), 10, ROOT . 'admin/index/type/tag/page/');
                    $this->tpls->assign('page', $this->page->show());
                    $this->tpls->assign('tagArray', $tagArray);
                    break;
                
                case 'link':
                    $this->tpls->assign('LinksList', json_decode(file_get_contents("application/cache/links.json"), true));
                    break;
                
                case 'edit_link':
                    if (isset($GLOBALS['Router']['params']['position']) && is_numeric($GLOBALS['Router']['params']['position']))
                    {
                        $position = intval($GLOBALS['Router']['params']['position']);
                        
                        $LinksList = json_decode(file_get_contents("application/cache/links.json"), true);
                        
                        foreach ($LinksList as $link)
                        {
                            if ($link['position'] == intval($position))
                            {
                                $this->tpls->assign('link', $link);
                                
                                break;
                            }
                        }
                    }
                    break;
                
                case 'clear':
                    # code...
                    break;
                
                default:
                    break;
            }
            
            $this->tpls->assign('type', $adminType);
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->display('admin');
        }
    }
    
    private function format_size($filesize)
    {
        if ($filesize >= 1073741824)
        {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        }
        elseif ($filesize >= 1048576)
        {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        }
        elseif ($filesize >= 1024)
        {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        }
        else
        {
            $filesize = $filesize . ' Bytes';
        }
        return $filesize;
    }
    
    private function checkManagePrivate($force = false)
    {
        if ($this->loginInfo['groupid'] != 9)
        {
            if ($force)
            {
                header('location:' . ROOT . 'user/login/');
            }
            
            $this->showMsg('抱歉，权限不足！', 'error');
        }
        else
        {
            return true;
        }
    }
}
?>