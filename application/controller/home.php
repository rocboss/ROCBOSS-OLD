<?php
!defined('ROC') && exit('REFUSED!');
Class homeControl extends commonControl
{
    public $page;
    public $per = 30;
    public function index()
    {
        $type = isset($_COOKIE['type']) && $_COOKIE['type'] === 'lasttime' ? 'lasttime' : 'posttime';

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
                $this->per * (Utils::getCurrentPage() - 1),
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
        
        $this->page = new Page($this->per, $this->db->count("roc_topic"), Utils::getCurrentPage(), 8, ROOT . 'home/index/page/');
        
        if ($this->loginInfo['uid'] > 0)
        {
            $this->tpls->assign('signStatus', $this->getSignStatus());
            
            $this->tpls->assign('mine', $this->getMineInfo());
        }
        
        $this->getHot();
        
        $this->getTodayTopSign();
        
        $this->tpls->assign('topicArray', $datas);
        
        $this->tpls->assign('page', $this->page->show());
        
        $this->tpls->assign('loginInfo', $this->loginInfo);
        
        $this->tpls->assign('LinksList', json_decode(file_get_contents('application/cache/links.json'), true));
        
        $this->tpls->display('index');
    }
    
    public function read()
    {
        $tid = intval($GLOBALS['Router']['params']);
        
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
            
            $this->tpls->assign('seo', $this->getSiteSEO($topicInfo[0]['title'], implode(',', $topicInfo[0]['tagArray']), $topicInfo[0]['content']));
            
            $topicInfo[0]['content'] = $this->getPictures(Utils::parseUser(Utils::parseUrl($topicInfo[0]['content'])), $topicInfo[0]['uid']);
            
            if ($this->loginInfo['uid'] > 0)
            {
                if ($this->db->has('roc_favorite', array(
                    'AND' => array(
                        'tid' => $tid,
                        'uid' => $this->loginInfo['uid']
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
                        'uid' => $this->loginInfo['uid']
                    )
                )))
                {
                    $topicInfo[0]['ispraise'] = 1;
                }
                else
                {
                    $topicInfo[0]['ispraise'] = 0;
                }
                
                $this->tpls->assign('signStatus', $this->getSignStatus());
                
                $this->tpls->assign('mine', $this->getMineInfo());
            }
            
            $this->getHot();
            
            $this->getReplyList($topicInfo[0]['tid']);
            
            $this->tpls->assign('topicInfo', $topicInfo[0]);
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->display('read');
        }
        else
        {
            $this->tpls->display('404');
        }
    }
    
    public function search()
    {
        $search = isset($GLOBALS['Router']['params']['s']) ? Filter::in($GLOBALS['Router']['params']['s']) : '';
        
        if (Utils::getStrlen($search) < 2)
        {
            die('Search word is too short.');
        }
        
        if ($this->loginInfo['uid'] > 0)
        {
            $this->tpls->assign('signStatus', $this->getSignStatus());
            
            $this->tpls->assign('mine', $this->getMineInfo());
        }
        
        $this->getHot();
        
        $this->tpls->assign('loginInfo', $this->loginInfo);
        
        $this->tpls->assign('search', $search);
        
        $this->tpls->assign('seo', $this->getSiteSEO('搜索：' . $search, '搜索：' . $search, '搜索：' . $search));
        
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
                $this->per * (Utils::getCurrentPage() - 1),
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
        
        $this->page = new Page($this->per, $this->db->count('roc_topic', array(
            'LIKE' => array(
                'title' => $search
            )
        )), Utils::getCurrentPage(), 8, ROOT . 'home/search/s/' . $search . '/page/');
        
        $this->tpls->assign('page', $this->page->show());
        
        $this->tpls->assign('topicArray', $datas);
        
        $this->tpls->display('search');
    }
    
    public function tag()
    {
        $tagName = isset($GLOBALS['Router']['params']['name']) ? Filter::in($GLOBALS['Router']['params']['name']) : null;
        
        if ($this->loginInfo['uid'] > 0)
        {
            $this->tpls->assign('signStatus', $this->getSignStatus());
            
            $this->tpls->assign('mine', $this->getMineInfo());
        }
        
        $this->getHot();
        
        $this->tpls->assign('loginInfo', $this->loginInfo);
        
        $this->tpls->assign('seo', $this->getSiteSEO($tagName . ' - 标签', $tagName, $tagName . ' - 标签'));
        
        $this->tpls->assign('tagName', $tagName);
        
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
                    $this->per * (Utils::getCurrentPage() - 1),
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
            
            $this->page = new Page($this->per, $this->db->count('roc_topic', array(
                'tid' => $relateTopic
            )), Utils::getCurrentPage(), 8, ROOT . 'home/tag/name/' . $tagName . '/page/');
            
            $this->tpls->assign('topicArray', $datas);
            
            $this->tpls->assign('page', $this->page->show());
        }
        
        $this->tpls->display('tag');
    }
    
    public function getReplyList($t = 0)
    {
        $tid = isset($_POST['tid']) ? intval($_POST['tid']) : $t;
        
        if ($this->db->has('roc_topic', array(
            'tid' => $tid
        )))
        {
            
            $last = isset($_POST['last']) && intval($_POST['last']) > 0 ? intval($_POST['last']) : 0;
            
            $amount = isset($_POST['amount']) && intval($_POST['amount']) > 0 ? intval($_POST['amount']) : 30;
            
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
                    $last,
                    $amount
                )
            ));
            
            foreach ($replyList as $key => $value)
            {
                $replyList[$key]['avatar'] = Image::getAvatarURL($value['uid'], 50);
                
                $replyList[$key]['content'] = $this->getPictures(Utils::parseUser(Utils::parseUrl(Filter::topicOut($value['content']))), $value['uid']);
                
                $replyList[$key]['posttime'] = Utils::formatTime($value['posttime']);
                
                $replyList[$key]['floor'] = $this->getReplyFloorList($value['pid']);
            }
            
            if ($t == 0)
            {
                echo json_encode($replyList);
            }
            else
            {
                $this->tpls->assign('replyList', $replyList);
            }
        }
    }
    
    public function getReplyFloorList($pid = 0)
    {
        $pid = (isset($_POST['pid']) && intval($_POST['pid']) > 0) ? intval($_POST['pid']) : $pid;
        
        $page = (isset($_POST['page']) && intval($_POST['page']) > 0) ? intval($_POST['page']) : 0;
        
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
                    5 * $page,
                    5
                )
            ));
            
            foreach ($replyFloorList as $key => $value)
            {
                $replyFloorList[$key]['avatar'] = Image::getAvatarURL($value['floorUid'], 50);
                
                $replyFloorList[$key]['floorContent'] = Utils::parseUser(Utils::parseUrl(Filter::topicOut($value['floorContent'])));
                
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
    
    private function getTopicPraise($tid)
    {
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
    
    private function getTodayTopSign()
    {
        $this->tpls->assign('signList', $this->db->select('roc_score', array(
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
        )));
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
                $str = preg_replace('/\[:' . $value . '\]/i', '<a href="' . ROOT . $res['path'] . '" class="picPre"><img src="' . ROOT . $res['path'] . '.thumb.png" alt=""/></a>', $str);
            }
            else
            {
                $str = preg_replace('/\[:' . $value . '\]/i', '[此处非法引用 OR 图片已不存在]', $str);
            }
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
}
?>