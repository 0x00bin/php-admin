<?php

// 连接类型
class ConnectionType {
    const SSH2   = 1;
    const Telnet = 2;
    const Mysql  = 3;
};

// 任务状态
class TaskStatus {
    const Normal = 0; // 正常
    const Stop   = 1; // 停止
    const Now    = 2; // 马上执行(后台队列)
};

// 循环类型
class TaskType {
    const Normal = 1; // 普通任务
    const Cron   = 2; // 循环任务
    const Timing = 3; // 定时任务
};

// 指令类型
class CommandType {
    const Single = 1; // 单个指令
    const Set    = 2; // 指令集
};

// 指令类型
class TaskDeviceType {
    const Single = 1; // 单个设备
    const Group  = 2; // 组设备
};
