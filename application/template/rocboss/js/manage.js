function topTopic(tid, status) {
    $.post(root + 'manage/topTopic/', {
        'tid': tid,
        'status': status
    }, function(data) {
        data = eval('(' + data + ')');
        if (data.result == 'success') {
            if (data.position == 1) {
                $('.topTopic').html('<i class=\"icon icon-locationfill x2\"></i>取消置顶');
            } else {
                $('.topTopic').html('<i class=\"icon icon-location x2\"></i>置顶');
            }
            $('.topTopic').attr('href', 'javascript:topTopic('+tid+', '+data.position+')');
            alertMessage(data.message);
        } else {
            alertMessage(data.message);
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
                $('.lockTopic').html('<i class=\"icon icon-lock x2\"></i>解锁');
            } else {
                $('.lockTopic').html('<i class=\"icon icon-unlock x2\"></i>锁定');
            }
            $('.lockTopic').attr('href', 'javascript:lockTopic('+tid+', '+data.position+')');
            alertMessage(data.message);
        } else {
            alertMessage(data.message);
        }
    })
}

function delTopic(tid){
    var that = $('#delTopic-'+tid);
    that.removeAttr('onclick');
    that.html(that.html().replace('删除','确认删除?')).unbind();
    that.click(function(){
        that.attr('disabled', 'disabled');
        $.post(root+'do/deleteTopic/', {
            'tid': tid
        }, function(data) {
            if (data.result == "success") {
                $('#topic-'+tid).slideUp(500, function() {
                    $(that).remove();
                });
            } else {
                that.removerAttr('disabled');
                alert(data.message);
            }
        }, "json");
    });
    setTimeout(function() {
        that.html('删除').attr('onclick', 'javascript:delTopic(' + tid + ')').unbind();
    }, 3000);
}

function delAllTopic(){
    var status=false;
    $('.checkbox').each(function(){
        if($(this).prop('checked')){
            status = true;
            var tid = $(this).val();
            delTopic(tid);
            $('#delTopic-'+tid).click();
        }
    });
    if(!status){
        alert('至少选择一项');
        return false;
    }
}

function delReply(pid){
    var that = $('#delReply-'+pid);
    that.removeAttr('onclick');
    that.html(that.html().replace('删除','确认删除？')).unbind();
    that.click(function(){
        that.attr('disabled', 'disabled');
        $.post(root+'do/deleteReply/', {
            'pid': pid
        }, function(data) {
            if (data.result == "success") {
                $('#reply-'+pid).slideUp(500, function() {
                    $(that).remove();
                });
            } else {
                that.removerAttr('disabled');
                alert(data.message);
            }
        }, "json");
    });
    setTimeout(function() {
        that.html('删除').attr('onclick', 'javascript:delReply('+pid+')').unbind();
    }, 3000)
}

function delAllReply(){
    var status=false;
    $('.checkbox').each(function(){
        if($(this).prop('checked')){
            status = true;
            var pid = $(this).val();
            delReply(pid);
            $('#delReply-'+pid).click();
        }
    });
    if(!status){
        alert('至少选择一项');
        return false;
    }
}

function ban(uid, t, status){
    $(t).attr('disabled', 'disabled');
    $.post(root+'manage/ban/', {
            'uid': uid,
            'status': status
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.result == "success") {
                if (status == 1) {
                    $(t).html('禁言');
                    $(t).attr('onclick', 'javascript:ban('+uid+',this,0);');
                }
                if (status == 0) {
                    $(t).html('解除禁言');
                    $(t).attr('onclick', 'javascript:ban('+uid+',this,1);');
                }
                if (status == 9) {
                    $(t).attr('onclick', 'javascript:ban('+uid+',this,0);');
                    location.reload();
                }
                $(t).removeAttr('disabled');
            } else {
                $(t).removeAttr('disabled');
                alert(data.message);
            }
        });
}