<?php

// ��������
class ConnectionType {
    const SSH2   = 1;
    const Telnet = 2;
    const Mysql  = 3;
};

// ����״̬
class TaskStatus {
    const Normal = 0; // ����
    const Stop   = 1; // ֹͣ
    const Now    = 2; // ����ִ��(��̨����)
};

// ѭ������
class TaskType {
    const Normal = 1; // ��ͨ����
    const Cron   = 2; // ѭ������
    const Timing = 3; // ��ʱ����
};

// ָ������
class CommandType {
    const Single = 1; // ����ָ��
    const Set    = 2; // ָ�
};

// ָ������
class TaskDeviceType {
    const Single = 1; // �����豸
    const Group  = 2; // ���豸
};
