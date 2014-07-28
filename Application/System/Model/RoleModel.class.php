<?php

namespace System\Model;

/**
 * Role
 * 角色模型
 */
class RoleModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;
    protected $_validate = array(
        // 角色名称不能为空
        array('name', 'require', '角色名称不能为空！', 1, 'regex', 3),
        // 角色名称不能大于32个字符
        array('name', '0,32', '角色名称不能超过32个字符！', 1, 'length', 3),

        // 状态
        array('status', '0,1', '无效的状态！', 1, 'in', 3),

        // 父角色不能为空
        array('pid', 'require', '父角色不能为空！', 1, 'regex', 3),
    );

    protected $_auto = array(
        // description
        array('description', 'htmlspecialchars', 3, 'function'),
        // 创建时间
        array('created', 'time', 1, 'function'),
        // 更新时间
        array('updated', 'time', 3, 'function')
    );
}
