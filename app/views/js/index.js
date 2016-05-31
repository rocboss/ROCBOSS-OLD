define(function(require, exports, module) {
    var $ = require("jquery");
    var lazyload = require("lazyload")($);
    var vue = require("vue");
    var laypage = require("laypage");
    var tpl, page, sort, cid, per, pages, href, key;
    exports.init = function(obj) {
        Vue.directive('lazyload', function() {
            setTimeout(function() {
                $(".topic-avatar").lazyload({
                    placeholder : "/app/views/img/loading.gif",
                    effect: "fadeIn"
                });
            }, 100);
        })
        tpl = new Vue({
            el: '#page-wrapper',
            data: {
                sort: obj.sort,
                topics: obj.topics
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

        $(".sort-choice").on('click', function(event) {
            changeSort($(this), $(this).data('sort'));
        });

        $(".do-contriute").on('click', function(event) {
            var self = this;
            var o = $(self).html();
            $(self).html('功能建设中...');
            $(self).attr('disabled', 'disabled');
            setTimeout(function() {
                $(self).removeAttr('disabled');
                $(self).html(o);
            }, 1200);
        });

        require.async("peity", function(peity) {
            $("span.pie").peity("pie", {
                fill: ['#1ab394', '#d7d7d7', '#ffffff']
            })

            $(".line").peity("line", {
                fill: '#1ab394',
                stroke: '#169c81',
            })

            $(".bar").peity("bar", {
                fill: ["#1ab394", "#d7d7d7"]
            })

            $(".bar_dashboard").peity("bar", {
                fill: ["#1ab394", "#d7d7d7"],
                width: 100
            })

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
        href = '/search?q='+q;
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
                    var url = '/search?q='+q+'&page=' + e.curr;
                    window.location.href = url;
                }
            }
        });
    }

    function changeSort(obj, s) {
        var o = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i> 加载中...');
        $.get('/changeSort/' + cid + '/1/' + s, function(data) {
            if (data.status == 'success') {
                cid = data.data.cid;
                sort = data.data.sort;
                tpl.sort = data.data.sort;
                tpl.topics = data.data.rows;
                $(obj).html(o);
                var page = data.data.page;
                var per = data.data.per;
                var pages = Math.ceil(data.data.total / data.data.per);
                var href = data.data.cid > 0 ? '/' + data.data.cid + '/' : '/';
                url = data.data.cid > 0 ? '/' + data.data.cid + '/' + page : '/';
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
});
