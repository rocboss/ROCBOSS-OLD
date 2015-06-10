<?php
!defined('ROC') && exit('REFUSED!');
class commonControl
{
    protected $db;
    protected $tpls;
    protected $loginInfo;
    protected $pushService;

    public function __construct()
    {
        if ($GLOBALS['sys_config']['db_switch'] == true)
        {
            $this->db = new DB($GLOBALS['db_config']);
            $this->pushService = new PushService();
        }
        $this->tpls = new Template();
        
        $this->tpls->tplDir = 'application/template/' . $GLOBALS['tpl_config']['tpl_dir'] . '/';
        
        $this->tpls->cacheDir = 'application/cache/' . $GLOBALS['tpl_config']['tpl_cache'] . '/';
        
        $this->tpls->tplExt = $GLOBALS['tpl_config']['tpl_ext'];
        
        $this->tpls->cacheTime = $GLOBALS['tpl_config']['tpl_time'];
        
        $this->loginInfo = $this->isLogin($GLOBALS['sys_config']['ROCKEY'], $_COOKIE);
        
        $this->tpls->assign('seo', $this->getSiteSEO());
    }
    
    protected function isLogin($sKey, $cookie)
    {
        $userInfo = array(
            'uid' => 0,
            
            'username' => '',
            
            'signature' => '',
            
            'groupid' => 0,
            
            'groupname' => '',
            
            'logintime' => 0,
            
            'avatar' => ''
        );
        
        if (isset($cookie['roc_login'], $cookie['roc_secure']))
        {
            $userArr = json_decode(Secret::decrypt($cookie['roc_secure'], $sKey), true);
            
            if (count($userArr) == 4)
            {
                if ($cookie['roc_login'] == $userArr[1])
                {
                    $userInfo['uid'] = $userArr[0];
                    
                    $userInfo['username'] = $userArr[1];
                    
                    $userInfo['groupid'] = $userArr[2];
                    
                    $userInfo['logintime'] = $userArr[3];
                    
                    $userInfo['avatar'] = Image::getAvatarURL($userArr[0]);
                    
                    $userInfo['groupname'] = Utils::getGroupName($userArr[2]);
                }
            }
        }
        return $userInfo;
    }
    
    protected function loginCookie($sKey, $uid, $name, $group)
    {
        $loginTime = time();
        
        setcookie('roc_login', $name, $loginTime + 1209600, '/');
        
        $loginEncode = Secret::encrypt(json_encode(array(
            $uid,
            $name,
            $group,
            $loginTime
        )), $sKey);
        
        setcookie('roc_secure', $loginEncode, $loginTime + 1209600, '/');
        
        setcookie('roc_connect', '');
    }
    
    protected function getTopicTag($tid)
    {
        return $this->db->select('roc_topic_tag_connection', array(
            '[>]roc_tag' => 'tagid'
        ), 'roc_tag.tagname', array(
            'roc_topic_tag_connection.tid' => $tid
        ));
    }
    
    protected function getSiteSEO($title = '', $keywords = '', $description = '')
    {
        $seoArray = array(
            'title' => ($title == '') ? $GLOBALS['sys_config']['sitename'] : $title,
            
            'keywords' => ($keywords == '') ? $GLOBALS['sys_config']['keywords'] : $keywords,
            
            'description' => ($description == '') ? $GLOBALS['sys_config']['description'] : $description
        );
        
        return $seoArray;
    }
    
    protected function updateLasttime($uid)
    {
        $this->db->update('roc_user', array(
            'lasttime' => time()
        ), array(
            'uid' => $uid
        ));
    }
    
    protected function updateUserScore($uid, $changed, $type)
    {
        $ori = $this->db->get('roc_user', 'scores', array(
            'uid' => $uid
        ));
        
        $this->db->beginTransaction();

        if ($changed > 0)
        {
            $this->db->update('roc_user', array(
                'scores[+]' => $changed
            ), array(
                'uid' => $uid
            ));
        }
        else
        {
            $this->db->update('roc_user', array(
                'scores[-]' => abs($changed)
            ), array(
                'uid' => $uid
            ));
        }
        
        $scoreArray = array(
            'uid' => $uid,
            
            'changed' => $changed,
            
            'remain' => ($changed > 0) ? ($changed + $ori) : $ori - abs($changed),
            
            'type' => $type,
            
            'time' => time()
        );
        
        $insertID = $this->db->insert('roc_score', $scoreArray);

        $this->db->checkResult($insertID);
    }
    
    protected function showMsg($message, $type = 'success', $position = 0)
    {
        header("Content-type:text/html;charset=utf-8");
        
        die('{"result":"' . $type . '","message":"' . $message . '","position":' . $position . '}');
    }
    
      
}
?>