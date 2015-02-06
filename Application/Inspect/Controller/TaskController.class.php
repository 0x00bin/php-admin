<?php
namespace Inspect\Controller;


/**
 * TaskController
 * 任务管理
 */
class TaskController extends \Libs\Framework\Controller {

    /**
     * 执行任务
     */
    public function exec() {
        $id = $this->getRequest("id", 0, true);

        $task = $this->_get_service()->getDataById($id);

        if ($task === false) {
            $this->error("任务不存在");
        }

        include_once LIBS_PATH . "CommandExecutor.class.php";
        $executor = new \CommandExecutor;
        if (!$executor->execTask($task)) {
            $this->error($executor->getError());
        } else {
            $this->success('执行成功');
        }
    }
}
