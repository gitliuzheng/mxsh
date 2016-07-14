<?php
namespace Home\Model;
use Think\Model;
class MemberModel extends Model {
    /**
     * 取得会员详细信息（优先查询缓存）
     * 如果未找到，则缓存所有字段
     * @param int $member_id
     * @param string $field 需要取得的缓存键值, 例如：'*','member_name,member_sex'
     * @return array
     */
    public function getMemberInfoByID($member_id, $fields = '*') {
        $member_info = $this->getMemberInfo(array('member_id'=>$member_id),'*',true);
        return $member_info;
    }


    /**
     * 会员详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getMemberInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }
}   














