<?php

// 定义自用const
define("APP_NAME",  MODULE_NAME);
define("MOD_NAME",  CONTROLLER_NAME);
define("ACT_NAME",  ACTION_NAME);

include (COMMON_PATH . "Conf/config.consts.php");
include (COMMON_PATH . "Common/function.php");

// 载入扩展框架库
include (COMMON_PATH . "Libs/Framework/Controller.class.php");
include (COMMON_PATH . "Libs/Framework/Model.class.php");
include (COMMON_PATH . "Libs/Framework/Service.class.php");
