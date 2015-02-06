<?php


class SSH2 {
    private $host = '127.0.0.1';
    private $port = 22;
    private $socket = null;

    public function __construct($host = '127.0.0.1', $port = 22, $timeout = 10, $prompt = '', $stream_timeout = 1) {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    public function connect() {
        $this->socket = ssh2_connect($this->host, $this->port);
        if (!$this->socket) {
            throw new Exception("Cannot connect to $this->host on port $this->port");
        }
        return true;
    }

    public function login($username, $password) {
        $result = ssh2_auth_password($this->socket, $username, $password);
         var_dump($this->socket);
        var_dump($result);
        if (!$result){
            throw new Exception("Login failed.");
        } else {
            echo "Authentication Successful!\n";
        }
        return true;
    }

	public function disconnect() {
		return true;
	}

	/**
	 * @param string $command Command to execute
	 * @return string Command result
	 */
	public function exec($command) {
		$stdout_stream = ssh2_exec($this->socket, $command);

        $err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
        $dio_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDDIO);
        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);

        $result_err = stream_get_contents($err_stream);
        $result_dio = stream_get_contents($dio_stream);
        fclose($stdout_stream);

        if ($result_err) {
            throw new Exception("exec error:".$result_err);
        }

        return $result_dio;
	}
}