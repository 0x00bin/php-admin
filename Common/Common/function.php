<?php

/**
 * 为当前所在菜单项样式
 * @param  string $controller_name
 * @param  string $action_name
 * @param  string $style
 * @return string
 */
function activedLink($controller_name, $action_name, $style) {
    $cname = APP_NAME . '_' . MOD_NAME;
        
    if (isset($action_name)
        && (false !== stripos($controller_name, $cname))
        && strtolower(ACT_NAME) == strtolower($action_name)) {
        return $style;
    }

    if (!isset($action_name)
        && (false !== stripos($controller_name, $cname))) {
        return $style;
    }

    return '';
}

function targetToID($target) {
    return str_replace("/", "_", strtolower($target));
}

/**
 * 解析并执行字符串比较表达式
 * @param string $str 比较表达式 e.g. level < 3
 * @param array  $data 比较用的参数
 * @return bool
 */
function strcompare($str, $data) {
    $expr = explode(" ", $str);
    if (!isset($data[$expr[0]])) {
        return false;
    }
    if (count($expr) != 3) {
        return false;
    }
    $value = $data[$expr[0]];

    switch($expr[1]) {
    case "<":
        return $value < $expr[2];
    case ">":
        return $value > $expr[2];
    case ">=":
        return $value >= $expr[2];
    case "<=":
        return $value <= $expr[2];
    case "==":
        return $value == $expr[2];
    default:
        return false;
    }
    return false;
}

function cryption_encode($str) {
    include_once LIBS_PATH . "utils/Cryption.php";
    return \Cryption::encode($str);
}

function cryption_decode($str) {
    include_once LIBS_PATH . "utils/Cryption.php";
    return \Cryption::decode($str);
}

function client_factory($params, $type) {
    $client = null;
    $type_name = "";
    if ($type == ConnectionType::SSH2) {
        include_once LIBS_PATH . "client/SSH2.php";
        $type_name = "ssh2";	
        $client = new \SSH2($params["host"], $params["port"]);
    } else if($type == ConnectionType::Telnet) {
        include_once LIBS_PATH . "client/Telnet.php";
        $type_name = "telnet";	
        $client = new \Telnet($params["host"], $params["port"]);
    } else if($type == ConnectionType::Mysql) {
        include_once LIBS_PATH . "client/Mysql.php";
        $type_name = "mysql";	
        $client = new \Mysql($params);
    }
    \Think\Log::write("client type is {$type_name}.", \Think\Log::INFO);
    return $client;
}
