<?php

!defined('ROC') && exit('REFUSED!');

Class mobileAdminControl extends commonControl
{
    public function searchUser() {
        
        $this->validateToken();
        
        $loginUid = $_POST['loginUserId'];
        $groupId = $_POST['groupId'];
        $search = $_POST['keyword'];        
        $pageIndex = $_POST['pageIndex'];
        
        $result = $this->checkManagePrivate($groupId,$loginUid);
        
        if($result == FALSE){
            
            $this->echoAppJsonResult('无管理权限',array(),1);
            return;
        }
        
        $datas = $this->db->select('roc_user' ,array(
            'uid',
            'username',
            'signature',
            'regtime',
            'lasttime',
            'groupid'
        ), array(
            
            'LIKE' => array(
                'username' => $search
            ),
            
            'LIMIT' => array(
                20 * ($pageIndex - 1),
                20
            )
            
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['uid']);
        }
            
        $this->echoAppJsonResult('查询用户',$datas,0);
    }
    
    public function lockUserAction(){
        
        $this->validateToken();
        $groupdId = $_POST['groupId'];
        $requestUid = $_POST['userId'];
        $loginUid = $_POST['loginUserId'];
        $status = $_POST['status'];
        
        $result = $this->checkManagePrivate($groupId, $loginUid);
        
        if($result == FALSE){
          
            $this->echoAppJsonResult('无管理权限',array(),1);
            
            return;
        } 
        
        $update = $this->db->update('roc_user',array('groupid' => 1 - $status),array('uid' => $requestUid));
        
        $this->echoAppJsonResult('操作结果',array(),$update);
    }
    
    public function topicApplyList() {
        
        $this->validateToken();

        $loginUid = $_POST['loginUserId'];
        $groupId = $_POST['groupId'];
        $pageIndex = $_POST['pageIndex'];
        $status = $_POST['status'];
        
        $result = $this->checkManagePrivate($groupId,$loginUid);
        
        if($result == FALSE){
            
            $this->echoAppJsonResult('无管理权限',array(),1);
            return;
        }
        
        $datas = $this->db->select('roc_apply',array(
            
            'user_id',
                        
            'reason',
            
            'topic_title',
            
            'tid',
            
            'create_time',
            
            'status',
            
            'username',
            
            'groupid',
            
            'type'
                
        ),array(
            
            'AND' => array(
                'status' => $status,
                'type' => 2
            ),
            
            'LIMIT'=>array(
               20 * ($pageIndex - 1),
               20 
            )
            
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['user_id']);
        }
        
        $this->echoAppJsonResult('获取成功',$datas,0);
    }
    
    public function userApplyList() {
        
        $this->validateToken();

        $loginUid = $_POST['loginUserId'];
        $groupId = $_POST['groupId'];
        $pageIndex = $_POST['pageIndex'];
        $status = $_POST['status'];
        
        $result = $this->checkManagePrivate($groupId,$loginUid);
        
        if($result == FALSE){
            
            $this->echoAppJsonResult('无管理权限',array(),1);
            return;
        }
        
        $datas = $this->db->select('roc_apply',array(
            
            'user_id',
                        
            'reason',
                                    
            'create_time',
            
            'status',
            
            'username',
            
            'groupid',
            
            'type'
                
        ),array(
            
            'AND' => array(
                'status' => $status,
                'type' => 1
            ),
            
            'LIMIT'=>array(
               20 * ($pageIndex - 1),
               20 
            )
            
        ));
        
        foreach ($datas as $key => $value)
        {
            $datas[$key]['avatar'] = Image::getAvatarURL($datas[$key]['user_id']);
        }
        
        $this->echoAppJsonResult('获取成功',$datas,0);
        
    }


    private function checkManagePrivate($groupId,$userId)
    {
        $userInfo = $this->getMemberInfo('uid', $userId);
                
        if (empty($userInfo['uid']))
        {
            return FALSE;
        }
                
        if ($groupId < 8)
        {
            return FALSE;
        }else{
            return TRUE;
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
    
    private function echoAppJsonResult($msg,$resultDictionary = array(),$status){
                        
        $resultArray = array('status'=>$status,'data'=>$resultDictionary,'msg'=>$msg);
        
        echo json_encode($resultArray);
        
    }
}