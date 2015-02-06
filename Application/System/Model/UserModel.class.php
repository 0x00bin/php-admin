<?php

namespace System\Model;

/**
 * User
 * 用户模型
 */
class UserModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;

    protected $_validate = array(
        // 用户不能为空
        array('name', 'require', '用户名不能为空！', 1, 'regex', 3),

        // 用户名唯一性
        array('name', '', '用户名已经存在，请更换一个！', 1, 'unique', 1),

        // 登录密码不能为空
        array('password', 'require', '登录密码不能为空！', 1, 'regex', 1),

        // 确认密码不一致
        array('password', 'cfm_password', '确认密码不一致！', 2, 'confirm', 3),

        // 状态
        array('status', '0,1', '无效的状态！', 1, 'in', 3),
    );

    protected $_auto = array(
        // password
        array('password', 'encryptPassword', 3, 'callback'),
        // remark
        array('remark', 'htmlspecialchars', 3, 'function'),

        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );

    /**
     * 加密密码
     * @param  string $password 需要被加密的密码
     * @return string
     */
    protected function encryptPassword($password) {
        if ('' == $password) {
            return null;
        }

        return D('System/User', 'Service')->encrypt($password);
    }
}
