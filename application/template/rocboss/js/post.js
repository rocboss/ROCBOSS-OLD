var rand;
var newDiv;
var newDiv2;
$(function() {
    $('#tagInput').click(function() {
        $('.form-tag-input').focus();
    })
    $(document).on("click", "#tags span", function() {
        $(this).remove();
        $('#final').val(getNowTagStr());
    });
    $('.form-tag-input').bind('keyup', function(event) {
        if (event.keyCode == 13) {
            var txt = $(this).val();
            if (txt != '') {
                txts = new Array();
                $('#tags .tag').each(function() {
                    txts += $(this).attr('name') + ','
                });
                if (txts == '') {
                    txts = new Array();
                } else {
                    txts = txts.split(",");
                }
                if (txts.length > 5) {
                    alertMessage('每次标签最多只允许添加5个哦~');
                    return false;
                };
                var exist = $.inArray(txt, txts);
                if (exist < 0) {
                    $('#tags').append('<span name=' + txt + ' class="tag">' + txt + '</span>');
                    $('#final').val(getNowTagStr());
                    $(this).val('');
                } else {
                    $(this).val('');
                }
            }
        }
    });
});

$('#post-pictures-file').on('click',function(){
    $(this).localResizeIMG({
        width: 1000,
        quality: 0.9,
        before: function() {
            if (checkHtml5Support() == false) {
                alertMessage("你的老掉牙浏览器不支持HTML5，请使用先进浏览器");
                return false;
            }
        },
        success: function(result) {
            var img = new Image();
            img.src = result.base64;
            rand = new Date().getTime();
            newDiv = '<div id=\"uploadFile' + rand + '\" class=\"uploadResult\"><div class=\"info\">压缩上传中...</div><img class=\"previewImage\"></div>';
            $('.showLine').before(newDiv);
            $.ajax({
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            // 获取进度百分比
                            var percentComplete = parseInt((evt.loaded / evt.total)*100);
                            $(".info:last").html("已上传："+percentComplete + "%");
                        }
                    });
                    return xhr;
                },
                url: root + 'do/uploadPicture',
                type: 'POST',
                data: {
                    'base64': result.base64
                },
                timeout: 45000,
                error: function() {
                    alertMessage('上传发生错误或超时，请重试');
                    $('#uploadFile' + rand).hide();
                },
                success: function(data) {
                    data = eval("(" + data + ")");
                    if (data.result == 'success') {
                        newDiv2 = $('<div class=\"delPic\" onclick=\"javascript:delUploadImage(\'uploadFile' + rand + '\', ' + data.position + ');\" title=\"删除\" style=\"display: block;\"><i class=\"icon icon-roundclose x2\"></i></div>');
                        $('#uploadFile' + rand + ' img').after(newDiv2);
                        $('#uploadFile' + rand + ' img').attr("src", result.base64);
                        $('#uploadFile' + rand + ' .info').html('上传完成');
                        insertResId(data.position);
                    } else {
                        alertMessage(data.message);
                        $('#uploadFile' + rand).hide();
                    }
                }
            });
        }
    });
});

$('#post-avatar').on('click',function(){
    $(this).localResizeIMG({
        width: 200,
        quality: 0.9,
        before: function() {
            if (checkHtml5Support() == false) {
                alertMessage("你的老掉牙浏览器不支持HTML5，请使用先进浏览器");
                return false;
            }
            newDiv = $('<span class=\"notice\">正在上传...</span>')
            $('.input-group').append(newDiv);
        },
        success: function(result) {
            var img = new Image();
            img.src = result.base64;
            $.ajax({
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            // 获取进度百分比
                            var percentComplete = parseInt((evt.loaded / evt.total)*100);
                            $(".nitice").html("已上传："+percentComplete + "%");
                        }
                    });
                    return xhr;
                },
                url: root + 'do/uploadAvatar/',
                type: 'POST',
                data: {
                    'base64': result.base64
                },
                timeout: 45000,
                error: function() {
                    alertMessage('上传发生错误或超时，请重试');
                },
                success: function(data) {
                    data = eval("(" + data + ")");
                    if (data.result == 'success') {
                        $('.notice').remove();
                        $('.avatar-now').attr('src', $('.avatar-now').attr('src')+'?'+Math.random());
                    } else {
                        alertMessage(data.message);
                        $('.notice').remove();
                    }
                }
            });
        }
    });
});

function getNowTagStr() {
    nowStr = new Array();
    $('#tags .tag').each(function() {
        nowStr += $(this).attr('name') + ' ';
    });
    return nowStr;
}

function delUploadImage(obj, id) {
    $.post(root + 'do/delPic/', {
        'id': id,
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            $('textarea[name=subject]').val($.trim($('textarea[name=subject]').val()).replace('[:' + id + ']', ''));
            $('textarea[name=subject]').focus();
            $('#' + obj).hide();
        } else {
            alertMessage(data.message);
        }
    })
}

function insertResId(id) {
    $('textarea[name=subject]').val($.trim($('textarea[name=subject]').val()) + '\n[:' + id + ']');
    $('textarea[name=subject]').focus();
}

function checkHtml5Support() {
    if (window.applicationCache) {
        return true;
    } else {
        return false;
    }
}

function postNewTopic() {
    $("#create").attr("disabled", "disabled");
    $.post(root + "do/postTopic/", {
        "title": $("#talk-add #title").val(),
        "tag": $("#talk-add #final").val(),
        "msg": $.trim($("#talk-add textarea[name=subject]").val())
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            alertMessage(data.message);
            $("#post-newtopic").toggle("fast");
            window.setTimeout("window.location='" + root + "home/read/" + data.position + "'", 1000)
        } else {
            alertMessage(data.message);
            $("#create").removeAttr("disabled")
        }
    })
}

function postNewReply() {
    $("#create").attr("disabled", "disabled");
    $.post(root + "do/postReply/", {
        "tid": $("#reply-add #tid").val(),
        "content": $.trim($("#reply-add textarea[name=subject]").val())
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            alertMessage(data.message);
            $("#more").append('<div class=\"reply-list\" id=\"d-reply-'+data.position+'\"><div class=\"reply-left\"><a href=\"/user/index/uid/'+login_uid+'\" class=\"uid\"><img src=\"'+$('#myAvatar').attr('src')+'\" alt=\"'+$('#myAvatar').attr('alt')+'\" class=\"avatar\"></a></div><div class=\"reply-content\"><div class=\"reply-detail\"><span class=\"content\">'+$.trim($("#reply-add textarea[name=subject]").val())+'</span></div><div class=\"reply-bottom\"><span class=\"reply-bottom-span\"><a href=\"/user/index/uid/5\" class=\"uid\"><span class=\"username\">'+$('#myAvatar').attr('alt')+'</span></a></span><span class=\"client reply-bottom-span\"></span><span class=\"posttime reply-bottom-span\"><i class="icon icon-time"></i> 刚刚</span><span class=\"reply-admin right\"><a class=\"deleteReply\" href=\"javascript:deleteReply('+data.position+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span></div></div></div>');
            $(".uploadResult").remove();
            $("#reply-add textarea[name=subject]").val('');
        } else {
            alertMessage(data.message);
        }
        $("#create").removeAttr("disabled");
    })
}

function postNewFloor(pid) {
    $('.reply-submit').attr("disabled", "disabled");
    $.post(root + "do/postFloor/", {
        "pid": parseInt(pid),
        "content": $("#do-floor-reply-" + pid + " .reply-text").val()
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            alertMessage(data.message);
            rand = new Date().getTime();
            $('#floor-more-' + pid).prepend('<div id=\"floor-list-' + rand + '\" class=\"floor-list\"></div>');
            $('#floor-more-' + pid + ' #floor-list-' + rand).append('<span class=\"floor-avatar\"><img src=\"' + $('#myAvatar').attr('src') + '\"></span>');
            $('#floor-more-' + pid + ' #floor-list-' + rand).append('<span class=\"floor-username\">' + $('#myAvatar').attr('alt') + '</span>');
            $('#floor-more-' + pid + ' #floor-list-' + rand).append('<span class=\"floor-time right\">刚刚评论</span><div class=\"clear\"></div>');
            $('#floor-more-' + pid + ' #floor-list-' + rand).append('<span class=\"floor-content\">' + $("#do-floor-reply-" + pid + " .reply-text").val() + '</span>');
            $('.floor-reply').remove();
        } else {
            alertMessage(data.message);
            $('.reply-text').focus();
            $('.reply-submit').removeAttr("disabled")
        }
    })
}

function deleteTopic(tid) {
    var o = $('.deleteTopic');

    var h = o.html();

    o.removeAttr("href").html(h.replace("删除", "确定？")).unbind();

    o.click(function() {
        o.hide();

        $.post(root+'do/deleteTopic/', {
            'tid': tid
        }, function(data) {
            if (data.result == "success") {
                $('.topic-view').slideUp(300, function() {
                    $(this).remove();
                    alertMessage(data.message);
                    window.setTimeout("window.location='" + root + "'", 1000);
                });
            } else {
                alertMessage(data.message);
                o.show();
            }
        }, "json");
    });

    setTimeout(function() {
        o.html(h).attr('href', 'javascript:deleteTopic(' + tid + ')').unbind();
    }, 3000);
}

function deleteReply(pid) {
    var o = $('#d-reply-' + pid + ' .deleteReply');

    var h = o.html();

    o.removeAttr("href").html(h.replace("删除", "确定？")).unbind();

    o.click(function() {
        o.hide();

        $.post(root+'do/deleteReply/', {
            'pid': pid
        }, function(data) {
            if (data.result == "success") {
                $('#d-reply-' + pid).slideUp(300, function() {
                    $(this).remove();
                    alertMessage(data.message);
                });
            } else {
                alertMessage(data.message);
                o.show();
            }
        }, "json");
    });

    setTimeout(function() {
        o.html(h).attr('href', 'javascript:deleteReply(' + pid + ')').unbind();
    }, 3000);
}

function deleteFloor(id) {
    var o = $('#floor-list-' + id + ' .floor-admin .delete-btn');

    var h = o.html();

    o.removeAttr("href").html(h.replace("删除", "确定？")).unbind();

    o.click(function() {
        o.hide();

        $.post(root+'do/deleteFloor/', {
            'id': id
        }, function(data) {
            if (data.result == "success") {
                $('#floor-list-' + id).slideUp(300, function() {
                    $(this).remove();
                    alertMessage(data.message);
                });
            } else {
                alertMessage(data.message);
                o.show();
            }
        }, "json");
    });

    setTimeout(function() {
        o.html(h).attr('href', 'javascript:deleteFloor(' + id + ')').unbind();
    }, 3000);
}

function favorTopic(tid, status) {
    $.post(root + 'do/favorTopic/', {
        'tid': tid,
        'status': status
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            if (data.position == 1) {
                $('.favorTopic').html('<i class=\"icon icon-favorfill x2\"></i>取消收藏');
            } else {
                $('.favorTopic').html('<i class=\"icon icon-favor x2\"></i>收藏');
            }
            $('.favorTopic').attr('href', 'javascript:favorTopic('+tid+', '+data.position+')');
        }
    })
}

function praiseTopic(tid, status) {
    $.post(root + 'do/praiseTopic/', {
        'tid': tid,
        'status': status
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            if (data.position == 1) {
                $('.praiseTopic').html('<i class=\"icon icon-appreciatefill x2\"></i>取消赞');
                $('.topic-praise').show();
                $('.topic-praise').prepend('<img src=\"'+$('#myAvatar').attr('src')+'\" title=\"'+$('#myAvatar').attr('alt')+'\" alt=\"'+$('#myAvatar').attr('alt')+'\" class="avatarC">');
            } else {
                $('.praiseTopic').html('<i class=\"icon icon-appreciate x2\"></i>点赞');
                $('.topic-praise img[title=' + $('#myAvatar').attr('alt') + ']').remove();
            }
            $('.praiseTopic').attr('href', 'javascript:praiseTopic('+tid+', '+data.position+')');
        }
    })
}

function doSign() {
    $.post(root + 'do/doSign/', {
        'do': 'doSign'
    }, function(data) {
        data = eval('(' + data + ')');
        alertMessage(data.message);
        if (data.position > 0) {
            $('#today-sign').html('<i class=\"icon icon-selectionfill x2\"></i>今日已签到');
            $('#mine-score').html(parseInt($('#mine-score').html()) + data.position);
        }
    })
}
function setSignature() {
    $.post(root + 'do/setSignature/', {
        'signature': $("#signature").val()
    }, function(data) {
        data = eval('(' + data + ')');
        alertMessage(data.message);
    })
}

function setEmail() {
    $.post(root + 'do/setEmail/', {
        'email': $("#email").val(),
        'password': $("#password").val()
    }, function(data) {
        data = eval('(' + data + ')');
        alertMessage(data.message);
        $("#password").val('');
    })
}

function setPassword() {
    var password = $("#password").val();
    var newPassword = $("#newPassword").val();
    var reNewPassword = $("#reNewPassword").val();
    if (password == newPassword) {
        alertMessage('新密码不能和老密码一样');
        return false;
    }
    if (reNewPassword != newPassword) {
        alertMessage('两次新密码输入不一致');
        return false;
    }
    $.post(root + 'do/setPassword/', {
        'password': password,
        'newPassword': newPassword
    }, function(data) {
        data = eval('(' + data + ')');
        alertMessage(data.message);
        if (data.result == 'success') {
            $("#password").val('');
            $("#newPassword").val('');
            $("#reNewPassword").val('');
        };
    })
}