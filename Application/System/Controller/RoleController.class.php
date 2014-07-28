<?php
namespace System\Controller;

/**
 * RoleController
 * 角色信息
 */
class RoleController extends \Libs\Framework\Controller {
    /**
     * 角色管理列表
     * @return
     */
    public function index() {
        $roles = D('Role', 'Service')->getRole();

        $this->assign('roles', $roles);
        $this->assign('rows_count', count($roles));
        $this->display();
    }

    /**
     * 添加角色
     * @return
     */
    public function add() {
        $this->assign('roles', D('Role', 'Service')->getRole());
        $this->display();
    }

    /**
     * 创建角色
     * @return
     */
    public function create() {
        if (!isset($_POST['role'])) {
            return $this->error('无效的操作！');
        }

        $result = D('Role', 'Service')->addRole($_POST['role']);
        if (!$result['status']) {
            return $this->error($result['data']['error']);
        }

        return $this->success('添加角色成功！', U('Role/index'));
    }

    /**
     * 编辑角色信息
     * @return
     */
    public function edit() {
        $roleService = D('Role', 'Service');
        if (!isset($_GET['id']) || !$roleService->existRole($_GET['id'])) {
            return $this->error('需要编辑的角色不存在！');
        }

        $role = M('Role')->getById($_GET['id']);

        $this->assign('role', $role);
        $this->assign('roles', $roleService->getRole());
        $this->assign('sids', $roleService->getSonRoleIds($role['id']));
        $this->display();
    }

    /**
     * 更新角色信息
     * @return
     */
    public function update() {
        $roleService = D('Role', 'Service');
        if (!isset($_POST['role'])
            || !$roleService->existRole($_POST['role']['id'])) {
            return $this->error('无效的操作！');
        }

        $result = $roleService->updateRole($_POST['role']);
        if (!$result['status']) {
            return $this->error($result['data']['error']);
        }

        return $this->success('更新角色信息成功！', U('Role/index'));
    }


    /**
     * 权限分配
     * @return
     */
    public function assignAccess() {
        $roleService = D('Role', 'Service');
        if (!isset($_GET['id'])
            || !$roleService->existRole($_GET['id'])) {
            return $this->error('需要分配权限的角色不存在！');
        }

        $role = M('Role')->getById($_GET['id']);
        if (0 == $role['pid']) {
            return $this->error('您无权限进行该操作！');
        }

        $access = D('Access')->relation(true)
                             ->where("role_id={$role['id']}")
                             ->select();
        $rAccess = array();
        foreach ($access as $key => $item) {
            $rAccess[$key]['val'] = "{$item['node_id']}:"
                                    . "{$item['node']['level']}:"
                                    . "{$item['node']['pid']}";
        }

        $role['access'] = json_encode($rAccess);

        $this->assign('role', $role);
        $this->assign('nodes', D('Node', 'Service')->getLevelNodes());
        $this->display('assign_access');
    }

    /**
     * 处理分配权限
     * @return
     */
    public function doAssignAccess() {
        $roleService = D('Role', 'Service');
        if (!isset($_POST['id']) || !$roleService->existRole($_POST['id'])) {
            return $this->error('需要分配权限的角色不存在！');
        }

        if (empty($_POST['access'])) {
            $_POST['access'] = array();
        }

        $result = $roleService->assignAccess($_POST['id'], $_POST['access']);
        if (!$result['status']) {
            return $this->error($result['data']['error']);
        }

        if (!empty($result['data'])) {
            return $this->success($result['data']);
        }

        return $this->success('分配权限成功！');
    }
}
