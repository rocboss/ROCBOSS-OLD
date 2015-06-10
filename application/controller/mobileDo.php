<?php
!defined('ROC') && exit('REFUSED!');
Class mobileDoControl extends commonControl
{
    public $page;
    public $per = 20;
    public function postTopic()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        $client = $_POST['client'];
        
        if ($this->checkPrivate(1,$loginUid) == true)
        {
            $isFlood = $this->checkFloodTime($loginUid, 2);
            
            if($isFlood == 3){
                
                $this->echoAppJsonResult('用户Id不存在',array(),1);
                
                return;
                
            }  elseif ($isFlood == 1) {
                
                $this->echoAppJsonResult('不允许灌水',array(),1);
                
                return;
            }
            
            if (isset($_POST['title'], $_POST['msg'], $_POST['tag']) && Filter::topicIn($_POST['msg']) != '')
            {
                if (trim($_POST['tag']) != '')
                {
                    $tagArray = array_filter(explode(',', trim($_POST['tag'])));
                }
                
                $contentReturn = $this->doAtUser(Filter::topicIn($_POST['msg']),$loginUid);
                
                $topicArray = array(
                    'uid' => $loginUid,
                    
                    'title' => (trim($_POST['title']) != '') ? Filter::topicIn($_POST['title']) : Utils::cutSubstr(Filter::topicIn($_POST['msg'])),
                    
                    'content' => $contentReturn['content'],
                    
                    'comments' => 0,
                    
                    'client' => $client,
                    
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
                    
                    $this->updateLasttime($loginUid);
                    
                    $this->updateUserScore($loginUid, $GLOBALS['sys_config']['scores']['topic'], 1);
                    
                    $this->echoAppJsonResult('发表成功~', array('topicId'=>$insertTopicID),0);
                }
                else
                {
                    $this->echoAppJsonResult('发表失败，请重试！',array(),1);
                }
            }
            else
            {
                $this->echoAppJsonResult('请检查您的输入是否合法，正文详情必填哦~',array(),2);
            }
        }
        else
        {
            $this->echoAppJsonResult('您尚未登录或已被禁言，无法创建新主题哦~',array(),3);
        }
    }
    
    public function postReply()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        $client = $_POST['client'];
        
        if ($this->checkPrivate(1,$loginUid) == true)
        {
            $isFlood = $this->checkFloodTime($loginUid, 2);
            
            if($isFlood == 3){
                
                $this->echoAppJsonResult('用户Id不存在',array(),1);
                
                return;
                
            }  elseif ($isFlood == 1) {
                
                $this->echoAppJsonResult('不允许灌水',array(),1);
                
                return;
            }
            
            if (isset($_POST['content'], $_POST['tid']) && Filter::topicIn($_POST['content']) != '' && is_numeric($_POST['tid']) && Utils::getStrlen($_POST['content']) <= 3000)
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
                        $this->echoAppJsonResult('抱歉，主题已锁，无法再回复了',array(),1);
                        return;
                    }
                    
                    $contentReturn = $this->doAtUser(Filter::topicIn($_POST['content']),$loginUid);
                    
                    $topicArray = array(
                        'tid' => $tid,
                        
                        'uid' => $loginUid,
                        
                        'content' => $contentReturn['content'],
                        
                        'client' => $client,
                        
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
                                    'uid' => $loginUid,
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
                                'uid' => $loginUid,
                                'tid' => $tid,
                                'pid' => $insertReplyID,
                                'fid' => 0,
                                'isread' => 0
                            ));
                        }
                        
                        $this->updateLasttime($loginUid);
                        
                        $this->updateUserScore($loginUid, $GLOBALS['sys_config']['scores']['reply'], 2);
                        
                        $this->echoAppJsonResult('发表成功~',array('replyId'=>$insertReplyID),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('发表失败，请重试！',array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('该帖子不存在，无法回复！',array(),3);
                }
            }
            else
            {
                $this->echoAppJsonResult('请检查您的输入是否合法，回复非空且不能超过3000个字',array(),4);
            }
        }
        else
        {
            $this->echoAppJsonResult('您尚未登录或已被禁言，无法创建新主题哦~',array(),5);
        }
    }
    
    public function postFloor()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(1,$loginUid) == true)
        {
            $isFlood = $this->checkFloodTime($loginUid, 2);
            
            if($isFlood == 3){
                
                $this->echoAppJsonResult('用户Id不存在',array(),1);
                
                return;
                
            }  elseif ($isFlood == 1) {
                
                $this->echoAppJsonResult('不允许灌水',array(),1);
                
                return;
            }
            
            if (isset($_POST['content'], $_POST['pid']) && Filter::topicIn($_POST['content']) != '' && Utils::getStrlen($_POST['content']) <= 500 && is_numeric($_POST['pid']))
            {
                $pid = intval($_POST['pid']);
                
                if ($this->db->has('roc_reply', array(
                    'pid' => $pid
                )))
                {
                    $contentReturn = $this->doAtUser(Filter::topicIn($_POST['content']),$loginUid);
                    
                    $tid = $this->db->get('roc_reply', 'tid', array(
                        'pid' => $pid
                    ));
                    
                    $floorArray = array(
                        'pid' => $pid,
                        
                        'uid' => $loginUid,
                        
                        'content' => $contentReturn['content'],
                        
                        'posttime' => time()
                    );
                    
                    $insertFloorID = $this->db->insert('roc_floor', $floorArray);
                    
                    if ($insertFloorID > 0)
                    {
                        $this->updateLasttime($loginUid);
                        
                        if (!empty($contentReturn['atUidArray']))
                        {
                            foreach ($contentReturn['atUidArray'] as $atuid)
                            {
                                $this->db->insert('roc_notification', array(
                                    'atuid' => $atuid,
                                    'uid' => $loginUid,
                                    'tid' => $tid,
                                    'pid' => $pid,
                                    'fid' => $insertFloorID,
                                    'isread' => 0
                                ));
                            }
                        }
                        
                        $this->echoAppJsonResult('评论成功~',array('replyFloorId'=>$insertFloorID),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('评论失败，请重试！',array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('非法pid参数，请检查您的输入',array(),3);
                }
            }
            else
            {
                $this->echoAppJsonResult('请检查您的输入是否合法，评论不可为空且不能超过500字',array(),4);
            }
        }
        else
        {
            $this->echoAppJsonResult('您已被禁言，无法评论哦~',array(),5);
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
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(1,$loginUid) == true)
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
                    
                    $target = $path . '/' . md5($time . '_' . $loginUid . '_' . rand(1000, 9999)) . '.png';
                    
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
                        'uid' => $loginUid,
                        
                        'path' => $target,
                        
                        'time' => $time,
                        
                        'tid' => 0,
                        
                        'pid' => 0
                    );
                    
                    $aID = $this->db->insert('roc_attachment', $aArray);
                    
                    if ($aID > 0)
                    {
                        $this->echoAppJsonResult('图片上传成功',array('pictureId'=>$aID),0);
                    }
                    else
                    {
                        @unlink($target);
                        
                        @unlink($target . '.thumb.png');
                        
                        $this->echoAppJsonResult('图片上传处理失败，请重试',array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('图片上传失败，请检查上传文件是否合法',array(),3);
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('您已被禁言，无权上传图片哦~',array(),4);
        }
    }
    
    public function uploadAvatar()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
        {
            $time = time();
            
            $img = $_POST['base64'];
            
            $path = 'application/uploads/avatars/' . intval($loginUid / 1000) . '/' . $loginUid;
            
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
                    
                    $this->echoAppJsonResult('头像上传成功',array(),0);
                }
                else
                {
                    $this->echoAppJsonResult('头像上传失败，请检查上传文件是否合法',array(),1);
                }
            }
        }
    }
    
    public function uploadHomeThemeBack()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
        {
            $time = time();
            
            $img = $_POST['base64'];
            
            $path = 'application/uploads/homeThemeBacks/' . intval($loginUid / 1000) . '/' . $loginUid;
            
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
                        $width = 500 * $i;
                        
                        $height = 500 * $i;
                        
                        $image_p = imagecreatetruecolor($width, $height);
                        
                        $image = imagecreatefromjpeg($target);
                        
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                        
                        imagejpeg($image_p, $path . '/' . (500 * $i) . '.png', 100);
                        
                        imagedestroy($image_p);
                    }
                    
                    @unlink($target);
                    
                    $this->echoAppJsonResult('主页主题背景上传成功',array(),0);
                }
                else
                {
                    $this->echoAppJsonResult('主页主题背景上传成功，请检查上传文件是否合法',array(),1);
                }
            }
        }
    }
    
    public function deleteTopic()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
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
                    
                    if ($uid == $loginUid)
                    {
                        $dID = $this->db->delete('roc_topic', array(
                            'tid' => $tid
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $loginUid
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_topic', array(
                                'tid' => $tid
                            ));
                        }
                        else
                        {
                            $this->echoAppJsonResult('抱歉，您无权删除此主题',array(),1);
                            return;
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
                        
                        $this->echoAppJsonResult('删除成功',array(),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('删除失败，请重试',array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('此主题不存在或已删除',array(),3);
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('抱歉，您无权删除此主题', array(),4);
        }
    }
    
    public function deleteReply()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
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
                    
                    if ($uid == $loginUid)
                    {
                        $dID = $this->db->delete('roc_reply', array(
                            'pid' => $pid
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $loginUid
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_reply', array(
                                'pid' => $pid
                            ));
                        }
                        else
                        {
                            $this->echoAppJsonResult('抱歉，您无权删除此回复',array(),1);
                            return;
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
                        
                        $this->echoAppJsonResult('删除成功',array(),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('删除失败，请重试', array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('此回复不存在或已删除', array(),3);
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('抱歉，您无权删除此回复', array(),4);
        }
    }
    
    public function deleteFloor()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
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
                    
                    if ($uid == $loginUid)
                    {
                        $dID = $this->db->delete('roc_floor', array(
                            'id' => $id
                        ));
                    }
                    else
                    {
                        $groupid = $this->db->get('roc_user', 'groupid', array(
                            'uid' => $loginUid
                        ));
                        
                        if ($groupid == 9)
                        {
                            $dID = $this->db->delete('roc_floor', array(
                                'id' => $id
                            ));
                        }
                        else
                        {
                            $this->echoAppJsonResult('抱歉，您无权删除此评论',array(),1);
                            return;
                        }
                    }
                    if ($dID > 0)
                    {
                        $this->echoAppJsonResult('删除成功', array(),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('删除失败，请重试', array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('此评论不存在或已删除', array(),3);
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('抱歉，您无权删除此评论', array(),4);
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
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
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
                            'uid' => $loginUid,
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->db->delete('roc_favorite', array(
                            'AND' => array(
                                'uid' => $loginUid,
                                'tid' => $tid
                            )
                        ));
                    }
                    else
                    {
                        $resID = $this->db->insert('roc_favorite', array(
                            'uid' => $loginUid,
                            'tid' => $tid
                        ));
                    }
                    
                    if ($resID > 0)
                    {
                        if($status == 0){
                            
                            $this->echoAppJsonResult('收藏成功',array(),0);
                            
                        }else{
                            
                            $this->echoAppJsonResult('取消收藏',array(),0);

                        }
                    }
                    else
                    {
                        $this->echoAppJsonResult('操作失败', array(), 2);
                    }
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('您尚未登录，无权操作',array(),3);
        }
    }
    
    public function praiseTopic()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
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
                            'uid' => $loginUid,
                            'tid' => $tid
                        )
                    )))
                    {
                        $resID = $this->db->delete('roc_praise', array(
                            'AND' => array(
                                'uid' => $loginUid,
                                'tid' => $tid
                            )
                        ));
                        
                        $type = 8;
                        
                        $changed = -$GLOBALS['sys_config']['scores']['praise'];
                    }
                    else
                    {
                        $resID = $this->db->insert('roc_praise', array(
                            'uid' => $loginUid,
                            'tid' => $tid
                        ));
                        
                        $type = 5;
                        
                        $changed = $GLOBALS['sys_config']['scores']['praise'];
                    }
                    
                    if ($resID > 0)
                    {
                        $this->updateUserScore($topicUid, $changed, $type);
                        
                        $this->echoAppJsonResult('操作成功', array(), 0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('操作失败', array(), 1);
                    }
                }
            }
        }
        else
        {
            $this->echoAppJsonResult('您尚未登录，无权操作', array(),2);
        }
    }
    
    public function follow()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) && isset($_POST['uid']) && is_numeric($_POST['uid']))
        {
            $fuid = intval($_POST['uid']);
            
            if ($this->db->has('roc_user', array(
                'uid' => $fuid
            )))
            {
                if ($this->db->has('roc_follow', array(
                    'AND' => array(
                        'uid' => $loginUid,
                        'fuid' => $fuid
                    )
                )))
                {
                    $this->db->delete('roc_follow', array(
                        'AND' => array(
                            'uid' => $loginUid,
                            'fuid' => $fuid
                        )
                    ));
                    
                    $this->echoAppJsonResult('取消关注成功', array('status'=>0), 0);
                }
                else
                {
                    $this->db->insert('roc_follow', array(
                        'uid' => $loginUid,
                        'fuid' => $fuid
                    ));
                    
                    $this->echoAppJsonResult('关注成功', array('status'=>1), 0);
                }
            }
        }
    }
    
    public function doSign()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
        {
            if ($this->db->has('roc_score', array(
                'AND' => array(
                    'uid' => $loginUid,
                    'type' => 3,
                    'time[>]' => strtotime(date('Y-m-d', time()))
                )
            )))
            {
                $this->echoAppJsonResult('您今天已经签到过啦~明天记得再来哦', array(),1);
            }
            else
            {
                $signScore = $GLOBALS['sys_config']['scores']['sign'];
                
                $this->updateUserScore($loginUid, $signScore, 3);
                
                $this->echoAppJsonResult('签到成功~恭喜你获得 ' . $signScore . ' 积分', array(), 0);
            }
        }
    }
    
    public function deliverWhisper()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        
        if ($this->checkPrivate(0,$loginUid) == true)
        {
            if (isset($_POST['atuid'], $_POST['content']) && is_numeric($_POST['atuid']) && Utils::getStrlen($_POST['content']) < 500 && strlen($_POST['content']) > 0)
            {
                $atuid = intval($_POST['atuid']);
                
                $content = Filter::in(trim($_POST['content']));
                
                if ($this->db->has('roc_user', array(
                    'uid' => $atuid
                )))
                {
                    $myScore = $this->db->get('roc_user', 'scores', array(
                        'uid' => $loginUid
                    ));
                    
                    if ($myScore - $GLOBALS['sys_config']['scores']['whisper'] >= 0)
                    {
                        $WID = $this->db->insert('roc_whisper', array(
                            'atuid' => $atuid,
                            'uid' => $loginUid,
                            'content' => $content,
                            'posttime' => time(),
                            'isread' => 0,
                            'del_flag' => 0
                        ));
                        
                        if ($WID > 0)
                        {
                            $this->updateUserScore($loginUid, -$GLOBALS['sys_config']['scores']['whisper'], 4);
                            
                            $this->echoAppJsonResult('私信成功~', array(),0);
                            
                            $this->pushService->pushMessageToMobile('你有一条新私信','重要消息',1,array('whispter'=>$_POST['content']),'',$_POST['atuid']);

                        }
                        else
                        {
                            $this->echoAppJsonResult('传送失败，请重试', array(),1);
                        }
                    }
                    else
                    {
                        $this->echoAppJsonResult('您的积分不足，发送私信需消耗' . $GLOBALS['sys_config']['scores']['whisper'] . '积分', array(),2);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('该用户不存在', array(),3);
                }
            }
            else
            {
                $this->echoAppJsonResult('请检车您的输入是否合法', array(),4);
            }
        }
        else
        {
            $this->echoAppJsonResult('您尚未登录，无权操作', array(),5);
        }
    }
    
    public function setEmail()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];

        if ($this->checkPrivate(0,$loginUid) == true)
        {
            if (isset($_POST['email']))
            {
                $email = strtolower(stripslashes(trim($_POST['email'])));
                                
                if ($email == '')
                {
                    if ($email == '')
                    {
                        $this->echoAppJsonResult('邮箱不能为空', array(), 1);
                        return;
                    }
                }
                
                if (!Utils::checkEmailValidity($email))
                {
                    $this->echoAppJsonResult('邮件地址不正确', array(), 1);
                    return;
                }
                
                if ($this->db->has('roc_user', array(
                    'email' => $email
                )))
                {
                    $this->echoAppJsonResult('邮件地址已被占用', array(), 1);
                    return;
                }
                
                if ($this->db->has('roc_user', array(
                    'AND' => array(
                        'uid' => $loginUid,
                    )
                )))
                {
                    $resID = $this->db->update('roc_user', array(
                        'email' => $email
                    ), array(
                        'uid' => $loginUid
                    ));
                    
                    if ($resID > 0)
                    {
                        $this->echoAppJsonResult('邮箱设置成功',array(),0);
                    }
                    else
                    {
                        $this->echoAppJsonResult('邮箱设置失败', array(),4);
                    }
                }
                else
                {
                    $this->echoAppJsonResult('密码验证失败，请重试', array(), 5);
                }
            }
        }
    }
    
    public function setSignature()
    {
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];

        if ($this->checkPrivate(0,$loginUid) == true)
        {
            if (isset($_POST['signature']))
            {
                $signature = Filter::in($_POST['signature']);
                
                if (empty($signature))
                {
                    $this->echoAppJsonResult('个性签名不能为空', array(), 1);
                    return;
                }
                
                if (Utils::getStrlen($signature) >= 32)
                {
                    $this->echoAppJsonResult('个性签名不能超过32个字', array(), 1);
                    return;
                }
                
                $resID = $this->db->update('roc_user', array(
                    'signature' => $signature
                ), array(
                    'uid' => $loginUid
                ));
                
                if ($resID > 0)
                {
                    $this->echoAppJsonResult('个性签名设置成功',array(),0);
                }
                else
                {
                    $this->echoAppJsonResult('个性签名设置失败',array(),2);
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
    
    private function doAtUser($content,$loginUid)
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
                    if ($userInfo['uid'] == $loginUid)
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
                return 1;
            }
            
            return 2;
        }
        else
        {
            return 3;
        }
    }
    
    private function checkPrivate($type = 0,$requestUid)
    {
        if ($type == 0)
        {
            if ($requestUid > 0)
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
                'uid' => $requestUid
            ));
            
            if ($requestUid > 0 && $groupid != 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    
    private function echoAppJsonResult($msg,$resultDictionary = array(),$status){
                        
        $resultArray = array('status'=>$status,'data'=>$resultDictionary,'msg'=>$msg);
        
        echo json_encode($resultArray);
        
    }
    
    protected function validateToken(){
        
        if (isset($_POST['token'])){
           
            $validate = Secret::validateLoginToken($_POST['token']);
        
        if($validate){
            
            return $validate;
            
        }  else {
           
            $this->echoAppJsonResult('token非法', array(),1);
                        
            exit();
        }
        
       }else{
           
           $this->echoAppJsonResult('非法请求', array(),1);
                        
            exit();
       }
    } 
}
?>