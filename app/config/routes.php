<?php

route('GET /', ['api\HomeController', 'index']);

route('GET /test', ['api\HomeController', 'test']);

route('GET /user', ['api\HomeController', 'user'])->auth();
