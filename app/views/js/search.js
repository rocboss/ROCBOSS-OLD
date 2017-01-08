define(function(require, exports, module) {
    var bootstrap = require("bootstrap"),
        lazyload = require("lazyload")($),
        Vue = require("vue"),
        layer = require("layer"),
        peity = require('peity'),
        laypage = require("laypage"),
        common = require("js/base/common");
    var tpl, sort, cid, pages, href, key;

    layer.config({
        path: '/vendor/layer/',
        extend: 'extend/layer.ext.js'
    });

    pages = Math.ceil(total / per);
    href = '/search?q=' + q;
    laypage.dir = '/dist/css/laypage.css';
    laypage({
        dir: '/dist/css/laypage.css',
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
});
