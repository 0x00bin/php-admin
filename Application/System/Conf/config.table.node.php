<?php

return array (
    'height'  => 500,
    'caption' => '节点',
    'columns' => array( // 列表中需要的字段
        'id'         => array('label' => '编号', 'width' => 50),
        'name'       => array('label' => '名称'),
        'title'      => array('label' => '标题'),
        'pid'        => array('label' => '父节点',   'input' => 'hidden'),
        'level'      => array('label' => '节点等级', 'input' => 'hidden'),
        'status'     => array('label' => '状态',     'input' => 'select', 'dictionary' => 'switchs'),
        'created'    => array('label' => '创建日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit' => '编辑',
        'delete'  => '删除',
        'add'  => array('label' => '添加子节点',  'cols' => 'id:pid,level', 'param' => '', 'oper' => 'doact', "condition" => "level <= 2"),
        'index' => array('label' => '查看子节点', 'cols' => 'id:pid', 'oper' => 'doact', "condition" => "level <= 2" ) //condition col oper num 注意空格必须
    ),

   // 'checkbox' => true,
);
?>

