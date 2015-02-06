<?php
namespace System\Controller;


/**
 * IndexController
 * 系统信息管理
 */
class IndexController extends \Libs\Framework\Controller {
    /**
     * 网站，服务器基本信息
     * @return
     */
    public function index(){
        $gd = '不支持';
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd['GD Version'];
        }

        $hostport = $_SERVER['SERVER_NAME']
                    ."($_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT])";
        $mysql = function_exists('mysql_close') ? mysql_get_client_info()
                                                : '不支持';
        $info = array(
            'system' => php_uname('s') . " " .php_uname('r'),
            'hostport' => $hostport,
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'php_env' => php_sapi_name(),
            'app_dir' => ROOT_PATH,
            'mysql' => $mysql,
            'gd' => $gd,
            'upload_size' => ini_get('upload_max_filesize'),
            'exec_time' => ini_get('max_execution_time') . '秒',
            'disk_free' => round((@disk_free_space(".")/(1024 * 1024)),2).'M',
            'server_time' => date("Y-n-j H:i:s"),
            'beijing_time' => gmdate("Y-n-j H:i:s", time() + 8 * 3600),
            'reg_gbl' => get_cfg_var("register_globals") == '1'? 'ON' : 'OFF',
            'quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'quotes_runtime' => (1===get_magic_quotes_runtime()) ?'YES' : 'NO',
            'fopen' => ini_get('allow_url_fopen') ? '支持' : '不支持'
        );

        $this->assign('info', $info);
        if (IS_AJAX || !C('PRF_MODE')) {
            $this->display("index");
        } else {
            $this->display("all_index");
        }
    }

    /**
     * 编辑个人密码
     * @return
     */
    public function edit() {
        $this->display('edit_password');
    }

    /**
     * 更新个人密码
     * @return
     */
    public function update() {
        $password = $this->getRequest('password', '', true);
        $cfm_password = $this->getRequest('cfm_password', '', true);
        if (empty($_SESSION['user']['id'])) {
            return $this->error('请先登录');
        }

        $user = $_SESSION['user'];

        if ($password !== $cfm_password) {
            $this->error("两次输入的密码不一致！");
        }
        $data = array(
            'id'       => $user['id'],
            'name'     => $user['name'],
            'password' => $password,
            'cfm_password' => $cfm_password,
            'status' => 1
        );
        $service = D('System/User', 'Service');
        if ( false === $service->save($data)) {
            return $this->error($service->getError());
        }

        return $this->success('修改密码成功！');
    }
}
