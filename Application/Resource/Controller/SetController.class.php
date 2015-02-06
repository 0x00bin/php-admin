<?php
namespace Resource\Controller;


/**
 * SetController
 * 指令集管理
 */
class SetController extends \Libs\Framework\Controller {
    /**
     * 管理指令集内指令
     * @return
     */
    public function manage() {
        include LIBS_PATH . "utils/One2ManyHelper.class.php";
        $helper = new \One2ManyHelper($this, $this->view, "command", array('params', 'created', 'updated'));
        $helper->manage();
    }
}
