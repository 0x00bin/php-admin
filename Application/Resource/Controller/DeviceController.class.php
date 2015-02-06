<?php
namespace Resource\Controller;


/**
 * DeviceController
 * 设备管理
 */
class DeviceController extends \Libs\Framework\Controller {

    /**
     * 测试设备
     * @return
     */
    public function test() {
        $id = $this->getRequest("id", 0, true);

        include_once LIBS_PATH . "CommandExecutor.class.php";
        $executor = new \CommandExecutor;
        $client = $executor->connectDevice($id);
	if ($client === false) {
            $this->error($executor->getError());
        } else {
            $client->disconnect();
            $this->success("设备连接成功");
        }
    }

    /**
     * import
     * 批量导入
     *
     * @return void
     */
    public function import() {
        $this->_import(array($this, 'add_datas'));
    }
    
    protected function add_datas($datas) {
        if ( empty($datas) ) {
            $this->error("datas is empty.");
            return;
        }
        $service = new \Resource\Service\DeviceService('device', true);
        $service->startTrans();
        foreach($datas as $data) {
            if ( false === $service->add($data)) {
                $error = $service->getError();
                $service->endTrans();
                return $this->error($data['name'] . ": " . $error);     
            }
        }
        $service->endTrans();
    }
}
