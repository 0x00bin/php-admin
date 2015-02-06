<?php
// dictionary cache

 // 除了system模块的dictionaries表, 如果需要在更改时自动清理缓存请配置table_name参数
 return array(
        'commands' => array(
            'service'  => 'Resource/Command',   // 指定接口Service名
            // 'table_name' => 'os_idcs',// 指定表名
            // 'key'        => 'id',     // 数据表中作字典key的字段 default id
            // 'value'      => 'name',   // 数据表中作字典value的字段 default name
            // 'where'      => 'type=1'  // 支持where条件
        ),
        'command_type1' => array(
            'service'  => 'Resource/Command',   // 指定接口Service名
        ),
        'command_type2' => array(
            'service'  => 'Resource/Set',   // 指定接口Service名
        ),
        'sets' => array(
            'service'  => 'Resource/Set',
        ),
        
        'device_type1' => array(
            'service'  => 'Resource/Device',   // 指定接口Service名
        ),
        'device_type2' => array(
            'service'  => 'Resource/Group',   // 指定接口Service名
        ),
        
        'groups' => array(
            'service'  => 'Resource/Group',
        ),

        'roles' => array(
            'service'  => 'System/Role',
        ),

        'users' => array(
            'service'  => 'System/User',
        ),

        'devices' => array(
            'service'  => 'Resource/Device',
        ),
         'tasks' => array(
            'service'  => 'Inspect/Task',
        ),
    );
