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


        if (C('PRF_MODE')) {
            layout(false);
        } //else {
        // TODO 菜单全部传到前端处理
            // 菜单分配
            if ( CONTROLLER_NAME !== 'Public' && !IS_AJAX) {
                // 分配菜单
                $this->_assign_menus();
                // 面包屑位置
                $this->_assign_location_guide();
            }
       // }
        // 定义常量 关闭layout

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

    private function _check_access() {
        if (!C('USER_AUTH_ON')) {
            return true;
        }

        if (isset($_SESSION['user']['is_super']) && $_SESSION['user']['is_super'] == 1) {
            return true;
        }
        return false;
    }

    public function checkActionAccess($act) {
        if ($this->_check_access()) {
            return true;
        }

        if (\Org\Util\Rbac::AccessDecision(APP_NAME, $act)) {
            return true;
        }

        return false;
    }

    /**
     * 权限过滤
     * @return
     */
    protected function filterAccess() {
        if ($this->_check_access()) {
            return;
        }

        if (\Org\Util\Rbac::AccessDecision()) {
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
    public function getList($model, $where, $fields="*", $order="") {
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
            $page = new \Org\Util\Page($count, C('PAGE_LIST_ROWS'), C('PAGE_VAR'));
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
     * 从table配置中载入数据字典
     * @param array tables 字典配置支持多个数据字典
     * @return array
     */
    public function getDictionaries(&$tables=array()) {
        //$dictionaries = include COMMON_PATH . "Conf/config.dictionaries.php";
        if (empty($tables)){
            $tables  = array($this->getTableConfig(MOD_NAME));
        }
        include_once LIBS_PATH . "utils/Dictionary.class.php";

        $result = array();
        foreach($tables as $table) {
            foreach($table['columns'] as $colname => $column) {
                if (isset($column['dictionary']) ) {
                    $name = $column['dictionary'];
                    $result[$name] = \Dictionary::Get($name);
                }

                if (isset($column['dictionary2']) ) {
                    $name = $column['dictionary2'];
                    $result[$name] = \Dictionary::Get($name);
                }
            }
        }
        return $result;
    }

    /**
     * 分配字典到视图层
     * @param array names e.g. ('dict1', 'dict2' => array(1=> 'a', 2 => 'b'));
     * @return array
     */
    protected function _assign_dictionaries($names=array()) {
        if ( empty($names) ) {
            $names = $this->getDictionaries();
        }

        if ( empty($names) ) {
            return;
        }
        include_once LIBS_PATH . "utils/Dictionary.class.php";

        foreach($names as $name => $dictionary) {
            if (is_array($dictionary)) {
                $this->assign($name, $dictionary);
            } else {
                $this->assign($dictionary, \Dictionary::Get($dictionary));
            }
        }
    }

    public function select() {
        $dname = $this->getRequest("dname", "", true);

        include_once LIBS_PATH . "utils/Dictionary.class.php";
        $dictionary = \Dictionary::Get($dname);
        echo json_encode($dictionary);
    }

    private function _get_menu_mapping(&$menus) {
        $mappings = array();
        foreach ($menus as $key => $item) {
            if (isset($item["mapping"])) {
                $mappings[$key] = $item["mapping"];
            }
        }
        return $mappings;
    }

    protected function _get_curr_mod() {
        return strtolower(APP_NAME . '_' . MOD_NAME);
    }

    /**
     * 分配菜单
     * @return
     */
    protected function _assign_menus() {
        $menus = $this->_get_meuns();

        if (C('PRF_MODE')) {
            $curr_app_mod = $this->_get_curr_mod();
            $all_sub_menus = array();
            $access = $this->_get_access();
            $config_menus = C('MENU');

            foreach($menus['main_menu'] as $name => $menu) {
                $app_mod_names = explode("-", $name);
                $app_mod_name = $app_mod_names[0];
                if ($app_mod_name != $curr_app_mod) {
                    list($app, $mod) = explode("_", $app_mod_name);
                    $all_sub_menus[targetToID($menu["target"])] = $this->_get_submenus($config_menus[$app_mod_name], $access[strtoupper($app)]);
                } else {
                    $all_sub_menus[targetToID($menu["target"])] = $menus['sub_menu'];
                }
            }
            $this->assign('all_sub_menus', json_encode($all_sub_menus));
            $menu_mapping = $this->_get_menu_mapping($config_menus);
            $this->assign('menu_mapping', json_encode($menu_mapping));
        }

        $this->assign('main_menu', $menus['main_menu']);
        $this->assign('sub_menu',  $menus['sub_menu']);
    }

    /**
     * 获取请求的参数
     * @param string $name 参数名
     * @param mix    $default 默认参数
     * @param bool   $halt 是否报错停止
     */
    public function getRequest($name, $default=null, $halt=false) {
        $value = I('request.'.$name, $default);
        if ($halt && !isset($_REQUEST[$name]) ) {
            $this->error('参数错误:'.$name);
        }
        return $value;
    }

    /**
     * 分配位置指引
     * @return
     */
    protected function _assign_location_guide() {
        $location_guide = $this->_get_location_guide();

        $this->assign('location_guide', $location_guide);
    }

    /**
     * 获取访问列表
     * @return
     */
    private function _get_access() {
        if (!isset($_SESSION['_ACCESS_LIST'])) {
            $_SESSION['_ACCESS_LIST'] = \Org\Util\Rbac::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
        }
        return $_SESSION['_ACCESS_LIST'];
    }

    /**
     * 得到菜单
     * @return array
     */
    protected function _get_meuns() {
        $menus = C('MENU');

        // 主菜单
        // 访问权限
        $access = $this->_get_access();
        //$authGroup = strtoupper(C('GROUP_AUTH_NAME'));
        $main_menus = $this->_get_mainmenus($menus, $access);

        // 子菜单
        $ctrlName  = $this->_get_curr_mod();
        if (isset($menu[$ctrlName]['mapping'])) {
            $ctrlName = $menu[$ctrlName]['mapping'];
        }

        $sub_menus = $this->_get_submenus($menus[$ctrlName], $access[strtoupper(APP_NAME)]);

        return array(
            'main_menu' => $main_menus,
            'sub_menu'  => $sub_menus
        );
    }

    private function _get_mainmenus(&$menus, &$access) {
        $main_menus = array();
        // 已被映射过的键值
        $mapped = array();

        // 处理主菜单
        foreach ($menus as $key => $item) {
            // 不显示无权限访问的主菜单
            list($app, $mod) = explode("_", $key);
            if (!$_SESSION[C('ADMIN_AUTH_KEY')] && !isset($access[strtoupper($app)])) {
                continue ;
            }

            // 主菜单是否存在映射
            $new_key = $key;
            if (isset($item['mapping'])) {
                // 映射名
                $mapping = $item['mapping'];
                $old_key = "";
                if (isset($mapped[$mapping])) {
                    $new_key = "{$mapped[$mapping]}-{$key}";
                    $old_key = $mapped[$mapping];
                    if (isset($main_menus[$old_key]) ){
                        $main_menus[$new_key]['name']   = $main_menus[$old_key]['name'];
                        $main_menus[$new_key]['target'] = $main_menus[$old_key]['target'];
                        unset($main_menus[$old_key]);
                    }
                } else {
                    $new_key = "{$mapping}-{$key}";
                }

                $mapped[$mapping] = $new_key;
            } else {
                $main_menus[$new_key]['name']   = $item['name'];
                $main_menus[$new_key]['target'] = $item['target'];
                $mapped[$new_key] = $new_key;
            }
        }

        return $main_menus;
    }

    private function _get_submenus(&$menus, &$actions) {
        $sub_menus = array();
        // 主菜单如果为隐藏，则子菜单也不被显示
        foreach ($menus['sub_menu'] as $item) {
            // 子菜单是否需要显示
            if (isset($item['hidden']) && true === $item['hidden']) {
                continue ;
            }
            $keys  = array_keys($item['item']);
            $route = array_shift($keys);
            $action = explode('/', strtoupper($route));

            // 不显示无权限访问的子菜单
            if (!$_SESSION[C('ADMIN_AUTH_KEY')] &&
                ( !isset($actions[$action[1]]) || !isset($actions[$action[1]][$action[2]]))) {
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

        return $sub_menus;
    }

    /**
     * 得到位置指引
     * @return string
     */
    public function _get_location_guide() {
        $menus = C('MENU');
        $ctrlName  = $this->_get_curr_mod();
        $menu = $menus[$ctrlName];
        // 主菜单显示名称
        $main = $menu['name'];
        // 子菜单显示名称
        $sub   = 'unkonwn';
        if (ACT_NAME == 'welcome') {
            $sub = $main;
        } else {
            $route =  MOD_NAME . '/' . ACT_NAME;

            foreach ($menu['sub_menu'] as $item) {
                // 以键值匹配路由
                foreach($item['item'] as $key => $value) {
                    if (  false !== stripos($key, $route) ) {
                        $sub = $item['item'][$key];
                        break;
                    }
                }
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
    protected function _download($fpath, $content="") {
        if ($content == "") {
            $content = file_get_contents($fpath);
        }
        $fname = basename($fpath);
        if (empty($fname)) {
            $fname = "noname";
        }
        $fsize = strlen($content);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; ' . 'filename="' . $fname  . '.sh"');
        header('Content-Length: ' . $fsize);
        echo $content;
    }

    public function welcome() {
        $this->display("../../../Common/Tpl/default/welcome");
    }

    /**
     * 列表方法
     * @return
     */
    public function index() {
        $this->_index();
    }

    /**
     * 列表方法
     * @return
     */
    public function _index($params=array(), $tables=array()) {
        include LIBS_PATH . "utils/TableHelper.class.php";
        $helper = new \TableHelper(MOD_NAME, $this->view, $this);
        $helper->create($params, $tables);

        $this->display();
    }

    /**
     * 获取表的字段配置
     * @param  模块名 $mod
     * @return
     */
    public function getTableConfig($mod="") {
        if ($mod == "") {
            $mod = MOD_NAME;
        }
        $mod = strtolower($mod);
        $file = MODULE_PATH."Conf/config.table.{$mod}.php";
        if ( !file_exists($file)) {
            $this->error("$file not exist.");
        }
        return include $file;
    }

    /**
     * 生产并分配表单到view层
     * @return
     */
    protected function _create_form($data=array()) {
        $table = $this->getTableConfig();
        if ( isset($table['form']) ) {
            $table['columns'] = array_merge($table['columns'], $table['form']);
        }

        if ( isset($table['form_sort']) ){
            $columns = array();
            $cols = explode(",",$table['form_sort']);
            foreach($cols as $col) {
                $columns[$col] = $table['columns'][$col];
            }
            $table['columns'] = $columns;
        }
        $this->assign('table', $table);
        $this->_assign_dictionaries();
        if (isset($_REQUEST['_set']) && $_REQUEST['_set'] == 1) {
            $params = $this->filterColumns($table['columns']);
            $data = array_merge($params, $data);
        }

        $this->assign('vo', $data);
    }

    public function filterColumns(&$columns) {
        $result = array();
        foreach($columns as $name => $column) {
            if (isset($_REQUEST[$name])) {
                $result[$name] = $_REQUEST[$name];
            }
        }
        return $result;
    }

    /**
     * 添加信息
     * @return
     */
    public function add() {
        $this->_create_form();
        $this->display();
    }

    protected function _get_service() {
        return D(APP_NAME . '/' . MOD_NAME, 'Service');
    }

    /**
     * 保存信息
     * @return
     */
    public function save() {
        if (!isset($_POST['data'])) {
            return $this->error('无效的操作！');
        }
        $service = $this->_get_service();
        if ( false === $service->add($_POST['data'])) {
            return $this->error($service->getError());
        }

        return $this->success('添加成功！', $this->getBackurl());
    }

    /**
     * 编辑信息
     * @return
     */
    public function edit() {
        $this->_edit();
    }

    /**
     * 编辑信息
     * @return
     */
    protected function _edit($data=array()) {
        $id = $this->getRequest('id', 0, true);
        if (empty($data) ) {
            $data = $this->_get_service()->getDataById($id);
        }
        if (false === $data) {
            return $this->error('编辑的信息不存在！');
        }
        $this->_create_form($data);
        $this->assign('backurl', $this->getBackurl(true));
        //$this->assign('vo', $data);
        $this->display();
    }

    protected function getBackurl($history=false) {
        if ($history && !empty($_SERVER["HTTP_REFERER"])) {
            $backurl = $_SERVER["HTTP_REFERER"];
        } else {
            $backurl = $this->getRequest('backurl', '');
            if ($backurl == '') {
                $backurl = U(APP_NAME . '/' . MOD_NAME . '/index');
            }
        }

        return $backurl;
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update() {
        if (!isset($_POST['data']) ) {
            return $this->error('无效的操作！');
        }

        $service = $this->_get_service();

        if ( false === $service->save($_POST['data']) ) {
            return $this->error($service->getError());
        }
        return $this->success('更新信息成功！', $this->getBackurl());
    }


    /**
     * 删除数据
     * @return
     */
    public function delete() {
        $id = $this->getRequest('id', 0, true);

        $service = $this->_get_service();
        if (false === $service->delete($id)) {
            return $this->error('删除数据失败！');
        }

        return $this->success('成功删除数据！');
    }

    protected function _import($callback, $params= array()) {

        if (!empty($_FILES) ) {
            include_once LIBS_PATH . "utils/TxtImporter.class.php";
            $importer = new \TxtImporter;
            if ( isset($params['use_cn_header']) ){
                if ( !isset($params['use_cn_header']['app']) ) {
                    $params['use_cn_header'] = array(
                        'app' => APP_NAME,
                        'mod' => $this->getActionName(),
                    );
                }
            }
            $datas = $importer->import($params);
            if ($datas === false) {
                $this->error($importer->getError());
            }
            call_user_func($callback, $datas);
            $this->success("导入数据成功", $this->getBackurl());
        } else {
            $use_cn_header = isset($params['use_cn_header'])? "&use_cn_header=1":"";
            $this->assign("downtpl_url", __URL__ . "&act=downtpl".$use_cn_header);
            $this->display();
        }
    }

    protected function _insert_datas($datas) {
        if ( empty($datas) ) {
            $this->error("datas is empty.");
            return;
        }
        $service = $this->_get_service();
        if ( false === $service->insertAll($datas) ) {
            $service->hasError();
            $this->error($service->getError());
        }
    }

    protected function _filter_search($params, $service=null) {
        if (is_null($service)) {
            $service = $this->_get_service();
        }
        $cols = $service->getTableCols();
        $result = array();
        foreach($params as $key => $value) {
            if (in_array($key, $cols)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    // 导出csv文件
    public function export() {
        $service = $this->_get_service();
        $params = $this->_filter_search($_REQUEST, $service);
        $options = array();
        if (!empty($params)){
            $options['where'] = $params;
        }
        $datas = $service->findAll($options);
        include_once LIBS_PATH ."/utils/Export.class.php";
        $export = new \Export;
        //var_dump($datas);exit;
        $export->csv(MOD_NAME, $this->getTableConfig(), $datas);
    }
}
