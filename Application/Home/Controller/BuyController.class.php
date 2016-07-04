<?php
namespace Home\Controller;
use Think\Controller;

class BuyController extends CommonController {

    /**
     * 会员信息
     */
    private  $_member_info = array();

    /**
     * 表单数据
     */
    private $_post_data = array();


    //提交订单
    public function buyStep2(){
        /*
        $this->_member_info['member_id'] = $member_id;
        $this->_member_info['member_name'] = $member_name;
        $this->_member_info['member_email'] = $member_email;
        $this->_post_data = $post;
        */


        //检查是否登录
//        if(!$this->is_cookie_login){
//            header("http://mxhhw-z.com/shop/index.php?act=login&ref_url=http%3A%2F%2Fmxhhw-z.com%2F");
//            die;
//        }


        //$model = Model('order');

        //第1步 表单验证
        //$this->_createOrderStep1();

        //第2步 得到购买商品信息
        //$this->_createOrderStep2();

        //第3步 得到购买相关金额计算等信息
        //$this->_createOrderStep3();

        //第4步 生成订单
        $this->_createOrderStep4();

        //第5步 处理预存款
        //$this->_createOrderStep5();
        //$model->commit();

        //第6步 订单后续处理
        //$this->_createOrderStep6();



    }

    //得到购买商品信息
    public function _createOrderStep2(){

    }


    //生成订单
    public function _createOrderStep4(){
        $model_order = D("Order");
        $member_id = $this->vr_member_id;
        $member_name = $this->vr_member_name;
        $member_email = $this->vr_member_email;

        $pay_sn = $this->makePaySn($member_id);
        $order_pay = array();
        $order_pay['pay_sn'] = $pay_sn;
        $order_pay['buyer_id'] = $member_id;
        $order_pay_id = $model_order->addOrderPay($order_pay);
        if (!$order_pay_id) {
            die("订单保存失败[未生成支付单]");
        }

        $model_cart = D("Cart");
        $where = array();
        $where['buyer_id'] = $member_id;
        $cart_list = $model_cart->listCart('db',$where);


        $order = array();
        $order_common = array();
        $order_goods = array();
        $order['order_sn'] = $this->makeOrderSn($order_pay_id);
        $order['pay_sn'] = $pay_sn;
        $order['store_id'] = 1;
        $order['store_name'] = '官方店铺';
        $order['buyer_id'] = $member_id;
        $order['buyer_name'] = $member_name;
        $order['buyer_email'] = $member_email;
        $order['add_time'] = time();
        $order['payment_code'] = '';
        $order['order_amount'] = $store_final_order_total[$store_id];
        $order['shipping_fee'] = $store_freight_total[$store_id];
        $order['goods_amount'] = $order['order_amount'] - $order['shipping_fee'];
        $order['order_from'] = $order_from;
        //如果支持方式为空时，默认为货到付款 33hao
        if( $order['payment_code']=="")
        {
            $order['payment_code']="offline";
        }

        foreach($cart_list as $key => $val){

        }


    }



    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id) {
        return mt_rand(10,99)
        . sprintf('%010d',time() - 946656000)
        . sprintf('%03d', (float) microtime() * 1000)
        . sprintf('%03d', (int) $member_id % 1000);
    }


    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_id 支付表自增ID
     * @return string
     */
    public function makeOrderSn($pay_id) {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num ++;
        }
        return (date('y',time()) % 9+1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }
}