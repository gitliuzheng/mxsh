<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model {
    /**
     * 插入订单支付表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrderPay($data) {
        $model_order_pay = M("Order_pay");
        $res = $model_order_pay->data($data)->add();
        return $res;
    }


    /**
     * 插入订单表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrder($data) {
        $insert = $this->data($data)->add();
        return $insert;
    }
}










