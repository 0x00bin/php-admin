<?php
namespace Resource\Controller;


/**
 * GroupController
 * 设备分组管理
 */
class GroupController extends \Libs\Framework\Controller {
    /**
     * 管理组内设备
     * @return
     */
    public function manage() {
        include LIBS_PATH . "utils/One2ManyHelper.class.php";
        $helper = new \One2ManyHelper($this, $this->view, "device", array('params', 'created', 'updated'));
        $helper->manage();
    }
}
