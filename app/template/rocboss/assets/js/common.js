$(document).ready(function(){
    if (!window.applicationCache) {
        layer.msg('你的浏览器不支持HTML5，请升级浏览器');
    }
    var h = window.location.hash;
    if (h != '') {
        $('html,body').animate({scrollTop:$(h).offset().top - 50}, 800);
    }
    $('.btn-circle').live('hover', function(event) {
        if(event.type == 'mouseenter' && $(this).attr('tip-title') != undefined) {
            layer.tips($(this).attr('tip-title'), $(this));
        }
    });
});

$(function() {
    $.fn.manhuatoTop = function(options) {
        var defaults = {            
            showHeight : 150,
            speed : 1000
        };
        var options = $.extend(defaults,options);
        $("body").prepend("<div id='totop'><a class='btn-circle' tip-title='回到顶部'><i class='icon icon-location x3'></i></a></div>");
        var $toTop = $(this);
        var $top = $("#totop");
        var $ta = $("#totop a");
        $toTop.scroll(function(){
            var scrolltop=$(this).scrollTop();      
            if(scrolltop>=options.showHeight){              
                $top.show();
            }
            else{
                $top.hide();
            }
        }); 
        $ta.hover(function(){       
            $(this).addClass("cur");    
        },function(){           
            $(this).removeClass("cur");     
        }); 
        $top.click(function(){
            $("html,body").animate({scrollTop: 0}, options.speed);  
        });
    }
});

$(function (){
    $(window).manhuatoTop({
        showHeight : 100,//设置滚动高度时显示
        speed : 500 //返回顶部的速度以毫秒为单位
    });
});
function showTopicForm() {
    layer.open({
        type: 1,
        area: ['80%', '540px'],
        shadeClose: false,
        title: '发表帖子',
        content: '<div id="post-newtopic"><form id="talk-add" class="add-post"><fieldset><div class="text-title"><input type="text" name="title" id="title" class="form-input" placeholder="（必填）请输入简要标题"></div><div class="textarea"><textarea id="subject" name="subject" rows="6" placeholder="（必填）请输入详细正文，上传、插入图片时请注意换行"></textarea></div><div id="tagInput" class="textarea"><div class="text-tag"><div id="tags" class="show-tag"></div><div class="clear"></div><input type="text" class="form-tag-input" placeholder="（必填）请输入标签并回车显示，最多支持5个标签，点击标签以删除"> <input type="text" id="final" style="display:none"></div></div><input type="text" name="tempTid" id="tempTid" value="" style="display:none"> <input type="text" name="pictureString" id="pictureString" value="" style="display:none"> <a class="right btn btn-default" id="create" href="javascript:postNewTopic();" rel="nofollow">创建</a><div class="upload-image" title="上传图片"><i class="icon icon-camerafill x6"></i> <input type="file" name="upfile" id="post-pictures-file" accept="image/*" style="opacity:0;left:0;top:0;bottom:0;margin:0;position:absolute;width:35px;cursor: pointer;"></div><div class="clear"></div><div class="showLine"></div></fieldset></form></div><div class="clear"></div>'
    });
    var editor = new Simditor({
      textarea: $('#subject'),
      defaultImage: root+'app/template/rocboss/assets/img/default.jpg'
    });
}

function showFloorReply(pid, u) {
    $('.floor-reply').remove();
    $('#floor-more-'+pid).before('<div class=\"floor-reply\" id=\"do-floor-reply-'+pid+'\"><input type=\"text\" class=\"reply-text\" maxlength=\"100\" placeholder=\"评论最多允许100个字哦~\"/><a class=\"reply-submit right\" href=\"javascript:postNewFloor('+pid+');\" id="reply">回复</a></div>');
    if ('@'+$('#myAvatar').attr('alt') != $.trim(u)) {
        $('.reply-text').val($('.reply-text').val()+u);
    }
    $('.reply-text').focus();
}

function readWhisper(id, d) {
    $.post(root+'do/readWhisper', {
        "id" : id
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            $('#whisper-' + id).slideUp(300, function() {
                $(this).remove();
                if(d) {
                    layer.msg(data.message);
                }
            });
        }
    });
}

function showWhisper(uid, username, flag) {
    if (flag > 0) {
        readWhisper(flag, false);
        showWhisper(uid, username, 0);
    }else{
        layer.open({
            type:1,
            area: ['300px', '220px'],
            shadeClose: false,
            title: '私信 '+username,
            content :'<form class="add-post"><div class="input-group"><input type="text" value="发给：'+username+'" style="display:none"><div class="textarea"><textarea id="whisper-content" name="content" rows="5" placeholder="请输入私信内容，最多允许250字哦~"></textarea></div><input type="hidden" name="touid" id="whisper-touid" value="'+uid+'"> <a class="btn btn-default mt10" href="javascript:whisper();" id="whisper-btn">发送</a></div></form>'
        });
    }
}
function getMoreFloor(pid, page) {
    var k = 0;
    $('#floor-more-'+pid+' .floor-more').html('正在加载更多评论...');
    $.post(root+"getReplyFloorList/", {
        "pid": pid,
        "page": page
    }, function(data) {
        value = eval("(" + data + ")");
        if (value != '') {
            $('#floor-more-'+pid+' .floor-more').remove();
            for (k = 0; k < value.length; k++) {
                $('#floor-more-'+pid).append('<div id=\"floor-list-'+value[k].floorId+'\" class=\"floor-list\"></div>');
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-avatar\"><a href=\"'+root+'user/'+value[k].floorUid+'/\"><img src=\"'+value[k].avatar+'\"></a></span>');
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-username\"><a href=\"'+root+'user/'+value[k].floorUid+'/\">'+value[k].floorUser+'</a></span>');
                if (login_groupid == 9) {
                    if (login_uid != value[k].floorUid) {
                        $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-admin right\"><a href=\"javascript:showFloorReply('+pid+',\'@'+value[k].floorUser+' \');\" title=\"回复TA\"><i class=\"icon icon-forward x1\"></i>回复</a><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[k].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                    } else {
                        $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-admin right\"><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[k].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                    }
                } else {
                    if (login_uid != value[k].floorUid && login_uid != 0) {
                        $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-admin right\"><a href=\"javascript:showFloorReply('+pid+',\'@'+value[k].floorUser+' \');\" title=\"回复TA\"><i class=\"icon icon-forward x1\"></i>回复</a></span>');
                    } else if(login_uid == value[k].floorUid && login_uid != 0) {
                        $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-admin right\"><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[k].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                    }
                }
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-time right\">'+value[k].floorTime+'</span><div class=\"clear\"></div>');
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-content\">'+value[k].floorContent+'</span>');
            }
            if (k >= 5) {
                $('#floor-more-'+pid).append('<div class=\"floor-more\"><a href=\"javascript:getMoreFloor('+pid+','+parseInt(page+1)+');\"><i class=\"icon icon-unfold x1\"></i> 点击加载更多评论</a></div>');
            } else {
                $('#floor-more-'+pid).append('<div class=\"floor-more\">已加载全部评论</div>');
            };
        } else {
            $('#floor-more-'+pid+' .floor-more').html('已加载全部评论');
        }
    })
}

function whisper()
{
    $('#whisper-btn').attr('disabled','disabled');
    $.post(root+"do/deliverWhisper", {
        "atuid": $('#whisper-touid').val(),
        "content": $.trim($('#whisper-content').val())
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            layer.msg(data.message);
            setTimeout(function(){
                window.location.href = root+'whisper/2';
            }, 1500);
        } else {
            layer.msg(data.message);
            $('#whisper-btn').removeAttr('disabled');
        }
    });
}

function search()
{
    var search = $('#searchWord').val();

    if (search.length < 2) {
        layer.msg('您的关键字太少了~');
        return false
    } else {
        window.location = root + 'search/' + search;
    }
}

function follow(uid)
{
    $.post(root+"do/follow/", {
        'uid': uid
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            if (data.position == 1) {
                $('#follow').html('<i class="icon icon-like x2"></i> 关注');
            }
            if (data.position == 0) {
                $('#follow').html('<i class="icon icon-likefill x2"></i> 取消关注');
            };
        }
    });
}

function ban(uid, status)
{
    $('#ban').removeAttr('href');
    $.post(root+"manage/ban/", {
        'uid': uid,
        'status': status
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            $('#ban').attr('href', 'javascript:ban('+uid+', '+(1-status)+');');
            if (status == 1) {
                $('#ban').html('<i class="icon icon-roundclose x2"></i> 禁言');
            }
            if (status == 0) {
                $('#ban').html('<i class="icon icon-roundclosefill x2"></i> 解禁');
            };
        }
    });
}