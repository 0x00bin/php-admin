<?php

$sysConfig = include('Common/Conf/system_config.php');

$config = array(
    // 表单令牌
    'TOKEN_ON' => false,
    'TOKEN_NAME' => '__hash__',
    'TOKEN_TYPE' => 'md5',
    'TOKEN_RESET' => true,

    // 认证token
    'AUTH_TOKEN' => '*(D^#L{MFG(%@()4#*M5N()@+_$#M',
    // 认证mask
    'AUTH_MASK' => '(#NF(#JF%#9!2#J@L(#8pL*jf9J',
    // 登录超时
    'LOGIN_TIMEOUT' => 3600,

    // 不用认证登录的模块
    'NOT_LOGIN_MODULES' => 'public',

    // 开启权限认证
    'USER_AUTH_ON' => true,
    // 登录认证模式
    'USER_AUTH_TYPE' => 1,
    // 认证识别号
    'USER_AUTH_KEY' => 'user_auth_id',
    // 超级管理员认证号
    'ADMIN_AUTH_KEY' => 'superadmin',
    // 游客识别号
    'GUEST_AUTH_ID' => 'guest',
    // 模块名称（不要修改）
    'GROUP_AUTH_NAME' => 'user',
    // 无需认证模块
    'NOT_AUTH_MODULE' => 'public',
    // 无需认证操作
    'NOT_AUTH_ACTION' => 'welcome,select',
    // 需要认证模块
    'REQUIRE_AUTH_MODULE' => '',
    // 认证网关
    'USER_AUTH_GATEWAY' => 'public/index',
    // 关闭游客授权访问
    'GUEST_AUTH_ON' => false,
    // 管理员模型
    'USER_AUTH_MODEL' => 'User',
    // 角色表
    'RBAC_ROLE_TABLE'  => 'sys_role',
    // 管理员-角色表
    'RBAC_USER_TABLE'  => 'sys_role_user',
    // 节点表
    'RBAC_NODE_TABLE'  => 'sys_node',
    // 节点访问表
    'RBAC_ACCESS_TABLE' => 'sys_access',
    // 操作别名
    'ACTION_ALIAS' => array(
         'INSERT' => 'ADD',
         'UPDATE' => 'EDIT',
         'DOCMD'  => 'WEBSHELL',
         'SAVEACCESS'  => 'ASSIGNACCESS',
    ),

);

// 登录标记
$config['LOGIN_MARKED'] = md5($config['AUTH_TOKEN']);

return $config;
