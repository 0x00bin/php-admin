<?php
namespace Inspect\Controller;

/**
 * IndexController
 * 系统信息管理
 */
class IndexController extends \Libs\Framework\Controller {
    public function index() {
        $this->display();
    }

    public function webshell() {
        include_once LIBS_PATH . "utils/Dictionary.class.php";
        $this->assign("devices", \Dictionary::Get("devices"));
        $this->display();
    }

    public function docmd() {
        $device_id = $this->getRequest("device_id", 0, true);
        $command   = $this->getRequest("cmd", 0, true);

        include_once LIBS_PATH . "CommandExecutor.class.php";
        $executor = new \CommandExecutor;
        $result = $executor->execCommand($device_id, $command);
        if ($result === false) {
            $this->error($executor->getError());
        } else {
            $this->success($result);
        }
    }

    public function cleardict() {
        $dname = $this->getRequest("dname", 0, true);
        include_once LIBS_PATH . "utils/Dictionary.class.php";
        if (\Dictionary::ClearCache($dname)) {
            echo "done.";
        } else {
            echo "clear fail.";
        }
    }
}
