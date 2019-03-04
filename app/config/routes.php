<?php
// 跨域
route('*', function () {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header('Access-Control-Allow-Headers: Content-Type,X-Authorization');
    if (app()->request()->method !== 'OPTIONS') {
        return true;
    }
});

// 输出系统版本（默认）
route('GET /', ['api\HomeController', 'index']);

// POST 维度

// 获取冒泡/文章列表
route('GET /post/list', ['api\PostController', 'list'])->auth(false);

// 获取冒泡/文章详情
route('GET /post/detail/@aliasId:\w+', ['api\PostController', 'detail'])->auth(false);
