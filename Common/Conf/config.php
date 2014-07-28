<?php
$systemConfig = include('Common/Conf/system_config.php');
$menuConfig = include('menu_config.php');
$securityConfig = include('security_config.php');

$appConfig =  array(
    // 调试页
    // 'SHOW_PAGE_TRACE' =>true,

    // 默认模块和Action
    'MODULE_ALLOW_LIST' => array('System', 'Inspect'),
    'DEFAULT_MODULE' => 'System',

    // 默认控制器
    'DEFAULT_CONTROLLER' => 'Public',

    'TAGLIB_PRE_LOAD'    => 'html',
    // 分页列表数
    'PAGE_LIST_ROWS' => 10,
    'CONTROLLER_LEVEL'      =>  1,
    // 开启布局
    'LAYOUT_ON' => true,
    'LAYOUT_NAME' => 'Common/layout',

    // error，success跳转页面
    'TMPL_ACTION_ERROR' => 'Common:dispatch_jump',
    'TMPL_ACTION_SUCCESS' => 'Common:dispatch_jump',

    // 菜单项配置
    'MENU' => $menuConfig,

    // 文件上传根目录
    'UPLOAD_ROOT' =>  'Public/uploads/',
    // 系统公用配置目录
    'COMMON_CONF_PATH' => ROOT_PATH . 'Common/Conf/'
);

return array_merge($appConfig, $systemConfig, $securityConfig);
