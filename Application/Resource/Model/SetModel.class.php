<?php

namespace Resource\Model;

/**
 * Set
 * 指令集模型
 */
class SetModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;

     protected $_validate = array(
        array('name', 'require', '指令集名称不能为空！', 1, 'regex', 3),
        array('name', '', '指令集名称已经存在，请更换一个！', 1, 'unique', 1),
    );

    protected $_auto = array(
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );
}
