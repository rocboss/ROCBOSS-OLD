<?php
//更新时间为2014年11月11日
//增加ClientId可自定义别名功能


header("Content-Type: text/html; charset=utf-8");

require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');

define('APPKEY','');
define('APPID','');
define('MASTERSECRET','');
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');
define('CID','');
//define('CID2','请输入ClientID');
define('ALIAS','请输入别名');

//pushMessageToSingle();

//pushMessageToList();

//aliasBind();    //ClientID与别名绑定

//aliasBatch();	//多个ClientID，使用同一别名绑定

//queryCID();	//根据别名查询ClientId

//queryAlias();	//根据ClientId查询别名

//aliasUnBind();  //解除ClientId别名绑定

//aliasUnBindAll(); 	//解除所有ClientId别名绑定

function aliasBind(){

	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
	$rep = $igt->bindAlias(APPID,ALIAS,CID);
	var_dump($rep);
	echo("<br><br>");

}

function aliasBatch(){

        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);

        $target = new IGtTarget();
        $target->set_clientId(CID);
        $target->set_alias(ALIAS);
	    $targetList[] = $target;

//        $target1 = new IGtTarget();
//        $target1->set_clientId(CID2);
//        $target1->set_alias(ALIAS);
//        $targetList[] = $target1;

        $rep = $igt->bindAliasBatch(APPID,$targetList);
        var_dump($rep);
        echo("<br><br>");
}

function queryCID(){
	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        $rep = $igt->queryClientId(APPID,ALIAS);
        var_dump($rep);
        echo("<br><br>");
}

function queryAlias(){
	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        $rep = $igt->queryAlias(APPID,CID);
        var_dump($rep);
        echo("<br><br>");
}

function aliasUnBind(){
	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        $rep = $igt->unBindAlias(APPID,ALIAS,CID);
        var_dump($rep);
        echo("<br><br>");
}

function aliasUnBindAll(){
        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        $rep = $igt->unBindAliasAll(APPID,ALIAS,CID);
        var_dump($rep);
        echo("<br><br>");
}

    //
    //服务端推送接口，支持三个接口推送
    //1.PushMessageToSingle接口：支持对单个用户进行推送
    //2.PushMessageToList接口：支持对多个用户进行推送，建议为50个用户
    //3.pushMessageToApp接口：对单个应用下的所有用户进行推送，可根据省份，标签，机型过滤推送
    //

//单推接口案例
function pushMessageToSingle(){
    	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
    
    //消息模版：
    // 1.TransmissionTemplate:透传功能模板
    // 2.LinkTemplate:通知打开链接功能模板
    // 3.NotificationTemplate：通知透传功能模板
    // 4.NotyPopLoadTemplate：通知弹框下载功能模板
	
    	$template = IGtNotyPopLoadTemplateDemo();
    	//$template = IGtLinkTemplateDemo();
    	//$template = IGtNotificationTemplateDemo();    
    	//$template = IGtTransmissionTemplateDemo();

    //个推信息体
	$message = new IGtSingleMessage();

	$message->set_isOffline(true);//是否离线
	$message->set_offlineExpireTime(3600*12*1000);//离线时间
	$message->set_data($template);//设置推送消息类型
	$message->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
	//接收方
	$target = new IGtTarget();
	$target->set_appId(APPID);
	//$target->set_clientId(CID);
	$target->set_alias(ALIAS);
	
	$rep = $igt->pushMessageToSingle($message,$target);

	var_dump($rep);
    echo ("<br><br>");
}


//多推接口案例
function pushMessageToList(){
	putenv("needDetails=true");
	$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
    //消息模版：
    // 1.TransmissionTemplate:透传功能模板
    // 2.LinkTemplate:通知打开链接功能模板
    // 3.NotificationTemplate：通知透传功能模板
    // 4.NotyPopLoadTemplate：通知弹框下载功能模板
	

    	//$template = IGtNotyPopLoadTemplateDemo();
    	//$template = IGtLinkTemplateDemo();
    	//$template = IGtNotificationTemplateDemo();
    	$template = IGtTransmissionTemplateDemo();
	//个推信息体
	$message = new IGtListMessage();

	$message->set_isOffline(true);//是否离线
	$message->set_offlineExpireTime(3600*12*1000);//离线时间
	$message->set_data($template);//设置推送消息类型
	//$message->set_PushNetWorkType(0);	//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
	$contentId = $igt->getContentId($message,'toList');
	//$contentId = $igt->getContentId($message,"toList任务别名功能");	//根据TaskId设置组名，支持下划线，中文，英文，数字

	//接收方1	
	$target1 = new IGtTarget();
	$target1->set_appId(APPID);
	//$target1->set_clientId(CID);
	$target1->set_alias(ALIAS);
	
	$targetList[] = $target1;

	$rep = $igt->pushMessageToList($contentId, $targetList);

	var_dump($rep);
    echo ("<br><br>");

}

    	//所有推送接口均支持四个消息模板，依次为通知弹框下载模板，通知链接模板，通知透传模板，透传模板
    	//注：IOS离线推送需通过APN进行转发，需填写pushInfo字段，目前仅不支持通知弹框下载功能

function IGtNotyPopLoadTemplateDemo(){
        $template =  new IGtNotyPopLoadTemplate();

        $template ->set_appId(APPID);//应用appid
        $template ->set_appkey(APPKEY);//应用appkey
        //通知栏
        $template ->set_notyTitle("个推");//通知栏标题
        $template ->set_notyContent("个推最新版点击下载");//通知栏内容
        $template ->set_notyIcon("");//通知栏logo
        $template ->set_isBelled(true);//是否响铃
        $template ->set_isVibrationed(true);//是否震动
        $template ->set_isCleared(true);//通知栏是否可清除
        //弹框
        $template ->set_popTitle("弹框标题");//弹框标题
        $template ->set_popContent("弹框内容");//弹框内容
        $template ->set_popImage("");//弹框图片
        $template ->set_popButton1("下载");//左键
        $template ->set_popButton2("取消");//右键
        //下载
        $template ->set_loadIcon("");//弹框图片
        $template ->set_loadTitle("地震速报下载");
        $template ->set_loadUrl("http://dizhensubao.igexin.com/dl/com.ceic.apk");
        $template ->set_isAutoInstall(false);
        $template ->set_isActived(true);

        return $template;
}

function IGtLinkTemplateDemo(){
        $template =  new IGtLinkTemplate();
        $template ->set_appId(APPID);//应用appid
        $template ->set_appkey(APPKEY);//应用appkey
        $template ->set_title("请输入通知标题");//通知栏标题
        $template ->set_text("请输入通知内容");//通知栏内容
        $template ->set_logo("");//通知栏logo
        $template ->set_isRing(true);//是否响铃
        $template ->set_isVibrate(true);//是否震动
        $template ->set_isClearable(true);//通知栏是否可清除
        $template ->set_url("http://www.igetui.com/");//打开连接地址
        // iOS推送需要设置的pushInfo字段
        //$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("",2,"","","","","","");
	return $template;
}

function IGtNotificationTemplateDemo(){
        $template =  new IGtNotificationTemplate();
        $template->set_appId(APPID);//应用appid
        $template->set_appkey(APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent("测试离线");//透传内容
        $template->set_title("个推");//通知栏标题
        $template->set_text("个推最新版点击下载");//通知栏内容
        $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        // iOS推送需要设置的pushInfo字段
        //$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("test",1,"message","","","","","");
        return $template;
}

function IGtTransmissionTemplateDemo(){
        $template =  new IGtTransmissionTemplate();
        $template->set_appId(APPID);//应用appid
        $template->set_appkey(APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent("测试离线");//透传内容
	//iOS推送需要设置的pushInfo字段
	//$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
	//$template ->set_pushInfo("", 0, "", "", "", "", "", "");
        return $template;
}


 
?>
