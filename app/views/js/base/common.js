require('./../../vendor/iconfont/iconfont.css');
require('./../../vendor/bootstrap/css/bootstrap.min.css');
require('./../../css/wangEditor.css');
require('./../../css/laypage.css');
require('./../../css/github-gist.css');
require('./../../css/animate.css');

var $ = require("jquery"),
    bootstrap = require("bootstrap");

$(document).ready(function() {
    $("[data-toggle='tooltip']").tooltip();
    $("#turn-light").on("click", function(event) {
        layer.load(2);
        $.post('/turn/light', {}, function(data) {
            if (data.status == 'success') {
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            }
        }, 'json');
    });
});
