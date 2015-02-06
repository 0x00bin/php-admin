<?php
namespace System\Model;
use Think\Model\RelationModel;

/**
 * Access
 * 访问模型
 */
class AccessModel extends RelationModel {
    // realtions
    protected $_link = array(
        // 一个field对应一个input
        'node' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Node',
            'foreign_key' => 'node_id',
            'mapping_fields' => 'pid,level'
        )
    );
}
