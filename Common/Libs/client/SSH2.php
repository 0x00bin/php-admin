<?php


class SSH2 {
    private $host = '127.0.0.1';
    private $port = 22;
    private $socket = null;
    private $shell = null;
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
        if (!$result){
            throw new Exception("Login failed.");
        }

        return true;
    }

	public function disconnect() {
        if (!is_null($this->shell)) {
            fclose($this->shell);
        }
    	return true;
    }

    /**
     * @param string $command Command to execute
     * @return string Command result
     */
	public function exec($command) {
        if ( !is_null($this->shell) ) {
            return $this->_shell_exec($command);
        }
        $stdout_stream = ssh2_exec($this->socket, $command);

        $err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
        $dio_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDIO);
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

    private function _match_command_result(&$command, &$output) {
        $pos = stripos($output, $command);
        if ($pos === false) {
            throw new Exception("shell exec: match command result error.".var_export($output, true));
        }
        $prompt = substr($output, 0, $pos);
        $start  = $pos + strlen($command);
        $end    = stripos($output, $prompt, $pos);
        $result = substr($output,$start , $end - $start);

        return trim($result);
    }

    private function _shell_exec(&$command) {
        if ($this->_shell_write_command($this->shell, $command) === false) {
            return false;
        }

        $line   = "";
        $output = "";
        $return_code = 1;
        while ( ($char = fgetc($this->shell)) !== false ) {
            $line .= $char;
            if ($char == "\n") {
                $output .= $line;
                $return_code = $this->_check_result_code($line);
                if ( $return_code !== false) {
                    break;
                }
                $line = "";
            }
        }

        $result = $this->_match_command_result($command, $output);
        // TODO 获取返回值和返回内容
        if  ( $return_code != 0 ) {
            throw new Exception($result);
        }
        return $result;
    }

    private function _shell_write_command($stream, $command) {
        return fputs($stream, "{$command}\necho [end] $?\n");
    }

    private function _check_result_code(&$line) {
        if (preg_match("/\[end\]\s*([0-9]+)/", $line, $matches)) {
            // End of command detected.
            return $matches[1];
        }
        return false;
    }

    public function su($username, $password) {
        if (is_null($this->socket)) {
            return false;
        }
        $stream = ssh2_shell($this->socket, "xtream", null, 200);
        if ($stream === false) {
            return false;
        }

        $this->shell = $stream;
        stream_set_blocking($stream, true);

        if (fputs($this->shell, "su $username -\n") === false) {
            return false;
        }

        $line = "";
        $output = "";
        $return_code = 1;
        while ( ($char = fgetc($stream)) !== false ) {
            $line .= $char;
            if ($char != "\n") {
                if (stripos($line, "密码：") !== false || stripos($line, "Password:") !== false) {
                    // Password prompt.
                    if ($this->_shell_write_command($stream, $password) === false) {
                        return false;
                    }
                    $line = "";
                }
                else if (stripos($line, "密码不正确") !== false || stripos($line, "incorrect") !== false) {
                    throw new Exception("su: password incorrect.");
                    return false;
                }
            }
            else {
                $output .= $line;
                $return_code = $this->_check_result_code($line);
                if ( $return_code !== false) {
                    break;
                }
                $line = "";
            }
        }

        return ($return_code == 0);
    }
}
