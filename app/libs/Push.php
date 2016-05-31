<?php

require_once("getui/IGt.Push.php");

require_once("getui/igetui/IGt.AppMessage.php");

require_once("getui/igetui/IGt.APNPayload.php");

require_once("getui/igetui/template/IGt.BaseTemplate.php");

require_once("getui/IGt.Batch.php");

class Push
{
    private $AppKey = '';

    private $AppID = '';

    private $MasterSecret = '';

    private $host = 'http://sdk.open.api.igexin.com/apiex.htm';

    public function setConfig(array $config)
    {
        $this->AppKey = $config['AppKey'];

        $this->AppID = $config['AppID'];

        $this->MasterSecret = $config['MasterSecret'];
    }

    /**
     * 推送单用户
     *
     * @param $template
     * @param $clientid
     */
    public function pushMessageToSingle($template, $clientid)
    {
        $igt     = new IGeTui($this->host, $this->AppKey, $this->MasterSecret);
        
        //个推信息体
        $message = new IGtSingleMessage();
        $message->set_isOffline(true); //是否离线
        $message->set_offlineExpireTime(172800000); //离线时间
        $message->set_data($template); //设置推送消息类型
        
        //接收方
        $target = new IGtTarget();
        $target->set_appId($this->AppID);
        $target->set_clientId($clientid);
        $rep = $igt->pushMessageToSingle($message, $target);
        
        return $rep;
    }

    /**
     * 给所有用户推送
     *
     * @param $phoneType
     * @param $template
     */
    public function pushMessageToApp($phoneType, $template)
    {
        // 此方式可通过获取服务端地址列表判断最快域名后进行消息推送，每10分钟检查一次最快域名
        $igt     = new IGeTui('', $this->AppKey, $this->MasterSecret);
        
        //个推信息体
        //基于应用消息体
        $message = new IGtAppMessage();
        
        $message->set_isOffline(true);
        
        //离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_offlineExpireTime(172800000);
        
        $message->set_data($template);
        
        //设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $message->set_PushNetWorkType(0);
        
        //设置群推接口的推送速度，单位为条/秒，例如填写100，则为100条/秒。仅对指定应用群推接口有效。
        $message->set_speed(1000);

        $message->set_appIdList([$this->AppID]);

        if ($phoneType == 1)
        {
            $message->set_phoneTypeList(['ANDROID']);
        }
        else
        {
            $message->set_phoneTypeList(['IOS']);
        }
        $rep = $igt->pushMessageToApp($message);

        return $rep;
    }

    /**
     * 通知透传模版
     *
     * @param $data
     * @return IGtNotificationTemplate
     */
    public function NotificationTemplate($data)
    {
        $template = new IGtNotificationTemplate();

        //应用appid
        $template->set_appId($this->AppID);
        
        //应用appkey
        $template->set_appkey($this->AppKey);
        
        //通知栏标题
        $template->set_title($data['title']);
        
        //通知栏内容
        $template->set_text($data['text']);
        
        //通知栏logo
        $template->set_logo($data['logo']);
        
        //是否响铃
        $template->set_isRing($data['isRing']);
        
        //是否震动
        $template->set_isVibrate($data['isVibrate']);
        
        //通知栏是否可清除
        $template->set_isClearable($data['isClearable']);
        
        //收到消息是否立即启动应用：1为立即启动，2则广播等待客户端自启动
        $template->set_transmissionType($data['transmissionType']);
        
        //透传内容
        $template->set_transmissionContent($data['transmissionContent']);

        return $template;
    }

    /**
     * 透传模版
     *
     * @param $data
     * @return IGtTransmissionTemplate
     */
    public function TransmissionTemplate($data)
    {
        $template = new IGtTransmissionTemplate();

        //应用appid
        $template->set_appId($this->AppID);
        
        //应用appkey
        $template->set_appkey($this->AppKey);
        
        //收到消息是否立即启动应用，1为立即启动，2则广播等待客户端自启动
        $template->set_transmissionType($data['transmissionType']);
        
        //透传内容
        $template->set_transmissionContent($data['transmissionContent']);
        
        $template->set_pushInfo($data['title'], 1, $data['text'], "", $data['transmissionContent'], "", "", "");
        
        return $template;
    }
    
    /**
     * 点击打开网页模版
     *
     * @param $data
     * @return IGtLinkTemplate
     */
    public function LinkTemplate($data)
    {
        $template = new IGtLinkTemplate();

        //应用appid
        $template->set_appId($this->AppID);

        //应用appkey
        $template->set_appkey($this->AppKey);

        //通知栏标题
        $template->set_title($data['title']);

        //通知栏内容
        $template->set_text($data['text']);
        
        //通知栏logo
        $template->set_logo($data['logo']);
        //通知栏logo链接
        //$template->set_logoURL("");
        
        //是否响铃
        $template->set_isRing($data['isRing']);
        
        //是否震动
        $template->set_isVibrate($data['isVibrate']);
        
        //通知栏是否可清除
        $template->set_isClearable($data['isClearable']);
        
        //打开连接地址
        $template->set_url($data['url']);
        
        //TODO:IOS待添加
        return $template;
    }

    /**
     * 通知栏弹框下载 IOS 不支持
     *
     * @param $data
     * @return IGtNotyPopLoadTemplate
     */
    public function NotyPopLoadTemplate($data)
    {
        $template = new IGtNotyPopLoadTemplate();

        $template->set_appId($this->AppID); //应用appid

        $template->set_appkey($this->AppKey); //应用appkey

        //通知栏
        //通知栏标题
        $template->set_notyTitle($data['notyTitle']);
        //通知栏内容
        $template->set_notyContent($data['notyContent']);
        //通知栏logo
        $template->set_notyIcon($data['notyIcon']);
        //是否响铃
        $template->set_isBelled($data['isBelled']);
        //是否震动
        $template->set_isVibrationed($data['isVibrationed']);
        //通知栏是否可清除
        $template->set_isCleared($data['isCleared']);
        //弹框
        //弹框标题
        $template->set_popTitle($data['popTitle']);
        //弹框内容
        $template->set_popContent($data['popContent']);
        //弹框图片
        $template->set_popImage($data['popImage']);
        //左键
        $template->set_popButton1($data['popButton1']);
        //右键
        $template->set_popButton2($data['popButton2']);
        //下载
        //弹框图片
        $template->set_loadIcon($data['loadIcon']);
        //弹框标题
        $template->set_loadTitle($data['loadTitle']);
        //下载地址
        $template->set_loadUrl($data['loadUrl']);
        //是否自动安装
        $template->set_isAutoInstall($data['isAutoInstall']);
        //安装完成后是否自动启动
        $template->set_isActived($data['isActived']);

        return $template;
    }
}
