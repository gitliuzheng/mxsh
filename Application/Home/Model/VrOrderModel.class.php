<?php
namespace Home\Model;
use Think\Model;
class VrOrderModel extends Model {
    /**
     * 新增订单
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrder($data) {

        $insert = $this->data($data)->add();
        return $insert;
    }
}










