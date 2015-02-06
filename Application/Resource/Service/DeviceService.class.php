<?php
namespace Resource\Service;

/**
 * DeviceService
 */
class DeviceService extends \Libs\Framework\Service {
    /**
     * �����豸��Ϣ
     * @param data array
     * @param option array
     * @return
     */
    public function save($data, $option=array()) {
        if (empty($data['pass'])) {
            unset($data['pass']);
        }
        if (empty($data['su_pass'])) {
            unset($data['su_pass']);
        }
        if (false === ($data = $this->_model->create($data))) {
            return $this->error($this->_model->getError());
        }

        if (false === parent::_save($data, $option)) {
            return $this->error("ϵͳ������, DB:". $this->_model->getDbError());
        }

        return $this->success();
    }

}
