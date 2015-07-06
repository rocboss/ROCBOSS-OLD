<?php

namespace app\controller;

Class doController extends base
{
    private $client;

    public function postTopic()
    {
        if ($this->checkPrivate(1) == true)
        {
            $this->checkFloodTime($this->loginInfo['uid'], 30);
            
            if (isset($_POST['title'], $_POST['msg'], $_POST['tag']) && $this->topicIn($_POST['msg']) != '')
            {
                if (trim($_POST['tag']) != '')
                {
                    $tagArray = array_filter(explode(' ', trim($_POST['tag'])));
                }
                else
                {
                    $this->showMsg('请输入自定义标签', 'error');
                }
                
                if (trim($_POST['title']) == '')
                {
                    $this->showMsg('请输入标题', 'error');
                }

                $this->clientLoad();

                $return_client = $this->client->Get_Useragent();

                $contentReturn = $this->doAtUser($this->topicIn($_POST['msg']));
                
                $topicArray = array(
                    'uid' => $this->loginInfo['uid'],
                    
                    'title' => (trim($_POST['title']) != '') ? $this->topicIn($_POST['title']) : $this->utils->cutSubstr($this->topicIn($_POST['msg'])),
                    
                    'content' => $contentReturn['content'],
                    
                    'comments' => 0,
                    
                    'client' => $return_client[3].' '.$return_client[5],
                    
                    'istop' => 0,
                    
                    'islock' => 0,
                    
                    'posttime' => time(),
                    
                    'lasttime' => time()
                );
                
                $insertTopicID = $this->app->db()->insert('roc_topic', $topicArray);
                
                if ($insertTopicID > 0)
                {
                    if (isset($tagArray) && is_array($tagArray) && !empty($tagArray))
                    {
                        foreach ($tagArray as $k => $v)
                        {
                            if ($this->app->db()->has('roc_tag', array('tagname' => $this->topicIn($v))))
                            {
                                $this->app->db()->update('roc_tag', array(
                                    'used[+]' => 1
                                ), array(
                                    'tagname' => $this->topicIn($v)
                                ));
                                
                                $insertTagID = $this->app->db()->get('roc_tag', 'tagid', array(
                                    'tagname' => $this->topicIn($v)
                                ));
                                
                                $this->app->db()->insert('roc_topic_tag_connection', array(
                                    'tid' => $insertTopicID,
                                    'tagid' => $insertTagID
                                ));
                            }
                            else
                            {
                                $insertTagID = $this->app->db()->insert('roc_tag', array(
                                    'tagname' => $this->topicIn($v),
                                    'used' => 1
                                ));
                                
                                if ($insertTagID > 0)
                                {
                                    $this->app->db()->insert('roc_topic_tag_connection', array(
                                        'tid' => $insertTopicID,
                                        'tagid' => $insertTagID
                                    ));
                                }
                            }
                        }
                    }
                    
                    if (!empty($contentReturn['atUidArray']))
                    {
                        foreach ($contentReturn['atUidArray'] as $atuid)
                        {
                            $this->app->db()->insert('roc_notification', array(
                                'atuid' => $atuid,
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $insertTopicID,
                                'pid' => 0,
                                'fid' => 0,
                                'isread' => 0
                            ));
                        }
                    }
                    
                    $this->updateAttachment($this->topicIn($_POST['msg']), array('tid' => $insertTopicID));
                    
                    $this->updateLasttime($this->loginInfo['uid']);
                    
                    $this->updateUserScore($this->loginInfo['uid'], $this->sys['scores_topic'], 1);
                    
                    $this->showMsg('发表成功~', 'success', $insertTopicID);
                }
                else
                {
                    $this->showMsg('发表失败，请重试！', 'error');
                }
            }
            else
            {
                $this->showMsg('请检查您的输入是否合法，正文详情必填哦~', 'error');
            }
        }
        else
        {
            $this->showMsg('您尚未登录或已被禁言，无法发布新帖子！', 'error');
        }
    }
    
    public function postReply()
    {
        if ($this->checkPrivate(1) == true)
        {
            $this->checkFloodTime($this->loginInfo['uid'], 15);

            if (isset($_POST['content'], $_POST['tid']) && $this->topicIn($_POST['content']) != '' && is_numeric($_POST['tid']) && $this->utils->getStrlen(trim($_POST['content'])) <= 250)
            {
                $tid = intval($_POST['tid']);
                
                if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
                {
                    if ($this->app->db()->get('roc_topic', 'islock', array(
                        'tid' => $tid
                    )) == 1)
                    {
                        $this->showMsg('抱歉，主题已锁，无法再回复了', 'error');
                    }
                    
                    $this->clientLoad();

                    $return_client = $this->client->Get_Useragent();

                    $contentReturn = $this->doAtUser($this->topicIn($_POST['content']));
                    
                    $topicArray = array(
                        'tid' => $tid,
                        
                        'uid' => $this->loginInfo['uid'],
                        
                        'content' => $contentReturn['content'],
                        
                        'client' => $return_client[3].' '.$return_client[5],
                        
                        'posttime' => time()
                    );
                    
                    $insertReplyID = $this->app->db()->insert('roc_reply', $topicArray);
                    
                    if ($insertReplyID > 0)
                    {
                        $this->app->db()->update('roc_topic', array(
                            'comments[+]' => 1,
                            'lasttime' => time()
                        ), array(
                            'tid' => $tid
                        ));
                        
                        $this->updateAttachment($this->topicIn($_POST['content']), array(
                            'pid' => $insertReplyID
                        ));
                        
                        if (!empty($contentReturn['atUidArray']))
                        {
                            foreach ($contentReturn['atUidArray'] as $atuid)
                            {
                                $this->app->db()->insert('roc_notification', array(
                                    'atuid' => $atuid,
                                    'uid' => $this->loginInfo['uid'],
                                    'tid' => $tid,
                                    'pid' => $insertReplyID,
                                    'fid' => 0,
                                    'isread' => 0
                                ));
                            }
                        }
                        
                        $authorUid = $this->app->db()->get('roc_topic', 'uid', array(
                            'tid' => $tid
                        ));
                        
                        if (!in_array($authorUid, $contentReturn['atUidArray']) && $authorUid != $this->loginInfo['uid'])
                        {
                            $this->app->db()->insert('roc_notification', array(
                                'atuid' => $authorUid,
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid,
                                'pid' => $insertReplyID,
                                'fid' => 0,
                                'isread' => 0
                            ));
                        }
                        
                        $this->updateLasttime($this->loginInfo['uid']);
                        
                        $this->updateUserScore($this->loginInfo['uid'], $this->sys['scores_reply'], 2);
                        
                        $this->showMsg('发表成功~', 'success', $insertReplyID);
                    }
                    else
                    {
                        $this->showMsg('发表失败，请重试！', 'error');
                    }
                }
                else
                {
                    $this->showMsg('该帖子不存在，无法回复！', 'error');
                }
            }
            else
            {
                $this->showMsg('请检查您的输入是否合法，回复非空且不能超过250个字', 'error');
            }
        }
        else
        {
            $this->showMsg('您尚未登录或已被禁言，无法创建新主题哦~', 'error');
        }
    }
    
    public function postFloor()
    {
        if ($this->checkPrivate(1) == true)
        {
            $this->checkFloodTime($this->loginInfo['uid'], 10);
            
            if (isset($_POST['content'], $_POST['pid']) && $this->topicIn($_POST['content']) != '' && $this->utils->getStrlen($_POST['content']) <= 100 && is_numeric($_POST['pid']))
            {
                $pid = intval($_POST['pid']);
                
                if ($this->app->db()->has('roc_reply', array('pid' => $pid)))
                {
                    $contentReturn = $this->doAtUser($this->topicIn($_POST['content']));
                    
                    $tid = $this->app->db()->get('roc_reply', 'tid', array(
                        'pid' => $pid
                    ));
                    
                    $floorArray = array(
                        'pid' => $pid,
                        
                        'uid' => $this->loginInfo['uid'],
                        
                        'content' => $contentReturn['content'],
                        
                        'posttime' => time()
                    );
                    
                    $insertFloorID = $this->app->db()->insert('roc_floor', $floorArray);
                    
                    if ($insertFloorID > 0)
                    {
                        $this->updateLasttime($this->loginInfo['uid']);
                        
                        if (!empty($contentReturn['atUidArray']))
                        {
                            foreach ($contentReturn['atUidArray'] as $atuid)
                            {
                                $this->app->db()->insert('roc_notification', array(
                                    'atuid' => $atuid,
                                    'uid' => $this->loginInfo['uid'],
                                    'tid' => $tid,
                                    'pid' => $pid,
                                    'fid' => $insertFloorID,
                                    'isread' => 0
                                ));
                            }
                        }
                        
                        $this->showMsg('评论成功~', 'success', $insertFloorID);
                    }
                    else
                    {
                        $this->showMsg('评论失败，请重试！', 'error');
                    }
                }
                else
                {
                    $this->showMsg('非法pid参数，请检查您的输入', 'error');
                }
            }
            else
            {
                $this->showMsg('请检查您的输入是否合法，评论不可为空且不能超过100字', 'error');
            }
        }
        else
        {
            $this->showMsg('您尚未登录或已被禁言，无法评论哦~', 'error');
        }
    }

    public function posttime()
    {
        setcookie('type', 'posttime', time()+1209600, '/');

        die('<script>history.go(-1);</script>');
    }

    public function lasttime()
    {
        setcookie('type','lasttime', time()+1209600, '/');

        die('<script>history.go(-1);</script>');
    }
    
    public function uploadPicture()
    {
        if ($this->checkPrivate(1) == true)
        {
            $time = time();
            
            $img = $_POST['base64'];
            
            $path = 'app/uploads/pictures/' . date('Y/n/j', $time);
            
            if (isset($img))
            {
                if (preg_match('/data:image\/([^;]*);base64,(.*)/', $img, $matches))
                {
                    $this->makeDir($path);
                    
                    $img = base64_decode($matches[2]);

                    $ext_name = ($matches[1] == 'gif') ? 'gif' : 'png';
                    
                    $target = $path . '/' . md5($time . '_' . $this->loginInfo['uid'] . '_' . rand(1000, 9999)) . '.' . $ext_name;
                    
                    @file_put_contents($target, $img);

                    if ($ext_name == 'gif')
                    {
                        @file_put_contents($target. '.thumb.png', $img);
                    }
                    else
                    {
                        list($width_orig, $height_orig) = getimagesize($target);
                        
                        $width = 200;
                        
                        $height = 150;
                        
                        if ($width_orig < $height_orig)
                        {
                            $width = ($height / $height_orig) * $width_orig;
                        }
                        else
                        {
                            $height = ($width / $width_orig) * $height_orig;
                        }
                    
                        $image_p = imagecreatetruecolor($width, $height);
                        
                        $image = imagecreatefromjpeg($target);
                        
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                        
                        imagejpeg($image_p, $target . '.thumb.png', 100);
                        
                        imagedestroy($image_p);
                    }
                    
                    $aArray = array(
                        'uid' => $this->loginInfo['uid'],
                        
                        'path' => $target,
                        
                        'time' => $time,
                        
                        'tid' => 0,
                        
                        'pid' => 0
                    );
                    
                    $aID = $this->app->db()->insert('roc_attachment', $aArray);
                    
                    if ($aID > 0)
                    {
                        $this->showMsg('图片上传成功', 'success', $aID);
                    }
                    else
                    {
                        @unlink($target);
                        
                        @unlink($target . '.thumb.png');
                        
                        $this->showMsg('图片上传处理失败，请重试', 'error');
                    }
                }
                else
                {
                    $this->showMsg('图片上传失败，请检查上传文件是否合法', 'error');
                }
            }
        }
        else
        {
            $this->showMsg('您尚未登录或已被禁言，无权上传图片哦~', 'error');
        }
    }
    
    public function uploadAvatar()
    {
        if ($this->checkPrivate() == true)
        {
            $time = time();
            
            $img = $_POST['base64'];
            
            $path = 'app/uploads/avatars/' . intval($this->loginInfo['uid'] / 1000) . '/' . $this->loginInfo['uid'];
            
            if (isset($img))
            {
                if (preg_match('/data:image\/([^;]*);base64,(.*)/', $img, $matches))
                {
                    $this->makeDir($path);
                    
                    $img = base64_decode($matches[2]);
                    
                    $target = $path . '/' . '200.png';
                    
                    @file_put_contents($target, $img);
                    
                    list($width_orig, $height_orig) = getimagesize($target);
                    
                    for ($i = 1; $i < 3; $i++)
                    {
                        $width = 50 * $i;
                        
                        $height = 50 * $i;
                        
                        $image_p = imagecreatetruecolor($width, $height);
                        
                        $image = imagecreatefromjpeg($target);
                        
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                        
                        imagejpeg($image_p, $path . '/' . (50 * $i) . '.png', 100);
                        
                        imagedestroy($image_p);
                    }
                    
                    @unlink($target);
                    
                    $this->showMsg('头像上传成功', 'success');
                }
                else
                {
                    $this->showMsg('头像上传失败，请检查上传文件是否合法', 'error');
                }
            }
        }
    }
    
    public function deleteTopic()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['tid']) && is_numeric($_POST['tid']))
            {
                $tid = intval($_POST['tid']);
                
                if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
                {
                    $uid = $this->app->db()->get('roc_topic', 'uid', array(
                        'tid' => $tid
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->app->db()->delete('roc_topic', array(
                            'tid' => $tid
                        ));
                    }
                    else
                    {
                        $groupid = $this->app->db()->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->app->db()->delete('roc_topic', array(
                                'tid' => $tid
                            ));
                        }
                        else
                        {
                            $this->showMsg('抱歉，您无权删除此主题', 'error');
                        }
                    }
                    
                    if ($dID > 0)
                    {
                        $pidArray = $this->app->db()->select('roc_reply', 'pid', array(
                            'tid' => $tid
                        ));
                        
                        foreach ($pidArray as $key => $value)
                        {
                            $this->app->db()->delete('roc_floor', array(
                                'pid' => $value
                            ));
                        }
                        
                        $tagidArray = $this->app->db()->select('roc_topic_tag_connection', 'tagid', array(
                            'tid' => $tid
                        ));
                        
                        foreach ($tagidArray as $key => $value)
                        {
                            $used = $this->app->db()->get('roc_tag', 'used', array(
                                'tagid' => $value
                            ));
                            
                            if ($used > 1)
                            {
                                $this->app->db()->update('roc_tag', array(
                                    'used[-]' => 1
                                ), array(
                                    'tagid' => $value
                                ));
                            }
                            else
                            {
                                $this->app->db()->delete('roc_tag', array(
                                    'tagid' => $value
                                ));
                            }
                        }
                        
                        $this->app->db()->delete('roc_topic_tag_connection', array(
                            'tid' => $tid
                        ));
                        
                        $this->app->db()->delete('roc_reply', array(
                            'tid' => $tid
                        ));
                        
                        $this->app->db()->delete('roc_notification', array(
                            'tid' => $tid
                        ));
                        
                        $this->app->db()->delete('roc_favorite', array(
                            'tid' => $tid
                        ));
                        
                        $this->delete_attachment_connect($tid, 'tid');
                        
                        $this->updateUserScore($uid, -$this->sys['scores_topic'], 6);
                        
                        $this->showMsg('删除成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('删除失败，请重试', 'error');
                    }
                }
                else
                {
                    $this->showMsg('此主题不存在或已删除', 'error');
                }
            }
        }
        else
        {
            $this->showMsg('抱歉，您无权删除此主题', 'error');
        }
    }
    
    public function deleteReply()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['pid']) && is_numeric($_POST['pid']))
            {
                $pid = intval($_POST['pid']);
                
                if ($this->app->db()->has('roc_reply', array('pid' => $pid)))
                {
                    $uid = $this->app->db()->get('roc_reply', 'uid', array(
                        'pid' => $pid
                    ));
                    
                    $tid = $this->app->db()->get('roc_reply', 'tid', array(
                        'pid' => $pid
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->app->db()->delete('roc_reply', array(
                            'pid' => $pid
                        ));
                    }
                    else
                    {
                        $groupid = $this->app->db()->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->app->db()->delete('roc_reply', array(
                                'pid' => $pid
                            ));
                        }
                        else
                        {
                            $this->showMsg('抱歉，您无权删除此回复', 'error');
                        }
                    }
                    
                    if ($dID > 0)
                    {
                        $this->app->db()->update('roc_topic', array(
                            'comments[-]' => 1
                        ), array(
                            'tid' => $tid
                        ));
                        
                        $this->app->db()->delete('roc_floor', array(
                            'pid' => $pid
                        ));
                        
                        $this->app->db()->delete('roc_notification', array(
                            'pid' => $pid
                        ));
                        
                        $this->delete_attachment_connect($pid, 'pid');
                        
                        $this->updateUserScore($uid, - $this->sys['scores_reply'], 7);
                        
                        $this->showMsg('删除成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('删除失败，请重试', 'error');
                    }
                }
                else
                {
                    $this->showMsg('此回复不存在或已删除', 'error');
                }
            }
        }
        else
        {
            $this->showMsg('抱歉，您无权删除此回复', 'error');
        }
    }
    
    public function deleteFloor()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['id']) && is_numeric($_POST['id']))
            {
                $id = intval($_POST['id']);
                
                if ($this->app->db()->has('roc_floor', array('id' => $id)))
                {
                    $uid = $this->app->db()->get('roc_floor', 'uid', array(
                        'id' => $id
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->app->db()->delete('roc_floor', array(
                            'id' => $id
                        ));
                    }
                    else
                    {
                        $groupid = $this->app->db()->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->app->db()->delete('roc_floor', array(
                                'id' => $id
                            ));
                        }
                        else
                        {
                            $this->showMsg('抱歉，您无权删除此评论', 'error');
                        }
                    }
                    if ($dID > 0)
                    {
                        $this->showMsg('删除成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('删除失败，请重试', 'error');
                    }
                }
                else
                {
                    $this->showMsg('此评论不存在或已删除', 'error');
                }
            }
        }
        else
        {
            $this->showMsg('抱歉，您无权删除此评论', 'error');
        }
    }

    public function deleteNotification()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['nid']) && is_numeric($_POST['nid']))
            {
                $nid = intval($_POST['nid']);

                if ($this->app->db()->has('roc_notification', array('AND'=>array('atuid'=>$this->loginInfo['uid'], 'nid'=>$nid))))
                {
                    $this->app->db()->delete('roc_notification', array('nid'=>$nid));

                    $this->showMsg('提醒删除成功', 'success');
                }
                else
                {
                    $this->showMsg('删除失败，请重试', 'error');
                }
            }
        }
    }

    public function deleteWhisper()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['id']) && is_numeric($_POST['id']))
            {
                $id = intval($_POST['id']);

                if ($this->app->db()->has('roc_whisper', array('id'=>$id)))
                {
                    $info = $this->app->db()->get('roc_whisper', array('uid', 'atuid', 'isread', 'del_flag'), array('id'=>$id));

                    if ($info['uid'] == $this->loginInfo['uid'] || $info['atuid'] == $this->loginInfo['uid'])
                    {
                        if ($info['uid'] == $this->loginInfo['uid'])
                        {
                            if ($info['isread'] == 0 || $info['del_flag'] == $info['atuid'])
                            {
                                $this->app->db()->delete('roc_whisper', array('id'=>$id));

                                $this->showMsg('私信双向删除成功（双方均不可见）', 'success');
                            }
                            else
                            {
                                $this->app->db()->update('roc_whisper', array('del_flag'=>$this->loginInfo['uid']), array('id'=>$id));

                                $this->showMsg('私信单向删除成功（对方仍可见）', 'success');
                            }
                        }
                        else
                        {
                            if ($info['isread'] == 0)
                            {
                                $this->app->db()->update('roc_whisper', array('isread'=>1, 'del_flag'=>$this->loginInfo['uid']), array('id'=>$id));

                                $this->showMsg('私信单向删除成功（对方仍可见）', 'success');
                            }
                            else
                            {
                                if ($info['del_flag'] == $info['uid'])
                                {
                                    $this->app->db()->delete('roc_whisper', array('id'=>$id));

                                    $this->showMsg('私信双向删除成功（双方均不可见）', 'success');
                                }
                                else
                                {
                                    $this->app->db()->update('roc_whisper', array('del_flag'=>$this->loginInfo['uid']), array('id'=>$id));

                                    $this->showMsg('私信单向删除成功（对方仍可见）', 'success');
                                }
                                
                            }
                        }
                    }
                    else
                    {
                        $this->showMsg('您没有权限删除本私信', 'error');
                    }
                }
                else
                {
                    $this->showMsg('不存在该私信', 'error');
                }
            }
        }
    }
    
    public function delPic()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['id']) && is_numeric($_POST['id']))
            {
                $id = intval($_POST['id']);
                
                if ($this->app->db()->has('roc_attachment', array(
                    'AND' => array(
                        'id' => $id,
                        'uid' => $this->loginInfo['uid']
                    )
                )))
                {
                    $path = $this->app->db()->get('roc_attachment', 'path', array(
                        'id' => $id
                    ));
                    
                    $dID = $this->app->db()->delete('roc_attachment', array(
                        'id' => $id
                    ));
                    
                    if ($dID > 0)
                    {
                        @unlink($path);
                        
                        @unlink($path . '.thumb.png');
                        
                        $this->showMsg('删除成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('删除失败，请重试', 'error');
                    }
                }
                else
                {
                    $this->showMsg('您无权删除此图片，或此图片已不存在', 'error');
                }
            }
        }
        else
        {
            $this->showMsg('抱歉，您无权删除本图片', 'error');
        }
    }
    
    public function favorTopic()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['tid'], $_POST['status']) && is_numeric($_POST['tid']) && is_numeric($_POST['status']))
            {
                $tid = intval($_POST['tid']);
                
                $status = intval($_POST['status']);
                
                if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
                {
                    if ($this->app->db()->has('roc_favorite', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->app->db()->delete('roc_favorite', array(
                            'AND' => array(
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid
                            )
                        ));
                    }
                    else
                    {
                        $resID = $this->app->db()->insert('roc_favorite', array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        ));
                    }
                    
                    if ($resID > 0)
                    {
                        $this->showMsg('操作成功', 'success', 1 - $status);
                    }
                    else
                    {
                        $this->showMsg('操作失败', 'error', 1 - $status);
                    }
                }
            }
        }
        else
        {
            $this->showMsg('您尚未登录，无权操作', 'error');
        }
    }
    
    public function praiseTopic()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['tid'], $_POST['status']) && is_numeric($_POST['tid']) && is_numeric($_POST['status']))
            {
                $tid = intval($_POST['tid']);
                
                $status = intval($_POST['status']);
                
                if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
                {
                    $topicUid = $this->app->db()->get('roc_topic', 'uid', array(
                        'tid' => $tid
                    ));
                    
                    if ($this->app->db()->has('roc_praise', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->app->db()->delete('roc_praise', array(
                            'AND' => array(
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid
                            )
                        ));
                        
                        $type = 8;
                        
                        $changed = -$this->sys['scores_praise'];
                    }
                    else
                    {
                        $resID = $this->app->db()->insert('roc_praise', array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        ));
                        
                        $type = 5;
                        
                        $changed = $this->sys['scores_praise'];
                    }
                    
                    if ($resID > 0)
                    {
                        $this->updateUserScore($topicUid, $changed, $type);
                        
                        $this->showMsg('操作成功', 'success', 1 - $status);
                    }
                    else
                    {
                        $this->showMsg('操作失败', 'error', 1 - $status);
                    }
                }
            }
        }
        else
        {
            $this->showMsg('您尚未登录，无权操作', 'error');
        }
    }
    
    public function follow()
    {
        if ($this->checkPrivate() && isset($_POST['uid']) && is_numeric($_POST['uid']))
        {
            $fuid = intval($_POST['uid']);
            
            if ($this->app->db()->has('roc_user', array('uid' => $fuid)))
            {
                if ($this->app->db()->has('roc_follow', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'fuid' => $fuid
                    )
                )))
                {
                    $this->app->db()->delete('roc_follow', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'fuid' => $fuid
                        )
                    ));
                    
                    $this->showMsg('取消关注成功', 'success', 1);
                }
                else
                {
                    $this->app->db()->insert('roc_follow', array(
                        'uid' => $this->loginInfo['uid'],
                        'fuid' => $fuid
                    ));
                    
                    $this->showMsg('关注成功', 'success', 0);
                }
            }
        }
    }
    
    public function doSign()
    {
        if ($this->checkPrivate() == true && $_POST['do'] == 'doSign')
        {
            if ($this->app->db()->has('roc_score', array(
                'AND' => array(
                    'uid' => $this->loginInfo['uid'],
                    'type' => 3,
                    'time[>]' => strtotime(date('Y-m-d', time()))
                )
            )))
            {
                $this->showMsg('您今天已经签到过啦~明天记得再来哦', 'error');
            }
            else
            {
                $signScore = $this->sys['scores_sign'];
                
                $this->updateUserScore($this->loginInfo['uid'], $signScore, 3);
                
                $this->showMsg('签到成功~恭喜你获得 ' . $signScore . ' 积分', 'success', $signScore);
            }
        }
    }

    public function readWhisper()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['id']))
            {
                $id = intval($_POST['id']);

                if ($this->app->db()->has('roc_whisper', array('AND'=>array('atuid'=>$this->loginInfo['uid'], 'id'=>$id, 'isread'=>0))))
                {
                    $this->app->db()->update('roc_whisper', array('isread'=>1), array('id'=>$id));
                }

                $this->showMsg('成功标记为已读', 'success');
            }
        }
    }
    
    public function deliverWhisper()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['atuid'], $_POST['content']) && is_numeric($_POST['atuid']) && $this->utils->getStrlen(trim($_POST['content'])) <= 250 && strlen($_POST['content']) > 0)
            {
                $atuid = intval($_POST['atuid']);

                if ($atuid == $this->loginInfo['uid'])
                {
                    $this->showMsg('不能私信自己', 'error');
                }
                
                $content = $this->topicIn(trim($_POST['content']));
                
                if ($this->app->db()->has('roc_user', array('uid' => $atuid)))
                {
                    $myScore = $this->app->db()->get('roc_user', 'scores', array('uid' => $this->loginInfo['uid']));
                    
                    if ($myScore - $this->sys['scores_whisper'] >= 0)
                    {
                        $WID = $this->app->db()->insert('roc_whisper', array(
                            'atuid' => $atuid,
                            'uid' => $this->loginInfo['uid'],
                            'content' => $content,
                            'posttime' => time(),
                            'isread' => 0,
                            'del_flag' => 0
                        ));
                        
                        if ($WID > 0)
                        {
                            $this->updateUserScore($this->loginInfo['uid'], - $this->sys['scores_whisper'], 4);
                            
                            $this->showMsg('私信成功，消耗了'.$this->sys['scores_whisper'].'积分', 'success');
                        }
                        else
                        {
                            $this->showMsg('传送失败，请重试', 'error');
                        }
                    }
                    else
                    {
                        $this->showMsg('您的积分不足，发送私信需消耗' . $this->sys['scores_whisper'] . '积分', 'error');
                    }
                }
                else
                {
                    $this->showMsg('该用户不存在', 'error');
                }
            }
            else
            {
                $this->showMsg('请检查您的输入是否合法', 'error');
            }
        }
        else
        {
            $this->showMsg('您尚未登录，无权操作', 'error');
        }
    }
    
    public function readNotification($nid)
    {
        if ($this->checkPrivate() == true)
        {
            $notifyInfo = $this->app->db()->get('roc_notification', array('atuid', 'isread', 'tid', 'pid'), array('nid'=>$nid));
            
            if ($notifyInfo['atuid'] == $this->loginInfo['uid'])
            {
                if ($notifyInfo['isread'] == 0)
                {
                    $this->app->db()->update('roc_notification', array(
                        'isread' => 1
                    ), array(
                        'nid' => $nid
                    ));
                }

                $this->app->redirect('/read/'.$notifyInfo['tid'].($notifyInfo['pid'] > 0 ? '#reply-'.$notifyInfo['pid'] : ''));
            }
        }
    }

    public function setEmail()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['email'], $_POST['password']))
            {
                $email = strtolower(stripslashes(trim($_POST['email'])));
                
                $password = stripslashes(trim($_POST['password']));
                
                if ($email == '' || $password == '')
                {
                    if ($email == '')
                    {
                        $this->showMsg('邮箱不能为空', 'error', 1);
                    }
                    if ($password == '')
                    {
                        $this->showMsg('密码不能为空', 'error', 3);
                    }
                }
                
                if (!$this->utils->checkEmailValidity($email))
                {
                    $this->showMsg('邮件地址不正确', 'error', 1);
                }
                
                if ($this->app->db()->has('roc_user', array('email' => $email)))
                {
                    $this->showMsg('邮件地址已被占用', 'error', 1);
                }
                
                if ($this->app->db()->has('roc_user', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'password' => md5($password)
                    )
                )))
                {
                    $resID = $this->app->db()->update('roc_user', array(
                        'email' => $email
                    ), array(
                        'uid' => $this->loginInfo['uid']
                    ));
                    
                    if ($resID > 0)
                    {
                        $this->showMsg('邮箱设置成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('邮箱设置失败', 'error');
                    }
                }
                else
                {
                    $this->showMsg('密码验证失败，请重试', 'error', 1);
                }
            }
        }
    }
    
    public function setSignature()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['signature']))
            {
                $signature = $this->topicIn($_POST['signature']);
                
                if (empty($signature))
                {
                    $this->showMsg('个性签名不能为空', 'error', 1);
                }
                
                if ($this->utils->getStrlen($signature) >= 32)
                {
                    $this->showMsg('个性签名不能超过32个字', 'error', 1);
                }
                
                $resID = $this->app->db()->update('roc_user', array(
                    'signature' => $signature
                ), array(
                    'uid' => $this->loginInfo['uid']
                ));
                
                if ($resID > 0)
                {
                    $this->showMsg('个性签名设置成功', 'success');
                }
                else
                {
                    $this->showMsg('个性签名设置失败', 'error');
                }
            }
        }
    }
    
    public function setPassword()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['password'], $_POST['newPassword']))
            {
                $password = stripslashes(trim($_POST['password']));
                
                $newPassword = stripslashes(trim($_POST['newPassword']));
                
                $userOriPassword = $this->app->db()->get('roc_user', 'password', array(
                    'uid' => $this->loginInfo['uid']
                ));
                
                if ($this->utils->getStrlen($newPassword) < 6)
                {
                    $this->showMsg('密码长度不能低于6位', 'error');
                }
                
                if ($this->app->db()->has('roc_user', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'password' => md5($password)
                    )
                )) || $userOriPassword == '')
                {
                    $resID = $this->app->db()->update('roc_user', array(
                        'password' => md5($newPassword)
                    ), array(
                        'uid' => $this->loginInfo['uid']
                    ));
                    
                    if ($resID > 0)
                    {
                        $this->showMsg('新密码设置成功', 'success');
                    }
                    else
                    {
                        $this->showMsg('新密码设置失败', 'error');
                    }
                }
                else
                {
                    $this->showMsg('密码验证失败，请重试', 'error', 1);
                }
            }
        }
    }
    
    private function delete_attachment_connect($id, $type)
    {
        if ($this->app->db()->has('roc_attachment', array($type => $id)))
        {
            $path = $this->app->db()->select('roc_attachment', 'path', array(
                $type => $id
            ));
            
            $this->app->db()->delete('roc_attachment', array(
                $type => $id
            ));
            
            foreach ($path as $key => $value)
            {
                @unlink($value);
                
                @unlink($value . '.thumb.png');
            }
        }
    }
    
    private function updateAttachment($content, $array)
    {
        preg_match_all('/\[:([0-9]+)\]/i', $content, $attachment);
        
        foreach ($attachment[1] as $k => $v)
        {
            if ($this->app->db()->has('roc_attachment', array(
                'AND' => array(
                    'uid' => $this->loginInfo['uid'],
                    'id' => $v,
                    'pid' => 0
                )
            )))
            {
                $this->app->db()->update('roc_attachment', $array, array(
                    'id' => $v
                ));
            }
        }
    }
    
    private function makeDir($path)
    {
        if (!is_dir($path))
        {
            $pathArray = explode("/", $path);
            
            $_path = '';
            
            for ($i = 0; $i < count($pathArray); $i++)
            {
                $_path .= $pathArray[$i] . "/";
                
                if ($pathArray[$i] != "" && !file_exists($_path))
                {
                    mkdir($_path, 0777);
                }
            }
        }
    }
    
    private function doAtUser($content)
    {
        $atUidArray = array();
        
        preg_match_all("@\@(.*?)([\s]+)@is", $content . " ", $nameArray);
        
        if (isset($nameArray[1]))
        {
            $writeName = array();
            
            foreach ($nameArray[1] as $name)
            {
                if (in_array(strtolower($name), $writeName))
                {
                    continue;
                }
                
                array_push($writeName, strtolower($name));
                
                $userInfo = $this->app->db()->get('roc_user', array(
                    'uid',
                    'username'
                ), array(
                    'username' => $name
                ));
                
                if (empty($userInfo['username']))
                {
                    $content = str_ireplace('@' . $name . ' ', '@' . $name . ' ', $content . ' ');
                }
                else
                {
                    if ($userInfo['uid'] == $this->loginInfo['uid'])
                    {
                        $content = str_ireplace('@' . $name . ' ', ' ', $content . ' ');
                    }
                    else
                    {
                        $content = str_ireplace('@' . $name . ' ', '@' . $userInfo['username'] . ' ', $content . ' ');
                        
                        array_push($atUidArray, $userInfo['uid']);
                    }
                }
            }
        }
        
        return array(
            'content' => $content,
            'atUidArray' => $atUidArray
        );
    }
    
    private function checkFloodTime($uid, $allowTime)
    {
        if ($this->app->db()->has('roc_user', array('uid' => $uid)))
        {
            $lasttime = $this->app->db()->get('roc_user', 'lasttime', array(
                'uid' => $uid
            ));
            
            if (time() - $lasttime < $allowTime)
            {
                $this->showMsg('您太活跃了，防水策略生效中，请稍后再试', 'error');
            }
        }
        else
        {
            $this->showMsg('抱歉，请求非法！', 'error');
        }
    }
    
    private function clientLoad()
    {
        # 初始化工具库
        $this->client = new \system\util\Client();
    }

    private function checkPrivate($type = 0)
    {
        if ($type == 0)
        {
            if ($this->loginInfo['uid'] > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $groupid = $this->app->db()->get('roc_user', 'groupid', array(
                'uid' => $this->loginInfo['uid']
            ));
            
            if ($this->loginInfo['uid'] > 0 && $groupid != 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}
?>