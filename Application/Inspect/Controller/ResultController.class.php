<?php
namespace Inspect\Controller;


/**
 * ResultController
 * 结果管理
 */
class ResultController extends \Libs\Framework\Controller {
    public function detail() {
        $id = $this->getRequest("id", 0, true);
        $data = $this->_get_service()->getDataById($id,"result");  
        if (false === $data) {
            $this->error("没有这个结果");
        }
        
        $result = str_replace(array("\n", "\t", " "), array("<br/>", "&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;"), $data["result"]); 
        $this->success($result,'', array('title' => '详细结果') );
    }
}
