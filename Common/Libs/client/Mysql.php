<?php


class Mysql {
    private $db = null;

    public function __construct($params) {
        $config = array(
            'hostname' => $params['host'],
            'username' => $params['user'],
            'password' => $params['pass'],
            'database' => "",
            'charset'  => 'utf8',
            'hostport' => $params['port'],
        );
        $class = 'Think\\Db\\Driver\\Mysqli';

        $this->db = new $class($config);
    }

    public function connect() {
        return true;
    }

    public function login($username, $password) {
        return true;
    }

	public function disconnect() {
	    $this->db->close();
	}

	/**
	 * @param string $command Command to execute
	 * @return string Command result
	 */
	public function exec($command) {
	    $result = $this->db->query($command);
	    if ($result === false) {
	        throw new Exception($this->db->getError());
	    }
	    return $result;
	}
}