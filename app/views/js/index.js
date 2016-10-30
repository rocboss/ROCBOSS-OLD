define(function(require, exports, module) {
    var $ = require("jquery"),
        bootstrap = require("bootstrap"),
        lazyload = require("lazyload")($),
        vue = require("vue"),
        layer = require("layer"),
        laypage = require("laypage"),
        common = require("js/common");
    var tpl, page, sort, cid, per, pages, href, key;

    layer.config({
        path: '/app/views/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });
    common.ready();
    // Index Init.
    exports.init = function(obj) {
        Vue.directive('lazyload', function() {
            setTimeout(function() {
                $(".topic-avatar").lazyload({
                    placeholder: "/app/views/img/loading.gif",
                    effect: "fadeIn"
                });
            }, 100);
        });
        tpl = new Vue({
            el: '#rocboss-app',
            data: {
                sort: obj.sort,
                topics: obj.topics
            },
            methods: {
                // 更改排列顺序
                changeSort: function(e, s) {
                    e.preventDefault();
                    var o = $(e.target).html();
                    $(e.target).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
                    $(e.target).attr('disabled', 'disabled');
                    $.get('/changeSort/' + cid + '/1/' + s, function(data) {
                        if (data.status == 'success') {
                            cid = data.data.cid;
                            sort = data.data.sort;
                            tpl.sort = data.data.sort;
                            tpl.topics = data.data.rows;

                            setTimeout(function() {
                                $(e.target).removeAttr('disabled');
                            }, 200);
                            $(e.target).html(o);

                            var page = data.data.page;
                            var per = data.data.per;
                            var pages = Math.ceil(data.data.total / data.data.per);
                            var href = data.data.cid > 0 ? '/' + data.data.cid + '/' : '/';
                            url = data.data.cid > 0 ? '/' + data.data.cid + '/' + page : '/topic';
                            window.history.pushState({
                                url: url
                            }, null, url);
                            laypage({
                                cont: 'pagination',
                                pages: pages,
                                curr: 1,
                                href: href + '(?)',
                                first: 1,
                                last: pages,
                                skin: 'molv',
                                prev: '<',
                                next: '>',
                                jump: function(e, first) {
                                    if (!first) {
                                        var url = cid > 0 ? '/' + cid + '/' + e.curr : '/' + e.curr;
                                        var state = {
                                            url: url,
                                            page: e.curr,
                                        };
                                        window.history.pushState(state, null, url);
                                        scroll(0, 0);
                                        $("#requesting").show();
                                        $.get('/changeSort/' + data.data.cid + '/' + e.curr + '/' + sort, function(data) {
                                            if (data.status == 'success') {
                                                cid = data.data.cid;
                                                sort = data.data.sort;
                                                page = e.curr;
                                                tpl.sort = data.data.sort;
                                                tpl.topics = data.data.rows;
                                                $("#requesting").hide()
                                            }
                                        }, 'json')
                                    }
                                }
                            })
                        }
                    }, 'json')
                }
            }
        });
        page = obj.page;
        sort = obj.sort;
        cid = obj.cid;
        per = obj.per;
        pages = Math.ceil(obj.total / per);
        href = obj.cid > 0 ? '/' + obj.cid + '/' : '/';
        laypage.dir = '/app/views/css/laypage.css';
        laypage({
            dir: '/app/views/css/laypage.css',
            cont: 'pagination',
            pages: pages,
            curr: page,
            href: href + '(?)',
            first: 1,
            last: pages,
            skin: 'molv',
            prev: '<',
            next: '>',
            jump: function(e, first) {
                if (!first) {
                    var url = cid > 0 ? '/' + cid + '/' + e.curr : '/' + e.curr;
                    var state = {
                        url: url,
                        page: e.curr,
                    };
                    window.history.pushState(state, null, url);
                    scroll(0, 0);
                    $("#requesting").show();
                    $.get('/changeSort/' + cid + '/' + e.curr + '/' + sort, function(data) {
                        if (data.status == 'success') {
                            cid = data.data.cid;
                            sort = data.data.sort;
                            page = e.curr;
                            tpl.sort = data.data.sort;
                            tpl.topics = data.data.rows;
                            $("#requesting").hide()
                        }
                    }, 'json')
                }
            }
        });
        window.addEventListener('popstate', function(e) {
            if (history.state) {
                window.location.href = e.state.url
            }
        }, false);
        require.async("peity", function(peity) {
            $("span.pie").peity("pie", {
                fill: ['#1ab394', '#d7d7d7', '#ffffff']
            });
            $(".line").peity("line", {
                fill: '#1ab394',
                stroke: '#169c81',
            });
            $(".bar").peity("bar", {
                fill: ["#1ab394", "#d7d7d7"]
            });
            $(".bar_dashboard").peity("bar", {
                fill: ["#1ab394", "#d7d7d7"],
                width: 100
            });
            var updatingChart = $(".updating-chart").peity("line", {
                fill: '#1ab394',
                stroke: '#169c81',
                width: 64
            })
            setInterval(function() {
                var random = Math.round(Math.random() * 10)
                var values = updatingChart.text().split(",")
                values.shift()
                values.push(random)
                updatingChart.text(values.join(",")).change()
            }, 1000);
        });
    }
    // 搜索
    exports.search = function(page, per, total, q) {
        pages = Math.ceil(total / per);
        console.log(pages)
        href = '/search?q=' + q;
        laypage.dir = '/app/views/css/laypage.css';
        laypage({
            dir: '/app/views/css/laypage.css',
            cont: 'pagination',
            pages: pages,
            curr: page,
            href: href + '(?)',
            first: 1,
            last: pages,
            skin: 'molv',
            prev: '<',
            next: '>',
            jump: function(e, first) {
                if (!first) {
                    var url = '/search?q=' + q + '&page=' + e.curr;
                    window.location.href = url;
                }
            }
        });
    }
});
