define(function(require, exports, module) {
    var bootstrap = require("bootstrap"),
        lazyload = require("lazyload")($),
        Vue = require("vue"),
        layer = require("layer"),
        common = require("js/base/common"),
        WebUploader = require("webuploader"),
        _csrf = $('meta[name=_csrf]').attr('content');

    layer.config({
        path: '/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });

    var DATA = {
        uploadToken: ''
    }
    exports.init = function(config) {
        var hash = document.location.hash;
        var hasHash = hash.replace("#", "") != '';

        var uid = config.uid,
            topics = config.topics,
            page = 1;

        Vue.directive('lazyload', function() {
            setTimeout(function() {
                $(".u-avatar").lazyload({
                    placeholder: "/dist/img/loading.gif",
                    effect: "fadeIn"
                });
            }, 100);
        });
        var tpl = new Vue({
            el: '#rocboss-app',
            data: {
                topics: topics,
                replys: [],
                articles: [],
                fans: [],
                follows: [],
                collections: [],
                nowTab: hasHash ? hash.replace("#", "") : 'topics'
            },
            methods: {
                changeTab: function(e, t) {
                    // e.preventDefault();
                    var o = $(e.target).html();
                    $(e.target).attr('disabled', 'disabled');
                    $(e.target).html('<i class="fa fa-spinner fa-spin"></i>');
                    $.get('/user/' + uid + '/change-tab/' + t, function(data) {
                        if (data.status == 'success') {
                            if (t == 'replys') {tpl.replys = data.data.rows;}
                            if (t == 'topics') {tpl.topics = data.data.rows;}
                            if (t == 'articles') {tpl.articles = data.data.rows;}
                            if (t == 'fans') {tpl.fans = data.data.rows;}
                            if (t == 'follows') {tpl.follows = data.data.rows;}
                            if (t == 'collections') {tpl.collections = data.data.rows;}
                            // tpl.t = data.data.rows;
                            tpl.nowTab = t;
                        }
                        $(e.target).html(o);
                        $(e.target).removeAttr('disabled');
                    }, 'json')
                },
                loadMoreTopic: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/topic/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.topics = tpl.topics.concat(data.data);
                            if (data.data.length != 0) {
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
                loadMoreReply: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/reply/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.replys = tpl.replys.concat(data.data);
                            if (data.data.length != 0) {
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
                loadMoreArticle: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/article/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.articles = tpl.articles.concat(data.data);
                            if (data.data.length != 0) {
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
                loadMoreFans: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/fans/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.fans = tpl.fans.concat(data.data);
                            if (data.data.length != 0) {
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
                loadMoreFollows: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/follows/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.follows = tpl.follows.concat(data.data);
                            if (data.data.length != 0) {
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
                loadMoreFollows: function(e) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/collection/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.collections = tpl.collections.concat(data.data);
                            if (data.data.length != 0) {
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
                doFollow: function(e, uid) {
                    var that = e.target;
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i>');
                    $.post('/do/follow/', {
                        fuid: uid
                    }, function(data) {
                        if (data.status == 'success') {
                            if (data.data == '1') {
                                $(that).html('<i class="fa fa-heart margin-r-5"></i> 取消关注');
                            } else {
                                $(that).html('<i class="fa fa-heart-o margin-r-5"></i> 关注TA');
                            }
                        } else {
                            $(that).html('<i class="fa fa-heart-o margin-r-5"></i> 关注TA');
                            layer.msg(data.data);
                        }
                        $(that).removeAttr('disabled');
                    }, 'json');
                },
                // 转账
                doTransfer: function(e, uid) {
                    var that = e.target;
                    layer.prompt({
                        title: '请输入转账积分数',
                        formType: 0,
                    }, function(score){
                        score = $.trim(score);
                        if (score > 0 && score <= 1000) {
                            $.post('/do/transfer', {
                                uid: uid,
                                score: score,
                                _csrf: _csrf,
                            }, function(data) {
                                if (data.status == 'success') {
                                    layer.msg(data.data, {icon: 1});
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    layer.msg(data.data, {icon: 2});
                                }
                            }, 'json');
                        } else {
                            layer.msg('单次转账积分范围 1~1000', {icon: 2});
                        }
                    }, '请输入整数积分，范围 1~1000');
                },
                doWhisper: function(e, at_uid, score) {
                    var that = e.target;
                    layer.open({
                        type: 1,
                        area : ['300px' , 'auto'],
                        title: false,
                        closeBtn: true,
                        shadeClose: false,
                        content: '<div class="form-group" style="padding: 20px 10px; text-align: center;">'+
                                '<div class="col-sm-12">'+
                                    '<textarea id="whisper-msg" class="form-control" placeholder="请输入私信内容，不超过200字" rows="5"></textarea>'+
                                    '<a class="deliver-whisper btn btn-primary btn-sm btn-block" data-at_uid="'+at_uid+'" style="margin: 5px 0 10px 0;"><i class="fa fa-envelope"></i> 发送私信</a>'+
                                    '<small class="help-block m-b-none">若对方设置了手机号，将同时收到短信提醒</small>'+
                                    '<small class="help-block m-b-none">本次私信将消耗 <b>'+score+'</b> 积分</small>'+
                                '</div>'+
                            '</div>',
                        success: function() {
                            $(".deliver-whisper").on('click', function(event) {
                                var content = tpl.removeHTMLTag($("#whisper-msg").val());
                                if (content.length < 2) {
                                    layer.msg('私信内容应不少于两个字符', {icon: 2});
                                    return;
                                } else {
                                    var that = this;
                                    $(that).attr('disabled', 'disabled');
                                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 发送中...');
                                    $.post('/deliver/whisper/', {
                                        content: content,
                                        at_uid: $(that).data('at_uid')
                                    }, function(data) {
                                        if (data.status == 'success') {
                                            layer.msg(data.data, {icon: 1});
                                            setTimeout(function() {
                                                layer.closeAll();
                                            }, 1200);
                                        } else {
                                            layer.msg(data.data, {icon: 2});
                                        }
                                        $(that).removeAttr('disabled');
                                    }, 'json');
                                }
                            });
                        }
                    });
                },
                doRecharge: function(e) {
                    layer.prompt({
                        title: '请输入充值金额（1元 = 100积分）',
                        formType: 0,
                    }, function(money){
                        money = $.trim(money);
                        if (money > 0 && money <= 1000) {
                            $.get('/recharge/'+money, function(data) {
                                if (data.code == 10000) {
                                    layer.msg('支付请求中', {icon: 16});
                                    setTimeout(function() {
                                        $('body').append(data.data);
                                    }, 800);
                                } else {
                                    layer.msg(data.data, {icon: 2});
                                }
                            }, 'json');
                        } else {
                            layer.msg('单次充值人民币范围 1~1000', {icon: 2});
                        }
                    }, '请输入人民币整数金额');
                    $(".recharge-input").keyup(function(){
                        var c=$(this);
                        if(/[^\d]/.test(c.val())) {
                          var temp_amount=c.val().replace(/[^\d]/g,'');
                          $(this).val(temp_amount);
                        }
                    });
                },
                doWithdraw: function(e) {
                    if (config.phone == '') {
                        layer.msg('请先设置手机号（支付宝账户）');
                        return;
                    }
                    layer.prompt({
                        title: '请输入需要兑换的积分， 100积分=1元',
                        formType: 0
                    }, function(num){
                        if (num >= 1000 && num <= 10000) {
                            if (num > parseInt(config.score)) {
                                layer.msg('积分余额不足');
                            } else {
                                layer.confirm('确定提现 <b>'+num+'</b> 积分吗<br />100积分 = 1元<br />需扣除200积分<br /><b>实际到账 '+parseFloat((num - 200) / 100).toFixed(2)+' 元</b><br /><b>提现支付宝账户：'+config.phone+'</b>', {
                                    title: '提现提示',
                                    icon: 7,
                                    btn: ['确定', '取消']
                                }, function() {
                                    $.post('/do/withdraw', {
                                        num: num,
                                        _csrf: _csrf,
                                    }, function(data) {
                                        if (data.status == 'success') {
                                            layer.msg(data.data, {icon: 1});
                                            setTimeout(function() {
                                                layer.closeAll();
                                                window.location.href = '/scores';
                                            }, 1000);
                                        } else {
                                            layer.msg(data.data, {icon: 2});
                                        }
                                    }, 'json');
                                }, function() {
                                });
                            }
                        } else {
                            layer.msg('提现积分范围 1000~10000');
                        }
                    }, '单次提现积分范围 1000~10000');
                    $(".recharge-input").keyup(function(){
                        var c=$(this);
                        if(/[^\d]/.test(c.val())) {
                          var temp_amount=c.val().replace(/[^\d]/g,'');
                          $(this).val(temp_amount);
                        }
                    });
                },
                doUpgrade: function(e, v2p, v3p) {
                    layer.confirm('请点击选择所需升级的VIP。<br />V2所需积分：'+v2p+'<br />V3所需积分：'+v3p, {
                        title: '升级提示',
                        icon: 7,
                        btn: ['VIP2', 'VIP3', '取消'],
                        btn1: function(index, layero) {
                            $.post('/upgrade/vip/2', {}, function(data, textStatus, xhr) {
                                if (data.status == 'success') {
                                    layer.msg(data.data, {icon: 1});
                                    setTimeout(function() {
                                        window.location.href = "/login";
                                    }, 1500);
                                } else {
                                    layer.msg(data.data, {icon: 2});
                                }
                            }, 'json');
                        },
                        btn2: function(index, layero) {
                            $.post('/upgrade/vip/3', {}, function(data, textStatus, xhr) {
                                if (data.status == 'success') {
                                    setTimeout(function() {
                                        window.location.href = "/login";
                                    }, 1500);
                                    layer.msg(data.data, {icon: 1});
                                } else {
                                    layer.msg(data.data, {icon: 2});
                                }
                            }, 'json');
                        },
                        btn3: function(index, layero) {
                            layer.close(index);
                        }
                    });
                },
                removeHTMLTag: function(str) {
                    str = str.replace(/<\/?[^>]*>/g,'');
                    str = str.replace(/[ | ]*\n/g,'\n');
                    str=str.replace(/&nbsp;/ig,'');
                    return str;
                }
            }
        });
        if (hasHash) {
            tpl.changeTab(this, hash.replace("#", ""));
        }
    }
    exports.setting = function(uploadToken, saveKey) {
        // ### 头像上传
        // 初始化Web Uploader
        DATA.uploadToken = uploadToken;
        var uploader = WebUploader.create({
            auto: true,
            swf: '/app/_static/js/vendor/webuploader/Uploader.swf',
            server: 'https://up.qbox.me/',
            pick: '#avatarPicker',
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
        // 当有文件添加进来的时候
        uploader.on('fileQueued', function(file) {
            var $list = $("#authList");
            var $li = $(
                    '<div id="' + file.id + '" class="file-item thumbnail">' +
                        '<img>' +
                        '<div class="info">' + file.name + '</div>' +
                    '</div>'
                    ),
                $img = $(".my-avatar");

            $("#avatarPicker").hide();
            $li.on('click', function(event) {
                $(this).remove();
                uploader.removeFile(uploader.getFile($(this).attr('id')));
                $("#avatarPicker").show();
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
            // $("#id_card_pic").val(response.key);
            $("#u-tips").addClass('hide');
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
        $(".save-profile").on('click', function(event) {
            var password = $.trim($("#new-password").val());
            var repassword = $.trim($("#re-password").val());

            if (password != '') {
                if (password.length < 8) {
                    layer.msg('密码长度不能少于八位', {icon: 2});
                    $('input[name=new-password]').focus();
                    return false;
                } else if (password != repassword) {
                    layer.msg('两次密码不一致', {icon: 2});
                    $('input[name=new-password]').focus();
                    return false;
                }
            }

            var that = this;
            var o = $(that).html();
            $(that).attr('disabled', 'disabled');
            $(that).html('<i class="fa fa-spinner fa-spin"></i> 保存中...');
            $.post('/save/profile/', {
                uid: $(that).data('uid'),
                email: $.trim($('#email').val()),
                phone: $.trim($('#phone').val()),
                password: password
            }, function(data) {
                if (data.status == 'success') {
                    layer.msg(data.data, {icon: 1});
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    layer.msg(data.data, {icon: 2});
                }
                $(that).removeAttr('disabled');
                $(that).html(o);
            }, 'json');
        });
    }
    exports.scores = function(config) {
        var scores = config.scores;
        var tpl = new Vue({
            el: '#rocboss-app',
            data: {
                scores: scores,
            }
        });
        $(document).ready(function() {
            $("#scores-info").attr('style', 'display: block;');
        });
    }
    exports.chat = function(config) {
        $(document).ready(function() {
            $("#end-whisper")[0].scrollIntoView(false);
        });
        var data = config.data,
            page = 1;
        var tpl = new Vue({
            el: '#rocboss-app',
            data: {
                data: data,
            },
            methods: {
                loadMoreWhisper: function(e, uid) {
                    e.preventDefault();
                    page ++;
                    var that = e.target;
                    var o = $(that).html();
                    $(that).attr('disabled', 'disabled');
                    $(that).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $.get('/user/whisper/'+uid+'/'+page, function(data) {
                        $(that).html(o);
                        if (data.status == 'success') {
                            tpl.data.rows = data.data.rows.concat(tpl.data.rows);
                            if (data.data.rows.length != 0) {
                                $(that).removeAttr('disabled');
                            } else {
                                $(that).html('已加载全部');
                            }
                        } else {
                            layer.msg(data.data, {icon: 2});
                            $(that).removeAttr('disabled');
                        }
                    }, 'json');
                },
                postWhisper: function(e) {
                    var content = tpl.removeHTMLTag($("#whisper-msg").val());
                    if (content.length < 2) {
                        layer.msg('私信内容应不少于两个字符', {icon: 2});
                        return;
                    } else {
                        var that = e.target;
                        $(that).attr('disabled', 'disabled');
                        $(that).html('<i class="fa fa-spinner fa-spin"></i> 发送中...');
                        $.post('/deliver/whisper/', {
                            content: content,
                            at_uid: $(that).data('at_uid')
                        }, function(data) {
                            if (data.status == 'success') {
                                layer.msg(data.data, {icon: 1});
                                setTimeout(function() {
                                    layer.closeAll();
                                    window.location.reload();
                                }, 1200);
                            } else {
                                layer.msg(data.data, {icon: 2});
                            }
                            $(that).removeAttr('disabled');
                        }, 'json');
                    }
                },
                removeHTMLTag: function(str) {
                    str = str.replace(/<\/?[^>]*>/g,'');
                    str = str.replace(/[ | ]*\n/g,'\n');
                    str=str.replace(/&nbsp;/ig,'');
                    return str;
                }
            }
        });
    }
});
