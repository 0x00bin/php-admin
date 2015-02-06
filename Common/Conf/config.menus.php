<?php

$sub_menus = array (
    'system' => array(
        array('item' => array('system/user/index' => '用户列表')),
        array('item' => array('system/user/add' => '添加用户')),
        array('item' => array('system/user/edit'=>'编辑用户'), 'hidden' => true),

        array('item' => array('system/role/index' => '角色列表')),
        array('item' => array('system/role/add' => '添加角色')),
        array('item' => array('system/role/edit' => '编辑角色'),'hidden'=> true),
        array('item' => array('system/role/user' => '角色用户'), 'hidden'=> true ),
        array('item' => array('system/role/assignaccess' => '分配权限'), 'hidden'=> true ),
        array('item' => array('system/role/manage'  => '管理指令'),'hidden'=> true),

        array('item' => array('system/node/index' => '节点列表') ),
        array('item' => array('system/node/add'   => '添加节点')),
        array('item' => array('system/node/edit'  => '编辑节点'),'hidden'=> true),
    ),

    'resource' => array(
        //array('item' => array('resource/index/index' => '资源管理')),
        array('item' => array('resource/device/index' => '设备列表')),
        array('item' => array('resource/device/add'   => '添加设备')),
        array('item' => array('resource/device/import'   => '导入设备')),
        array('item' => array('resource/device/edit'  => '编辑设备'),'hidden'=> true),

        array('item' => array('resource/group/index' => '设备组列表')),
        array('item' => array('resource/group/add'   => '添加设备组')),
        array('item' => array('resource/group/edit'  => '编辑设备组'),'hidden'=> true),
        array('item' => array('resource/group/manage'  => '管理设备'),'hidden'=> true),

        array('item' => array('resource/command/index'  => '指令列表')),
        array('item' => array('resource/command/add'    => '添加指令')),
        array('item' => array('resource/command/edit'   => '编辑指令'),'hidden'=> true),


        array('item' => array('resource/regex/index'  => '正则列表')),
        array('item' => array('resource/regex/add'    => '添加正则'),'hidden'=> true),
        array('item' => array('resource/regex/edit'   => '编辑正则'),'hidden'=> true),

        array('item' => array('resource/set/index' => '指令集列表')),
        array('item' => array('resource/set/add'   => '添加指令集')),
        array('item' => array('resource/set/edit'  => '编辑指令集'),'hidden'=> true),
        array('item' => array('resource/set/manage'  => '管理指令'),'hidden'=> true),
    ),

    'inspect' =>  array(
        //array('item' => array('inspect/index/index' => '巡检管理')),
        array('item' => array('inspect/index/webshell' => 'webshell')),

        array('item' => array('inspect/task/index' => '任务列表')),
        array('item' => array('inspect/task/add'   => '添加任务')),
        array('item' => array('inspect/task/edit'  => '编辑任务'), 'hidden' => true),

        array('item' => array('inspect/result/index' => '结果列表')),
    ),
);

// 菜单项配置
$menus = array(
    // 后台首页
    'system_index' => array(
        'name' => '首页',
        'target' => 'system/index/index',
        'sub_menu' => array(
            array('item' => array('system/index/index' => '系统信息')),
            array('item' => array('system/index/edit' => '修改密码')),
        )
    ),

    // 数据管理
    'system_user' => array(
        'name' => '系统管理',
        'target' => 'system/user/welcome',
        'sub_menu' => $sub_menus['system'],
    ),

    // 角色管理
    'system_role' => array(
        'name' => '系统管理',
        'target' => 'system/role/index',
        'mapping' => 'system_user',
        'sub_menu' => $sub_menus['system'],
    ),

    // 节点管理
    'system_node' => array(
        'name' => '系统管理',
        'target' => 'system/node/index',
        'mapping' => 'system_user',
        'sub_menu' => $sub_menus['system'],
    ),


    'resource_device' => array(
        'name' => '资源管理',
        'target' => 'resource/device/welcome',
        'sub_menu' => $sub_menus['resource'],
    ),

    'resource_group' => array(
        'name' => '设备组管理',
        'target' => 'resource/group/index',
        'mapping' => 'resource_device',
        'sub_menu' => $sub_menus['resource'],
    ),

    'resource_set' => array(
        'name' => '指令集管理',
        'target' => 'resource/set/index',
        'mapping' => 'resource_device',
        'sub_menu' => $sub_menus['resource'],
    ),

    'resource_command' => array(
        'name' => '脚本管理',
        'target' => 'resource/command/index',
         'mapping' => 'resource_device',
        'sub_menu' => $sub_menus['resource'],
    ),

    'resource_regex' => array(
        'name' => '正则管理',
        'target' => 'resource/regex/index',
        'mapping' => 'resource_device',
        'sub_menu' => $sub_menus['resource'],
    ),

    'inspect_index' => array(
        'name' => '巡检管理',
        'target' => 'inspect/index/welcome',
        'sub_menu' => $sub_menus['inspect'],
    ),

    'inspect_task' => array(
        'name' => '任务管理',
        'target' => 'inspect/index/index',
        'mapping' => 'inspect_index',
        'sub_menu' => $sub_menus['inspect'],
    ),

    'inspect_result' => array(
        'name' => '结果管理',
        'target' => 'inspect/index/index',
        'mapping' => 'inspect_index',
        'sub_menu' => $sub_menus['inspect'],
    ),

);

return $menus;
