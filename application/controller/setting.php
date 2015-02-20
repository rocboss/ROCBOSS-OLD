<?php
!defined('ROC') && exit('REFUSED!');
Class settingControl extends commonControl
{
    public $page;
    public $per = 30;
    public function index()
    {
        if ($this->checkPrivate())
        {
            $settingType = isset($GLOBALS['Router']['params']['type']) && in_array($GLOBALS['Router']['params']['type'], array(
                'avatar',
                'signature',
                'email',
                'password'
            )) ? $GLOBALS['Router']['params']['type'] : 'avatar';
            
            switch ($settingType)
            {
                case 'avatar':
                    # code...
                    break;
                
                case 'signature':
                    # code...
                    break;
                
                case 'email':
                    # code...
                    break;
                
                case 'password':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }
            
            $this->tpls->assign('userInfo', $this->getMemberInfo('uid', $this->loginInfo['uid']));
            
            $this->tpls->assign('loginInfo', $this->loginInfo);
            
            $this->tpls->assign('settingType', $settingType);
            
            $this->tpls->display('setting');
            
        }
    }
    
    private function getMemberInfo($key, $value)
    {
        $memberArray = array();
        
        $DBArray = $this->db->get('roc_user', array(
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
            
            $memberArray['avatar'] = Image::getAvatarURL($DBArray['uid']);
            
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
            
            $memberArray['groupname'] = Utils::getGroupName($DBArray['groupid']);
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
            header('location:' . ROOT);
        }
    }
}
?>