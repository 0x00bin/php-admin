<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('BIND_MODULE','Console');

// 网站文件入口位置
define('ROOT_PATH', dirname(__FILE__) . '/');

// 定义应用目录
define('APP_PATH', ROOT_PATH . 'Application/');
define('THINK_PATH', ROOT_PATH . 'ThinkPHP/');

// 应用公共目录
define('COMMON_PATH', ROOT_PATH . 'Common/');
define('LIBS_PATH', COMMON_PATH . 'Libs/');
define('TPL_PATH', COMMON_PATH . 'Tpl/default/');

// 运行缓存目录
define('RUNTIME_PATH', ROOT_PATH . 'Runtime/');

// 开启调试
define('APP_DEBUG', true);


// 引入ThinkPHP入口文件
require THINK_PATH . 'ThinkPHP.php';
