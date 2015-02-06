<?php

namespace Inspect\Model;

/**
 * User
 * 用户模型
 */
class TaskModel extends \Libs\Framework\Model {
    protected $_dir = __DIR__;

    protected $_auto = array(
        array('created', 'date', 1, 'function', 'Y-m-d H:i:s'),
    );
}
