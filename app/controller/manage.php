<?php

namespace app\controller;

Class manage extends base
{
    public function editTopic()
    {
        $this->checkManagePrivate();

        if (isset($_POST['tid'], $_POST['title'], $_POST['msg']))
        {
            if (trim($_POST['title']) == '')
            {
                $this->showMsg('请输入标题', 'error');
            }

            if (trim($_POST['msg']) == '')
            {
                $this->showMsg('请输入帖子详情', 'error');
            }

            $tid = intval($_POST['tid']);

            $title = $this->topicIn($_POST['title']);

            $content = $this->topicIn($_POST['msg']);

            if ($this->app->db()->has('roc_topic', array('tid'=>$tid)))
            {
                $this->app->db()->update('roc_topic', array('title'=>$title, 'content'=>$content), array('tid'=>$tid));

                $this->showMsg('主题编辑成功', 'success');
            }
            else
            {
                $this->showMsg('不存在该主题', 'error');
            }
        }
    }

    public function editReply()
    {
        $this->checkManagePrivate();

        if (isset($_POST['pid'], $_POST['msg']))
        {
            $pid = intval($_POST['pid']);

            $content = $this->topicIn($_POST['msg']);

            if ($this->app->db()->has('roc_reply', array('pid'=>$pid)))
            {
                $this->app->db()->update('roc_reply', array('content'=>$content), array('pid'=>$pid));

                $this->showMsg('回复编辑成功', 'success');
            }
            else
            {
                $this->showMsg('不存在该回复', 'error');
            }
        }
    }

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

    public function getTopicInfo()
    {
        $this->checkManagePrivate();

        if (isset($_POST['tid']) && is_numeric($_POST['tid']))
        {
            $tid = intval($_POST['tid']);

            if ($this->app->db()->has('roc_topic', array('tid'=>$tid)))
            {
                $info = $this->app->db()->get('roc_topic', array('tid', 'title', 'content'), array('tid'=>$tid));

                $info['title'] = $this->topicOut($info['title']);
                
                $info['content'] = $this->topicOut($info['content']);

                $info['tag'] = implode(',', $this->getTopicTag($info['tid']));

                die(json_encode($info));
            }
        }
    }

    public function getReplyInfo()
    {
        $this->checkManagePrivate();

        if (isset($_POST['pid']) && is_numeric($_POST['pid']))
        {
            $pid = intval($_POST['pid']);

            if ($this->app->db()->has('roc_reply', array('pid'=>$pid)))
            {
                $info = $this->app->db()->get('roc_reply', array('pid', 'content'), array('pid'=>$pid));

                $info['content'] = $this->topicOut($info['content']);

                die(json_encode($info));
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
    
    public function ban()
    {
        $this->checkManagePrivate();
        
        $uid = isset($_POST['uid']) && is_numeric($_POST['uid']) ? intval($_POST['uid']) : 0;
        
        $status = isset($_POST['status']) && is_numeric($_POST['status']) ? intval($_POST['status']) : 0;
        
        if ($this->app->db()->has('roc_user', array('uid' => $uid)))
        {
            $this->app->db()->update('roc_user', array(
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
                'url' => $this->filter->in($_POST['url']),
                'text' => $this->filter->in($_POST['text']),
                'position' => intval($_POST['position'])
            );
            
            $LinksArray = array();
            
            $LinksList = json_decode(file_get_contents('app/cache/links.json'), true);
            
            foreach ($LinksList as $key => $link)
            {
                if ($postArray['position'] == $link['position'] && $link['text'] == $postArray['text'])
                {
                    continue;
                }

                if ($link['text'] == $postArray['text'] && $link['url'] == $postArray['url'])
                {
                    $LinksList[$key]['position'] = $postArray['position'];

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
            
            file_put_contents('app/cache/links.json', json_encode($LinksArray));

            $this->app->redirect('/admin/link');
        }
    }
    
    public function del_link($position)
    {
        $this->checkManagePrivate();
        
        $LinksArray = array();

        $LinksList = json_decode(file_get_contents('app/cache/links.json'), true);
        
        foreach ($LinksList as $link)
        {
            if ($link['position'] == intval($position))
            {
                continue;
            }
            
            $LinksArray[$link['position']] = $link;
        }
        
        ksort($LinksArray);
        
        file_put_contents('app/cache/links.json', json_encode($LinksArray));

        $this->app->redirect('/admin/link/');
    }

    public function del_tag($tagid)
    {
        $this->checkManagePrivate();

        if ($this->app->db()->has('roc_tag', array('tagid' => $tagid)))
        {
            $this->app->db()->delete('roc_tag', array(
                'tagid' => $tagid
            ));

            $this->app->db()->delete('roc_topic_tag_connection', array(
                'tagid' => $tagid
            ));

            $this->app->redirect('/admin/tag');
        }
    }
    
    public function ClearCache($type)
    {
        $this->checkManagePrivate();
                
        switch ($type)
        {
            case 'template':
                $this->app->view()->Clean($this->app->view()->cache_dir);
                
                die('<script>alert(\'Clear template cache success~\');history.go(-1);</script>');
                
                break;
            
            case 'attachment':
                $un_use_attachment = array();
                
                $un_use_attachment = $this->app->db()->select('roc_attachment', '*', array(
                    'AND' => array(
                        'tid' => 0,
                        'pid' => 0,
                        'time[<]' => (time() - 7200)
                    )
                ));
                
                foreach ($un_use_attachment as $attachment)
                {
                    @unlink($attachment['path']);
                    
                    $this->app->db()->delete('roc_attachment', array(
                        'id' => $attachment['id']
                    ));
                }
                
                die('<script>alert(\'Clear useless attachment success ~\');history.go(-1);</script>');
                
                break;
            
            case 'score':
                $score_record = $this->app->db()->select('roc_score', '*', array(
                    'time[<]' => (time() - 86400 * 30)
                ));
                
                foreach ($score_record as $score)
                {
                    $this->app->db()->delete('roc_score', array(
                        'id' => $score['id']
                    ));
                }
                
                die('<script>alert(\'Clear useless score success ~\');history.go(-1);</script>');
                
                break;
            
            default:
                # code...
                break;
        }
    }
    
    private function doTopicUpdate($tid, $status, $type)
    {
        if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
        {
            $newStatus = 1 - $status;
            
            $this->app->db()->update('roc_topic', array(
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