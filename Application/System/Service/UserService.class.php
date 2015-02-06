<?php
namespace System\Service;

/**
 * AdminService
 */
class UserService extends \Libs\Framework\Service {
    protected $_name = 'user'; // 规则命名可以不写  前缀+表名+Service

    /**
     * 添加用户
     * @param  array $user 用户信息
     * @return array
     */
    public function add($data, $option=array()) {
        if (false === ($data = $this->_model->create($data))) {
            return $this->error($this->_model->getError());
        }

        unset($data['cfm_password']);
        $user_id = parent::_add($data, $option);
        if ($user_id === false) {
            return $this->error("系统出错了, DB:". $this->_model->getError());
        }
        return $this->success();
    }

    /**
     * 更新用户信息
     * @return
     */
    public function save($data, $option=array()) {
        if (false === ($data = $this->_model->create($data))) {
            return $this->error($this->_model->getError());
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['cfm_password']);

        if (false === parent::_save($data, $option)) {
            return $this->error("系统出错了, DB:". $this->_model->getDbError());
        }

        return $this->success();
    }

    /**
     * 用户登录认证
     * @param  array $data 用户信息
     * @return array
     */
    public function login($username, $password) {
        $data = $this->_model->getByName($username);
        if ($data === false) {
            return $this->error('用户名错误！');
        }
        // 密码验证
        if ($data['password'] != $this->encrypt($password)) {
            return $this->error('密码不正确！');
        }

        if ($data['status'] == 0) {
            return $this->error('账户已被禁用！');
        }

        // 生成登录session
        $_SESSION[C('USER_AUTH_KEY')] = $data['id'];

        $time = time();
        $_SESSION['user'] = $data;
        $_SESSION['login_time'] = $time;
        if ($data['is_super'] == 1) { // 超级用户无需认证
            $_SESSION[C('ADMIN_AUTH_KEY')] = true;
        }

        // 缓存访问权限
        if (C('USER_AUTH_ON')) {
            \Org\Util\Rbac::saveAccessList();
        }

        // 更新最后登录时间
        $this->_model->where("id={$data['id']}")->save(array('last_login' => date("Y-m-d H:i:s")));

        return $this->success(true);
    }

    /**
     * 用户登出
     * @return
     */
    public function logout() {
        unset($_SESSION[C('USER_AUTH_KEY')]);
        unset($_SESSION['user']);
        session_destroy();
    }

    /**
     * 检查登录状态
     * @return bool
     */
    public function checkLogin() {
        // 是否已登录
        if ( !isset($_SESSION[C('USER_AUTH_KEY')]) ) {
            return $this->error('尚未登录，请先进行登录！');
        }

        // 是否登录超时
        if (time() > ($_SESSION['login_time'] + C('LOGIN_TIMEOUT'))) {
            return $this->error('登录超时，请重新登录！');
        }
        // 更新登录时间
        $_SESSION['login_time'] = time();

        return $this->success(true);
    }

    /**
     * 加密数据
     * @param  string $str 需要加密的数据
     * @return string
     */
    public function encrypt($str) {
        return md5(C('AUTH_MASK') . md5($str));
    }
}
