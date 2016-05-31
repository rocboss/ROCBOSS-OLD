define(function(require, exports, module) {
    var $ = require("jquery");
    var bootstrap = require("bootstrap");
    var vue = require("vue");
    var layer = require("layer");
    var _csrf;
    layer.config({
        path: '/app/views/js/vendor/layer/'
    });
    exports.init = function(config) {
        var unread_notification = config.unread_notification;
        var unread_whisper = config.unread_whisper;
        var notification = config.notification;
        var whisper = config.whisper;
        var isForMe = true;
        tpl = new Vue({
            el: '#notice-list',
            data: {
                unread_notification: unread_notification,
                unread_whisper: unread_whisper,
                notification: notification,
                whisper: whisper,
                isForMe: isForMe
            }
        });
        $(document).ready(function() {
            $("#notice-list").show();
            _csrf = $('meta[name=_csrf]').attr('content');
            $(".do-read").on('click', function(event) {
                var that = this;
                $.post('/do/read/'+$(that).data('type'), {
                    id: $(that).data('id'),
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        $("#"+$(that).data('type')+"-"+$(that).data('id')).hide('fast');
                    }
                }, 'json');
            });
            $(".switch-whisper").on('click', function(event) {
                var type = $(this).data('type');
                tpl.isForMe = type > 0 ? false : true;
                var o = $(this).html();
                $(this).attr('disabled', 'disabled');
                $(this).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                var that = this;
                $.get('/get/whisper/'+type+'/1', function(data) {
                    if (data.status == 'success') {
                        tpl.whisper = data.data;
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                    $(that).removeAttr('disabled');
                    $(that).html(o);
                }, 'json');
            });
            $(document).on('click', ".delete-whisper", function(event) {
                var that = this;
                layer.confirm('确定删除该私信吗？', {
                    title: '提醒',
                    btn: ['确定','取消']
                }, function() {
                    $.post('/delete/whisper', {
                        id: $(that).data('id'),
                        _csrf: _csrf
                    }, function(data) {
                        if (data.status == 'success') {
                            $("#whisper-"+$(that).data('id')).hide('fast');
                        }
                    }, 'json');
                    layer.closeAll();
                }, function() {
                });
            });
        });
    }
});
