<?php

// cache interface
class One2ManyHelper {
    private $_controller = null;
    private $_view       = null;
    private $_one        = '';
    private $_many       = '';
    private $_unsets     = array();
    /*
     * @param $many many module name
     */
    public function __construct($controller, $view, $many, $unsets=array()) {

        $this->_controller = $controller;
        $this->_view       = $view;
        $this->_one        = strtolower(MOD_NAME);
        $this->_many       = $many;
        $this->_unsets     = $unsets;
    }

    private function _get_model() {
        static $model = null;
        if (is_null($model)) {
            $model = D(ucfirst($this->_one));
        }
        return $model;
    }

    private function get_manys_by_one($id) {
        $model = $this->_get_model();
        $datas = $model->setTableName($this->_one ."_" .$this->_many)->getDatasByWhere($this->_one ."_id=".$id, $this->_many ."_id");

        $model->setTableName($this->_one);

        return $datas;
    }

    public function manage() {
        $oper = $this->_controller->getRequest('oper', 'manage');
        $this->{"_".$oper}();
    }

    public function _manage() {
        $id = $this->_controller->getRequest('id', 0, true);
        $vo = $this->_get_model()->getDataById($id);

        if (false === $vo) {
            return $this->_controller->error($id . '不存在！');
        }

        $this->_view->assign('vo', $vo);

        $datas = $this->get_manys_by_one($id);
        $relations = array();
        foreach ($datas as $data){
			$relations[$data[$this->_many . '_id']] = $data[$this->_many . '_id'];
		}
        $in_where = " 0 ";
        $not_in_where = " 1 ";
        if (!empty($relations)) {
            $in_where     = "id IN(".implode(",", $relations).")";
            $not_in_where = "id NOT IN(".implode(",", $relations).")";
        }
        $caption = $this->_get_mod_caption($modname);
        $params = array(
            0 => array(
                'mod' => $this->_many, //
                'where' => $in_where,
                'table' => array(
                    'caption' => $vo['name'].'内' . $caption,
                    'oper'    => false,
                    'conf'    => array(
                         'page'  => true,
                        'count' => false,
                    ),
                    'checkbox' => true,
                ),

                'unsets' => $this->_unsets,
            ),
            1 => array(
                'mod'   => $this->_many,  // 表格请求的模块名
                'where' => $not_in_where,
                'table' => array(
                    'caption' => '其他' . $caption,
                    'oper'    => false,
                    'conf'    => array(
                         'page'  => true,
                        'count' => false,
                    ),
                    'checkbox' => true,
                ),

              'unsets' => $this->_unsets,
            ),
        );

        include LIBS_PATH . "utils/TableHelper.class.php";
        $helper = new \TableHelper(MOD_NAME, $this->_view, $this->_controller);
        $helper->create($params);
        // 默认是当前模板下的view目录
        $this->_view->display(  "../../../Common/Tpl/default/one2many.html");
    }

    public function _add() {
        $one_id  = $this->_controller->getRequest('one_id', 0, true);
        $many_id = $this->_get_many_ids();

        $this->_add_many($one_id, $many_id);
        $this->_controller->success("添加成功");
    }

    public function _remove() {
        $one_id  = $this->_controller->getRequest('one_id', 0, true);
        $many_id = $this->_get_many_ids();

        if ($this->_remove_many($one_id, $many_id)) {
            $this->_controller->success("移除成功");
        } else {
            $this->_controller->success("移除失败");
        }
    }

    private function _get_mod_caption($modname) {
        $config = $this->_controller->getTableConfig($modname);
        return isset($config['caption'])? $config['caption']:"";
    }

    private function _get_many_ids() {
        $many_ids = $this->_controller->getRequest('many_id', 0, true);
        if (is_string($many_ids)) {
            $many_ids = explode(",", $many_ids);
        }

        if (is_numeric($many_ids)) {
            $many_ids = array($many_ids);
        }
        return $many_ids;
    }

    // 添加指令到指令集
    public function _add_many($one_id, $many_ids) {
        $model = $this->_get_model();
        $model->setTableName($this->_one ."_" .$this->_many);

        $data = array(
            $this->_one  . '_id' => $one_id,
            $this->_many . '_id' => 0,
        );
        foreach($many_ids as $many_id) {
            $data[$this->_many.'_id'] = $many_id;
            $model->add($data);
        }

        $model->setTableName($this->_one);
        return true;
    }

    // 移除指令从指令集
    public function _remove_many($one_id, $many_ids) {
        $model = $this->_get_model();
        $model->setTableName($this->_one ."_" .$this->_many);

        $options = array(
            'where' => array(
                $this->_one . "_id" => $one_id,
                $this->_many. "_id" => array('in', $many_ids),
            )
        );
        $result = $model->delete($options);

        $model->setTableName($this->_one);
        return $result;
    }
}