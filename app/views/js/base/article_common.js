define(function(require, exports, module) {
    var bootstrap = require("bootstrap"),
        lazyload = require("lazyload")($),
        Vue = require("vue"),
        layer = require("layer"),
        laypage = require("laypage"),
        common = require("js/base/common"),
        WebUploader = require("webuploader");

    layer.config({
        path: '/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });

    var DATA = {
        uploadToken: '',
        rows: [],
        page: 1,
        per: 12,
        total: 0
    }, key = '';
    var OPTIONS = {
        postTopic: function(isNew) {
            var url = '/add/article/';
            if (isNew == true) {
                var aid = $('#aid').val();
                url = '/edit/article/'+aid;
            }
            var poster = $('#poster').val();
            var title = $('#title').val();
            var content = $('.editor').val();
            if (poster == '') {
                layer.msg('请上传文章封面图', {icon: 2});
                return;
            }
            if (title == '') {
                layer.msg('请填写文章标题', {icon: 2});
                return;
            }
            if (title.length < 5) {
                layer.msg('文章标题应不少于五个字符', {icon: 2});
                return;
            }
            if (OPTIONS.removeHTMLTag(content).length < 100) {
                layer.msg('内容应不少于100个字符', {icon: 2});
                return;
            }
            $('#post-btn').attr('disabled', 'disabled');
            $('#post-btn').html('<i class="fa fa-spinner fa-spin"></i> 发布中 ...');

            $.post(url, {
                poster: poster,
                title: title,
                content: content,
                _csrf: _csrf
            }, function(data) {
                if (data.status == 'success') {
                    localStorage.removeItem(key);
                    layer.msg('投稿成功，请耐心等待审核', {icon: 1});
                    setTimeout(function() {
                        location.href = '/article';
                    }, 1000);
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
        removeHTMLTag: function(str) {
            str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
            str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
            //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
            str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
            return str;
        }
    }
    var _csrf = $('meta[name=_csrf]').attr('content');
    exports.init = function(config) {
        DATA.rows = config.rows;
        DATA.page = config.page;
        DATA.per = config.per;
        DATA.total = config.total;
        var tpl = new Vue({
            el: '#rocboss-app',
            data: DATA,
            methods: {

            }
        });
        var pages = Math.ceil(DATA.total / DATA.per);
        var href = '/article/';
        laypage.dir = '/dist/css/laypage.css';
        laypage({
            dir: '/dist/css/laypage.css',
            cont: 'pagination',
            pages: pages,
            curr: DATA.page,
            href: href + '(?)',
            first: 1,
            last: pages,
            skin: 'molv',
            prev: '<',
            next: '>',
            jump: function(e, first) {
                if (!first) {
                    var url = href + e.curr;
                    window.location.href = url;
                }
            }
        });
    }
    exports.read = function(aid) {
        $(".do-praise").on('click', function(event) {
            $(this).attr('disabled', 'disabled');
            $(this).html('<i class="fa fa-spinner fa-spin"></i> 点赞中...');
            var that = this;
            $.post('/do/article-praise/' + aid, {
                _csrf: _csrf
            }, function(data) {
                if (data.status == 'success') {
                    $(".p-tips").show();
                    $(".article-praise .no-data").hide();
                    $(".article-praise .clear").before('<a href="/user" class="praise-user"><img alt="image" class="img-circle u-avatar" src="'+$("#my-avatar").attr('src')+'"></a>');
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
            $.post('/do/article-collection/' + aid, {
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
    }
    exports.newArticle = function(uploadToken, saveKey) {
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
            key = 'new-article';
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
        });
        // ### 封面上传
        // 初始化Web Uploader
        DATA.uploadToken = uploadToken;
        var uploader = WebUploader.create({
            auto: true,
            swf: '/app/views/js/vendor/webuploader/Uploader.swf',
            server: 'https://up.qbox.me/',
            pick: '#posterPicker',
            fileNumLimit: 1,
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png'
            },
            formData: {
                token: DATA.uploadToken,
                key: saveKey
            }
        });
        console.log(uploader);
        // 当有文件添加进来的时候
        uploader.on('fileQueued', function(file) {
            var $list = $("#authList");
            var $li = $(
                    '<div id="' + file.id + '" class="file-item thumbnail">' +
                        '<img>' +
                        '<div class="info">' + file.name + '</div>' +
                    '</div>'
                    ),
                $img = $(".poster-img");

            $("#posterPicker").hide();
            $li.on('click', function(event) {
                $(this).remove();
                uploader.removeFile(uploader.getFile($(this).attr('id')));
                $("#posterPicker").show();
            });

            // $list为容器jQuery实例
            $list.append($li);
            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            uploader.makeThumb(file, function(error, src) {
                if (error) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }
                $img.attr( 'src', src );
            }, 100, 100);
        });

        // uploadBeforeSend
        uploader.on('uploadBeforeSend', function(obj, data, headers) {
            $("#u-tips").removeClass('hide');
            // $.extend(data, {
            //     'x:name': obj.file.__hash + String(Math.random())
            // });
        });

        // 文件上传过程中创建进度条实时显示。
        uploader.on('uploadProgress', function(file, percentage) {

        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on('uploadSuccess', function(file, response) {
            $("#poster").val(response.key);
            $("#u-tips").addClass('hide');
            $(".poster-img").show();
            layer.msg('上传成功');
        });

        // 文件上传失败，显示上传出错。
        uploader.on('uploadError', function(file, response) {
            $("#u-tips").addClass('hide');
            layer.msg('上传失败');
        });

        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on('uploadComplete', function(file) {
            $("#u-tips").addClass('hide');
        });
    }
});
