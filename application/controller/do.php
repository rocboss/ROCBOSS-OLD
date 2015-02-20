<?php
!defined('ROC') && exit('REFUSED!');
Class doControl extends commonControl
{
    public $page;
    public $per = 30;
    public function postTopic()
    {
        if ($this->checkPrivate(1) == true)
        {
            $this->checkFloodTime($this->loginInfo['uid'], 30);
            
            if (isset($_POST['title'], $_POST['msg'], $_POST['tag']) && Filter::topicIn($_POST['msg']) != '')
            {
                if (trim($_POST['tag']) != '')
                {
                    $tagArray = array_filter(explode(' ', trim($_POST['tag'])));
                }
                
                $contentReturn = $this->doAtUser(Filter::topicIn($_POST['msg']));
                
                $topicArray = array(
                    'uid' => $this->loginInfo['uid'],
                    
                    'title' => (trim($_POST['title']) != '') ? Filter::topicIn($_POST['title']) : Utils::cutSubstr(Filter::topicIn($_POST['msg'])),
                    
                    'content' => $contentReturn['content'],
                    
                    'comments' => 0,
                    
                    'client' => Utils::getClient(),
                    
                    'istop' => 0,
                    
                    'islock' => 0,
                    
                    'posttime' => time(),
                    
                    'lasttime' => time()
                );
                
                $insertTopicID = $this->db->insert('roc_topic', $topicArray);
                
                if ($insertTopicID > 0)
                {
                    if (is_array($tagArray) && !empty($tagArray))
                    {
                        foreach ($tagArray as $k => $v)
                        {
                            if ($this->db->has('roc_tag', array(
                                'tagname' => Filter::in($v)
                            )))
                            {
                                $this->db->update('roc_tag', array(
                                    'used[+]' => 1
                                ), array(
                                    'tagname' => Filter::in($v)
                                ));
                                
                                $insertTagID = $this->db->get('roc_tag', 'tagid', array(
                                    'tagname' => Filter::in($v)
                                ));
                                
                                $this->db->insert('roc_topic_tag_connection', array(
                                    'tid' => $insertTopicID,
                                    'tagid' => $insertTagID
                                ));
                            }
                            else
                            {
                                $insertTagID = $this->db->insert('roc_tag', array(
                                    'tagname' => Filter::in($v),
                                    'used' => 1
                                ));
                                
                                if ($insertTagID > 0)
                                {
                                    $this->db->insert('roc_topic_tag_connection', array(
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
                            $this->db->insert('roc_notification', array(
                                'atuid' => $atuid,
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $insertTopicID,
                                'pid' => 0,
                                'fid' => 0,
                                'isread' => 0
                            ));
                        }
                    }
                    
                    $this->updateAttachment(Filter::topicIn($_POST['msg']), array(
                        'tid' => $insertTopicID
                    ));
                    
                    $this->updateLasttime($this->loginInfo['uid']);
                    
                    $this->updateUserScore($this->loginInfo['uid'], $GLOBALS['sys_config']['scores']['topic'], 1);
                    
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
            $this->showMsg('您尚未登录或已被禁言，无法创建新主题哦~', 'error');
        }
    }
    
    public function postReply()
    {
        if ($this->checkPrivate(1) == true)
        {
            $this->checkFloodTime($this->loginInfo['uid'], 15);
            
            if (isset($_POST['content'], $_POST['tid']) && Filter::topicIn($_POST['content']) != '' && is_numeric($_POST['tid']) && Utils::getStrlen($_POST['content']) <= 250)
            {
                $tid = intval($_POST['tid']);
                
                if ($this->db->has('roc_topic', array(
                    'tid' => $tid
                )))
                {
                    if ($this->db->get('roc_topic', 'islock', array(
                        'tid' => $tid
                    )) == 1)
                    {
                        $this->showMsg('抱歉，主题已锁，无法再回复了', 'error');
                    }
                    
                    $contentReturn = $this->doAtUser(Filter::topicIn($_POST['content']));
                    
                    $topicArray = array(
                        'tid' => $tid,
                        
                        'uid' => $this->loginInfo['uid'],
                        
                        'content' => $contentReturn['content'],
                        
                        'client' => Utils::getClient(),
                        
                        'posttime' => time()
                    );
                    
                    $insertReplyID = $this->db->insert('roc_reply', $topicArray);
                    
                    if ($insertReplyID > 0)
                    {
                        $this->db->update('roc_topic', array(
                            'comments[+]' => 1,
                            'lasttime' => time()
                        ), array(
                            'tid' => $tid
                        ));
                        
                        $this->updateAttachment(Filter::topicIn($_POST['content']), array(
                            'pid' => $insertReplyID
                        ));
                        
                        if (!empty($contentReturn['atUidArray']))
                        {
                            foreach ($contentReturn['atUidArray'] as $atuid)
                            {
                                $this->db->insert('roc_notification', array(
                                    'atuid' => $atuid,
                                    'uid' => $this->loginInfo['uid'],
                                    'tid' => $tid,
                                    'pid' => $insertReplyID,
                                    'fid' => 0,
                                    'isread' => 0
                                ));
                            }
                        }
                        
                        $authorUid = $this->db->get('roc_topic', 'uid', array(
                            'tid' => $tid
                        ));
                        
                        if (!in_array($authorUid, $contentReturn['atUidArray']) && $authorUid != $this->loginInfo['uid'])
                        {
                            $this->db->insert('roc_notification', array(
                                'atuid' => $authorUid,
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid,
                                'pid' => $insertReplyID,
                                'fid' => 0,
                                'isread' => 0
                            ));
                        }
                        
                        $this->updateLasttime($this->loginInfo['uid']);
                        
                        $this->updateUserScore($this->loginInfo['uid'], $GLOBALS['sys_config']['scores']['reply'], 2);
                        
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
            
            if (isset($_POST['content'], $_POST['pid']) && Filter::topicIn($_POST['content']) != '' && Utils::getStrlen($_POST['content']) <= 100 && is_numeric($_POST['pid']))
            {
                $pid = intval($_POST['pid']);
                
                if ($this->db->has('roc_reply', array(
                    'pid' => $pid
                )))
                {
                    $contentReturn = $this->doAtUser(Filter::topicIn($_POST['content']));
                    
                    $tid = $this->db->get('roc_reply', 'tid', array(
                        'pid' => $pid
                    ));
                    
                    $floorArray = array(
                        'pid' => $pid,
                        
                        'uid' => $this->loginInfo['uid'],
                        
                        'content' => $contentReturn['content'],
                        
                        'posttime' => time()
                    );
                    
                    $insertFloorID = $this->db->insert('roc_floor', $floorArray);
                    
                    if ($insertFloorID > 0)
                    {
                        $this->updateLasttime($this->loginInfo['uid']);
                        
                        if (!empty($contentReturn['atUidArray']))
                        {
                            foreach ($contentReturn['atUidArray'] as $atuid)
                            {
                                $this->db->insert('roc_notification', array(
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

        echo '<script>history.go(-1);</script>';
    }

    public function lasttime()
    {
        setcookie('type','lasttime', time()+1209600, '/');

        echo '<script>history.go(-1);</script>';
    }
    
    public function uploadPicture()
    {
        if ($this->checkPrivate(1) == true)
        {
            $time = time();
            
            $img = $_POST['base64'];
            
            $path = 'application/uploads/pictures/' . date('Y/n/j', $time);
            
            if (isset($img))
            {
                if (preg_match('/data:image\/([^;]*);base64,(.*)/', $img, $matches))
                {
                    $this->makeDir($path);
                    
                    $img = base64_decode($matches[2]);
                    
                    $target = $path . '/' . md5($time . '_' . $this->loginInfo['uid'] . '_' . rand(1000, 9999)) . '.png';
                    
                    @file_put_contents($target, $img);
                    
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
                    
                    $aArray = array(
                        'uid' => $this->loginInfo['uid'],
                        
                        'path' => $target,
                        
                        'time' => $time,
                        
                        'tid' => 0,
                        
                        'pid' => 0
                    );
                    
                    $aID = $this->db->insert('roc_attachment', $aArray);
                    
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
            
            $path = 'application/uploads/avatars/' . intval($this->loginInfo['uid'] / 1000) . '/' . $this->loginInfo['uid'];
            
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
                
                if ($this->db->has('roc_topic', array(
                    'tid' => $tid
                )))
                {
                    $uid = $this->db->get('roc_topic', 'uid', array(
                        'tid' => $tid
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->db->delete('roc_topic', array(
                            'tid' => $tid
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_topic', array(
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
                        $pidArray = $this->db->select('roc_reply', 'pid', array(
                            'tid' => $tid
                        ));
                        
                        foreach ($pidArray as $key => $value)
                        {
                            $this->db->delete('roc_floor', array(
                                'pid' => $value
                            ));
                        }
                        
                        $tagidArray = $this->db->select('roc_topic_tag_connection', 'tagid', array(
                            'tid' => $tid
                        ));
                        
                        foreach ($tagidArray as $key => $value)
                        {
                            $used = $this->db->get('roc_tag', 'used', array(
                                'tagid' => $value
                            ));
                            
                            if ($used > 1)
                            {
                                $this->db->update('roc_tag', array(
                                    'used[-]' => 1
                                ), array(
                                    'tagid' => $value
                                ));
                            }
                            else
                            {
                                $this->db->delete('roc_tag', array(
                                    'tagid' => $value
                                ));
                            }
                        }
                        
                        $this->db->delete('roc_topic_tag_connection', array(
                            'tid' => $tid
                        ));
                        
                        $this->db->delete('roc_reply', array(
                            'tid' => $tid
                        ));
                        
                        $this->db->delete('roc_notification', array(
                            'tid' => $tid
                        ));
                        
                        $this->db->delete('roc_favorite', array(
                            'tid' => $tid
                        ));
                        
                        $this->delete_attachment_connect($tid, 'tid');
                        
                        $this->updateUserScore($uid, -$GLOBALS['sys_config']['scores']['topic'], 6);
                        
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
                
                if ($this->db->has('roc_reply', array(
                    'pid' => $pid
                )))
                {
                    $uid = $this->db->get('roc_reply', 'uid', array(
                        'pid' => $pid
                    ));
                    
                    $tid = $this->db->get('roc_reply', 'tid', array(
                        'pid' => $pid
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->db->delete('roc_reply', array(
                            'pid' => $pid
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_reply', array(
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
                        $this->db->update('roc_topic', array(
                            'comments[-]' => 1
                        ), array(
                            'tid' => $tid
                        ));
                        
                        $this->db->delete('roc_floor', array(
                            'pid' => $pid
                        ));
                        
                        $this->db->delete('roc_notification', array(
                            'pid' => $pid
                        ));
                        
                        $this->delete_attachment_connect($pid, 'pid');
                        
                        $this->updateUserScore($uid, -$GLOBALS['sys_config']['scores']['reply'], 7);
                        
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
                
                if ($this->db->has('roc_floor', array(
                    'id' => $id
                )))
                {
                    $uid = $this->db->get('roc_floor', 'uid', array(
                        'id' => $id
                    ));
                    
                    if ($uid == $this->loginInfo['uid'])
                    {
                        $dID = $this->db->delete('roc_floor', array(
                            'id' => $id
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $this->loginInfo['uid']
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_floor', array(
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
    
    public function delPic()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['id']) && is_numeric($_POST['id']))
            {
                $id = intval($_POST['id']);
                
                if ($this->db->has('roc_attachment', array(
                    'AND' => array(
                        'id' => $id,
                        'uid' => $this->loginInfo['uid']
                    )
                )))
                {
                    $path = $this->db->get('roc_attachment', 'path', array(
                        'id' => $id
                    ));
                    
                    $dID = $this->db->delete('roc_attachment', array(
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
                
                if ($this->db->has('roc_topic', array(
                    'tid' => $tid
                )))
                {
                    if ($this->db->has('roc_favorite', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->db->delete('roc_favorite', array(
                            'AND' => array(
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid
                            )
                        ));
                    }
                    else
                    {
                        $resID = $this->db->insert('roc_favorite', array(
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
                
                if ($this->db->has('roc_topic', array(
                    'tid' => $tid
                )))
                {
                    $topicUid = $this->db->get('roc_topic', 'uid', array(
                        'tid' => $tid
                    ));
                    
                    if ($this->db->has('roc_praise', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->db->delete('roc_praise', array(
                            'AND' => array(
                                'uid' => $this->loginInfo['uid'],
                                'tid' => $tid
                            )
                        ));
                        
                        $type = 8;
                        
                        $changed = -$GLOBALS['sys_config']['scores']['praise'];
                    }
                    else
                    {
                        $resID = $this->db->insert('roc_praise', array(
                            'uid' => $this->loginInfo['uid'],
                            'tid' => $tid
                        ));
                        
                        $type = 5;
                        
                        $changed = $GLOBALS['sys_config']['scores']['praise'];
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
            
            if ($this->db->has('roc_user', array(
                'uid' => $fuid
            )))
            {
                if ($this->db->has('roc_follow', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'fuid' => $fuid
                    )
                )))
                {
                    $this->db->delete('roc_follow', array(
                        'AND' => array(
                            'uid' => $this->loginInfo['uid'],
                            'fuid' => $fuid
                        )
                    ));
                    
                    $this->showMsg('取消关注成功', 'success', 1);
                }
                else
                {
                    $this->db->insert('roc_follow', array(
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
            if ($this->db->has('roc_score', array(
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
                $signScore = $GLOBALS['sys_config']['scores']['sign'];
                
                $this->updateUserScore($this->loginInfo['uid'], $signScore, 3);
                
                $this->showMsg('签到成功~恭喜你获得 ' . $signScore . ' 积分', 'success', $signScore);
            }
        }
    }
    
    public function deliverWhisper()
    {
        if ($this->checkPrivate() == true)
        {
            if (isset($_POST['atuid'], $_POST['content']) && is_numeric($_POST['atuid']) && Utils::getStrlen($_POST['content']) < 250 && strlen($_POST['content']) > 0)
            {
                $atuid = intval($_POST['atuid']);
                
                $content = Filter::in(trim($_POST['content']));
                
                if ($this->db->has('roc_user', array(
                    'uid' => $atuid
                )))
                {
                    $myScore = $this->db->get('roc_user', 'scores', array(
                        'uid' => $this->loginInfo['uid']
                    ));
                    
                    if ($myScore - $GLOBALS['sys_config']['scores']['whisper'] >= 0)
                    {
                        $WID = $this->db->insert('roc_whisper', array(
                            'atuid' => $atuid,
                            'uid' => $this->loginInfo['uid'],
                            'content' => $content,
                            'posttime' => time(),
                            'isread' => 0,
                            'del_flag' => 0
                        ));
                        
                        if ($WID > 0)
                        {
                            $this->updateUserScore($this->loginInfo['uid'], -$GLOBALS['sys_config']['scores']['whisper'], 4);
                            
                            $this->showMsg('私信成功~', 'success');
                        }
                        else
                        {
                            $this->showMsg('传送失败，请重试', 'error');
                        }
                    }
                    else
                    {
                        $this->showMsg('您的积分不足，发送私信需消耗' . $GLOBALS['sys_config']['scores']['whisper'] . '积分', 'error');
                    }
                }
                else
                {
                    $this->showMsg('该用户不存在', 'error');
                }
            }
            else
            {
                $this->showMsg('请检车您的输入是否合法', 'error');
            }
        }
        else
        {
            $this->showMsg('您尚未登录，无权操作', 'error');
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
                
                if (!Utils::checkEmailValidity($email))
                {
                    $this->showMsg('邮件地址不正确', 'error', 1);
                }
                
                if ($this->db->has('roc_user', array(
                    'email' => $email
                )))
                {
                    $this->showMsg('邮件地址已被占用', 'error', 1);
                }
                
                if ($this->db->has('roc_user', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'password' => md5($password)
                    )
                )))
                {
                    $resID = $this->db->update('roc_user', array(
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
                $signature = Filter::in($_POST['signature']);
                
                if (empty($signature))
                {
                    $this->showMsg('个性签名不能为空', 'error', 1);
                }
                
                if (Utils::getStrlen($signature) >= 32)
                {
                    $this->showMsg('个性签名不能超过32个字', 'error', 1);
                }
                
                $resID = $this->db->update('roc_user', array(
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
                
                $userOriPassword = $this->db->get('roc_user', 'password', array(
                    'uid' => $this->loginInfo['uid']
                ));
                
                if (Utils::getStrlen($newPassword) < 6)
                {
                    $this->showMsg('密码长度不能低于6位', 'error');
                }
                
                if ($this->db->has('roc_user', array(
                    'AND' => array(
                        'uid' => $this->loginInfo['uid'],
                        'password' => md5($password)
                    )
                )) || $userOriPassword == '')
                {
                    $resID = $this->db->update('roc_user', array(
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
        if ($this->db->has('roc_attachment', array(
            $type => $id
        )))
        {
            $path = $this->db->select('roc_attachment', 'path', array(
                $type => $id
            ));
            
            $this->db->delete('roc_attachment', array(
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
            if ($this->db->has('roc_attachment', array(
                'AND' => array(
                    'uid' => $this->loginInfo['uid'],
                    'id' => $v,
                    'pid' => 0
                )
            )))
            {
                $this->db->update('roc_attachment', $array, array(
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
                
                $userInfo = $this->db->get('roc_user', array(
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
        if ($this->db->has('roc_user', array(
            'uid' => $uid
        )))
        {
            $lasttime = $this->db->get('roc_user', 'lasttime', array(
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
            $groupid = $this->db->get('roc_user', 'groupid', array(
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