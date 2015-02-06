<?php

namespace System\Model;

/**
 * Node
 * 节点模型
 */
class NodeModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;
    protected $_validate = array(
        // 角色名称不能为空
        array('name', 'require', '名称不能为空！', 1, 'regex', 3),
        // 角色名称不能大于32个字符
        array('name', '0,32', '名称不能超过32个字符！', 1, 'length', 3),

        // 状态
        array('status', '0,1', '无效的状态！', 1, 'in', 3),

        // 父角色不能为空
        array('pid', 'require', '父节点不能为空！', 1, 'regex', 3),
    );

    protected $_auto = array(
        // 创建时间
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );
}
