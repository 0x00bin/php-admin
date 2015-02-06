<?php
namespace System\Controller;

/**
 * PublicController
 * 公开页面访问接口
 */
class PublicController extends \Libs\Framework\Controller {

   /**
    * 初始化
    * @return
    */
    public function _initialize() {
        // 登录后不可访问的action
        $filterAction = array('index', 'login');
        if (in_array(ACTION_NAME, $filterAction) && $this->isLogin()) {
            return $this->redirect('/');
        }
    }

    /**
     * 管理员登录页
     * @return
     */
    public function index() {
        layout(false);
        $this->display();
    }

    /**
     * 管理员登录
     * @return
     */
    public function login() {
        $url = U('Public/index');
        $username = $this->getRequest('username', null, true);
        $password = $this->getRequest('password', null, true);
        if (empty($username)) {
            return $this->error('请填写用户名！', $url);
        }

        if (empty($password)) {
            return $this->error('请填写登录密码！', $url);
        }

        /*$Verify = new \Think\Verify();
        if (!$Verify->check($_POST['verify_code'])) {
            return $this->error('验证码不正确！');
        }*/

        $service = D('System/User', 'Service');
        // 登录认证
        if (!$service->login($username, $password) ) {
            return $this->error($service->getError());
        }

        return $this->success('登录成功！', $this->getBackurl(U('System/Index/index')));
    }

    /**
     * 管理员登出
     * @return
     */
    public function logout() {
        D('System/User', 'Service')->logout();

        $this->success('登出成功！', U('System/Public/index'));
    }


    /**
     * 验证码图片
     * @return
     */
    public function verifyCode() {
        $config = array(
            'imageW' => 85,
            'imageH' => 30,
            'fontSize' => 12,
            'length' => 4,
            'useNoise' => false,
            'codeSet' => '0123456789'
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    public function test() {
        $service = D("Inspect/Mytest", "Service");
        $datas = $service->getDatasByWhere("1");
        var_dump($datas);
    }
}
