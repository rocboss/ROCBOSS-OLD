define(function(require, exports, module) {
    var bootstrap = require("bootstrap"),
        lazyload = require("lazyload")($),
        Vue = require("vue"),
        layer = require("layer"),
        peity = require('peity'),
        laypage = require("laypage"),
        common = require("js/base/common");
    var tpl, page, sort, cid, per, pages, href, key;

    layer.config({
        path: '/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });

    Vue.directive('lazyload', function() {
        setTimeout(function() {
            $(".topic-avatar").lazyload({
                placeholder: "./../img/loading.gif",
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
                        var href = data.data.cid > 0 ? '/category-' + data.data.cid + '-1.html' : '/';
                        var url = data.data.cid > 0 ? '/category-' + data.data.cid + '-' + page + '.html' : '/';
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
                                    var url = cid > 0 ? '/category-' + cid + '-' + e.curr + '.html' : '/page-' + e.curr + '.html';
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
    href = obj.cid > 0 ? '/category-' + obj.cid + '-(?).html' : '/page-(?).html';
    laypage.dir = '/dist/css/laypage.css';
    laypage({
        dir: '/dist/css/laypage.css',
        cont: 'pagination',
        pages: pages,
        curr: page,
        href: href,
        first: 1,
        last: pages,
        skin: 'molv',
        prev: '<',
        next: '>',
        jump: function(e, first) {
            if (!first) {
                var url = cid > 0 ? '/category-' + cid + '-' + e.curr +'.html' : '/page-' + e.curr + '.html';
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
