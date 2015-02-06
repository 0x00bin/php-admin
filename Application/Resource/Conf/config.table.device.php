<?php

return array (
    'height'  => 500,
    'caption' => '设备',
    'columns' => array( // 列表中需要的字段
        'id'       => array('label' => '编号', 'width' => 50),
        'name'     => array('label' => '设备名称', ),
        'type'     => array('label' => '设备类型', 'input' => 'select', 'dictionary' => 'device_types'),
        'host'     => array('label' => '主机地址', ),
        'conn_type'=> array('label' => '连接方式', 'input' => 'select', 'dictionary' => 'conn_types'),
        'port'     => array('label' => '连接端口', ),
        'user'     => array('label' => '连接用户', ),
        'pass'     => array('label' => '连接密码',  'input' => 'password', 'list' => 'null', 'placeholder' => '编辑时不修改不需要填写'),

        'su_user'     => array('label' => 'SU用户', ),
        'su_pass'     => array('label' => 'SU密码',  'input' => 'password', 'list' => 'null', 'placeholder' => '编辑时不修改不需要填写'),

      //  'parent_id'  => array('label' => '父设备', 'input' => 'select', 'dictionary' => 'devices'),
        'user_id'  => array('label' => '负责人', 'input' => 'select', 'dictionary' => 'users'),
        'remark'   => array('label' => '设备描述', 'input' => 'textarea', 'list' => 'null', 'placeholder' => '注释信息'),
        'created'  => array('label' => '创建日期', 'input' => 'null', ),
        'updated'  => array('label' => '更新日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit' => '编辑',
        'delete'  => '删除',
        'test' => array('label' => '测试', 'cols' => 'id', 'oper' => 'doact_ajax' ),
       // 'add' => array('label' => '添加子设备', 'cols' => 'id:parent_id', 'oper' => 'doact', 'param' => '_set=1', "condition" => "parent_id == 0"),
        //'index' => array('label' => '查看子设备', 'cols' => 'id:parent_id', 'oper' => 'doact', 'param' => '_search=1', "condition" => "parent_id == 0"),
    ),

    //'checkbox' => true,
);
?>

