<?php
namespace Inspect\Controller;


/**
 * IndexController
 * 系统信息管理
 */
class IndexController extends \Libs\Framework\Controller {
    public function index(){
        $service = D("System/User", "Service");
        $datas = $service->getDatasByWhere("1");
        var_dump($datas);
        \Think\Log::write("testmsg");
    }
}
