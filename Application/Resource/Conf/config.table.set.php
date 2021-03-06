<?php

return array (
    'height'  => 500,
    'caption' => '指令集',
    'columns' => array( // 列表中需要的字段
        'id'       => array('label' => '编号', 'width' => 50),
        'name'     => array('label' => '指令集名称', ),
        'platform' => array('label' => '平台类型', 'input' => 'select', 'dictionary' => 'platforms'),
        'special'  => array('label' => '专业方向', 'input' => 'select', 'dictionary' => 'specials'),
        'created'  => array('label' => '创建日期', 'input' => 'null', ),
        'updated'  => array('label' => '更新日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
        'manage'  => array('label' => '管理指令', 'cols' => 'id', 'oper' => 'doact',),
    ),

);
?>

