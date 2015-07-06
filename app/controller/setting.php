<?php

namespace app\controller;

Class setting extends base
{
    public function index($type)
    {
        if ($this->checkPrivate())
        {
            $settingType = isset($type) && in_array($type, array('avatar', 'signature', 'email', 'password')) ? $type : 'avatar';
            
            $this->app->view()->assign('userInfo', $this->getMemberInfo('uid', $this->loginInfo['uid']));
            
            $this->app->view()->assign('loginInfo', $this->loginInfo);
            
            $this->app->view()->assign('settingType', $settingType);
            
            $this->setViewBase('设置', 'setting');
        }
    }
    
    private function getMemberInfo($key, $value)
    {
        $memberArray = array();
        
        $DBArray = $this->app->db()->get('roc_user', array(
            'uid',
            'username',
            'email',
            'signature',
            'password',
            'regtime',
            'lasttime',
            'qqid',
            'scores',
            'money',
            'groupid'
        ), array(
            $key => $value
        ));
        
        if (!empty($DBArray['uid']))
        {
            $memberArray['uid'] = $DBArray['uid'];
            
            $memberArray['avatar'] = $this->getUserAvatar($DBArray['uid']);
            
            $memberArray['username'] = $DBArray['username'];
            
            $memberArray['email'] = $DBArray['email'];
            
            $memberArray['signature'] = $DBArray['signature'];
            
            $memberArray['password'] = $DBArray['password'];
            
            $memberArray['regtime'] = date('Y年n月j日 H:i', $DBArray['regtime']);
            
            $memberArray['lasttime'] = date('Y年n月j日 H:i', $DBArray['lasttime']);
            
            $memberArray['scores'] = $DBArray['scores'];
            
            $memberArray['money'] = $DBArray['money'];
            
            $memberArray['qqid'] = $DBArray['qqid'];
            
            $memberArray['groupid'] = $DBArray['groupid'];
            
            $memberArray['groupname'] = $this->getGroupName($DBArray['groupid']);
        }
        
        return $memberArray;
    }
    
    private function checkPrivate()
    {
        if ($this->loginInfo['uid'] > 0)
        {
            return true;
        }
        else
        {
            $this->app->redirect('/');
        }
    }
}
?>