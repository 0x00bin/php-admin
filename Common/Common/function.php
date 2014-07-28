<?php
include('_init.php');

/**
 * Ϊ��ǰ���ڲ˵�����ʽ
 * @param  string $controller_name
 * @param  string $action_name
 * @param  string $style
 * @return string
 */
function activedLink($controller_name, $action_name, $style) {
    if (isset($action_name)
        && (false !== strpos($controller_name, CONTROLLER_NAME))
        && ACTION_NAME == $action_name) {
        return $style;
    }

    if (!isset($action_name)
        && (false !== strpos($controller_name, CONTROLLER_NAME))) {
        return $style;
    }

    return '';
}
