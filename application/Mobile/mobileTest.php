

<html>
    <title>手机端测试</title>
    <body>
       
        注册测试：<br>
        <form action="/rocboss/mobileUser/register" method="post">
            
           邮箱: <input type="text" name="email" > <br>
           用户名: <input type="text" name="nickname"> <br>
           密码: <input type="text" name="password"> <br>
           密码: <input type="text" name="do" value="register"> <br>
           <input type="submit" name="注册"><br>
           
        </form>
 
        登陆测试:<br>
        <form action="/rocboss/mobileUser/login" method="post">
            
           邮箱: <input type="text" name="email" > <br>
           密码: <input type="text" name="password"> <br>
           <input type="submit" name="登陆"><br>
           
        </form>
        
        用户详情:<br>
        <form action="/rocboss/mobileUser/userDetailInfo" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="userId" > <br>
           登陆用户Id: <input type="text" name="loginUserId"> <br>
           <input type="submit" name="查询"><br>
           
        </form>
        
        我的帖子列表:<br>
        <form action="/rocboss/mobileUser/topics" method="post">
           
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="userId" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        我的回复列表:<br>
        <form action="/rocboss/mobileUser/reply" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="userId" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        我的私信列表:<br>
        <form action="/rocboss/mobileUser/whisper" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           状态: <input type="text" name="status" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        某用户的关注列表:<br>
        <form action="/rocboss/mobileUser/follow" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="userId" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        关注用户:<br>
        <form action="/rocboss/mobileDo/follow" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           登陆用户Id: <input type="text" name="loginUserId" > <br>
           用户Id: <input type="text" name="uid" > <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        某用户的粉丝列表:<br>
        <form action="/rocboss/mobileUser/fans" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="userId" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        我的提醒列表:<br>
        <form action="/rocboss/mobileUser/notification" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           状态: <input type="text" name="status" > <br>
           页码: <input type="text" name="pageIndex"> <br>
           <input type="submit" name="获取"><br>
           
        </form>
        
        发布帖子:<br>
        <form action="/rocboss/mobileDo/postTopic" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           标题: <input type="text" name="title" > <br>
           内容: <input type="text" name="msg" > <br>
           标签(用逗号隔开): <input type="text" name="tag"> <br>
           客户端类型: <input type="text" name="client"> <br>
           
           <input type="submit" name="发布"><br>
           
        </form>
        
        回复帖子:<br>
        <form action="/rocboss/mobileDo/postReply" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           内容: <input type="text" name="content" > <br>
           客户端类型: <input type="text" name="client"> <br>
           
           <input type="submit" name="发布"><br>
           
        </form>
        
        
        回复评论:<br>
        <form action="/rocboss/mobileDo/postFloor" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           评论Id: <input type="text" name="pid" > <br>
           内容: <input type="text" name="content" > <br>
           
           <input type="submit" name="发布"><br>
           
        </form>
        
        删除帖子:<br>
        <form action="/rocboss/mobileDo/deleteTopic" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        收藏帖子:<br>
        <form action="/rocboss/mobileDo/favorTopic" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           动作(0收藏,1取消): <input type="text" name="status" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        点赞帖子:<br>
        <form action="/rocboss/mobileDo/praiseTopic" method="post">
            
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           动作(0赞,1取消): <input type="text" name="status" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        发送私信:<br>
        <form action="/rocboss/mobileDo/deliverWhisper" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           发送者Id: <input type="text" name="loginUserId" > <br>
           接受者Id: <input type="text" name="atuid" > <br>
           内容: <input type="text" name="content" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        获取首页帖子列表:<br>
        <form action="/rocboss/mobileHome/index" method="post">
           
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>
           用户Id: <input type="text" name="loginUserId" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           排序方法(lasttime,posttime): <input type="text" name="type" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        获取帖子详情:<br>
        <form action="/rocboss/mobileHome/read" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        搜索帖子:<br>
        <form action="/rocboss/mobileHome/search" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           关键字: <input type="text" name="keyword" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        按标签查询帖子:<br>
        <form action="/rocboss/mobileHome/tag" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           标签名: <input type="text" name="tag" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        查询帖子回复列表:<br>
        <form action="/rocboss/mobileHome/getReplyList" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           帖子Id: <input type="text" name="tid" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           
           <input type="submit" name="确定"><br>
           
        </form>
        
        设置邮箱:<br>
        <form action="/rocboss/mobileDo/setEmail" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           用户Id: <input type="text" name="loginUserId" > <br>
           邮箱: <input type="text" name="email" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        设置签名:<br>
        <form action="/rocboss/mobileDo/setSignature" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           用户Id: <input type="text" name="loginUserId" > <br>
           邮箱: <input type="text" name="signature" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        签到:<br>
        <form action="/rocboss/mobileDo/doSign" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           用户Id: <input type="text" name="loginUserId" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        查询热门标签:<br>
        <form action="/rocboss/mobileHome/getHotTags" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           <input type="submit" name="确定"><br>
           
        </form>
        
        查询热门帖子:<br>
        <form action="/rocboss/mobileHome/getHotTopics" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           <input type="submit" name="确定"><br>
           
        </form>
        
        查询签到榜单:<br>
        <form action="/rocboss/mobileHome/getTodayTopSign" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br>            
           <input type="submit" name="确定"><br>
           
        </form>
        
        锁住帖子:<br>
        <form action="/rocboss/mobileManage/lockTopic" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           用户组Id: <input type="text" name="groupId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           状态:(0,解锁，1锁定)<input type="text" name="status" > <br>
           <input type="submit" name="确定"><br>
        </form>
        
        置顶帖子:<br>
        <form action="/rocboss/mobileManage/topTopic" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           用户组Id: <input type="text" name="groupId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           状态:(0,置顶，1取消置顶)<input type="text" name="status" > <br>
           <input type="submit" name="确定"><br>
        </form>
        
        申请帖子锁定行为:<br>
        <form action="/rocboss/mobileUser/applyLockTopic" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           被申请用户角色:<input type="text" name="groupId" > <br>
           被申请用户名:<input type="text" name="userName" > <br>
           发帖者Id: <input type="text" name="userId" > <br>
           帖子Id: <input type="text" name="tid" > <br>
           帖子标题: <input type="text" name="topicTitle" > <br>
           原因: <input type="text" name="reason" > <br>
           状态:(0,申请解锁，1举报帖子)<input type="text" name="status" > <br>
           <input type="submit" name="确定"><br>
        </form>
        
        申请用户锁定行为:<br>
        <form action="/rocboss/mobileUser/applyLockAction" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           被申请用户角色:<input type="text" name="groupId" > <br>
           被申请用户名:<input type="text" name="userName" > <br>
           发帖者Id: <input type="text" name="userId" > <br>
           原因: <input type="text" name="reason" > <br>
           状态:(0,申请解锁，1举报此人)<input type="text" name="status" > <br>
           <input type="submit" name="确定"><br>
        </form>
        
        用户申请行为列表:<br>
        <form action="/rocboss/mobileAdmin/userApplyList" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           用户组Id: <input type="text" name="groupId" > <br>
           状态:(0,申请解禁,1举报此人)<input type="text" name="status" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        帖子申请行为:<br>
        <form action="/rocboss/mobileAdmin/topicApplyList" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           用户Id: <input type="text" name="loginUserId" > <br>
           用户组Id: <input type="text" name="groupId" > <br>
           状态:(0,申请解锁，1举报帖子)<input type="text" name="status" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
         搜索用户:<br>
        <form action="/rocboss/mobileAdmin/searchUser" method="post">
           token:<input type="text" name="token" value="zZDPYHZshlxueIjkB3VUgqe0Zw0kZfwG"> <br> 
           关键字: <input type="text" name="keyword" > <br>
           页码: <input type="text" name="pageIndex" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        注册设备:<br>
        <form action="/rocboss/mobileUser/registDevice" method="post">
                       
           用户Id: <input type="text" name="loginUserId" > <br>
           设备Token: <input type="text" name="deviceToken" > <br>
           设备类型: <input type="text" name="deviceType" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
        测试iOS推送:<br>
        <form action="/rocboss/mobileUser/pushTest" method="post">
                       
           用户Id: <input type="text" name="loginUserId" > <br>
           <input type="submit" name="确定"><br>
           
        </form>
        
    </body>
    
</html>
