<?php

namespace Resource\Model;

/**
 * Command
 * 指令模型
 */
class CommandModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;


    protected $_validate = array(
        // 用户不能为空
         //array(field,rule,message,condition,type,when,params)
        array('name', 'require', '指令名称不能为空！', 1, 'regex', 3),

        array('content', 'require', '内容不能为空！', 1, 'regex', 3),

        // 用户名唯一性
        array('name', '', '指令名称已经存在，请更换一个！', 1, 'unique', 1),
    );

    protected $_auto = array(
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );
}
