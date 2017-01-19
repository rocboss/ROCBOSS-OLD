define(function(require, exports, module) {
    var $ = require("jquery"),
        vue = require("vue"),
        layer = require("layer"),
        common = require("js/base/common");
    layer.config({
        path: '/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });
    var _csrf, cid = 0;

    exports.init = function() {
        $(document).ready(function() {
            var menu;
            var isMobile = navigator.userAgent.match(/(iPad).*OS\s([\d_]+)/) || navigator.userAgent.match(/(iPhone\sOS)\s([\d_]+)/) || navigator.userAgent.match(/(Android)\s+([\d.]+)/);
            if (isMobile) {
                menu = ['emotion', 'img'];
            } else {
                menu = ['bold', 'underline', 'italic', 'strikethrough', 'emotion', 'img', 'unorderlist', 'orderlist', 'link', 'unlink', 'insertcode', 'source', 'undo'];
            }
            _csrf = $('meta[name=_csrf]').attr('content');
            $(".more-input").on('click', function(event) {
                $(".wangEditor-txt").attr('style', 'height: '+($(".wangEditor-txt").height() + 150)+'px');
            });
            $("#post-btn").on('click', function(event) {
                event.preventDefault();
                OPTIONS.postTopic(false);
            });
            key = 'new-topic';
            if (localStorage.getItem(key) != null || localStorage.getItem(key) != '') {
                $('#editor').val(localStorage.getItem(key));
            }
            var emoji = [];
            for (var i = 1; i <= 36; i++) {
                emoji.push('/dist/img/emoji/' + i + '.gif');
            };
            require('wangEditor');
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
            $('.wangEditor-menu-container').append('<div id="saving-tip" class="saving-tip pull-right"><i class="fa fa-spinner fa-spin"></i> 自动保存草稿中...</div><div class="clearfix"></div>');
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
            $(".chooseClub").on('click', function(event) {
                OPTIONS.chooseClub(this);
            });
        });
    }
    exports.edit = function(config) {
        $(document).ready(function() {
            cid = config.cid;
            var menu;
            var isMobile = navigator.userAgent.match(/(iPad).*OS\s([\d_]+)/) || navigator.userAgent.match(/(iPhone\sOS)\s([\d_]+)/) || navigator.userAgent.match(/(Android)\s+([\d.]+)/);
            if (isMobile) {
                menu = ['emotion', 'img'];
            } else {
                menu = ['bold', 'underline', 'italic', 'strikethrough', 'emotion', 'img', 'unorderlist', 'orderlist', 'link', 'unlink', 'insertcode', 'source', 'undo'];
            }
            _csrf = $('meta[name=_csrf]').attr('content');
            $(".more-input").on('click', function(event) {
                $(".wangEditor-txt").attr('style', 'height: '+($(".wangEditor-txt").height() + 150)+'px');
            });
            $("#post-btn").on('click', function(event) {
                event.preventDefault();
                OPTIONS.postTopic(true);
            });
            key = 'edit-topic';
            if (localStorage.getItem(key) != null || localStorage.getItem(key) != '') {
                $('#editor').val(localStorage.getItem(key));
            }
            var emoji = [];
            for (var i = 1; i <= 36; i++) {
                emoji.push('/dist/img/emoji/' + i + '.gif');
            };
            require('wangEditor');
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
            editor.$txt.html(config.txt);
            $("#editor").val(config.txt);
            $('.wangEditor-menu-container').append('<div id="saving-tip" class="saving-tip pull-right"><i class="fa fa-spinner fa-spin"></i> 自动保存草稿中...</div><div class="clearfix"></div>');
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
            $(".chooseClub").on('click', function(event) {
                OPTIONS.chooseClub(this);
            });
        });
    }
    var OPTIONS = {
        postTopic: function(isNew) {
            var url = '/add/topic/';
            if (isNew == true) {
                var tid = $('#tid').val();
                url = '/edit/topic/'+tid;
            }
            var title = $('#title').val();
            var content = $('.editor').val();
            if (cid == 0) {
                layer.msg('请先选择分类', {icon: 2});
                return;
            }
            if (title == '') {
                layer.msg('请填写标题', {icon: 2});
                return;
            }
            if (title.length < 5) {
                layer.msg('标题应不少于五个字符', {icon: 2});
                return;
            }
            if (OPTIONS.removeHTMLTag(content).length < 5) {
                layer.msg('内容应不少于五个字符', {icon: 2});
                return;
            }
            $('#post-btn').attr('disabled', 'disabled');
            $('#post-btn').html('<i class="fa fa-spinner fa-spin"></i> 发布中 ...');

            $.post(url, {
                cid: cid,
                title: title,
                content: content,
                _csrf: _csrf
            }, function(data) {
                if (data.status == 'success') {
                    localStorage.removeItem(key);
                    location.href = '/read/'+data.data;
                } else {
                    if (data.code == 10006) {
                        layer.msg(data.msg, {icon: 2});
                    } else {
                        layer.msg(data.data, {icon: 2});
                    }
                    $('#post-btn').removeAttr('disabled');
                    $('#post-btn').html('<i class="fa fa-check "></i> 提交');
                }
            }, 'json');
        },
        chooseClub: function(obj) {
            $('.chooseClub').attr('class', 'chooseClub btn btn-sm bg-gray');
            $(obj).attr('class', 'chooseClub btn btn-sm bg-olive');
            cid = $(obj).data('cid');
        },
        removeHTMLTag: function(str) {
            str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
            str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
            //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
            str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
            return str;
        }
    }
});
