<?php

namespace Resource\Model;

/**
 * Device
 * 设备模型
 */
class DeviceModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;

     protected $_validate = array(
        array('name', 'require', '设备名称不能为空！', 1, 'regex', 3),
        array('host', 'require', '主机地址不能为空！', 1, 'regex', 3),

        array('name', '', '设备名称已经存在，请更换一个！', 1, 'unique', 1),
        array('pass', '0,32', '密码不能超过32个字符！', 1, 'length', 3),
        array('su_pass', '0,32', '密码不能超过32个字符！', 1, 'length', 3),
    );

    protected $_auto = array(
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
        array('pass', 'cryption_encode', 3, 'function'),
        array('su_pass', 'cryption_encode', 3, 'function'),
    );
}
