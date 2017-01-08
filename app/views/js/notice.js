define(function(require, exports, module) {
    var $ = require("jquery"),
        bootstrap = require("bootstrap"),
        Vue = require("vue"),
        layer = require("layer"),
        common = require("js/base/common"),
        _csrf;
    layer.config({
        path: '/vendor/layer/'
    });

    $(document).ready(function() {
        _csrf = $('meta[name=_csrf]').attr('content');
    });

    var page = 1;
    var hash = document.location.hash;
    var hasHash = hash.replace("#", "") != '';

    Vue.directive('tooltip', function() {
        $("[data-toggle='tooltip']").tooltip('destroy');
        $("[data-toggle='tooltip']").tooltip();
    });
    tpl = new Vue({
        el: '#rocboss-app',
        data: {
            unread: config.unread,
            notice: [],
            whisper: [],
            nowTab: hasHash ? hash.replace("#", "") : 'unread'
        },
        methods: {
            changeTab: function(e, t) {
                var o = $(e.target).html();
                $(e.target).attr('disabled', 'disabled');
                $(e.target).html('<i class="fa fa-spinner fa-spin"></i>');
                $.get('/notice/change-type/' + t, function(data) {
                    if (data.status == 'success') {
                        if (t == 'unread') {tpl.unread = data.data.rows;}
                        if (t == 'notice') {tpl.notice = data.data.rows;}
                        if (t == 'whisper') {tpl.whisper = data.data.rows;}
                        tpl.nowTab = t;
                    }
                    $(e.target).html(o);
                    $(e.target).removeAttr('disabled');
                }, 'json')
            },
            doRead: function(type, id, tid, pid, uid) {
                $.post('/do/read/'+type, {
                    id: id,
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        $("#"+type+"-"+id).hide('fast');
                        if (type == 'notice' && typeof(tid) != 'undefined') {
                            window.location.href = '/read/'+tid+(typeof(pid) != 'undefined' ? '#reply-'+pid : '');
                        }
                        if (type == 'whisper' && typeof(uid) != 'undefined') {
                            window.location.href = '/chat-with-'+uid;
                        }
                    }
                }, 'json');
            },
            loadMoreNotice: function(e) {
                e.preventDefault();
                page ++;
                var that = e.target;
                var o = $(that).html();
                $(that).attr('disabled', 'disabled');
                $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                $.get('/get/notice/'+page, function(data) {
                    $(that).html(o);
                    if (data.status == 'success') {
                        tpl.notice = tpl.notice.concat(data.data.rows);
                        if (data.data.rows.length != 0) {
                            $(that).removeAttr('disabled');
                        } else {
                            $(that).html('已加载全部');
                            setTimeout(function() {
                                $(that).html(o);
                                $(that).removeAttr('disabled');
                            }, 3000);
                        }
                    } else {
                        layer.msg(data.data, {icon: 2});
                        $(that).removeAttr('disabled');
                    }
                }, 'json');
            },
            loadMoreWhisper: function(e) {
                e.preventDefault();
                page ++;
                var that = e.target;
                var o = $(that).html();
                $(that).attr('disabled', 'disabled');
                $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                $.get('/get/whisper/'+page, function(data) {
                    $(that).html(o);
                    if (data.status == 'success') {
                        tpl.whisper = tpl.whisper.concat(data.data.rows);
                        if (data.data.rows.length != 0) {
                            $(that).removeAttr('disabled');
                        } else {
                            $(that).html('已加载全部');
                            setTimeout(function() {
                                $(that).html(o);
                                $(that).removeAttr('disabled');
                            }, 3000);
                        }
                    } else {
                        layer.msg(data.data, {icon: 2});
                        $(that).removeAttr('disabled');
                    }
                }, 'json');
            }
        }
    });
    if (hasHash) {
        tpl.changeTab(this, hash.replace("#", ""));
    }
    $(document).ready(function() {

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
});
