<?php

return array (
    'height'  => 500,
    'caption' => '任务',
    'columns' => array( // 列表中需要的字段
        'id'       => array('label' => '编号', 'width' => 50),
        'name'     => array('label' => '任务名称', ),
        'type'     => array('label' => '任务类型', 'input' => 'select', 'dictionary' => 'task_types'),
        'command_type'=> array('label' => '指令类型', 'input' => 'select', 'dictionary' => 'command_types'),
        'command_id' => array('label' => '指令名', 'input' => 'select', 'dictionary' => 'commands', 'dictionary2' => 'sets'),
        'device_type'=> array('label' => '设备类型', 'input' => 'select', 'dictionary' => 'task_device_types'),
        'device_id'=> array('label' => '任务设备', 'input' => 'select', 'dictionary' => 'devices', 'dictionary2' => 'groups'),        
        'time'     => array('label' => '任务时间', 'input' => 'time', ),
        'status'   => array('label' => '任务状态', 'input' => 'select', 'dictionary' => 'task_status'),
        'remark'   => array('label' => '任务注释', 'input' => 'textarea', 'list' => 'null', 'placeholder' => '注释'),
        'created'  => array('label' => '创建日期', 'input' => 'null', ),
//        'updated'  => array('label' => '更新日期', 'input' => 'null'),
    ),
    'oper' => array(
        'edit' => '编辑',
        'delete'  => '删除',
        'exec' => array('label' => '执行', 'cols' => 'id', 'oper' => 'doact_ajax' ),
        'view_result' => '查看结果',
    ),

   // 'checkbox' => true,
);
?>

