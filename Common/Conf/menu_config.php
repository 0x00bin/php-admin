<?php

// 菜单项配置
$systemMenu = array(
    // 后台首页
    'Index' => array(
        'name' => '首页',
        'target' => 'Index/index',
        'sub_menu' => array(
            array('item' => array('Index/index' => '系统信息')),
            array('item' => array('Index/editPassword' => '修改密码')),
        )
    ),

    // 缓存管理
    'Cache' => array(
        'name' => '缓存管理',
        'target' => 'Cache/index',
        'mapping' => 'Index',
        'sub_menu' => array(
            array('item' => array('Cache/index' => '缓存列表'))
        )
    ),

    // 数据管理
    'User' => array(
        'name' => '用户权限',
        'target' => 'User/index',
        'sub_menu' => array(
            array('item' => array('User/index' => '用户信息')),
            array('item' => array('Role/index' => '角色管理')),
            array('item' => array('Node/index' => '节点管理')),
            array('item' => array('User/add' => '添加用户')),
            array('item' => array('Role/add' => '添加角色')),
            array('item' => array('User/edit'=>'编辑用户信息'),'hidden'=>true),
            array('item' => array('Role/edit'=>'编辑角色信息'),'hidden'=>true)
        )
    ),

    // 角色管理
    'Role' => array(
        'name' => '角色管理',
        'target' => 'Role/index',
        'mapping' => 'User',
        'sub_menu' => array(
            array('item' => array('Role/index' => '角色列表')),
            array('item' => array('Role/add' => '添加角色')),
            array('item' => array('Role/edit' => '编辑角色信息'),'hidden'=>true),
            array('item' => array('Role/assignAccess' => '分配权限'),
                  'hidden'=> true )
        )
    ),

    // 节点管理
    'Node' => array(
        'name' => '节点管理',
        'target' => 'Node/index',
        'mapping' => 'User',
        'sub_menu' => array(
            array('item' => array('Node/index' => '节点列表'))
        )
    ),
);

return $systemMenu;
