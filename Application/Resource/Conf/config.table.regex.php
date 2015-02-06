<?php

return array (
    'height'  => 500,
    'caption' => '正则',
    'columns' => array( // 列表中需要的字段
        'id'       => array('label' => '编号',  'width' => 50),
        'command_id'  => array('label' => '指令名称',  'input' => 'hidden', 'dictionary' => 'commands', ),
        'regex' => array('label' => '正则表达式', 'size' => 60,  'placeholder' => '数据库查询结果不需要正则'),
        'expression'  => array('label' => '结果表达式', 'size' => 60),
        'tips'  => array('label' => '匹配提示', 'size' => 60),
        'created'  => array('label' => '创建日期', 'input' => 'null', ),
        'updated'  => array('label' => '更新日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
       // 'manage'  => array('label' => '管理指令', 'cols' => 'id', 'oper' => 'doact',),
    ),

   // 'checkbox' => true,
);
?>

