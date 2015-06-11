<?php

  Class PushService{
      
      protected $db;
      protected $apns;
      protected $androidPush;
      protected $secret = 'your app secret';
      protected $package = 'your app packagename';
      protected $apnsSandboxCer = 'application/apns/apns-dev.pem';
      protected $apnsDistributionCer = 'application/apns/apns-dev.pem';
      protected $apnsLogPath = 'application/apns/apnslog.txt';

      public function __construct() {
          
        if ($GLOBALS['sys_config']['db_switch'] == true)
        {            
            $this->db = new DB($GLOBALS['db_config']);
            
            $apnsDBConnection = new DbConnect($GLOBALS['db_config']['server'], 
                                                    $GLOBALS['db_config']['username'],
                                                    $GLOBALS['db_config']['password'], 
                                                    $GLOBALS['db_config']['database_name']);
            
            $this->apns = new APNS($apnsDBConnection,NULL,$this->apnsDistributionCer,$this->apnsSandboxCer,$this->apnsLogPath,'sandbox');
            
        }
        
      }

      //注册iOS设备
      public function registAppleDevice($appname, $appversion, $deviceuid, $devicetoken, $devicename, $devicemodel, $deviceversion, $pushbadge, $pushalert, $pushsound,$clientid,$environment){
          
          $this->apns->registerDevice($appname, $appversion, $deviceuid, $devicetoken, $devicename, $devicemodel, $deviceversion, $pushbadge, $pushalert, $pushsound,$clientid,$environment);
          
      }

      public function pushMessageToMobile($title,$description,$bageNumber,$payload = array(),$sound,$userId) {
          
          $type =  $this->db->get('roc_device',array('last_used_device'),array('user_id'=>$userId));

          $deviceType = $type['last_used_device'];
                    
          if($deviceType == 0){
           
             $this->applePushMessage($title, $description, $bageNumber, $payload, $sound,$userId);

          }else{
              
             $this->androidPushMessage($title, $description, $bageNumber, $payload, $sound,$userId);
          }
          
      }
      
      public function pushMessageToAllMobile($title,$description,$bageNumber,$payload = array(),$sound) {
          
          $this->androidPushAllDeviceMessage($title, $description, $bageNumber, $payload, $sound);
          $this->applePushAllDevice($title, $description, $bageNumber, $payload, $sound);
      }

      // Android Xiao Mi Push
      private function androidPushMessage($title,$description,$bageNumber,$payload,$sound,$userId) {
          
          
      }
      
      private function androidPushAllDeviceMessage($title,$description,$bageNumber,$payload,$sound){
          
      }


      // APNS
      private function applePushMessage($title,$description,$bageNumber,$payload,$sound,$userId) {
          
          $deviceToken = $this->deviceTokenByUserId($userId);
                    
          if($deviceToken != ''){
              
              $this->apns->newMessageByDeviceUId($deviceToken,time(),$userId); // FUTURE DATE NOT APART OF APPLE EXAMPLE
              $this->apns->addMessageAlert($title, $description);
              $this->apns->addMessageBadge(1);
              $this->apns->addMessageCustom('content', $payload);
              $this->apns->queueMessage();
              
              // SEND ALL MESSAGES NOW
              $this->apns->processQueue();
              
          }
      }
      
      private function applePushAllDevice($title,$description,$bageNumber,$payload,$sound){
          
          $this->apns->newMessage(time()); // FUTURE DATE NOT APART OF APPLE EXAMPLE
          $this->apns->addMessageAlert($title, $description);
          $this->apns->addMessageBadge(1);
          $this->apns->addMessageCustom('content', $payload);
          $this->apns->queueMessage();
          
          // SEND ALL MESSAGES NOW
          $this->apns->processQueue();
      }


      private function deviceTokenByUserId($userId){
          
          $resultToken = '';
          
          $type =  $this->db->get('roc_device',array('last_used_device'),array('user_id'=>$userId));
          
          $deviceType = $type['last_used_device'];
          
          if($deviceType == 0){
              
             $resultToken = $this->db->get('roc_device',array('ios_token'),array('user_id'=>$userId));
              
             return $resultToken['ios_token'];

          }else{
             
             $resultToken = $this->db->get('roc_device',array('android_token'),array('user_id'=>$userId));
 
             return $resultToken['android_token'];
          }
          
      }
  }

