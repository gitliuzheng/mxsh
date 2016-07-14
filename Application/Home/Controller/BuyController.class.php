<?php
namespace Home\Controller;
use Think\Controller;

class BuyController extends CommonController {

    /**
     * 虚拟商品购买第一步
     */
    public function buy_step1(){
        $result = $this->getBuyStepData($_GET['goods_id'], $_GET['quantity'],$_SESSION['member_id']);
        if (!$result['state']) {
            $this->error($result['msg']);
        }

        $this->assign('goods_info',$result['data']['goods_info']);
        $this->assign('store_info',$result['data']['store_info']);
        $this->assign('member_info',$result['data']['member_info']);
        $this->display();
    }


    /**
     * 虚拟商品购买第二步
     */
    public function buy_step2(){
        $_POST['order_from'] = 1;

        $result1 = $this->buyStep2($_POST,$_SESSION['member_id']);
        if (!$result1['state']) {
            $this->error($result1['msg']);
        }
        //转向到商城支付页面
        echo "<script>window.location.href='/index.php/Home/Buy/pay_index'</script>";

    }

    //显示支付页面
    public function pay_index(){
        $this->display("buy_step2");
    }




    /**
     * 得到虚拟商品购买数据(商品、店铺、会员)
     * @param int $goods_id 商品ID
     * @param int $quantity 购买数量
     * @param int $member_id 会员ID
     * @return array
     */
    public function getBuyStepData($goods_id, $quantity, $member_id) {
        $model_VrGoods = D("VrGoods");
        $goods_info = $model_VrGoods->getVirtualGoodsOnlineInfoByID($goods_id);
        if(empty($goods_info)){
            return $this->callback(false,'该商品不符合购买条件，可能的原因有：下架、不存在、过期等');
        }

        //购买上限
        if ($goods_info['virtual_limit'] > $goods_info['goods_storage']) {
            $goods_info['virtual_limit'] = $goods_info['goods_storage'];
        }

        //取得抢购信息 ,先不做，放着
        //$goods_info = $this->_getGroupbuyInfo($goods_info);
        $quantity = abs(intval($quantity));
        $quantity = $quantity == 0 ? 1 : $quantity;
        $quantity = $quantity > $goods_info['virtual_limit'] ? $goods_info['virtual_limit'] : $quantity;
        if ($quantity > $goods_info['goods_storage']) {
            return $this->callback(false,'该商品库存不足');
        }

        $goods_info['quantity'] = $quantity;
        $goods_info['goods_total'] = ncPriceFormat($goods_info['goods_price'] * $goods_info['quantity']);
        $goods_info['goods_image_url'] = '暂时不会';

        $return = array();
        $return['goods_info'] = $goods_info;

        $model_store = D('Store');
        $return['store_info'] = $model_store->getStoreOnlineInfoByID($goods_info['store_id'],'store_name,store_id,member_id');


        $model_member = D('Member');
        $return['member_info'] = $model_member->getMemberInfoByID($member_id);

        return $this->callback(true,'',$return);
    }




    /**
     * 虚拟商品购买第二步
     * @param array $post 接收POST数据，必须传入goods_id:商品ID，quantity:购买数量,buyer_phone:接收手机,buyer_msg:买家留言
     * @param int $member_id
     * @return array
     */
    public function buyStep2($post, $member_id) {
        $result = $this->getBuyStepData($post['goods_id'], $post['quantity'], $member_id);
        if (!$result['state']) return $result;

        $goods_info = $result['data']['goods_info'];
        $member_info = $result['data']['member_info'];

        //应付总金额计算
        $pay_total = $goods_info['goods_price'] * $goods_info['quantity'];
        $store_id = $goods_info['store_id'];
        $store_goods_total_list = array($store_id => $pay_total);
        $pay_total = $store_goods_total_list[$store_id];

        //整理数据
        $input = array();
        $input['quantity'] = $goods_info['quantity'];
        $input['buyer_phone'] = $post['buyer_phone'];
        $input['buyer_msg'] = $post['buyer_msg'];
        $input['pay_total'] = $pay_total;
        $input['order_from'] = $post['order_from'];

        try {
            $model_VrGoods = D('VrGoods');
            //开始事务
            $model_VrGoods->startTrans();

            //生成订单
            $order_info = $this->_createOrder($input,$goods_info,$member_info);

            /*
            if (!empty($post['password'])) {
                if ($member_info['member_paypwd'] != '' && $member_info['member_paypwd'] == md5($post['password'])) {
                    //充值卡支付
                    if (!empty($post['rcb_pay'])) {
                        $order_info = $this->_rcbPay($order_info, $post, $member_info);
                    }
                    //预存款支付
                    if (!empty($post['pd_pay'])) {
                        $this->_pdPay($order_info, $post, $member_info);
                    }
                }
            }
            */

            //提交事务
            $model_VrGoods->commit();

        }catch (Exception $e){
            //回滚事务
            $model_VrGoods->rollback();
            return $this->callback(false, $e->getMessage());
        }

        //变更库存和销量
        /*QueueClient::push('createOrderUpdateStorage', array($goods_info['goods_id'] => $goods_info['quantity']));*/

        //更新抢购信息
        //$this->_updateGroupBuy($goods_info);

        //发送兑换码到手机
        /*
        $param = array('order_id'=>$order_info['order_id'],'buyer_id'=>$member_id,'buyer_phone'=>$order_info['buyer_phone']);
        QueueClient::push('sendVrCode', $param);
        */

        return $this->callback(true,'',array('order_id' => $order_info['order_id'],'order_sn'=>$order_info['order_sn']));


    }


    /**
     * 生成订单
     * @param array $input 表单数据
     * @param unknown $goods_info 商品数据
     * @param unknown $member_info 会员数据
     * @throws Exception
     * @return array
     */
    private function _createOrder($input, $goods_info, $member_info) {
        extract($input);
        $model_VrOrder = D('VrOrder');
        //存储生成的订单,函数会返回该数组
        $order_list = array();
        $order = array();
        $order_code = array();

        $order['order_sn'] = $this->_makeOrderSn($member_info['member_id']);
        $order['store_id'] = $goods_info['store_id'];
        $order['store_name'] = $goods_info['store_name'];
        $order['buyer_id'] = $member_info['member_id'];
        $order['buyer_name'] = $member_info['member_name'];
        $order['buyer_phone'] = $input['buyer_phone'];
        $order['buyer_msg'] = $input['buyer_msg'];
        $order['add_time'] = time();
        $order['order_state'] = 10;
        $order['order_amount'] = $pay_total;
        $order['goods_id'] = $goods_info['goods_id'];
        $order['goods_name'] = $goods_info['goods_name'];
        $order['goods_price'] = $goods_info['goods_price'];
        $order['goods_num'] = $input['quantity'];
        $order['goods_image'] = $goods_info['goods_image'];
        $order['commis_rate'] = 200;
        $order['gc_id'] = $goods_info['gc_id'];
        $order['vr_indate'] = $goods_info['virtual_indate'];
        $order['vr_invalid_refund'] = $goods_info['virtual_invalid_refund'];
        $order['order_from'] = $input['order_from'];
        if ($goods_info['ifgroupbuy'] == 1) {
            $order['order_promotion_type'] = 1;
            $order['promotions_id'] = $goods_info['groupbuy_id'];
        }


        $order_id = $model_VrOrder->addOrder($order);


        if (!$order_id) {
            return '订单保存失败';
        }

        $order['order_id'] = $order_id;


        // 提醒[库存报警]
        /*
        if ($goods_info['goods_storage_alarm'] >= ($goods_info['goods_storage'] - $input['quantity'])) {
            $param = array();
            $param['common_id'] = $goods_info['goods_commonid'];
            $param['sku_id'] = $goods_info['goods_id'];
            QueueClient::push('sendStoreMsg', array('code' => 'goods_storage_alarm', 'store_id' => $goods_info['store_id'], 'param' => $param));
        }
        */

        return $order;
    }


    /**
     * 规范数据返回函数
     * @param unknown $state
     * @param unknown $msg
     * @param unknown $data
     * @return multitype:unknown
     */
    function callback($state = true, $msg = '', $data = array()) {
        return array('state' => $state, 'msg' => $msg, 'data' => $data);
    }


    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    private function _makeOrderSn($member_id) {
        return mt_rand(10,99)
        . sprintf('%010d',time() - 946656000)
        . sprintf('%03d', (float) microtime() * 1000)
        . sprintf('%03d', (int) $member_id % 1000);
    }

}