function showTopicForm() {
    $("#post-newtopic").toggle('fast');
    $("#talk-add textarea[name=subject]").focus();
}
function showFloorReply(pid, u) {
    $('.floor-reply').remove();
    $('#floor-more-'+pid).before('<div class=\"floor-reply\" id=\"do-floor-reply-'+pid+'\"><input type=\"text\" class=\"reply-text\" maxlength=\"100\" placeholder=\"评论最多允许100个字哦~\"/><a class=\"reply-submit right\" href=\"javascript:postNewFloor('+pid+');\" id="reply">回复</a></div>');
    if ('@'+$('#myAvatar').attr('alt') != $.trim(u)) {
        $('.reply-text').val($('.reply-text').val()+u);
    }
    $('.reply-text').focus();
}
function showWhisper(id, uid, username) {
    $('.whisper-reply').remove();
    $('#whisper-'+id+' .topic').append('<div class=\"whisper-reply\"><input type=\"text\" id=\"whisper-atuid\" value=\"'+uid+'\" style=\"display: none;\"><input type=\"text\" id=\"whisper-content\" class=\"reply-text\" maxlength=\"250\" placeholder=\"私信最多允许250个字哦~\"/><a class=\"reply-submit right\" href=\"javascript:whisper('+id+');\" id=\"whisper-btn\">传送</a></div>');
    $('.reply-text').focus();
}
function alertMessage(msg) {
    $(".alert-messages .message .message-text").html(msg);
    $(".alert-messages").fadeIn();
    setTimeout('$(".alert-messages").fadeOut()', 2000)
}
function getMoreFloor(pid, page) {
    var k = 0;
    $('#floor-more-'+pid+' .floor-more').html('正在加载更多评论...');
    $.post(root+"home/getReplyFloorList/", {
        "pid": pid,
        "page": page
    }, function(data) {
        value = eval("(" + data + ")");
        if (value != '') {
            $('#floor-more-'+pid+' .floor-more').remove();
            for (k = 0; k < value.length; k++) {
                $('#floor-more-'+pid).append('<div id=\"floor-list-'+value[k].floorId+'\" class=\"floor-list\"></div>');
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-avatar\"><a href=\"'+root+'user/index/uid/'+value[k].floorUid+'/\"><img src=\"'+value[k].avatar+'\"></a></span>');
                $('#floor-more-'+pid+' #floor-list-'+value[k].floorId).append('<span class=\"floor-username\"><a href=\"'+root+'user/index/uid/'+value[k].floorUid+'/\">'+value[k].floorUser+'</a></span>');
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

function whisper(type)
{
    if (type == 'new') {
        var touid = $('#touid').val();
        var content =$.trim($('#content').val());
        if (content == '') {
            alertMessage('内容不能为空!');
            return false;
        }
        $('#whisper-btn').attr('disabled','disabled');
        $.post(root+"do/deliverWhisper", {
            "atuid": touid,
            "content": content
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.result == "success") {
                $('#content').val('');
                alertMessage(data.message);
                window.setTimeout("window.location='"+root+"user/whisper/status/2'",1000); 
            } else {
                $('#whisper-btn').removeAttr('disabled');
                alertMessage(data.message); 
            }
        });
    } else {
        $('#whisper-btn').attr('disabled','disabled');
        $.post(root+"do/deliverWhisper", {
            "atuid": $('#whisper-atuid').val(),
            "content": $.trim($('#whisper-content').val())
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.result == "success") {
                $('#whisper-'+type).slideUp(300, function() {
                    $(this).remove();
                    alertMessage(data.message);
                });
            } else {
                alertMessage(data.message);
                $('#whisper-btn').removeAttr('disabled');
                $('.reply-text').focus();
            }
        });
    }
}

function search()
{
    var search = $('#searchWord').val();

    if (search.length < 2) {
        alertMessage('您的关键字太少了~');
        return false
    } else {
        window.location = root + 'home/search/s/' + search;
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