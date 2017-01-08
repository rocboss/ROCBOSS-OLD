<?php
/**
 * System infrastructure configuration
 */
return [
    // HTTPS
    'system.secure' => false,

    // Debug mode
    'system.handle_errors' => false,

    // Webpack Debug
    'system.webpack_debug' => false,

    // Controller path
    'system.controller.path' => '../app/controllers',

    // Service path
    'system.service.path' => '../app/services',

    // Model path
    'system.model.path' => '../app/models',

    // Libs path
    'system.libs.path' => '../app/libs',

    // View path
    'system.views.path' => '../app/views',

    // View cache path
    'system.views.cache' => '../app/cache',

    // View cache expire time, Unit: Second
    'system.views.cacheTime' => 0,

    // Whisper score 私信积分设置
    'system.score.whisper' => 10,

    # System register switch 网站注册开关
    'system.register.switch' => true,

    // API Domain API接口地址
    'api.domain' => 'my.api.com',
];
