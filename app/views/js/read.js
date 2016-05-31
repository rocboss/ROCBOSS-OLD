define(function(require, exports, module) {
    var $ = require("jquery");
    var bootstrap = require("bootstrap");
    // var vue = require("vue");
    var laypage = require("laypage");
    var layer = require("layer");
    var _csrf;
    layer.config({
        path: '/app/views/js/vendor/layer/'
    });
    // 主题详情
    exports.init = function(config) {
        $(document).ready(function() {
            var menu;
            var tid = config.tid;
            var isMobile = navigator.userAgent.match(/(iPad).*OS\s([\d_]+)/) || navigator.userAgent.match(/(iPhone\sOS)\s([\d_]+)/) || navigator.userAgent.match(/(Android)\s+([\d.]+)/);
            if (isMobile) {
                menu = ['emotion', 'img'];
            } else {
                menu = ['bold', 'underline', 'italic', 'strikethrough', 'emotion', 'img', 'unorderlist', 'orderlist', 'link', 'unlink', 'insertcode', 'source', 'undo'];
            }
            _csrf = $('meta[name=_csrf]').attr('content');
            $(".load-at-reply").on('click', function(event) {
                loadAtReply($(this).data('at_pid'), $(this).data('tid'));
            });
            $(".load-at-reply").hover(function(event) {
                if (event.type == 'mouseenter') {
                    layer.tips('点击加载全部', this, {
                        tips: [4, '#4B9DBD']
                    });
                } else {
                    layer.closeAll();
                }
            });
            $(".change-club").on('click', function(event) {
                $.post('/change/club/' + tid, {
                    cid: $(this).data('cid'),
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        layer.msg(data.data, {icon: 1});
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                }, 'json');
            });
            $(".top-topic").on('click', function(event) {
                event.preventDefault();
                $.post('/top/topic/'+tid, {
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        layer.msg(data.data, {icon: 1});
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                }, 'json');
            });
            $(".lock-topic").on('click', function(event) {
                event.preventDefault();
                $.post('/lock/topic/'+tid, {
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        layer.msg(data.data, {icon: 1});
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                }, 'json');
            });
            $(".delete-topic").on('click', function(event) {
                event.preventDefault();
                layer.confirm('确定删除该主题么？', {
                    title: '提醒',
                    btn: ['确定','取消']
                }, function() {
                    $.post('/delete/topic/' + tid, {
                        _csrf: _csrf
                    }, function(data) {
                        if (data.status == 'success') {
                            layer.msg(data.data, {icon: 1});
                            setTimeout(function () {
                                location.reload();
                            }, 1200);
                        } else {
                            layer.msg(data.data, {icon: 2});
                        }
                    }, 'json');
                }, function() {
                });
            });
            $(".do-praise").on('click', function(event) {
                $(this).attr('disabled', 'disabled');
                $(this).html('<i class="fa fa-spinner fa-spin"></i> 点赞中...');
                var that = this;
                $.post('/do/praise/' + tid, {
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        $(".p-tips").show();
                        $(".topic-praise .clear").before('<a href="/user" class="praise-user"><img alt="image" class="img-circle" src="'+$("#my-avatar").attr('src')+'"></a>');
                        $(that).html('<i class="fa fa-thumbs-up "></i> 已点赞');
                    } else {
                        $(that).removeAttr('disabled');
                        $(that).html('<i class="fa fa-thumbs-up "></i> 点赞');
                    }
                }, 'json');
            });
            $(".do-collection").on('click', function(event) {
                var o = $(this).html();
                var that = this;
                $(this).attr('disabled', 'disabled');
                $(this).html('<i class="fa fa-spinner fa-spin"></i> 收藏中...');
                $.post('/do/collection/' + tid, {
                    _csrf: _csrf
                }, function(data) {
                    if (data.status == 'success') {
                        $(that).html(data.data);
                    } else {
                        $(that).html(o);
                    }
                    $(that).removeAttr('disabled');
                }, 'json');
            });
            $(".do-reward").on('click', function(event) {
                $(".reward-input").toggle('fast');
            });
            $(".confirm-reward").on('click', function(event) {
                var o = $(this).html();
                var that = this;
                var score = $(".reward-score").val();
                if (score >= 1 && score <= 1000) {
                    $(this).attr('disabled', 'disabled');
                    $(this).html('<i class="fa fa-spinner fa-spin"></i> 打赏中...');
                    $.post('/do/reward/' + tid, {
                        score: score,
                        _csrf: _csrf
                    }, function(data) {
                    if (data.status == 'success') {
                        layer.msg(data.data, {icon: 1});
                        $(".reward-score").val('');
                        $(".reward-input").toggle('fast');
                        $(".topic-reward").show();
                        $(".topic-reward").append('<p><a href="/user">我</a> &nbsp; <small>刚刚</small> &nbsp; 打赏了 <strong>'+score+'</strong> 积分</p>')
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                    $(that).html(o);
                    $(that).removeAttr('disabled');
                }, 'json');
                } else {
                    layer.msg('单次打赏积分范围1~1000', {icon: 2});
                }
            });
            $(".reward-score").keyup(function(){
                var c=$(this);
                if(/[^\d]/.test(c.val())) {
                  var temp_amount=c.val().replace(/[^\d]/g,'');
                  $(this).val(temp_amount);
                }
            });
            $(".do-reply").on('click', function(event) {
                if ($(this).data('pid') == 0) {
                    $(".reply-list").after($("#reply-input"));
                } else {
                    $(".reply-input-blank").remove();
                    $(this).parent('.pull-right').after($("#reply-input"));
                    $(this).parent('.pull-right').after('<div class="clearfix reply-input-blank"></div>');
                }
                $("#post-btn").data('at_pid', $(this).data('pid'));
                $(".wangEditor-txt").focus();
            });
            $(".delete-reply").on('click', function(event) {
                var that = this;
                layer.confirm('确定删除该回复么？', {
                    title: '提醒',
                    btn: ['确定','取消']
                }, function() {
                    $(that).attr('disabled', 'disabled');
                    $.post('/delete/reply/' + $(that).data('pid'), {
                        _csrf: _csrf
                    }, function(data) {
                        if (data.status == 'success') {
                            layer.msg(data.data, {icon: 1});
                            setTimeout(function() {
                                $("#reply-"+$(that).data('pid')).hide('fast');
                            }, 300);
                        } else {
                            layer.msg(data.data, {icon: 2});
                        }
                        $(that).removeAttr('disabled');
                    });
                }, function() {
                });
            });
            $(".more-input").on('click', function(event) {
                $(".wangEditor-txt").attr('style', 'height: '+($(".wangEditor-txt").height() + 150)+'px');
            });
            $("#post-btn").on('click', function(event) {
                postReply($(this).data('at_pid'), $(this).data('tid'));
            });
            key = 'topic-' + tid + '-reply';
            if (localStorage.getItem(key) != null || localStorage.getItem(key) != '') {
                $('#editor').val(localStorage.getItem(key));
            }
            var emoji = [];
            for (var i = 1; i <= 36; i++) {
                emoji.push('/app/views/emoji/' + i + '.gif');
            };
            require('wangEditor')($);
            wangEditor.config.printLog = false;
            var editor = new wangEditor('editor');
            editor.config.menuFixed = false;
            editor.config.emotions = {
                'default': {
                    title: '默认',
                    size: 18,
                    imgs: emoji
                }
            };
            editor.config.menus = menu;
             // 上传图片
            editor.config.uploadImgUrl = '/uploads';
            editor.create();
            $('.wangEditor-menu-container').append('<div id="saving-tip" class="saving-tip pull-right">' + '<i class="fa fa-spinner fa-spin"></i> 本地草稿自动保存中...' + '</div>' + '<div class="clearfix"></div>');
            // 每10秒自动保存草稿
            var s = setInterval(function() {
                if ($('.editor').val() != '') {
                    localStorage.setItem(key, $('#editor').val());
                    $('#saving-tip').attr('class', 'saving-tip active pull-right');

                    setTimeout(function() {
                        $('#saving-tip').attr('class', 'saving-tip pull-right');
                    }, 3000);
                }
            }, 10000);
            require.async("fancybox", function(fancybox) {
                $('.fancybox').fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                });
            });
        });
        function loadAtReply(pid, tid) {
            $('.at-reply-' + pid).html('<p><i class="fa fa-spinner fa-spin"></i> 完整引用回复加载中 ...</p>');
            setTimeout(function() {
                var tpl = '<div class="content">' + $('#reply-' + pid + ' .content').html() + '</div>';

                if ($('#reply-' + pid + ' .image-list').html() != undefined) {
                    tpl += '<div class="image-list">' + $('#reply-' + pid + ' .image-list').html() + '</div>';
                }
                $('.at-reply-' + pid).html(tpl);
                $('.at-reply-' + pid).parent('.well').attr('class', 'well topic-detail at-reply-detail');
            }, 400);
        }
        function postReply(atPid, tid) {
            var content = $('.editor').val();
            if (removeHTMLTag(content).length < 3) {
                layer.msg('内容应不少于三个字符', {icon: 2});
                return;
            }
            $('#post-btn').attr('disabled', 'disabled');
            $('#post-btn').html('<i class="fa fa-spinner fa-spin"></i> 发布中 ...');

            $.post('/add/reply/' + tid, {
                content: content,
                at_pid: atPid,
                _csrf: _csrf
            }, function(data) {
                if (data.status == 'success') {
                    localStorage.removeItem(key);
                    location.reload();
                } else {
                    layer.msg(data.data, {icon: 2});
                    $('#post-btn').removeAttr('disabled');
                    $('#post-btn').html('<i class="fa fa-check "></i> 提交');
                }
            }, 'json');
        }
        function removeHTMLTag(str) {
            str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
            str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
            str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
            return str;
        }
    }
});
