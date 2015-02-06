<?php

class CommandExecutor {
    private $_task     = array();
    private $_commands = array(); //command_id => command;
    private $_set2command_ids = array(); // set_id => command_ids
    private $_error = '';
    private $_devices = array(); // device_id => device;
    public function __construct() {

    }

    public function getError() {
        return $this->_error;
    }

    private function error($msg) {
        $this->_error = $msg;
        return false;
    }

    public function execTask(array &$task) {
        $this->_task = &$task;
        if (TaskDeviceType::Single == $task['device_type']) {
            return $this->execTaskDevice($this->_task['device_id']);
        }
        $devices = D("Resource/GroupDevice", "Service")->getDatasByWhere("group_id=".$this->_task['device_id']);
        if (empty($devices)) {
            return $this->error("devices is empty.");
        }
        foreach($devices as $device) {
            if ( false === $this->execTaskDevice($device['device_id']) ) {
                continue;
            }
        }
        return true;
    }

    protected function execTaskDevice($device_id) {
        $client = $this->connectDevice($device_id);
        if ($client === false) {
            $this->_fail_result($this->_task['id'], $this->getError(), $device_id);
            return false;
        }

        try {
            $commands = $this->_get_commands();
            foreach($commands as $command) {
                try {
                    $result = $client->exec($command['content']);
                    $this->_process_result($this->_task['id'], $device_id, $command['id'], $result);
                }catch(Exception $e) {
                    $this->_fail_result($this->_task['id'], $e->getMessage(), $device_id, $command['id']);
                }
            }

            $client->disconnect();
        } catch(Exception $e) {
            $message = $e->getMessage();
            $this->_fail_result($this->_task['id'], $message, $device_id);
            return $this->error($e->getMessage());
        }
        return true;
    }

    private function _get_device($device_id) {
        if (!isset($this->_devices[$device_id])) {
            $this->_devices[$device_id] = D("Resource/Device", "Service")->getDataById($device_id);
        }

        return $this->_devices[$device_id];
    }

    public function connectDevice($device_id) {
        $device = $this->_get_device($device_id);
        if (empty($device)) {
            return $this->error("device[{$device_id}] not exists.");
        }
        include_once LIBS_PATH . "utils/Cryption.php";
        $device['pass'] = \Cryption::decode($device['pass']);

        try {
            $client = client_factory($device, $device["conn_type"]);
            $client->login($device["user"], $device['pass']);

            if ($device["conn_type"] != ConnectionType::Mysql &&
                $device["su_user"] != "" && $device["su_pass"] != "") {
                $client->su($device["su_user"], $device['pass']);
            }

            return $client;
        } catch(Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->error("unknow error");
    }

    private function _process_result($task_id, $device_id, $command_id, &$result) {
        $regexs = D("Resource/Regex", "Service")->getDatasByWhere("command_id=".$command_id);
        $data = array(
            'task_id'    => $task_id,
            'device_id'  => $device_id,
            'command_id' => $command_id,
            'result'     => $result,
        );

        if ( is_array($regexs) ) {
            $briefs  = array();
            $matches = array();
            if (is_array($result)) { // mysql
                $data['result'] = json_encode($result);
                foreach($result as $key => $value) {
                    $matches[$value['Variable_name']] = $value['Value'];
                }
            }

            foreach($regexs as $regex) {
                if ( empty($regex['expression']) ) {
                    continue;
                }

                if ( !empty($regex['regex']) ) {
                    $matches = array();
                    preg_match($regex['regex'], $result, $matches);
                }
                $code = str_replace("_[", '$matches[', $regex['expression']);

                eval( '$ret ='.$code.';');
                if ($ret === true) {
                    $briefs[] = $regex['tips'];
                } else {
                    $briefs[] = "normal";
                }
            }
            $data["brief_result"] = implode(",", array_unique($briefs));
        } // end is_array($regexs)

        $device = $this->_get_device($device_id);
        $data['host'] = $device['host'];

        D("Inspect/Result", "Service")->add($data);
    }

    private function _fail_result($task_id, $result, $device_id=0, $command_id=0) {
        $data = array(
            'task_id'    => $task_id,
            'device_id'  => $device_id,
            'command_id' => $command_id,
            'result'     => $result,
            'brief_result' => $result,
            'host' => '',
        );
        if ($device_id != 0) {
            $device = $this->_get_device($device_id);
            $data['host'] = isset($device['host'])? $device['host']:"";
        }
        D("Inspect/Result", "Service")->add($data);
    }

    private function _get_command_ids_by_set_id($set_id) {
        if (!isset($this->_set2command_ids[$set_id])) {
            $datas = D("Resource/SetCommand", "Service")->getDatasByWhere("set_id=".$set_id);
            $command_ids = array();
            if (!empty($datas)) {
                foreach($datas as $data) {
                    $command_ids[] = $data["command_id"];
                }
            }
            $this->_set2command_ids[$set_id] = $command_ids;
        }
        return $this->_set2command_ids[$set_id];
    }

    // TODO 放入内存以备下次使用
    private function _get_commands() {
         $command_ids = array();
        if ($this->_task['command_type'] == CommandType::Set) {
            $command_ids = $this->_get_command_ids_by_set_id($this->_task['command_id']);
        } else {
            $command_ids[] = $this->_task['command_id'];
        }

        if (empty($command_ids)) {
            throw new Exception("task command is empty.");
        }

        return $this->_get_commands_by_command_ids($command_ids);
    }

    private function _get_commands_by_command_ids($command_ids) {
        $commands = array();
        $noexists = array();
        foreach($command_ids as $command_id) {
            if (isset($this->_commands[$command_id])) {
                $commands[] = $this->_commands[$command_id];
            } else {
                $noexists[] = $command_id;
            }
        }
        if (!empty($noexists)) {
            $datas = $this->_load_commands_by_command_ids($noexists);
            if (!empty($datas)) {
                $commands = array_merge($commands, $datas);
            }
        }
        return $commands;
    }
    // load commands from db
    private function _load_commands_by_command_ids($command_ids) {
        $datas = D("Resource/Command", "Service")->getDatasByWhere("id IN(".implode(",", $command_ids).")");
        if (empty($datas) ) return false;
        foreach($datas as $data) {
            $this->_commands[$data['id']] = $data;
        }
        return $datas;
    }

    public function execCommand($device_id, $command) {
        $client = $this->connectDevice($device_id);
        if ($client === false) {
            return false;
        }

        try {
            $result = $client->exec($command);
            $client->disconnect();
        } catch(Exception $e) {
            return $this->error($e->getMessage());
        }

        return $result;
    }
}
