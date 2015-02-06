<?php
$menus    = include('config.menus.php');
$security = include('config.security.php');

$config =  array(
    // 调试页
    'SHOW_PAGE_TRACE' => false,
    'SITE_TITLE' => '测试网站',

    // 默认模块和Action
    'MODULE_ALLOW_LIST' => array('System', 'Inspect'),
    'DEFAULT_MODULE' => 'System',

    // 默认控制器
    'DEFAULT_CONTROLLER' => 'Index',

    'TAGLIB_PRE_LOAD'    => 'html',

    // 分页列表数
    'PAGE_LIST_ROWS' => 10,
    'CONTROLLER_LEVEL'      =>  1,
    'PAGE_VAR' => 'p',

    'PRF_MODE'   => false, // Partial refresh mode
    // 开启布局
    'LAYOUT_ON'   => true,
    'LAYOUT_NAME' => 'layout',

    // error，success跳转页面
    'TMPL_ACTION_ERROR'   => 'dispatch_jump.html',
    'TMPL_ACTION_SUCCESS' => 'dispatch_jump.html',

    // 菜单项配置
    'MENU' => $menus,

    // 文件上传根目录
    'UPLOAD_ROOT' =>  'Public/uploads/',
    // 系统公用配置目录
    'COMMON_CONF_PATH' => ROOT_PATH . 'Common/Conf/',

   'AUTOLOAD_NAMESPACE' => array(
        'Libs'     => ROOT_PATH . 'Common/Libs/',
        'Extend'   => ROOT_PATH . 'Common/Tpl/',
    ),

    // 数据库配置
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'easy',
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'sys_',
   // 'DB_DSN' => 'mysql:host=localhost;dbname=easy;charset=utf8' // for pdo
);

return array_merge($config, $security);
