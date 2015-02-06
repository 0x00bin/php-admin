<?php

return array (
    'height'  => 500,
    'caption' => '用户',
    'columns' => array( // 列表中需要的字段
        'id'         => array('label' => '编号', 'width' => 50),
        'name'       => array('label' => '名称'),
        'status'  => array('label' => '状态', 'input' => 'select', 'dictionary' => 'switchs'),
        'is_super'  => array('label' => '超级管理员', 'input' => 'select', 'dictionary' => 'yesno'),
        'created'    => array('label' => '创建日期', ),
        'last_login' => array('label' => '上次登录时间', 'input' => 'datetime'),
        'remark'     => array('label' => '备注', 'input' => 'textarea', 'placeholder' => '用户备注信息'),
    ),
    'form' => array( // 添加编辑表单需要的字段
        'password' => array(
            'input' => 'password',
            'label' => '密码',
            'placeholder'  => '编辑时不修改不需要填写',
        ),
        'cfm_password' => array(
            'input' => 'password',
            'label' => '确认密码',
            'placeholder'  => '编辑时不修改不需要填写',
        ),
    ),
    'form_sort' => 'name,password,cfm_password,status,remark,id', // 声明顺序决定表单顺序
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
    ),
    //'checkbox' => true,
);
?>

