<?php
namespace System\Controller;

/**
 * NodesController
 * 节点信息
 */
class NodeController extends \Libs\Framework\Controller {

    /**
     * 切换节点状态
     * @return
     */
    public function toggle() {
        $nodeService = D('System/Node', 'Service');
        if (!isset($_GET['id'])
            || !$nodeService->existNode($_GET['id'])) {
            return $this->error('无效的操作！');
        }

        if (!$_GET['status']) {
            $nodeService->setStatus($_GET['id'], 1);
        } else {
            $nodeService->setStatus($_GET['id'], 0);
        }

        $info = $_GET['status'] ? '禁用成功！' : '启用成功！';
        $this->success($info);
    }


    /**
     * 管理节点
     * @return
     */
    public function index() {
        $pid = $this->getRequest('pid', 0);

        $where = "pid=".$pid;

        $params = array(
            0 => array(
                'mod' => 'node',
                'where' => $where,
            ),
        );

        parent::_index($params);
    }

    /**
     * 添加信息
     * @return
     */
    public function add() {
        $pid   = $this->getRequest('pid', 0);
        $level = $this->getRequest('level', 0);
        $level++;

        $this->_create_form(array('pid' => $pid, 'level' => $level));
        $this->assign('backurl', $this->getBackurl() . "&pid=".$pid);
        $this->display();
    }
}
