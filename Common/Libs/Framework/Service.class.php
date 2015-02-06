<?php
namespace Libs\Framework;

/**
 * Service
 */
abstract class Service {

    protected $_error;
    /**
     * 返回错误信息
     * @return fixed  $result
     */
    public function getError() {
        return $this->_error;
    }

    /**
     * 返回成功的结果值
     * @return fixed  $result
     */
    protected function success($result=true) {
        return $result;
    }

    protected function message($message) {
        return array("message" => $message);
    }


    /**
     * 设置错误信息 并返回错误的结果值
     * @param  string $error 错误信息
     * @return bool
     */
    protected function error($error) {
        $this->_error = $error;
        return false;
    }

    protected $_model = null;

	// 不带前缀和后缀的表名
	protected $_name  = '';

	protected $_show_log = false;
    protected $_use_trans = false;
    protected $_trans_started = false; // 是否已经开始了事务

    /**
     * Service 构造函数
     * @param string $name 参数名
     * @param bool   $use_trans 是否使用事务
     * @result
     */
	public function __construct($name = "", $use_trans = false)
	{
		$this->_initialize();
		$this->_use_trans = $use_trans;
	}

    /**
     * 写日志
     * @param string $msg 日志消息
     * @param bool   $is_error 是否错误
     * @result
     */
    protected function _log($msg, $is_error = false) {
       if ($this->_show_log){
            echo "\n<br>".$msg . "\n<br>";
       }
       \Think\Log::write($msg, $is_error ? \Think\Log::ERR : \Think\Log::INFO);
    }

    /**
     * 获取Model配置
     * @param string $name 模块名
     * @param bool   $is_error 是否错误
     * @result array
     */
    protected function _get_config($name='') {
        $configs = $this->_model->getConfig();
        if ( $name != '' && isset($configs[$name]) ) {
             return $configs[$name];
        }
        return $configs;
    }

    /**
     * Service类初始化
     * @result
     */
	protected function _initialize() {
        $name = $this->getName();
	    $model = str_replace(array('Service'), 'Model', get_class($this));

		$this->_model = new $model();
	    $this->_model->setTableName($name);
	}

    protected function getName() {
        if ( empty($this->_name) ) {
            $name = basename(str_replace('\\','/', get_class($this)));
		    $this->_name = strtolower(str_replace(array('Service'),'', $name));
	    }

	    return $this->_name;
    }

	public function startTrans() {
	    if ($this->_use_trans && !$this->_trans_started) {
		    $this->_model->startTrans();
		    $this->_log(get_class($this).'::start trans');
		    $this->_trans_started = true;
		}
	}

    protected function _save($data, $options=array()) {
        $this->startTrans();
		return $this->_model->save($data, $options);
    }

    public function add($data){
        if (false === ($data = $this->_model->create($data))) {            
            return $this->error($this->_model->getError());
        }

        return $this->_add($data);
    }

    protected function _add($data) {
        $this->startTrans();
		return $this->_model->add($data);
    }

    public function insertAll($datas, $options=array()) {
        $this->startTrans();
        return $this->_model->insertAll($datas, $options);
    }

    // 获取指定ID的一条记录
	public function getDataById($id, $cols='*') {
        return $this->getDataByWhere("id='{$id}'", $cols);
	}

    public function find($options) {
        return $this->_model->find($options);
    }

    public function getDataByWhere($where, $cols='*') {
		return $this->_model->find(array(
            'field' => $cols,
            'where' => $where,
        ));
	}

    public function getDatasByWhere($where, $cols="*") {
        $options = array(
            'where' => $where,
            'field' => $cols,
        );

        return $this->_model->select($options);
    }

    public function findAll($options = array()) {
        return $this->_model->select($options);
    }

    public function delete($where){
        if (is_numeric($where)) {// delete by id
            $where = "id=".$where;
        } elseif (is_array($where)) { // delete by ids
            $where = "id IN(".implode(",", $where).")";
        }

        // default is where
	    return $this->_model->delete(
	        array(
                'where' => $where,
            )
	    );
	}

    public function save($data, $options=array()) {
        if (false === ($data = $this->_model->create($data))) {
            return $this->error($this->_model->getError());
        }

		$result = $this->_save($data, $options);

		return $result;
	}

	protected function getModelError() {
	    $error = array();
		if ($this->_model->getError() != '') {
			$error[] = 'Model:' .$this->_model->getError();
		}
		if ($this->_model->getDbError() != '') {
			$error[] = 'DB:' . $this->_model->getDbError();
		}
        return implode(",", $error);
	}

    public function hasError($error='')
    {
        if (!empty($error)) {
            $this->_error .= $error;
        }
        $this->_error .= " " .  $this->getModelError();
        $this->_log(get_class($this).'::hasError:'.$this->_error);
    }

    public function getTableCols(){
        return $this->_model->getTableCols();
    }

    public function endTrans(){
    	if ($this->_use_trans && $this->_trans_started)  {
            $this->_trans_started = false;
            if ( empty($this->_error) ) { // nothing error
                    $this->_model->commit();
                    $this->_log(get_class($this).'::model::commit();');
            } else {
                $this->_log($this->_error,true);
                $this->_model->rollback();
                $this->_log(get_class($this).'::model::rollback');
                $this->_error = ""; // 重置错误信息
            }
        }
    }

    private function _get_cache_data_for_customize_dictionary(&$params) {
         // 字段中指定一个为key, 一个为value, 缓存的字典名称时什么
        if ( !isset($params['key']) || !isset($params['value']) || !isset($params['dict_name'])) {
            return $this->error('params error');
        }
        $key   = $params['key'];
        $value = $params['value'];
        $options = array('field' => "{$key}, {$value}");
        if (isset($params['group'])) {
            $options['group'] = $params['group'];
        }
        if (isset($params['where'])) {
            $options['where'] = $params['where'];
        }
        $rows = $this->_model->select($options);

        if ( empty($rows) ) {
            return $this->error('data is empty');
        }

        $data = array();
        $data[0] = '无';
        foreach( $rows as $row ) {
            $data[$row[$key]] = ($row[$value] == '')? '空值':$row[$value];
        }
        return $data;
    }

    public function cacheDictionary($params) {
        if ( isset($params['table_name']) ) {
            $this->_model->setTrueTableName($params['table_name']);
        }

        $ret = true;
        $err = '';
        if ( isset($params['id']) ) { //default form table sys_dictioinaries
            $data = $this->_model->getById ($params['id']);
            if (empty($data) ) {
                return $this->error("data is empty");
            }
            $ret = \Dictionary::Cache($data, $err);
        } else { // 定制表的字典cache
            $data = $this->_get_cache_data_for_customize_dictionary($params);
            if ($data === false) {
                return false;
            }
            $ret = \Dictionary::CacheById($data, $params['dict_name'], $err);
        }

        if (!$ret) {
            $this->error($err);
        }
        return $ret;
    }

    public function throwException($msg) {
        $this->_error = '';
        $this->hasError($msg);
        $error = $this->getError();
        throw_exception($error);
    }

    public function __destruct() {
    	try { // 防止析构时mysql的异常
            $this->endTrans();
        }catch(Exception $e) {
            $this->_log("__destruct(mysql execption),please set trans = false OR exec endTrans(). exception:".$e->getMessage());
        }
        \Think\Log::save();
    }
}
