<?php
namespace Console\Controller;


/**
 * TaskController
 * 任务管理
 */
class TaskController extends \Think\Controller {
    private $_time = 0;
    protected function _initialize() {
        $this->_time = time();
    }

    /**
     * 执行任务
     */
    public function run() {
        \Think\Log::write("Task::run start.", \Think\Log::INFO);
        $where  = "`status` != ".\TaskStatus::Stop;
        $where .= " AND (`type`=".\TaskType::Cron;
        $where .= " OR `status` = ". \TaskStatus::Now;

        $where .= " OR (`type` =".\TaskType::Timing;
        $where .= " AND  HOUR(`time`) = HOUR(NOW()) AND MINUTE(`time`) = MINUTE(NOW())";
        $where .= "))";
        //var_dump($where);
        $service = D("Inspect/Task", "Service");
        $tasks = $service->getDatasByWhere($where, "*,DATE_FORMAT(  `time`, '%H:%i:%s' )");
        //var_dump($tasks);
        if (empty($tasks)) {
            \Think\Log::write("task is noting.", \Think\Log::INFO );
            return;
        }
        include_once LIBS_PATH . "CommandExecutor.class.php";
        $executor = new \CommandExecutor;
        $data = array(
            'id'     => 0,
            'status' => \TaskStatus::Normal,
        );

        foreach($tasks as $i => $task) {
            if (!$this->_is_timing($task)) {
                continue;
            }
            \Think\Log::write("Task::run exec task[{$task['id']}]", \Think\Log::INFO);
            if ( false === $executor->execTask($task) ) {
                \Think\Log::write("Task::run task[{$task['id']}] error:".$executor->getError(), \Think\Log::ERR);
            }

            if ($task['status'] == \TaskStatus::Now) {
                $data['id'] = $task['id'];
                $service->save($data);
            }
        }

        \Think\Log::write("Task::run end.", \Think\Log::INFO);
    }

    public function _check_time($time) {
        return time() % $time < 120;
    }

    private function _is_timing(&$task) {
        if ($task['status'] == \TaskStatus::Now) {
            return true;
        }

        if ($task['type']  == \TaskType::Timing) {
            return true;
        }

        if ($task['type']  == \TaskType::Cron) {
            $times = explode(",", $task['time']);
            foreach($times as $time) {
                if ($this->_check_time($time)) {
                    return true;
                } else {
			\Think\Log::write("Task::run task[{$task['id']}] is not timing.", \Think\Log::INFO);
		}
            }
        }

        return false;
    }
}
