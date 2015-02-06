<?php

//static utils tools class
class TableHelper
{
    // string
    protected $_module; // current module name
    protected $_dictionaries = array();

    // instance of template engine, this instance need method assign('var', $var);
    protected $_view       = null;
    protected $_controller = null;

    public function __construct($module, $view, $controller) {
        $this->_module     = $module;
        $this->_view       = $view;
        $this->_controller = $controller;
    }

    protected function _log($msg, $is_error = false) {
        //echo "\n$msg\n";
        //Log::write($msg, $is_error ? Log::ERR : Log::INFO);
    }

    protected function _load_tables_config() {
        return $this->_get_tables_config( array(strtolower($this->_module)) );
    }

    // 批量unset
    protected function _unsets(&$array, $idxs) {
        foreach($idxs as $idx) {
            unset($array[$idx]);
        }
    }

    protected function _load_dictionaries(&$tables) {
        $dictionaries = $this->_controller->getDictionaries($tables);
        foreach($dictionaries as $name => $dictionary) {
            $this->_view->assign($name, $dictionary);
        }
    }

    protected function _get_tables_config($names, $params=array() ) {
        $result = array();
        if (empty($names) ){
            foreach($params as $param ) {
                $names[] = $param['mod'];
            }
        }
        foreach( $names as $i => $name ) {
            $table  = $this->_controller->getTableConfig($name);

            if (isset($table['oper']) ) {
                foreach($table['oper'] as $act => $oper) {
                    if (!$this->_controller->checkActionAccess($act)) {
                        unset($table['oper'][$act]);
                    }
                }
            }

            if ( isset($params[$i]['unsets']) ) {
                $this->_unsets($table['columns'], $params[$i]['unsets']);
            }

            $result[] = $table;
        }

        return $result;
    }

    protected function _get_list($name, &$table, &$params) {
        $where = isset($params["where"])? $params["where"]:"";
        return $this->_controller->getList($name, $where, implode(",",array_keys($table['columns'])));
    }

    // for jqgrid ext
    public function _copy_tables2ext(&$params_exts) {
        if ( !isset($params_exts['_copy_']['name']) ) {
            exit('ext_cols _copy_ params error:'.var_export($params_exts['_copy_'], true));
        }

        $module = $params_exts['_copy_']['name'];
        $cols   = isset($params_exts['_copy_']['cols'])? $params_exts['_copy_']['cols']:'';

        $config_tables_file = isset($params_exts['_copy_']['file'])? $params_exts['_copy_']['file']:"";

        if (file_exists($config_tables_file)) {
            $table_config  = require $config_tables_file;
        } else {
            $table_config = $this->_controller->getTableConfig($module);
        }
        if (!isset($table_config['columns']) ){
            exit($config_tables_file." tables[$module]['columns'] not exists");
        }

        $cp_cols = $table_config['columns'];
        if ( $cols == '' ) {
            $cols = array_keys($cp_cols);
        }

        $ext_cols = array();
        foreach( $cols as $col ) {
            $ext_cols[$col] = array('grid' => $cp_cols[$col]);
        }
        unset($params_exts['_copy_']);
        $params_exts = array_merge($params_exts, $ext_cols);

    }

    protected function _params_process(&$table, &$params, $idx) {
        if ( empty($table['columns']) ) {
            exit('table columns empty');
        }
        $module = isset($params['mod'])? $params['mod']:$this->_module;

        if ( isset($params['table']) ) {
            $table = array_merge($table, $params['table']);
        }
        // 表格搜索需要设置标志位_search=1
        if (isset($_REQUEST['_search']) && $_REQUEST['_search'] == 1) {
            $params["where"] = $this->_controller->filterColumns($table['columns']);
        }

        if ( isset($params['exts']) ) {
            foreach($params['exts'] as $i => $param) {
                if ( isset($param['ext_cols']['_copy_']) ) {
                    $this->_copy_tables2ext($param['ext_cols']);
                }
                $table['columns'] = array_merge($table['columns'], $param['ext_cols']);
            }
        }

        // conf 默认值
        if (!isset($table['conf'])) {
            $table['conf'] = array(
                'page'  => true,
                'count' => true,
            );
        }
    }

    public function create($params = array(), $tables = array()) {
        if ( empty($tables) && !empty($params) ) {
            $tables = $this->_get_tables_config("", $params);
        }

        if ( empty($tables) ) {
            $tables = $this->_load_tables_config();
        }

        $result = array();
        foreach($tables as $i => &$table) {
            $result[] = $this->_create($table, $i, isset($params[$i])? $params[$i]:'');
        }

        $this->_load_dictionaries($tables);
        $this->_view->assign('tables', $tables);
        $this->_view->assign('lists',  $result);
    }

    protected function _create(&$table, $i, &$params) {
        $this->_params_process($table, $params, $i);
        $modname = strtolower(isset($params['mod'])? $params['mod']:$this->_module);
        C('PAGE_VAR', 'p'.$i);
        return $this->_get_list($modname, $table, $params);
    }
}

?>
