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


    /**
     * 获取订单
     */
    public function getOrder($condition = array(),$fields = '*',$order = array()){
        $order_info = $this->field($fields)->where($condition)->order($order)->find();
        if(empty($order_info)){
            return array();
        }

        return $order_info;
    }


}










