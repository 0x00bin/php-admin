<?php
return array (
    'height'  => 500,
    'caption' => '结果',
    'columns' => array( // 列表中需要的字段
        'id'      => array('label' => '编号', 'width' => 50),
        'task_id'      => array('label' => '任务名称', 'dictionary' => 'tasks',),
        'device_id'    => array('label' => '设备名称', 'dictionary' => 'devices',),
        'host'         => array('label' => '设备主机', ),
        'command_id'   => array('label' => '指令名称', 'dictionary' => 'commands',),
        'brief_result' => array('label' => '结果摘要'),
        'result'       => array('label' => '结果', 'list' => 'null'),
        'created'      => array('label' => '创建日期', 'input' => 'null', ),
    ),
    'oper' => array(
        'edit'    => '编辑',
        'delete'  => '删除',
        'detail' => array('label' => '查看', 'cols' => 'id', 'oper' => 'doact_ajax' ),
    ),

   // 'checkbox' => true,
);
?>

