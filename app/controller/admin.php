<?php

namespace app\controller;

Class admin extends base
{
    protected $per = 30;

    public function index($type, $page)
    {
        if ($this->checkManagePrivate(true))
        {   
            $page = $page > 0 ? $page : 1;

            $type = !empty($type) ? $type : 'system';

            switch ($type)
            {
                case 'system':
                    $server = array();

                    $server['time'] = date('Y-m-d H:i:s', time());
                    
                    $server['port'] = $_SERVER['SERVER_PORT'];
                    
                    $server['os'] = @PHP_OS;
                    
                    $server['version'] = @PHP_VERSION;
                    
                    $server['root'] = $_SERVER['DOCUMENT_ROOT'];
                    
                    $server['name'] = $_SERVER['SERVER_NAME'];
                    
                    $server['upload'] = @ini_get('upload_max_filesize');
                    
                    $session_timeout = @ini_get('session.gc_maxlifetime');
                    
                    $server['timeout'] = $session_timeout ? $session_timeout / 60 : '未知';
                    
                    $server['memory_usage'] = $this->format_size(memory_get_usage());
                    
                    $server['user_count'] = $this->app->db()->count('roc_user');
                    
                    $server['sign_count'] = $this->app->db()->count('roc_score', array(
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
                    
                    $this->app->view()->assign('server', $server);

                    $this->app->view()->assign('signList', $this->app->db()->select('roc_score', array(
                        '[>]roc_user' => 'uid'
                    ), array(
                        'uid',
                        'time',
                        'roc_user.username'
                    ), array(
                        'AND' => array(
                            'time[>]' => strtotime(date('Y-m-d', time())),
                            'type' => 3
                        ),
                        'ORDER' => 'id ASC',
                        'LIMIT' => 32
                    )));
                    
                    break;
                
                case 'common':
                    if (isset($_POST['sitename'], $_POST['keywords'], $_POST['description'], $_POST['register'], $_POST['topic'], $_POST['reply'], $_POST['praise'], $_POST['whisper'], $_POST['rockey'], $_POST['ad'], $_POST['theme']))
                    {
                        if (!isset($_POST['hash']) || $_POST['hash'] != md5($_COOKIE['roc_secure']))
                        {
                            die('Deny Access!');
                        }
                        
                        $sitename = $this->filter->in($_POST['sitename']);
                        
                        $keywords = $this->filter->in($_POST['keywords']);
                        
                        $description = $this->filter->in($_POST['description']);
                        
                        $join_switch = (isset($_POST['join_switch']) && $_POST['join_switch'] == 1) ? 1 : 0;
                        
                        $register = intval($_POST['register']);
                        
                        $topic = intval($_POST['topic']);
                        
                        $reply = intval($_POST['reply']);
                        
                        $praise = intval($_POST['praise']);
                        
                        $whisper = intval($_POST['whisper']);
                        
                        $rockey = $_POST['rockey'];
                        
                        $ad = $_POST['ad'];
                        
                        $qq_appid = isset($_POST['appid']) ? intval($_POST['appid']) : '';
                        
                        $qq_appkey = isset($_POST['appkey']) ? $this->filter->in($_POST['appkey']) : '';

                        $theme = $this->filter->in($_POST['theme']);
                        
                        $this->app->db()->update('roc_system', array('value'=>$sitename), array('name'=>'sitename'));

                        $this->app->db()->update('roc_system', array('value'=>$keywords), array('name'=>'keywords'));

                        $this->app->db()->update('roc_system', array('value'=>$description), array('name'=>'description'));

                        $this->app->db()->update('roc_system', array('value'=>$join_switch), array('name'=>'join_switch'));

                        $this->app->db()->update('roc_system', array('value'=>$register), array('name'=>'scores_register'));

                        $this->app->db()->update('roc_system', array('value'=>$topic), array('name'=>'scores_topic'));

                        $this->app->db()->update('roc_system', array('value'=>$reply), array('name'=>'scores_reply'));

                        $this->app->db()->update('roc_system', array('value'=>$praise), array('name'=>'scores_praise'));

                        $this->app->db()->update('roc_system', array('value'=>$whisper), array('name'=>'scores_whisper'));

                        $this->app->db()->update('roc_system', array('value'=>$rockey), array('name'=>'rockey'));

                        $this->app->db()->update('roc_system', array('value'=>$ad), array('name'=>'ad'));

                        $this->app->db()->update('roc_system', array('value'=>$qq_appid), array('name'=>'appid'));

                        $this->app->db()->update('roc_system', array('value'=>$qq_appkey), array('name'=>'appkey'));

                        $this->app->db()->update('roc_system', array('value'=>$theme), array('name'=>'theme'));

                        @unlink('app/cache/sys_config_cache.php');

                        $this->app->view()->assign('code', '更新成功~');

                        $allSysData = $this->app->db()->select('roc_system', '*');

                        foreach ($allSysData as $key => $value)
                        {
                            $this->sys[$value['name']] = $value['value'];
                        }
                    }

                    if (isset($_POST['smtp_server'], $_POST['smtp_port'], $_POST['smtp_user'], $_POST['smtp_password']))
                    {
                        if (!isset($_POST['hash']) || $_POST['hash'] != md5($_COOKIE['roc_secure']))
                        {
                            die('Deny Access!');
                        }

                        $smtp_server = $this->filter->in($_POST['smtp_server']);

                        $smtp_port = intval($_POST['smtp_port']);

                        $smtp_user = $this->filter->in($_POST['smtp_user']);

                        $smtp_password = $this->filter->in($_POST['smtp_password']);

                        $this->app->db()->update('roc_system', array('value'=>$smtp_server), array('name'=>'smtp_server'));

                        $this->app->db()->update('roc_system', array('value'=>$smtp_port), array('name'=>'smtp_port'));

                        $this->app->db()->update('roc_system', array('value'=>$smtp_user), array('name'=>'smtp_user'));

                        $this->app->db()->update('roc_system', array('value'=>$smtp_password), array('name'=>'smtp_password'));

                        @unlink('app/cache/sys_config_cache.php');

                        $this->app->view()->assign('code', '更新成功~');

                        $allSysData = $this->app->db()->select('roc_system', '*');

                        foreach ($allSysData as $key => $value)
                        {
                            $this->sys[$value['name']] = $value['value'];
                        }
                    }

                    $handle = opendir('app/template/');

                    $tplName = array();

                    while ($file = readdir($handle))
                    {
                        if (is_dir('app/template/'.$file) && !in_array($file, array('.', '..')))
                        {
                            $tplName[] = $file;
                        }
                    }

                    $this->app->view()->assign('tplName', $tplName);
                    
                    $this->app->view()->assign('sys', $this->sys);
                    
                    break;
                
                case 'user':
                    $userArray  = $this->app->db()->select('roc_user', '*', array(
                        'ORDER' => 'lasttime DESC',
                        'LIMIT' => array(
                            $this->per * ($page - 1),
                            $this->per
                        )
                    ));

                    foreach ($userArray as $key => $value)
                    {
                        $userArray[$key]['avatar'] = $this->getUserAvatar($value['uid']);
                        
                        $userArray[$key]['lasttime'] = $this->utils->formatTime($value['lasttime']);
                    }

                    $this->setPage($page,$this->app->db()->count('roc_user'), 'admin/user/?');

                    $this->app->view()->assign('userArray', $userArray);

                    break;
                
                case 'topic':
                    $topicArray = $this->app->db()->select('roc_topic', array(
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
                            $this->per * ($page - 1),
                            $this->per
                        )
                    ));

                    foreach ($topicArray as $key => $value)
                    {
                        $topicArray[$key]['title'] = $this->topicOut($value['title']);

                        $topicArray[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
                    }
                    
                    $this->setPage($page,$this->app->db()->count('roc_topic'), 'admin/topic/?');
                    
                    $this->app->view()->assign('topicArray', $topicArray);
                    
                    break;
                
                case 'reply':
                    $replyArray = $this->app->db()->select('roc_reply', array(
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
                            $this->per * ($page - 1),
                            $this->per
                        )
                    ));

                    foreach ($replyArray as $key => $value)
                    {
                        $replyArray[$key]['content'] = $this->topicOut($value['content']);

                        $replyArray[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
                    }
                    
                    $this->setPage($page,$this->app->db()->count('roc_reply'), 'admin/reply/?');
                    
                    $this->app->view()->assign('replyArray', $replyArray);
                    
                    break;

                case 'tag':
                    $this->per = 50;

                    $tagArray = $this->app->db()->select('roc_tag', '*', array(
                        'ORDER' => 'used DESC',
                        'LIMIT' => array(
                            $this->per * ($page - 1),
                            $this->per
                        )
                    ));
                    
                    $this->setPage($page,$this->app->db()->count('roc_tag'), 'admin/tag/?');
                    
                    $this->app->view()->assign('tagArray', $tagArray);
                    
                    break;
                
                case 'link':
                    $this->app->view()->assign('LinksList', json_decode(file_get_contents("app/cache/links.json"), true));
                    
                    break;

                default:
                   
                    break;
            }
            
            $this->app->view()->assign('type', $type);
            
            $this->app->view()->assign('loginInfo', $this->loginInfo);
            
            $this->setViewBase('后台管理 - ', 'admin');
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
                $this->app->redirect('/login');
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