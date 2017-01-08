$(document).ready(function() {
    // 侧边栏开关
    $(".toggle-btn").on('click', function() {
        event.preventDefault();
        $("#right-content").toggleClass("on-padding-left");
        if ($(".sidebar").css('width') == '200px') {
            $(".sidebar").animate({width:5}, "fast");
        } else {
            $(".sidebar").animate({width:200}, "fast");
        }
    });
    // 清理缓存
    $('.clear-cache').on('click', function(event) {
        event.preventDefault();
        $.post('/backend/admin/do-clear-cache', {}, function(data, textStatus, xhr) {
            if (data.status == 'success') {
                layer.msg('<i class="fa fa-check-circle"></i> 清理成功');
            }
        }, 'json');
    });
});
