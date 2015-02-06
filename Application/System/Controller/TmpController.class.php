<?php
namespace System\Controller;


/**
 * IndexController
 * 系统信息管理
 */
class TmpController extends \Libs\Framework\Controller {

    private $_node_api = null; // system.NodeApi
    private $_apps = array(
        'system' => array(
            'title' => '系统管理',
            'ignores' => array( 'PublicController.class.php','TmpController.class.php'),
        ),
        'resource' => array(
            'title' => '资源管理',
            'ignores' => array( 'IndexController.class.php'),
        ),
        'inspect' => array(
            'title' => '巡检管理',
            'ignores' => array(),
        ),
    );
    // path or files
    function pathscan() {
        $apps = isset($_REQUEST['apps'])? $_REQUEST['apps']:'';
        if ($apps == '') {
            echo "param:apps empty!";
            exit;
        }
        $apps = explode(",", $apps);
        foreach($apps as $app) {
            if ( isset($this->_apps[$app]) ) {
                $result[$app] = $this->_apps[$app];
            }
        }
        $this->_apps_scan($result);
        exit;
    }

    private function _get_files($app) {
        $files = isset($_REQUEST[$app])? $_REQUEST[$app]:'';
        if ($files == '') {
           return array();
        }
        $files = explode(",", $files);
        foreach($files as $i => $file) {
            $files[$i] = ucfirst($file)."Controller.class.php";
        }
        return $files;
    }

    private function _apps_scan($apps) {
        //import("libs.utils.NodeScanner");
        include LIBS_PATH . "utils/NodeScanner.class.php";

        $scanner = new \NodeScanner();
        $results = array();
        foreach($apps as $app => $params) {
            $path = APP_PATH .$app.'/Controller';

            $scanner->setApp($app);
            $files = $this->_get_files($app);
            if ( empty($files) ) {
                $scanner->setMode(\NodeScanner::MODE_DIR);
            }else {
                $scanner->setMode(\NodeScanner::MODE_FILE);
            }
            if ( isset($params['ignores']) ) {
                $scanner->setIgnores($params['ignores']);
            }
            $results[$app] = $scanner->scanning($path, $files);
            $scanner->clear();
        }

        //var_dump($results);
        $this->_insert_nodes($results);
    }
    private function _log($msg) {
        echo "$msg\n";
    }
    function allscan() {
        $this->_apps_scan($this->_apps);
        exit;
    }

    private function _insert_nodes($results) {
        $this->_log('insert nodes ...');
        //import("system.Api.NodeApi");
        $this->_node_api = D('System/Node', 'Service');
        foreach($results as $app => $mods) {
            $this->_log("insert $app ...");
            $app_node_id = $this->_insert_app_node($app, $mods);
            foreach($mods as $file => $mod) {
                if ($file == 'app') continue;
                $mod_node_id = $this->_insert_mod_node($mod, $app_node_id);
                foreach($mod['sub'] as $actname => $act) {
                    $this->_insert_act_node($act, $mod_node_id);
                } // end acts
            } // end mods

        } // end results
    }

    private function _insert_app_node($app, $nodes) {
        if ( isset($nodes['app']) ) {

            $data = array (
                'level' => 1,
                'pid'   => 0,
                'name'  => $app,
                'title' => $this->_apps[$app]['title'],
            );
            $this->_log("insert $app node ".var_export($data,true));
            return $this->_node_api->addNode($data);
        } else {
            $data = $this->_node_api->getDataByWhere("name='$app'", 'id');
            if (isset($data['id'])) {
                return $data['id'];
            }
        }
        return 0;
    }

    private function _insert_mod_node($mod, $app_node_id) {
        $data = array (
            'level' => 2,
            'pid'   => $app_node_id,
            'name'  => $mod['name'],
            'title' => $mod['title'],
        );
        $this->_log("insert mod:".$mod['name']." node ".var_export($data,true));
        return $this->_node_api->addNode($data);
    }

     private function _insert_act_node($act, $mod_node_id) {
        $data = array (
            'level' => 3,
            'pid'   => $mod_node_id,
            'name'  => $act['name'],
            'title' => $act['title'],
        );
        $this->_log("insert act:".$act['name']." node ".var_export($data,true));
        return $this->_node_api->addNode($data);
    }

}
