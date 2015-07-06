<?php

namespace app\controller;

class home extends base
{
    public function index($page)
    {
        $page = $page > 0 ? $page : 1;

        $type = isset($_COOKIE['type']) && $_COOKIE['type'] == 'lasttime' ? 'lasttime' : 'posttime';

        if (!isset($_COOKIE['type']))
        {
            setcookie('type', 'posttime', time() + 604800, '/');
        }

        $datas = $this->app->db()->select('roc_topic', array(
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
            'roc_user.username',
            'roc_user.signature'
        ), array(
            'ORDER' => array(
                'roc_topic.istop DESC',
                'roc_topic.'.$type.' DESC'
            ),
            
            'LIMIT' => array(
                $this->per * ($page - 1),
                $this->per
            )
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['title'] = $this->topicOut($value['title']);
            
            $datas[$key]['avatar'] = $this->getUserAvatar($value['uid']);
            
            $datas[$key]['content'] = $this->topicOut($value['content']);
            
            $datas[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
            
            $datas[$key]['lasttime'] = $this->utils->formatTime($value['lasttime']);
            
            $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);

            $datas[$key]['hasPictures'] = $this->app->db()->has('roc_attachment', array('tid' => $value['tid']));

            $datas[$key]['praiseArray'] = $this->getTopicPraise($value['tid'], false);
        }
        
        $this->setPage($page, $this->app->db()->count("roc_topic"), '?');        
        
        $this->getHot();
        
        $this->getTodayTopSign();
        
        $this->app->view()->assign('topicArray', $datas);
        
        $this->app->view()->assign('loginInfo', $this->loginInfo);
        
        $this->app->view()->assign('LinksList', json_decode(file_get_contents('app/cache/links.json'), true));
        
        $this->setViewBase('', 'index');
    }

    public function read($tid)
    {   
        if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
        {
            $topicInfo = $this->app->db()->select('roc_topic', array(
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
            
            $topicInfo[0]['title'] = $this->topicOut($topicInfo[0]['title']);
            
            $topicInfo[0]['content'] = $this->topicOut($topicInfo[0]['content']);
            
            $topicInfo[0]['avatar'] = $this->getUserAvatar($topicInfo[0]['uid']);
            
            $topicInfo[0]['posttime'] = $this->utils->formatTime($topicInfo[0]['posttime']);
            
            $topicInfo[0]['tagArray'] = $this->getTopicTag($tid);
            
            $topicInfo[0]['praiseArray'] = $this->getTopicPraise($tid);
                        
            $topicInfo[0]['content'] = $this->getPictures($this->utils->parseUser($topicInfo[0]['content']), $topicInfo[0]['uid']);
            
            if ($this->loginInfo['uid'] > 0)
            {
                if ($this->app->db()->has('roc_favorite', array(
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
                
                if ($this->app->db()->has('roc_praise', array(
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
            }
            
            $this->getHot();
            
            $this->getReplyList($topicInfo[0]['tid']);
            
            $this->app->view()->assign('topicInfo', $topicInfo[0]);
            
            $this->app->view()->assign('loginInfo', $this->loginInfo);
            
            $this->setViewBase($topicInfo[0]['title'].' - ', 'read');
        }
        else
        {
            $this->app->view()->display('404');
        }
    }
    
    public function search($s, $page)
    {
        $page = $page > 0 ? $page : 1;

        $search = isset($s) ? $this->filter->in($s) : '';
        
        if ($this->utils->getStrlen($search) < 2)
        {
            die('Search word is too short.');
        }
        
        $this->getHot();
        
        $this->app->view()->assign('loginInfo', $this->loginInfo);
        
        $this->app->view()->assign('search', $search);
                
        $datas = $this->app->db()->select('roc_topic', array(
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
            'roc_user.username',
            'roc_user.signature'
        ), array(
            'LIKE' => array(
                'title' => $search
            ),
            
            'ORDER' => array(
                'roc_topic.istop DESC',
                'roc_topic.lasttime DESC'
            ),
            
            'LIMIT' => array(
                $this->per * ($page - 1),
                $this->per
            )
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['title'] = $this->topicOut($value['title']);
            
            $datas[$key]['avatar'] = $this->getUserAvatar($value['uid']);
                        
            $datas[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
            
            $datas[$key]['lasttime'] = $this->utils->formatTime($value['lasttime']);
            
            $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);

            $datas[$key]['hasPictures'] = $this->app->db()->has('roc_attachment', array('tid' => $value['tid']));

            $datas[$key]['praiseArray'] = $this->getTopicPraise($value['tid'], false);
        }
        
        $this->setPage($page, $this->app->db()->count('roc_topic', array(
            'LIKE' => array(
                'title' => $search
            )
        )), 'search/'.$search.'/?');
        
        $this->app->view()->assign('topicArray', $datas);
        
        $this->setViewBase('搜索“'.$search.'” - ', 'search');
    }
    
    public function tag($name, $page)
    {
        $page = $page > 0 ? $page : 1;

        $tagName = isset($name) ? $this->filter->in($name) : null;
        
        $this->getHot();
        
        $this->app->view()->assign('loginInfo', $this->loginInfo);
                
        $this->app->view()->assign('tagName', $tagName);
        
        if ($this->app->db()->has('roc_tag', array('tagname' => $tagName)))
        {
            $tagDetail = $this->app->db()->get('roc_tag', array(
                'tagid',
                'tagname',
                'used'
            ), array(
                'tagname' => $tagName
            ));
            
            $relateTopic = $this->app->db()->select('roc_topic_tag_connection', 'tid', array(
                'tagid' => $tagDetail['tagid']
            ));
            
            $datas = $this->app->db()->select('roc_topic', array(
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
                'roc_user.username',
                'roc_user.signature'
            ), array(
                'tid' => $relateTopic,
                
                'ORDER' => array(
                    'roc_topic.istop DESC',
                    'roc_topic.lasttime DESC'
                ),
                
                'LIMIT' => array(
                    $this->per * ($page - 1),
                    $this->per
                )
            ));
            
            foreach ($datas as $key => $value)
            {
                $datas[$key]['title'] = $this->topicOut($value['title']);
                
                $datas[$key]['avatar'] = $this->getUserAvatar($value['uid']);
                
                $datas[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
                
                $datas[$key]['lasttime'] = $this->utils->formatTime($value['lasttime']);
                
                $datas[$key]['tagArray'] = $this->getTopicTag($value['tid']);

                $datas[$key]['hasPictures'] = $this->app->db()->has('roc_attachment', array('tid' => $value['tid']));

                $datas[$key]['praiseArray'] = $this->getTopicPraise($value['tid'], false);
            }
            
            $this->setPage($page,$this->app->db()->count('roc_topic', array(
                'tid' => $relateTopic
            )), 'tag/'.$tagName.'/?');
            
            $this->app->view()->assign('topicArray', $datas);

            $this->setViewBase('标签', 'tag');
        }
        else
        {
            $this->app->redirect('/');
        }
        
    }
    
    public function getReplyList($t = 0)
    {
        $tid = isset($_POST['tid']) ? intval($_POST['tid']) : $t;
        
        if ($this->app->db()->has('roc_topic', array('tid' => $tid)))
        {
            
            $last = isset($_POST['last']) && intval($_POST['last']) > 0 ? intval($_POST['last']) : 0;
            
            $amount = isset($_POST['amount']) && intval($_POST['amount']) > 0 ? intval($_POST['amount']) : $this->per;
            
            $replyList = $this->app->db()->select('roc_reply', array(
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
                $replyList[$key]['avatar'] = $this->getUserAvatar($value['uid']);
                
                $replyList[$key]['content'] = $this->getPictures($this->utils->parseUser($this->topicOut($value['content'])), $value['uid']);
                
                $replyList[$key]['posttime'] = $this->utils->formatTime($value['posttime']);
                
                $replyList[$key]['floor'] = $this->getReplyFloorList($value['pid']);
            }
            
            if ($t == 0)
            {
                echo json_encode($replyList);
            }
            else
            {
                $this->app->view()->assign('replyList', $replyList);
            }
        }
    }
    
    public function getReplyFloorList($pid = 0)
    {
        $pid = (isset($_POST['pid']) && intval($_POST['pid']) > 0) ? intval($_POST['pid']) : $pid;
        
        $page = (isset($_POST['page']) && intval($_POST['page']) > 0) ? intval($_POST['page']) : 0;
        
        if ($this->app->db()->has('roc_floor', array('pid' => $pid)))
        {
            $replyFloorList = $this->app->db()->select('roc_floor', array(
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
                $replyFloorList[$key]['avatar'] = $this->getUserAvatar($value['floorUid'], 50);
                
                $replyFloorList[$key]['floorContent'] = $this->utils->parseUser($this->utils->parseUrl($this->topicOut($value['floorContent'])));
                
                $replyFloorList[$key]['floorTime'] = $this->utils->formatTime($value['floorTime']);
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
    
    private function getHot()
    {
        $this->app->view()->assign('hotTags', $this->app->db()->select('roc_tag', array(
            'tagid',
            'tagname',
            'used'
        ), array(
            'ORDER' => 'used DESC',
            'LIMIT' => 30
        )));
    }
    
    private function getTodayTopSign()
    {
        $this->app->view()->assign('signList', $this->app->db()->select('roc_score', array(
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
    
    private function getPictureList($tid)
    {
        $pictureArray = $this->app->db()->select('roc_attachment', 'path', array(
            'tid' => $tid
        ));
        
        return $pictureArray;
    }
}

?>