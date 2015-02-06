<?php
namespace System\Service;

/**
 * RoleService
 */
class RoleService extends \Libs\Framework\Service {


    /**
     * 分配角色权限
     * @param  int   $roleId 角色id
     * @param  array $access 权限访问数组
     * @return array
     */
    public function assignAccess($roleId, array $access) {
        $Access = M('Access');

        $Access->startTrans();
        $Access->where("role_id={$roleId}")->delete();
        if (0 === count($access)) {
            $Access->commit();
            return $this->message('清除数据成功！');
        }

        $newAccess = array();
        foreach ($access as $item) {
            $item = explode(':', $item);
            $newAccess[] = array('role_id' => $roleId, 'node_id' => $item[0]);
        }

        // 插入新权限
        if (false === $Access->addAll($newAccess)) {
            $Access->rollback();
            return $this->error('分配权限失败！');
        }

        $Access->commit();
        return $this->success(true);
    }

    /**
     * 得到子角色的id
     * @param  int   $id 角色id
     * @return array
     */
    public function getSonRoleIds($id) {
        $sRole = $this->getM()->field('id')->where("pid={$id}")->select();
        $sids = array();

        if (is_null($sRole)) {
            return $sids;
        }

        foreach ($sRole as $sRole) {
            $sids[] = $sRole['id'];
            $sids = array_merge($sids, $this->getSonRoleIds($sRole['id']));
        }

        return $sids;
    }
}
