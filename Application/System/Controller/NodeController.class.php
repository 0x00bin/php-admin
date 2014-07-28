<?php
namespace System\Controller;

/**
 * NodesController
 * 节点信息
 */
class NodeController extends \MyLib\BaseController {
    /**
     * 节点列表
     * @return
     */
    public function index() {
        $nodeService = D('Node', 'Service');
        $nodes = $nodeService->getNodes();

        foreach ($nodes as $key => $node) {
            $nodes[$key]['type'] = $nodeService->getNodeType($node['level']);
        }

        $this->assign('nodes', $nodes);
        $this->assign('rows_count', count($nodes));
        $this->display();
    }

    /**
     * 切换节点状态
     * @return
     */
    public function toggleStatus() {
        $nodeService = D('Node', 'Service');
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
}
