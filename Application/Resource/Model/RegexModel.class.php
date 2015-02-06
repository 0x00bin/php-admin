<?php

namespace Resource\Model;

/**
 * RegexModel
 * 正则模型
 */
class RegexModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;

     protected $_validate = array(
        array('command_id', 'require', '指令不能为空！', 1, 'regex', 3),
       // array('regex', 'require', '正则不能为空！', 1, 'regex', 3),
        array('expression', 'require', '结果表达式不能为空！', 1, 'regex', 3),
    );

    protected $_auto = array(
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );
}
