<?php

/**
 * 生成token
 */

return array(
    'switchs'      => array(1 => '启用', 0 => '禁用'),
    'yesno'        => array(1 => '是',   0 => '否'),
    'task_status'  => array(0 => '正常', 1 => '暂停', 2 => '立即执行'),

    'platforms'    => array(1 => "LINUX", 2 => "WINDOWS",), // 平台类型
    'specials'     => array(1 => "Mysql", 2 => "Oracle", 3 => "中间件",  4 => "操作系统"), // 专业方向
    'task_types'   => array(1 => '普通任务', 2 => '循环任务', 3 => '定时任务'),
    'device_types' => array(1 => 'Linux', 2 => 'Mysql'),
    'conn_types'   => array(1 => 'SSH2', 2 => 'Telnet', 3 => 'Mysql'),
    'command_types'=> array(1 => '单个指令', 2 => '指令集'),
    'task_device_types'=> array(1 => '单个设备', 2 => '设备组'),
    'cron_times'   => array(300 => '5分钟', 600 => '10分钟', 1800 => '30分钟', 3600 => '1小时', 7200 => '2小时'), // 索引为秒
);
