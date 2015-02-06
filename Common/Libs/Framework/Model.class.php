<?php
namespace Libs\Framework;


/**
 * CommonModel
 * 数据库、数据表信息操作
 */
class Model extends \Think\Model {
    protected $connection = ''; //'mysql://root:@localhost/ams';
    protected $autoCheckFields = false;
    protected $tablePrefix = '';
    protected $tableSuffix = '';
    protected $_dir = "";

    public function setTableName($name) {
        $this->name = $name;
        $this->trueTableName = $this->tablePrefix . $name . $this->tableSuffix;
        return $this;
    }

    public function setTrueTableName($name) {
        $this->trueTableName = $name;
        return $this;
    }

    // TODO 是否是同一个数据连接用于区分是否单独开启事务
    public function isSameConnect() { return empty($this->connection); }

    public function getConfig() {
        $config_path = $this->_dir .'/../Conf/config.php';
        if ( !file_exists($config_path) ) {
            \Think\Log::write( $config_path.' not exists!');
            throw new \Think\Exception($config_path.' not exists!');
        }
        return require $config_path;
    }

    public function getTableCols() {
        $this->_checkTableInfo();
        $result = $this->fields;
        unset($result['_autoinc']);
        unset($result['_pk']);
        $this->fields = array();
        return $result;
    }

    protected function _initialize() {
        if ( empty($this->_dir) ) {
            die('please set protected $_dir = __DIR__;');
        }
        // compare db config
        $api_configs = $this->getConfig();

        $loc_configs = C();
        // 使用中会有陷阱，如果为空会在Model::__construct被设置为loc_configs的配置
        $this->tablePrefix = isset($api_configs['DB_PREFIX'])? $api_configs['DB_PREFIX']:'';
        $this->tableSuffix = isset($api_configs['DB_SUFFIX'])? $api_configs['DB_SUFFIX']:'';

        if (!$this->_compare_db_conf($api_configs, $loc_configs)) {
            $this->connection = $this->_getDsn($api_configs);
        }
    }

    private function _getDsn($config) {
        if ( !empty($config['DB_DSN']) ) {
            return $config['DB_DSN'];
        } else {
            return $config['DB_TYPE'] . '://' .
                   $config['DB_USER'] . ':' .
                   $config['DB_PWD']  . '@' .
                   $config['DB_HOST'] . ':' .
                   $config['DB_PORT'] . '/' .
                   $config['DB_NAME'];
        }
    }

    private function _compare_db_conf($api_configs, $loc_configs) {
        $compares = array ( // need compare db config item's
            'DB_HOST', 'DB_NAME'
        );

        foreach ($compares as $item) {
            if ( !isset($api_configs[$item]) ) {
                return true;
            }
            if ( $api_configs[$item] != $loc_configs[strtolower($item)] ) {
                return false;
            }
        }
        return true;
    }

    public function insertAll($datas, $options=array()) {
        $options['table'] = $this->trueTableName;
        return $this->db->insertAll($datas, $options);
    }

     // 获取指定ID的一条记录
    public function getDataById($id, $cols='*') {
        return $this->getDataByWhere("id='{$id}'", $cols);
    }

    public function getDataByWhere($where, $cols='*') {
        return $this->find(array(
            'field' => $cols,
            'where' => $where,
        ));
    }

    public function getDatasByWhere($where, $cols="*") {
        $options = array(
            'where' => $where,
            'field' => $cols,
        );

        return $this->select($options);
    }
}
