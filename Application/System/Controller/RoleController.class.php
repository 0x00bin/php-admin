<?php
namespace System\Controller;

/**
 * RoleController
 * 角色信息
 */
class RoleController extends \Libs\Framework\Controller {
    protected $_dictionaies = array("switchs", "roles");

    /**
     * 管理某角色用户
     * @return
     */
    public function manage() {
        include LIBS_PATH . "utils/One2ManyHelper.class.php";
        $helper = new \One2ManyHelper($this, $this->view, "user", array('status', 'is_super', 'last_login', 'remark'));
        $helper->manage();
    }

    /**
     * 权限分配
     * @return
     */
    public function assignAccess() {
        $role_id = $this->getRequest('id', 0, true);

        $datas  = D('Access')->relation(true)
                             ->where("role_id={$role_id}")
                             ->select();
        $access = array();
        if (!empty($datas)) {
            foreach ($datas as $key => $data) {
                $access[$key]['val'] = "{$data['node_id']}:"
                                        . "{$data['node']['level']}:"
                                        . "{$data['node']['pid']}";
            }
        }

        $role = D('System/Role', 'Service')->getDataById($role_id);
        $role['access'] = json_encode($access);

        $this->assign('role', $role);
        $this->assign('nodes', D('System/Node', 'Service')->getLevelNodes());
        $this->display('assign_access');
    }

    /**
     * 处理分配权限
     * @return
     */
    public function saveAccess() {
        $role_id = $this->getRequest('id', 0, true);
        $access = $this->getRequest('access', array());
        $service = D('System/Role', 'Service');

        $result = $service->assignAccess($role_id, $access);
        if (false ===  $result ) {
            return $this->error($service->getError());
        }
        else if (!empty($result['message'])) {
            return $this->success($result['message']);
        }

        return $this->success('分配权限成功！');
    }
}
