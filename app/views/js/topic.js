define(function(require, exports, module) {
    var $ = require("jquery");
    var vue = require("vue");
    var layer = require("layer");
    layer.config({
        path: '/app/views/js/vendor/layer/'
    });
    var _csrf;
    exports.init = function() {
        var cid = 0;
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
                postTopic();
            });
            key = 'new-topic';
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
            $('.wangEditor-menu-container').append('<div id="saving-tip" class="saving-tip pull-right"><i class="fa fa-spinner fa-spin"></i> 本地草稿自动保存中...</div><div class="clearfix"></div>');
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
                $('.chooseClub').attr('class', 'chooseClub btn btn-sm btn-default');
                $(this).attr('class', 'chooseClub btn btn-sm btn-primary');
                cid = $(this).data('cid');
            });
        });

        function postTopic() {
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
            if (removeHTMLTag(content).length < 5) {
                layer.msg('内容应不少于五个字符', {icon: 2});
                return;
            }
            $('#post-btn').attr('disabled', 'disabled');
            $('#post-btn').html('<i class="fa fa-spinner fa-spin"></i> 发布中 ...');

            $.post('/add/topic/', {
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
        }
        function removeHTMLTag(str) {
            str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
            str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
            //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
            str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
            return str;
        }
    }
    exports.edit = function(config) {
        var cid = 0;
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
                postTopic();
            });
            key = 'edit-topic';
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
            editor.$txt.html(config.txt);
            $('.wangEditor-menu-container').append('<div id="saving-tip" class="saving-tip pull-right"><i class="fa fa-spinner fa-spin"></i> 本地草稿自动保存中...</div><div class="clearfix"></div>');
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
                $('.chooseClub').attr('class', 'chooseClub btn btn-sm btn-default');
                $(this).attr('class', 'chooseClub btn btn-sm btn-primary');
                cid = $(this).data('cid');
            });
        });

        function postTopic() {
            var tid = $('#tid').val();
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
            if (removeHTMLTag(content).length < 5) {
                layer.msg('内容应不少于五个字符', {icon: 2});
                return;
            }
            $('#post-btn').attr('disabled', 'disabled');
            $('#post-btn').html('<i class="fa fa-spinner fa-spin"></i> 发布中 ...');

            $.post('/edit/topic/'+tid, {
                cid: cid,
                title: title,
                content: content,
                _csrf: _csrf
            }, function(data) {
                if (data.status == 'success') {
                    localStorage.removeItem(key);
                    location.href = '/read/'+data.data;
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
            //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
            str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
            return str;
        }
    }
});
