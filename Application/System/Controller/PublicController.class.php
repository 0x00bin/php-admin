<?php
namespace System\Controller;

/**
 * PublicController
 * 公开页面访问接口
 */
class PublicController extends \Libs\Framework\Controller {
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

        $user_service = D('User', 'Service');
        // 登录认证
        if (!$user_service->login($username, $password) ) {
            return $this->error($user_service->getError());
        }

        return $this->success('登录成功！', U('Index/index'));
    }

    /**
     * 管理员登出
     * @return
     */
    public function logout() {
        D('User', 'Service')->logout();

        $this->success('登出成功！', U('Public/index'));
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
