function editReplyForm(pid) {
    $.post(root + 'manage/getReplyInfo', {
        'pid': pid
    }, function(data) {
        var data = eval('(' + data + ')');
        layer.open({
            type: 1,
            area: ['80%', '400px'],
            shadeClose: false,
            title: '编辑回复',
            content: '<div id="post-edittopic"><form id="talk-add" class="add-post"><input type="hidden" name="pid" id="pid" value="' + data.pid + '"><div class="bz-form bz-form-stacked"><fieldset><label for="name">回复详情：</label><textarea id="subject" name="subject" rows="6"></textarea><label></label><button class="bz-button bz-button-primary" id="edit-btn" type="button" onclick="javascript:postEditReply();">提交</button></fieldset></div></form></div><div class="clear"></div>'
        });
        var editor = new Simditor({
            textarea: $('#subject'),
            defaultImage: root + 'app/template/rocboss/assets/img/default.jpg'
        });
        editor.setValue(data.content)
    })
}
function editTopicForm(tid) {
    $.post(root + 'manage/getTopicInfo', {
        'tid': tid
    }, function(data) {
        var data = eval('(' + data + ')');
        layer.open({
            type: 1,
            area: ['80%', '540px'],
            shadeClose: false,
            title: '后台编辑帖子 - ' + data.title,
            content: '<div id="post-edittopic"><form id="talk-add" class="add-post"><input type="hidden" name="tid" id="tid" value="' + data.tid + '"><div class="bz-form bz-form-stacked"><fieldset><label for="name">标题：</label><input type="text" name="title" id="title" class="bz-input-1" placeholder="（必填）请输入简要标题" value="' + data.title + '"><label for="name">帖子详情：</label><textarea id="subject" name="subject" rows="6"></textarea><label></label><button class="bz-button bz-button-primary" id="edit-btn" type="button" onclick="javascript:postEditTopic();">提交</button></fieldset></div></form></div><div class="clear"></div>'
        });
        var editor = new Simditor({
            textarea: $('#subject'),
            defaultImage: root + 'app/template/rocboss/assets/img/default.jpg'
        });
        editor.setValue(data.content)
    })
}
function postEditTopic() {
    $("#edit-btn").attr("disabled", "disabled");
    $.post(root + "manage/editTopic/", {
        "tid": $("#talk-add #tid").val(),
        "title": $("#talk-add #title").val(),
        "msg": $.trim($("#talk-add textarea[name=subject]").val())
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            layer.msg(data.message);
            setTimeout(function() {
                window.location.reload()
            }, 1000)
        } else {
            layer.msg(data.message);
            $("#edit-btn").removeAttr("disabled")
        }
    })
}
function postEditReply() {
    $("#edit-btn").attr("disabled", "disabled");
    $.post(root + "manage/editReply/", {
        "pid": $("#talk-add #pid").val(),
        "msg": $.trim($("#talk-add textarea[name=subject]").val())
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            layer.msg(data.message);
            setTimeout(function() {
                window.location.reload()
            }, 1000)
        } else {
            layer.msg(data.message);
            $("#edit-btn").removeAttr("disabled")
        }
    })
}
function topTopic(tid, status) {
    $.post(root + 'manage/topTopic/', {
        'tid': tid,
        'status': status
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            if (data.position == 1) {
                $('.topTopic').html('<i class=\"icon icon-locationfill x2\"></i>');
                $('.topTopic').attr('tip-title', '取消置顶')
            } else {
                $('.topTopic').html('<i class=\"icon icon-location x2\"></i>');
                $('.topTopic').attr('tip-title', '置顶')
            }
            $('.topTopic').attr('href', 'javascript:topTopic(' + tid + ', ' + data.position + ')');
            alertMessage(data.message)
        } else {
            alertMessage(data.message)
        }
    })
}
function lockTopic(tid, status) {
    $.post(root + 'manage/lockTopic/', {
        'tid': tid,
        'status': status
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            if (data.position == 1) {
                $('.lockTopic').html('<i class=\"icon icon-lock x2\"></i>');
                $('.lockTopic').attr('tip-title', '解锁回复')
            } else {
                $('.lockTopic').html('<i class=\"icon icon-unlock x2\"></i>');
                $('.lockTopic').attr('tip-title', '锁定回复')
            }
            $('.lockTopic').attr('href', 'javascript:lockTopic(' + tid + ', ' + data.position + ')');
            alertMessage(data.message)
        } else {
            alertMessage(data.message)
        }
    })
}
function delTopic(tid) {
    var that = $('#delTopic-' + tid);
    that.attr('disabled', 'disabled');
    $.post(root + 'do/deleteTopic/', {
        'tid': tid
    }, function(data) {
        if (data.result == "success") {
            $('#topic-' + tid).slideUp(500, function() {
                $(that).remove()
            });
            layer.msg('删除成功~')
        } else {
            that.removerAttr('disabled');
            layer.msg(data.message)
        }
    }, "json")
}
function delAllTopic() {
    var status = false;
    $('.checkbox').each(function() {
        if ($(this).prop('checked')) {
            status = true;
            var tid = $(this).val();
            delTopic(tid)
        }
    });
    if (!status) {
        layer.msg('请至少选择一项');
        return false
    }
}
function selectAll() {
    var checklist = document.getElementsByName("tid[]");
    if (document.getElementById("controlAll").checked) {
        for (var i = 0; i < checklist.length; i++) {
            checklist[i].checked = 1
        }
    } else {
        for (var j = 0; j < checklist.length; j++) {
            checklist[j].checked = 0
        }
    }
}
function delReply(pid) {
    var that = $('#delReply-' + pid);
    that.attr('disabled', 'disabled');
    $.post(root + 'do/deleteReply/', {
        'pid': pid
    }, function(data) {
        if (data.result == "success") {
            $('#reply-' + pid).slideUp(500, function() {
                $(that).remove()
            });
            layer.msg('删除成功~')
        } else {
            that.removerAttr('disabled');
            layer.msg(data.message)
        }
    }, "json")
}
function delAllReply() {
    var status = false;
    $('.checkbox').each(function() {
        if ($(this).prop('checked')) {
            status = true;
            var pid = $(this).val();
            delReply(pid);
            $('#delReply-' + pid).click()
        }
    });
    if (!status) {
        alert('至少选择一项');
        return false
    }
}
function ban(uid, t, status) {
    $(t).attr('disabled', 'disabled');
    $.post(root + 'manage/ban/', {
        'uid': uid,
        'status': status
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.result == "success") {
            if (status == 1) {
                $(t).html('禁言');
                $(t).attr('class', 'bz-button bz-button-primary');
                $(t).attr('onclick', 'javascript:ban(' + uid + ',this,0);')
            }
            if (status == 0) {
                $(t).html('解禁');
                $(t).attr('class', 'bz-button danger');
                $(t).attr('onclick', 'javascript:ban(' + uid + ',this,1);')
            }
            if (status == 9) {
                $(t).attr('onclick', 'javascript:ban(' + uid + ',this,0);');
                location.reload()
            }
            $(t).removeAttr('disabled')
        } else {
            $(t).removeAttr('disabled');
            alert(data.message)
        }
    })
}
function editLink(position, text, url) {
    if (position == '') {
        var title = '新增链接'
    } else {
        var title = '正在编辑链接'
    }
    layer.open({
        type: 1,
        area: ['550px', '240px'],
        shadeClose: true,
        title: '<i class="iconfont icon-lianjie"></i> ' + title,
        content: '<form action="' + root + 'manage/edit_link/" method="post"><div class="bz-form bz-form-aligned"><fieldset><div class="bz-control-group"><label for="name">排序：</label><input type="text" name="position" placeholder="正整数，数字越大越靠后" value="' + position + '" size="40"></div><div class="bz-control-group"><label for="name">链接名称：</label><input type="text" name="text" placeholder="链接名称" value="' + text + '" size="40"></div><div class="bz-control-group"><label for="name">链接地址：</label><input type="text" name="url" placeholder="需要加 http://" value="' + url + '" size="40"></div><div class="bz-controls"><button class="bz-button bz-button-primary" id="doSign-btn" type="submit" i class="iconfont icon-queren2">提交</button></div></fieldset></div></form>'
    })
}