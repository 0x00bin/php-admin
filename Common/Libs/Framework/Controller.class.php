<?php
namespace Libs\Framework;

/**
 * Controller
 * 基础通用控制器
 */
class Controller extends \Think\Controller {

    // 定义视图中使用的字典
    protected $_dictionaies = array();

    /**
    * 全局初始化
    * @return
    */
    protected function _initialize() {
        header('Content-Type: text/html; charset=utf-8');

        // 登录过滤
        $modules = explode(',', C('NOT_LOGIN_MODULES'));
        if (!in_array(CONTROLLER_NAME, $modules)) {
            $this->checkLogin();
        }

        // 权限过滤
        $this->filterAccess();

        // 菜单分配
        if ( CONTROLLER_NAME !== 'Public' && !IS_AJAX) {
            // 分配菜单
            $this->assignMenu();
            // 面包屑位置
            $this->assignBreadcrumbs();
        }
    }

    /**
     * 登录过滤
     * @return
     */
    protected function checkLogin() {
        $user = D('System/User', 'Service');
        if (!$user->checkLogin()) {
            return $this->error($user->getError(), U('System/Public/index'));
        }
    }

    /**
     * 权限过滤
     * @return
     */
    protected function filterAccess() {
        if (!C('USER_AUTH_ON')) {
            return;
        }

        if (isset($_SESSION['user']['is_super']) && $_SESSION['user']['is_super'] == 1) {
            return;
        }

        if (\Org\Util\Rbac::AccessDecision(C('GROUP_AUTH_NAME'))) {
            return ;
        }

        return $this->error('您没有权限执行该操作！');
    }

    /**
     * 是否已登录
     * @return boolean
     */
    protected function isLogin() {
        return D('System/User', 'Service')->checkLogin();
    }

    /**
     * 空操作
     * @return
     */
    public function _empty() {
        $this->error('您访问的页面不存在！');
    }

    /**
     * 得到数据分页列表
     * @param  string $modelName 模型名称
     * @param  array  $where     分页条件
     * @param  string $fields    字段
     * @param  array  $order     排序
     * @return array
     */
    protected function getList($model, $where, $fields="*", $order="") {
        $model = D($model);
        // 总数据行数
        $count = $model->where($where)->count();

        $result = array(
            'count' => $count,
            'list'  => array(),
            'page'  => 1,
            'total' => 0,
        );

        if ($count > 0) {
        // 实例化分页
            $page = new \Org\Util\Page($count, C('PAGE_LIST_ROWS'));
            $options = array(
                'where' => $where,
                'order' => empty($order)? "" : "`" . $order . "` ", // . $sort,
                'limit' => $page->firstRow . ',' . $page->listRows,
                'field' => $fields,
            );

            $result['list'] = $model->select($options);
            $result['page'] = $page->show();
        }

        return $result;
    }

    /**
     * 分配字典到视图层
     * @return
     */
    protected function _assign_dictionaies($names=array()) {
        if ( empty($names) ) {
            $names = $this->_dictionaies;
        }

        if ( empty($names) ) {
            return;
        }

        $dictionaries = include COMMON_PATH . "Conf/dictionaries.config.php";
        foreach($names as $name) {
            if ( isset($dictionaries[$name]) ) {
                $this->assign($name, $dictionaries[$name]);
            }
        }
    }

    /**
     * 分配菜单
     * @return
     */
    protected function assignMenu() {
        $menu = $this->getMenu();

        $this->assign('main_menu', $menu['main_menu']);
        $this->assign('sub_menu',  $menu['sub_menu']);
    }

    /**
     * 获取请求的参数
     * @param string $name 参数名
     * @param mix    $default 默认参数
     * @param bool   $halt 是否报错停止
     */
    protected function getRequest($name, $default=null, $halt=false) {
        $value = I('request.'.$name, $default);
        if ($halt && !isset($_REQUEST[$name]) ) {
            $this->error('参数错误:'.$name);
        }
        return $value;
    }
    /**
     * 分配面包屑
     * @return
     */
    protected function assignBreadcrumbs() {
        $breadcrumbs = $this->getBreadcrumbs();

        $this->assign('breadcrumbs', $breadcrumbs);
    }

    /**
     * 得到菜单
     * @return array
     */
    protected function getMenu() {
        $menus = C('MENU');

        // 主菜单
        $main_menus = array();
        // 已被映射过的键值
        $mapped = array();

        // 访问权限
        $access = $_SESSION['_ACCESS_LIST'];
        if (empty($access)) {
            $authId = $_SESSION[C('USER_AUTH_KEY')];
            $access = \Org\Util\Rbac::getAccessList($authId);
        }
        $authGroup = strtoupper(C('GROUP_AUTH_NAME'));

        // 处理主菜单
        foreach ($menus as $key => $item) {
            // 不显示无权限访问的主菜单
            if (!$_SESSION[C('ADMIN_AUTH_KEY')]
                &&  !isset($access[$authGroup][strtoupper($key)])) {
                    echo __LINE__ . "<br>";
                continue ;
            }

            // 主菜单是否存在映射
            if (isset($item['mapping'])) {
                // 映射名
                $mapping = $item['mapping'];
                // 新的菜单键值
                if (!empty($mapped[$mapping])) {
                    $key = "{$mapped[$mapping]}-{$key}";
                    $mapping = $mapped[$mapping];
                } else {
                    $key = "{$mapping}-{$key}";
                }

                // 需要映射的键值已存在，则删除
                if (isset($main_menus[$mapping])) {
                    $main_menus[$key]['name']   = $main_menus[$mapping]['name'];
                    $main_menus[$key]['target'] = $main_menus[$mapping]['target'];
                    unset($main_menus[$mapping]);
                    $mapped[$mapping] = $key;
                }

                continue ;
            }

            $main_menus[$key]['name'] = $item['name'];
            $main_menus[$key]['target'] = $item['target'];
        }

        // 子菜单
        $sub_menus = array();
        $ctrlName = CONTROLLER_NAME;
        if (isset($menu[$ctrlName]['mapping'])) {
            $ctrlName = $menu[$ctrlName]['mapping'];
        }

        $actions = $access[$authGroup];
        // 主菜单如果为隐藏，则子菜单也不被显示
        foreach ($menus[$ctrlName]['sub_menu'] as $item) {
            // 子菜单是否需要显示
            if (isset($item['hidden']) && true === $item['hidden']) {
                continue ;
            }

            $route = array_shift(array_keys($item['item']));
            $action = explode('/', strtoupper($route));
            // 不显示无权限访问的子菜单
            if (!$_SESSION[C('ADMIN_AUTH_KEY')]
                && (!array_key_exists($action[0], $actions)
                    || !array_key_exists($action[1], $actions[$action[0]]))) {
                continue ;
            }

            // 子菜单是否有配置
            if (!isset($item['item']) || empty($item['item'])) {
                continue ;
            }

            $routes    = array_keys($item['item']);
            $itemNames = array_values($item['item']);
            $sub_menus[$routes[0]] = $itemNames[0];
        }

        return array(
            'main_menu' => $main_menus,
            'sub_menu'  => $sub_menus
        );
    }

    /**
     * 得到面包屑
     * @return string
     */
    public function getBreadcrumbs() {
        $menus = C('MENU');

        $menu = $menus[CONTROLLER_NAME];
        // 主菜单显示名称
        $main = $menu['name'];
        // 子菜单显示名称
        $sub = 'unkonwn';
        $route = CONTROLLER_NAME . '/' . ACTION_NAME;
        foreach ($menu['sub_menu'] as $item) {
            // 以键值匹配路由
            if (array_key_exists($route, $item['item'])) {
                $sub = $item['item'][$route];
            }
        }

        return $main . ' > ' . $sub;
    }

    /**
     * 下载文件
     * @param  文件路径 $filePath
     * @param  文件名称 $fileName
     * @return
     */
    protected function download($filePath, $fileName) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; ' . 'filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }
}
