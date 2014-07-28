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
    protected $_use_trans = true;
    protected $_trans_started = false; // 是否已经开始了事务

	public function __construct($use_trans = true)
	{
		$this->_initialize();
		$this->_use_trans = $use_trans;
	}

    protected function _log($msg, $is_error = false) {
       if ($this->_show_log){
            echo "\n<br>".$msg . "\n<br>";
       }
       \Think\Log::write($msg, $is_error ? Log::ERR : Log::INFO);
    }

    protected function _get_config($name='') {
        $configs = $this->_model->getConfig();
        if ( $name != '' && isset($configs[$name]) ) {
             return $configs[$name];
        }
        return $configs;
    }

	protected function _initialize() {
        $name = $this->getName();
	    $model = str_replace(array('Service'), 'Model', get_class($this));

		$this->_model = new $model();
	    $this->_model->setTableName($name);
	}

    protected function getName() {
        if ( empty($this->_name) ) {
            $name = basename(get_class($this));
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
        $this->_trim_array($where);
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
	    return $this->_model->delete(
	        array(
                'where' => $where,
            )
	    );
	}

    public function save($data, $options=array()) {
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
                $this->_log($this->_error);
                $this->_model->rollback();
                $this->_log(get_class($this).'::model::rollback');
                $this->_error = ""; // 重置错误信息
            }
        }
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
