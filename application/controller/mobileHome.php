<?php
!defined('ROC') && exit('REFUSED!');
Class mobileHomeControl extends commonControl
{
    public $page;
    public $per = 20;
    public function index()
    {
        $this->validateToken();
        
        #$type = isset($_COOKIE['type']) && $_COOKIE['type'] === 'lasttime' ? 'lasttime' : 'posttime';
        $type = $_POST['type'];
        $pageIndex = $_POST['pageIndex'];

        $datas = $this->db->select('roc_topic', array(
            '[>]roc_user' => 'uid'
        ), array(
            'tid',
            'title',
            'content',
            'comments',
            'client',
            'posttime',
            'roc_topic.lasttime',
            'istop',
            'roc_user.uid',
            'roc_user.username'
        ), array(
            'ORDER' => array(
                'roc_topic.istop DESC',
                'roc_topic.'.$type.' DESC'
            ),
            
            'LIMIT' => array(
                $this->per * ($pageIndex - 1),
                $this->per
            )
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['title'] = Filter::topicOut($value['title']);
            
            $datas[$key]['avatar'] = Image::getAvatarURL($value['uid']);
            
            $datas[$key]['content'] = Filter::topicOut($value['content']);
            
            $datas[$key]['posttime'] = Utils::formatTime($value['posttime']);
            
            $datas[$key]['lasttime'] = Utils::formatTime($value['lasttime']);
            
            $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);
            
            $datas[$key]['pictures'] = $this->getPictureList($value['tid']);
        }
        
        #$this->getHot();
        
        #$this->getTodayTopSign();
        
        $this->echoAppJsonResult('获取成功',$datas,0);
        
    }
    
    public function read()
    {
        $this->validateToken();
        
        $tid = $_POST['tid'];
        $loginUid = $_POST['loginUserId'];
        
        if ($this->db->has('roc_topic', array(
            'tid' => $tid
        )))
        {
            $topicInfo = $this->db->select('roc_topic', array(
                '[>]roc_user' => 'uid'
            ), array(
                'tid',
                'title',
                'content',
                'comments',
                'client',
                'istop',
                'islock',
                'posttime',
                'roc_topic.lasttime',
                'roc_user.uid',
                'roc_user.username'
            ), array(
                'tid' => $tid
            ));
            
            $topicInfo[0]['title'] = Filter::topicOut($topicInfo[0]['title']);
            
            $topicInfo[0]['content'] = Filter::topicOut($topicInfo[0]['content']);
            
            $topicInfo[0]['avatar'] = Image::getAvatarURL($topicInfo[0]['uid']);
            
            $topicInfo[0]['posttime'] = Utils::formatTime($topicInfo[0]['posttime']);
            
            $topicInfo[0]['tagArray'] = $this->getTopicTag($tid);
            
            $topicInfo[0]['praiseArray'] = $this->getTopicPraise($tid);
                        
            $topicInfo[0]['pictures'] = $this->getPictures(Utils::parseUser(Utils::parseUrl($topicInfo[0]['content'])), $topicInfo[0]['uid']);
            
            $topicInfo[0]['content'] = $this->clearContentPicTags(Utils::parseUser(Utils::parseUrl($topicInfo[0]['content'])), $topicInfo[0]['uid']);

            if ($loginUid > 0)
            {
                if ($this->db->has('roc_favorite', array(
                    'AND' => array(
                        'tid' => $tid,
                        'uid' => $loginUid
                    )
                )))
                {
                    $topicInfo[0]['isfavorite'] = 1;
                }
                else
                {
                    $topicInfo[0]['isfavorite'] = 0;
                }
                
                if ($this->db->has('roc_praise', array(
                    'AND' => array(
                        'tid' => $tid,
                        'uid' => $loginUid
                    )
                )))
                {
                    $topicInfo[0]['ispraise'] = 1;
                }
                else
                {
                    $topicInfo[0]['ispraise'] = 0;
                }
            }
            
            #$this->getHot();
            
            #$this->getReplyList($topicInfo[0]['tid']);
            
            $this->echoAppJsonResult('获取成功',$topicInfo,0);
        }
        else
        {
            $this->echoAppJsonResult('帖子不存在',array(),3);
        }
    }
    
    public function search()
    {
        $this->validateToken();
        
        $search = $_POST['keyword'];
        $pageIndex = $_POST['pageIndex'];
        
        #$this->getHot();
        
        $datas = $this->db->select('roc_topic', array(
            '[>]roc_user' => 'uid'
        ), array(
            'tid',
            'title',
            'content',
            'comments',
            'client',
            'posttime',
            'roc_topic.lasttime',
            'istop',
            'roc_user.uid',
            'roc_user.username'
        ), array(
            'LIKE' => array(
                'title' => $search
            ),
            
            'ORDER' => array(
                'roc_topic.istop DESC',
                'roc_topic.lasttime DESC'
            ),
            
            'LIMIT' => array(
                $this->per * ($pageIndex - 1),
                $this->per
            )
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['title'] = Filter::topicOut($value['title']);
            
            $datas[$key]['avatar'] = Image::getAvatarURL($value['uid']);
            
            $datas[$key]['content'] = Filter::topicOut($value['content']);
            
            $datas[$key]['posttime'] = Utils::formatTime($value['posttime']);
            
            $datas[$key]['lasttime'] = Utils::formatTime($value['lasttime']);
            
            $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);
            
            $datas[$key]['pictures'] = $this->getPictureList($value['tid']);
        }
 
        $this->echoAppJsonResult('搜索成功', $datas,0);
    }
    
    public function tag()
    {
        $this->validateToken();
        
        $tagName = $_POST['tag'];
        $pageIndex = $_POST['pageIndex'];
        
        #$this->getHot();
        
        if ($this->db->has('roc_tag', array(
            'tagname' => $tagName
        )))
        {
            $tagDetail = $this->db->get('roc_tag', array(
                'tagid',
                'tagname',
                'used'
            ), array(
                'tagname' => $tagName
            ));
            
            $relateTopic = $this->db->select('roc_topic_tag_connection', 'tid', array(
                'tagid' => $tagDetail['tagid']
            ));
            
            $datas = $this->db->select('roc_topic', array(
                '[>]roc_user' => 'uid'
            ), array(
                'tid',
                'title',
                'content',
                'comments',
                'client',
                'posttime',
                'roc_topic.lasttime',
                'istop',
                'roc_user.uid',
                'roc_user.username'
            ), array(
                'tid' => $relateTopic,
                
                'ORDER' => array(
                    'roc_topic.istop DESC',
                    'roc_topic.lasttime DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = Filter::topicOut($value['title']);
                
                $datas[$key]['avatar'] = Image::getAvatarURL($value['uid']);
                
                $datas[$key]['content'] = Filter::topicOut($value['content']);
                
                $datas[$key]['posttime'] = Utils::formatTime($value['posttime']);
                
                $datas[$key]['lasttime'] = Utils::formatTime($value['lasttime']);
                
                $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);
                
                $datas[$key]['pictures'] = $this->getPictureList($value['tid']);
            }
            
            $this->echoAppJsonResult('获取成功', $datas,0);
        }
    }
    
    public function getReplyList()
    {
        $this->validateToken();
        
        $tid = $_POST['tid'];
        
        if ($this->db->has('roc_topic', array(
            'tid' => $tid
        )))
        {
            
            $pageIndex = isset($_POST['pageIndex']) && intval($_POST['pageIndex']) > 0 ? intval($_POST['pageIndex']) : 0;
                        
            $replyList = $this->db->select('roc_reply', array(
                '[>]roc_user' => 'uid'
            ), array(
                'pid',
                'tid',
                'uid',
                'content',
                'client',
                'posttime',
                'roc_user.username'
            ), array(
                'roc_reply.tid' => $tid,
                
                'ORDER' => 'roc_reply.pid ASC',
                
                'LIMIT' => array(
                    $this->per * ($pageIndex - 1),
                    $this->per
                )
            ));
            
            foreach ($replyList as $key => $value)
            {
                $replyList[$key]['avatar'] = Image::getAvatarURL($value['uid'], 50);
                
                $replyList[$key]['content'] = Filter::topicIn($value['content']);
           
                $replyList[$key]['pictures'] = $this->getPictures(Utils::parseUser(Utils::parseUrl(Filter::topicOut($value['content']))), $value['uid']);
                
                $replyList[$key]['posttime'] = Utils::formatTime($value['posttime']);
                
                $replyList[$key]['floor'] = $this->getInnerReplyFloorList($value['pid']);
            }
                        
            $this->echoAppJsonResult('帖子回复列表', $replyList,0);
        }
    }
    
    public function getInnerReplyFloorList($pid = 0)
    {        
        $page = 0;
        
        if ($this->db->has('roc_floor', array(
            'pid' => $pid
        )))
        {
            $replyFloorList = $this->db->select('roc_floor', array(
                '[>]roc_user' => 'uid'
            ), array(
                'id(floorId)',
                'pid(floorPid)',
                'uid(floorUid)',
                'content(floorContent)',
                'posttime(floorTime)',
                'roc_user.username(floorUser)'
            ), array(
                'roc_floor.pid' => $pid,
                
                'ORDER' => 'roc_floor.id ASC',
                
                'LIMIT' => array(
                    3 * $page,
                    3
                )
            ));
            
            foreach ($replyFloorList as $key => $value)
            {
                $replyFloorList[$key]['avatar'] = Image::getAvatarURL($value['floorUid'], 50);
               
                $replyFloorList[$key]['floorContent'] = Filter::topicOut($value['floorContent']);
                
                $replyFloorList[$key]['floorTime'] = Utils::formatTime($value['floorTime']);
            }
            
            if (isset($_POST['pid']))
            {
                echo json_encode($replyFloorList);
            }
            else
            {
                return $replyFloorList;
            }
        }
        else
        {
            return array();
        }
    }
 
    public function getReplyFloorList()
    {
        $this->validateToken();
        
        $pid = $_POST['pid'];
        
        $page = $_POST['pageIndex'];
        
        if ($this->db->has('roc_floor', array(
            'pid' => $pid
        )))
        {
            $replyFloorList = $this->db->select('roc_floor', array(
                '[>]roc_user' => 'uid'
            ), array(
                'id(floorId)',
                'pid(floorPid)',
                'uid(floorUid)',
                'content(floorContent)',
                'posttime(floorTime)',
                'roc_user.username(floorUser)'
            ), array(
                'roc_floor.pid' => $pid,
                
                'ORDER' => 'roc_floor.id ASC',
                
                'LIMIT' => array(
                    20 * $page,
                    20
                )
            ));
            
            foreach ($replyFloorList as $key => $value)
            {
                $replyFloorList[$key]['avatar'] = Image::getAvatarURL($value['floorUid'], 50);
                
                $replyFloorList[$key]['floorContent'] = Filter::topicOut($value['floorContent']);
                
                $replyFloorList[$key]['floorTime'] = Utils::formatTime($value['floorTime']);
            }
            
            $this->echoAppJsonResult('获取评论回复列表',$replyFloorList,0);
        }
        else
        {
            $this->echoAppJsonResult('获取评论回复列表',array(),0);
        }
    }
    
    public function getTopicPraise()
    {
        $this->validateToken();
        
        $tid = $_POST['tid'];
        
        $praiseArray = $this->db->select('roc_praise', array(
            '[>]roc_user' => 'uid'
        ), array(
            'roc_user.username(praiseUsername)',
            
            'roc_user.uid(praiseUid)'
        ), array(
            'roc_praise.tid' => $tid
        ));
        
        foreach ($praiseArray as $key => $value)
        {
            $praiseArray[$key]['praiseAvatar'] = Image::getAvatarURL($value['praiseUid']);
        }
        
        return $praiseArray;
    }
    
    private function getSignStatus()
    {
        if ($this->db->has('roc_score', array(
            'AND' => array(
                'uid' => $this->loginInfo['uid'],
                'type' => 3,
                'time[>]' => strtotime(date('Y-m-d', time()))
            )
        )))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    private function getHot()
    {
        $this->tpls->assign('hotTags', $this->db->select('roc_tag', array(
            'tagid',
            'tagname',
            'used'
        ), array(
            'ORDER' => 'used DESC',
            'LIMIT' => 30
        )));
        
        $this->tpls->assign('hotTopics', $this->db->select('roc_topic', array(
            'tid',
            'title',
            'comments'
        ), array(
            'ORDER' => 'comments DESC',
            'LIMIT' => 10
        )));
    }
    
    public function getHotTags(){
        
        $hotTags = $this->db->select('roc_tag', array(
            'tagid',
            'tagname',
            'used'
        ), array(
            'ORDER' => 'used DESC',
            'LIMIT' => 50
        ));
        
        $this->echoAppJsonResult('热门标签', $hotTags,0);
    }
    
    public function getHotTopics(){
        
        $hotTopics = $this->db->select('roc_topic', array(
            'tid',
            'title',
            'comments'
        ), array(
            'ORDER' => 'comments DESC',
            'LIMIT' => 50
        ));
        
        foreach ($hotTopics as $key => $value)
        {
            $hotTopics[$key]['pictures'] = $this->getPictureList($value['tid']);
        }
      
        $this->echoAppJsonResult('热门帖子', $hotTopics,0);
    }

    public function getTodayTopSign()
    {        
       $this->validateToken();
        
       $signList = $this->db->select('roc_score', array(
            '[>]roc_user' => 'uid'
        ), array(
            'uid',
            'changed',
            'time',
            'roc_user.username'
        ), array(
            'AND' => array(
                'time[>]' => strtotime(date('Y-m-d', time())),
                'type' => 3
            ),
            'ORDER' => 'id ASC',
            'LIMIT' => 32
        ));
       
       foreach ($signList as $key => $value)
       {
          $signList[$key]['avatar'] = Image::getAvatarURL($signList[$key]['uid']);
       }
            
       $this->echoAppJsonResult('签到用户排行', $signList,0);
    }
    
    private function getMineInfo()
    {
        return array(
            'notification' => $this->db->count('roc_notification', array(
                'AND' => array(
                    'atuid' => $this->loginInfo['uid'],
                    'isread' => 0
                )
            )),
            'whisper' => $this->db->count('roc_whisper', array(
                'AND' => array(
                    'atuid' => $this->loginInfo['uid'],
                    'isread' => 0
                )
            )),
            'scores' => $this->db->get('roc_user', 'scores', array(
                'uid' => $this->loginInfo['uid']
            ))
        );
    }
    
    private function getPictures($str, $uid)
    {
        preg_match_all('/\[:([0-9]+)\]/i', $str, $attachment);
        
        $picArray = array();
        foreach ($attachment[1] as $key => $value)
        {
            $res = $this->db->get('roc_attachment', array(
                'uid',
                'path'
            ), array(
                'id' => $value
            ));
            
            if (!empty($res['path']) && $uid == $res['uid'])
            {                
                $picArray[] = ROOT . $res['path'];
            }
            else
            {
               $str = preg_replace('/\[:' . $value . '\]/i', '', $str);
            }
        }
        
        return $picArray;
    }
    
    private function clearContentPicTags($str, $uid){
        
        preg_match_all('/\[:([0-9]+)\]/i', $str, $attachment);
        
        $picArray = array();
        foreach ($attachment[1] as $key => $value)
        {
           $str = preg_replace('/\[:' . $value . '\]/i', '', $str);
        }
 
        return $str;
    }

        private function getPictureList($tid)
    {
        $pictureArray = $this->db->select('roc_attachment', 'path', array(
            'tid' => $tid
        ));
        
        return $pictureArray;
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