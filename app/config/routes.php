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

/***** Group 集合 *****/

// 获取 Group 列表
route('GET /groups', ['api\GroupController', 'list']);

// 获取 Group 下 Posts列表
route('GET /group/@groupId:\d+/_posts', ['api\GroupController', 'posts']);

/***** Post 集合 *****/

// 获取冒泡/文章列表
route('GET /posts', ['api\PostController', 'list'])->auth(false);

// 获取冒泡/文章详情
route('GET /post/detail/@aliasId:\w+', ['api\PostController', 'detail'])->auth(false);
