<?php

return array (
    'height'  => 500,
    'caption' => '角色',
    'columns' => array( // 列表中需要的字段
        'id'         => array('label' => '编号', 'width' => 50),
        'name'       => array('label' => '名称'),
        'pid'        => array('label' => '父角色', 'input' => 'select', 'dictionary' => 'roles'),
        'status'     => array('label' => '状态', 'input' => 'select', 'dictionary' => 'switchs'),
        'created'    => array('label' => '创建日期', 'input' => 'null'),
        'remark'     => array('label' => '备注', 'input' => 'textarea', 'placeholder' => '备注信息'),
    ),
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
        'manage'  => array('label' => '管理用户', 'cols' => 'id', 'oper' => 'doact',),
        'assignAccess' => array('label' => '分配权限', 'cols' => 'id', 'oper' => 'doact' ),
    ),

    //'checkbox' => true,
);
?>

