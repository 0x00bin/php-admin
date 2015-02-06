<?php

return array (
    'height'  => 500,
    'caption' => '设备组',
    'columns' => array( // 列表中需要的字段
        'id'       => array('label' => '编号', 'width' => 50),
        'name'     => array('label' => '组名称', ),                
        'created'  => array('label' => '创建日期', 'input' => 'null', ),
        'updated'  => array('label' => '更新日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
        'manage'  => array('label' => '管理设备', 'cols' => 'id', 'oper' => 'doact',),
    ),

);
?>

