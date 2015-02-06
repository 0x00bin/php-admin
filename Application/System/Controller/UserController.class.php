<?php
namespace System\Controller;

/**
 * UserController
 * 用户信息
 */
class UserController extends \Libs\Framework\Controller {
    protected $_dictionaies = array("switchs", "yesno");

    /**
     * 编辑信息
     * @return
     */
    public function edit() {
        $id = $this->getRequest('id', 0, true);
        $data = $this->_get_service()->getDataById($id);
        if (false === $data) {
            return $this->error('需要编辑的信息不存在！');
        }

        if ($data['is_super'] == 1 && !$_SESSION[C('ADMIN_AUTH_KEY')] ) {
            return $this->error('您没有权限执行该操作！');
        }
        parent::edit($data);
    }
}
