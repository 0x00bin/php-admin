<?php
namespace System\Controller;

/**
 * UserController
 * 用户信息
 */
class UserController extends \Libs\Framework\Controller {
    /**
     * 管理员列表
     * @return
     */
    public function index() {
        $result = $this->getList('User');

        $this->assign('list', $result['list']);
        $this->assign('count', $result['count']);
        $this->assign('page',  $result['page']);
        $this->display();
    }

    /**
     * 添加用户
     * @return
     */
    public function add() {
        $this->_assign_dictionaies(array("switchs"));
        $this->assign('roles', D('Role', 'Service')->getRole());
        $this->display();
    }

    /**
     * 保存用户
     * @return
     */
    public function save() {
        if (!isset($_POST['data'])) {
            return $this->error('无效的操作！');
        }

        $service = D('User', 'Service');
        if ( false === $service->add($_POST['data'])) {
            return $this->error($service->getError());
        }

        return $this->success('添加用户成功！', U('User/index'));
    }

    /**
     * 编辑管理员信息
     * @return
     */
    public function edit() {
        $id = $this->getRequest('id', 0, true);
        $data = M('User')->getById($id);
        if (false === $data) {
            return $this->error('需要编辑的用户信息不存在！');
        }

        if ($data['is_super'] == 1 && !$_SESSION[C('ADMIN_AUTH_KEY')] ) {
            return $this->error('您没有权限执行该操作！');
        }

        $this->assign('admin', $admin);
        $this->assign('roles', D('Role', 'Service')->getRole());
        $this->display();
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update() {
        $adminService = D('User', 'Service');
        if (!isset($_POST['admin'])
            || !$adminService->existUser($_POST['admin']['id'])) {
            return $this->error('无效的操作！');
        }

        $result = $adminService->update($_POST['admin']);
        if (!$result['status']) {
            return $this->error($result['data']['error']);
        }

        return $this->success('更新管理员信息成功！', U('User/index'));
    }
}
