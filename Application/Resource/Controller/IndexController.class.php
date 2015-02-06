<?php
namespace Resource\Controller;


/**
 * IndexController
 * 系统信息管理
 */
class IndexController extends \Libs\Framework\Controller {

    public function index(){

        ;

        try {

            $client = client_factory(array("host" => "10.14.2.45", "port" => 22), 1);
            $client->login("wangxianbin", "wangxb2376");
            echo $client->exec("ls");
            $client->disconnect();
        } catch(Exception $e) {
            die($e->getMessage());
        }
exit;
        include LIBS_PATH . "utils/Cryption.php";
        //$str = \Cryption::encode("3sdfsdfs3fsdfs3fsdf3sdfsdfsfsdfsd");
        $str = "VwgHVwBSBQJSAwFQAgZSD1RUBwgBUwEFA1QBUQ8AUANsCgcPDw0HagMGAQ==";
        $str = \Cryption::decode($str);
        var_dump($str);
        //$this->display();
    }
}
