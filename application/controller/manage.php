<?php
!defined('ROC') && exit('REFUSED!');
Class manageControl extends commonControl
{
    public $page;
    public $per = 30;
    public function topTopic()
    {
        $this->checkManagePrivate();
        
        if (isset($_POST['tid'], $_POST['status']) && is_numeric($_POST['tid']) && is_numeric($_POST['status']))
        {
            $tid = intval($_POST['tid']);
            
            $status = intval($_POST['status']);
            
            if ($this->doTopicUpdate($tid, $status, 'istop') != $status)
            {
                if ($status == 0)
                {
                    $this->showMsg('置顶成功', 'success', 1);
                }
                else
                {
                    $this->showMsg('取消置顶成功', 'success', 0);
                }
            }
            else
            {
                $this->showMsg('操作失败，请重试', 'error');
            }
        }
    }
    
    public function lockTopic()
    {
        $this->checkManagePrivate();
        
        if (isset($_POST['tid'], $_POST['status']) && is_numeric($_POST['tid']) && is_numeric($_POST['status']))
        {
            $tid = intval($_POST['tid']);
            
            $status = intval($_POST['status']);
            
            if ($this->doTopicUpdate($tid, $status, 'islock') != $status)
            {
                if ($status == 0)
                {
                    $this->showMsg('锁定主题成功', 'success', 1);
                }
                else
                {
                    $this->showMsg('解锁主题成功', 'success', 0);
                }
            }
            else
            {
                $this->showMsg('操作失败，请重试', 'error');
            }
        }
    }
    
    public function adminCommon()
    {
        $this->checkManagePrivate();
        
        if (isset($_POST['sitename'], $_POST['keywords'], $_POST['description'], $_POST['register'], $_POST['topic'], $_POST['reply'], $_POST['praise'], $_POST['whisper'], $_POST['ROCKEY'], $_POST['ad']))
        {
            if (!isset($_POST['hash']) || $_POST['hash'] != md5($_COOKIE['roc_secure']))
            {
                die('Deny Access!');
            }
            
            $sitename = Filter::in($_POST['sitename']);
            
            $keywords = Filter::in($_POST['keywords']);
            
            $description = Filter::in($_POST['description']);
            
            $join_switch = (isset($_POST['join_switch']) && $_POST['join_switch'] == 1) ? 'true' : 'false';
            
            $register = intval($_POST['register']);
            
            $topic = intval($_POST['topic']);
            
            $reply = intval($_POST['reply']);
            
            $praise = intval($_POST['praise']);
            
            $whisper = intval($_POST['whisper']);
            
            $ROCKEY = $_POST['ROCKEY'];
            
            $ad = $_POST['ad'];
            
            $qq_appid = isset($_POST['appid']) ? intval($_POST['appid']) : '';
            
            $qq_appkey = isset($_POST['appkey']) ? Filter::in($_POST['appkey']) : '';
            
            $content = file_get_contents('application/config/config.php');
            
            $content = preg_replace('/\'sitename\' => .+?\,/s', '\'sitename\' => \'' . $sitename . '\',', $content);
            
            $content = preg_replace('/\'keywords\' => .+?\'\,/s', '\'keywords\' => \'' . $keywords . '\',', $content);
            
            $content = preg_replace('/\'description\' => .+?\,/s', '\'description\' => \'' . $description . '\',', $content);
            
            $content = preg_replace('/\'ROCKEY\' => .+?\,/s', '\'ROCKEY\' => \'' . $ROCKEY . '\',', $content);
            
            $content = preg_replace('/\'join_switch\' => .+?\,/s', '\'join_switch\' => ' . $join_switch . ',', $content);
            
            $content = preg_replace('/\'ad\' => .+?\,/s', '\'ad\' => \'' . $ad . '\',', $content);
            
            $content = preg_replace('/\'register\' => .+?\,/s', '\'register\' => ' . $register . ',', $content);
            
            $content = preg_replace('/\'topic\' => .+?\,/s', '\'topic\' => ' . $topic . ',', $content);
            
            $content = preg_replace('/\'reply\' => .+?\,/s', '\'reply\' => ' . $reply . ',', $content);
            
            $content = preg_replace('/\'praise\' => .+?\,/s', '\'praise\' => ' . $praise . ',', $content);
            
            $content = preg_replace('/\'whisper\' => .+?\,/s', '\'whisper\' => ' . $whisper . ',', $content);
            
            $content = preg_replace('/\'appid\' => .+?\,/s', '\'appid\' => \'' . $qq_appid . '\',', $content);
            
            $content = preg_replace('/\'appkey\' => .+?\'/s', '\'appkey\' => \'' . $qq_appkey . '\'', $content);
            
            file_put_contents('application/config/config.php', $content);
            
            header('location:' . ROOT . 'admin/index/type/common');
        }
    }
    
    public function ban()
    {
        $this->checkManagePrivate();
        
        $uid = isset($_POST['uid']) && is_numeric($_POST['uid']) ? intval($_POST['uid']) : 0;
        
        $status = isset($_POST['status']) && is_numeric($_POST['status']) ? intval($_POST['status']) : 0;
        
        if ($this->db->has('roc_user', array(
            'uid' => $uid
        )))
        {
            $this->db->update('roc_user', array(
                'groupid' => $status
            ), array(
                'uid' => $uid
            ));
            
            $this->showMsg('操作成功', 'success');
        }
        else
        {
            $this->showMsg('操作失败，不存在该用户！', 'error');
        }
    }
    
    public function edit_link()
    {
        $this->checkManagePrivate();
        
        if (isset($_POST['position'], $_POST['text'], $_POST['url']))
        {
            $postArray = array(
                'url' => Filter::in($_POST['url']),
                'text' => Filter::in($_POST['text']),
                'position' => intval($_POST['position'])
            );
            
            $LinksArray = array();
            
            $LinksList = json_decode(file_get_contents('application/cache/links.json'), true);
            
            foreach ($LinksList as $link)
            {
                if ($postArray['position'] == $link['position'] && $link['text'] == $postArray['text'])
                {
                    continue;
                }
                
                $LinksArray[$link['position']] = $link;
            }
            
            $LinksArray[$postArray['position']] = array(
                'url' => $postArray['url'],
                'text' => $postArray['text'],
                'position' => $postArray['position']
            );
            
            ksort($LinksArray);
            
            file_put_contents('application/cache/links.json', json_encode($LinksArray));
            
            header('location:' . ROOT . 'admin/index/type/link/');
        }
    }
    
    public function del_link()
    {
        $this->checkManagePrivate();
        
        $position = isset($GLOBALS['Router']['params']['position']) ? intval($GLOBALS['Router']['params']['position']) : -1;
        
        $LinksArray = array();
        
        $LinksList = json_decode(file_get_contents('application/cache/links.json'), true);
        
        foreach ($LinksList as $link)
        {
            if ($link['position'] == intval($position))
            {
                continue;
            }
            
            $LinksArray[$link['position']] = $link;
        }
        
        ksort($LinksArray);
        
        file_put_contents('application/cache/links.json', json_encode($LinksArray));
        
        header('location:' . ROOT . 'admin/index/type/link/');
    }

    public function del_tag()
    {
        $this->checkManagePrivate();

        $tagid = isset($GLOBALS['Router']['params']) && is_numeric($GLOBALS['Router']['params']) ? intval($GLOBALS['Router']['params']) : 0;

        if ($this->db->has('roc_tag', array(
            'tagid' => $tagid
        )))
        {
            $this->db->delete('roc_tag', array(
                'tagid' => $tagid
            ));

            $this->db->delete('roc_topic_tag_connection', array(
                'tagid' => $tagid
            ));

            die('<script>alert(\'标签删除成功！\');history.go(-1);</script>');
        }
    }
    
    public function ClearCache()
    {
        $this->checkManagePrivate();
        
        $type = isset($GLOBALS['Router']['params']['type']) ? Filter::in($GLOBALS['Router']['params']['type']) : '';
        
        switch ($type)
        {
            case 'template':
                $this->tpls->Clean($this->tpls->cacheDir);
                
                die('<script>alert(\'模板缓存清理完成！\');history.go(-1);</script>');
                
                break;
            
            case 'attachment':
                $un_use_attachment = array();
                
                $un_use_attachment = $this->db->select('roc_attachment', '*', array(
                    'AND' => array(
                        'tid' => 0,
                        'pid' => 0,
                        'time[<]' => (time() - 86400)
                    )
                ));
                
                foreach ($un_use_attachment as $attachment)
                {
                    @unlink($attachment['path']);
                    
                    $this->db->delete('roc_attachment', array(
                        'id' => $attachment['id']
                    ));
                }
                
                die('<script>alert(\'无效附件清理完成！\');history.go(-1);</script>');
                
                break;
            
            case 'score':
                $score_record = $this->db->select('roc_score', '*', array(
                    'time[<]' => (time() - 86400 * 30)
                ));
                
                foreach ($score_record as $score)
                {
                    $this->db->delete('roc_score', array(
                        'id' => $score['id']
                    ));
                }
                
                die('<script>alert(\'冗余数据清理完成！\');history.go(-1);</script>');
                
                break;
            
            default:
                # code...
                break;
        }
    }
    
    private function doTopicUpdate($tid, $status, $type)
    {
        if ($this->db->has('roc_topic', array(
            'tid' => $tid
        )))
        {
            $newStatus = 1 - $status;
            
            $this->db->update('roc_topic', array(
                $type => $newStatus
            ), array(
                'tid' => $tid
            ));
            
            return $newStatus;
        }
        else
        {
            return $status;
        }
    }
    
    private function checkManagePrivate()
    {
        if ($this->loginInfo['groupid'] != 9)
        {
            $this->showMsg('抱歉，权限不足！', 'error');
        }
    }
}
?>