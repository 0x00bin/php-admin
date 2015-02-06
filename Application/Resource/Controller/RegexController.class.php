<?php
namespace Resource\Controller;

/**
 * CommandRegexController
 * 指令正则管理
 */
class RegexController extends \Libs\Framework\Controller {
    /**
     * 添加信息
     * @return
     */
    public function add() {
        $command_id = $this->getRequest("command_id", 0, true);
        $this->assign("vo", array("command_id" => $command_id));
        parent::add();
    }
}
