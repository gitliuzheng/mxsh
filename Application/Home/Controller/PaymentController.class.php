<?php
namespace Home\Controller;
use Common\CCP_REST_DEMO_PHP_v2_7r\Service\SendTemplateSMS;
use Common\Api\Payment\Alipay\alipay;
/**
 * 虚拟商品订单支付入口 
 * liuzhen 2016-07-08 16:59
 */
class PaymentController extends CommonController {
    const STATE_OPEN = '1'; //支付状态开启标识

  	/*
  	* 虚拟商品购买
  	*/
  	public function vrOrder(){
  	    $order_sn = '230521639088556004';
  	    // $payment_code = 'predeposit';
        $payment_code = 'alipay';

    		if(!preg_match('/^\d{18}$/',$order_sn)){
            $this->error("参数错误",'',1);
        }

        //查询支付信息
        if ('predeposit' != $payment_code){
            $result = $this->getPaymentInfo($payment_code);
            if(!$result['state']) {
                $this->error($result['msg'],'',1);
            }
            $payment_info = $result['data'];
        }

        //计算所需支付金额等支付单信息
        $result = $this->getVrOrderInfo($order_sn, 4);
        if(!$result['state']) {            
            $this->error($result['msg'],'',1);
        }

        if ($result['data']['order_state'] != 10 || empty($result['data']['api_pay_amount'])) {
            $this->error('该订单不需要支付','',1);
        }

        if ('predeposit' != $payment_code){
            //转到第三方API支付
            if($payment_info['payment_code'] == 'alipay'){
                $payment_api = new alipay($payment_info, $result['data']);
                @header("Location: ".$payment_api->get_payurl());
            }
        }else{
            //预存款支付
            $_POST['password'] = '123456';
            if (empty($_POST['password'])){
                return;
            }
            $buyer_info = M('member')->where(array('member_id'=>4))->find();
            if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($_POST['password'])){
                return;
            }

            //使用预存款支付
            $model_vr_order = M('vr_order');
            $order_list = $model_vr_order->where(array("order_sn"=>$order_sn))->find();
            $this->_vr_pdPay($order_list, $buyer_info);
        }
  	}

    /*
    * 取得所使用支付方式信息
    * @param string $payment_code
    */
    public function getPaymentInfo($payment_code){
        if (empty($payment_code)) {
            return callback(false,'系统不支持选定的支付方式');
        }

        $model_payment = M('payment');
        $condition = array();
        $condition['payment_code'] = $payment_code;
        $condition['payment_state'] = self::STATE_OPEN;
        $payment_info = $model_payment->where($condition)->find();
        if(empty($payment_info)) {
            return callback(false,'系统不支持选定的支付方式');
        }

        $payment_info['payment_config'] = unserialize($payment_info['payment_config']);

        return callback(true,'',$payment_info);
    }

	   /**
     * 取得虚拟订单所需支付金额等信息
     * @param int $order_sn
     * @param int $member_id
     * @return array
     */
    public function getVrOrderInfo($order_sn, $member_id = null) {
      	$model_vr_order = M('vr_order');
      	$condition = array();
        $condition['order_sn'] = $order_sn;
        if (!empty($member_id)) {
            $condition['buyer_id'] = $member_id;
        }
        $order_info = $model_vr_order->where($condition)->find();
        if(empty($order_info)){
            return callback(false,'该订单不存在');
        }

        if (isset($order_info['order_state'])) {
            $order_info['state_desc'] = $this->_orderState($order_info['order_state']);
            $order_info['state_desc'] = $order_info['state_desc'][0];
        }
        if (isset($order_info['payment_code'])) {
            $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        }

        $order_info['subject'] = '虚拟订单_'.$order_sn;
        $order_info['order_type'] = 'vr_order';
        $order_info['pay_sn'] = $order_sn;

        // 计算本次需要在线支付的订单总金额        
		    $pay_amount = ncPriceFormat(floatval($order_info['order_amount']) - floatval($order_info['pd_amount']) - floatval($order_info['rcb_amount']));

        $order_info['api_pay_amount'] = $pay_amount;

        return callback(true,'',$order_info);
    }

    /**
     * 取得订单状态文字输出形式
     *
     * @param array $order_info 订单数组
     * @return string $order_state 描述输出
     */
    private function _orderState($order_state) {
        switch ($order_state) {
          case 0:
              $order_state = '<span style="color:#999">已取消</span>';
              $order_state_text = '已取消';
              break;
          case 10:
              $order_state = '<span style="color:#36C">待付款</span>';
              $order_state_text = '待付款';
              break;
          case 20:
              $order_state = '<span style="color:#999">已支付</span>';
              $order_state_text = '已支付';
              break;
          case 40:
              $order_state = '<span style="color:#999">已完成</span>';
              $order_state_text = '已完成';
              break;
        }
        return array($order_state, $order_state_text);
    }

    /*
    * 预存款支付，虚拟商品购买
    * @param array $order_info
    * @param array $buyer_info
    */
    private function _vr_pdPay($order_info, $buyer_info){
        if ($order_info['order_state'] == 20){
            return;
        }

        $available_pd_amount = floatval($buyer_info['available_predeposit']);
        if ($available_pd_amount <= 0) return;

        $model_vr_order = M('vr_order');

        $order_amount = floatval($order_info['order_amount'])-floatval($order_info['rcb_amount']);
        $data_pd = array();
        $data_pd['member_id'] = $buyer_info['member_id'];
        $data_pd['member_name'] = $buyer_info['member_name'];
        $data_pd['amount'] = $order_amount;
        $data_pd['order_sn'] = $order_info['order_sn'];

        if ($available_pd_amount >= $order_amount){
            // 预存款立即支付，订单支付完成
            $this->changePd('order_pay',$data_pd);

            // 订单状态 置为已支付
            $data_order = array();
            $data_order['order_state'] = 20;
            $data_order['payment_time'] = time();
            $data_order['payment_code'] = 'predeposit';
            $data_order['pd_amount'] = $order_amount;
            $result = $model_vr_order->where(array('order_id'=>$order_info['order_id']))->save($data_order);
            if ($result === false) {
                throw new Exception('订单更新失败');
            }

            // 发放兑换码
            $vr_code = $this->addOrderCode($order_info);

            // 发送兑换码到手机
            $sendSMS = new SendTemplateSMS();
            $sendSMS->send_template_SMS($order_info['buyer_phone'],array($vr_code,'30'),1);

            // 支付成功发送店铺消息

            // 跳转
        }else{
            // 跳转
        }        
    }

    /*
    * 变更预存款
    * @param string $change_type
    * @param array $data
    */
    public function changePd($change_type,$data = array()){
        $data_log = array();
        $data_pd = array();

        $data_log['lg_member_id'] = $data['member_id'];
        $data_log['lg_member_name'] = $data['member_name'];
        $data_log['lg_add_time'] = time();
        $data_log['lg_type'] = $change_type;

        switch ($change_type){
            case 'order_pay':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '下单，支付预存款，订单号: '.$data['order_sn'];
                $data_pd['available_predeposit'] = array('exp','available_predeposit-'.$data['amount']);

                break;

            default:
                throw new Exception('参数错误');
                break;
        }

        $update = M('member')->where(array('member_id'=>$data['member_id']))->save($data_pd);
        if ($update === false) {
            throw new Exception('操作失败');
        }

        $insert = M('pd_log')->data($data_log)->add();
        if (!$insert) {
            throw new Exception('操作失败');
        }

        // 支付成功发送买家消息
    }

    /**
     * 生成兑换码
     * @param array $order_info
     * @return int 返回 insert_id
     */
    public function addOrderCode($order_info){
        $model_vr_order_code = M('vr_order_code');
        $vrc_num = $model_vr_order_code->where(array('order_id'=>$order_info['order_id']))->count();
        if (!empty($vrc_num)){
            return false;
        }

        if (empty($order_info)){
            return false;
        }

        //取得店铺兑换码前缀
        $store_info = M('store')->where(array('store_id'=>$order_info['store_id']))->find();
        $virtual_code_perfix = $store_info['store_vrcode_prefix'] ? $store_info['store_vrcode_prefix'] : rand(100,999);

        $vr_code = $this->_makeVrCode($virtual_code_perfix, $order_info['store_id'], $order_info['buyer_id']);

        $data_order_code = array();
        $data_order_code['order_id'] = $order_info['order_id'];
        $data_order_code['store_id'] = $order_info['store_id'];
        $data_order_code['buyer_id'] = $order_info['buyer_id'];
        $data_order_code['vr_code'] = $vr_code;
        $data_order_code['pay_price'] = $order_info['order_amount'];
        $data_order_code['vr_indate'] = $order_info['vr_indate'];
        $data_order_code['vr_invalid_refund'] = $order_info['vr_invalid_refund'];

        $model_vr_order_code->data($data_order_code)->add();

        return $vr_code;
    }

    /**
     * 生成兑换码
     * 长度 =3位 + 4位 + 2位 + 3位  + 1位 + 5位随机  = 18位
     * @param string $perfix 前缀
     * @param int $store_id
     * @param int $member_id
     * @param unknown $num
     * @return multitype:string
     */
    private function _makeVrCode($perfix, $store_id, $member_id) {
        $vr_code = $perfix
        . sprintf('%04d', (int) $store_id * $member_id % 10000)
        . sprintf('%02d', (int) $member_id % 100)
        . sprintf('%03d', (float) microtime() * 1000)
        . sprintf('%01d', (int) 0 % 10) . random(5,1);

        return $vr_code;
    }

    /**
     * 支付宝异步通知处理
     *
     */
    public function notifyUrl(){
        $success = 'success'; 
        $fail = 'fail';

        $order_type = $_POST['extra_common_param'];
        $out_trade_no = $_POST['out_trade_no'];
        $trade_no = $_POST['trade_no'];

        //参数判断
        if(!preg_match('/^\d{18}$/',$out_trade_no)){
            exit($fail);
        }

        if ($order_type == 'vr_order'){
            $result = $this->getVrOrderInfo($out_trade_no);
            if ($result['data']['order_state'] != 10) {
                exit($success);
            }
        } else {
            exit();
        }
        $order_pay_info = $result['data'];

        //取得支付方式
        $result = $this->getPaymentInfo('alipay');
        if (!$result['state']) {
            exit($fail);
        }
        $payment_info = $result['data'];

        //创建支付接口对象
        $payment_api = new alipay($payment_info,$order_pay_info);

        //对进入的参数进行远程数据判断
        // $verify = $payment_api->notify_verify();
        // if (!$verify) {
        //     exit($fail);
        // }

        //更改订单支付状态
        if($order_type == 'vr_order'){
            $result = $this->updateVrOrder($out_trade_no, $payment_info['payment_code'], $order_pay_info, $trade_no);
        }

        exit($result['state'] ? $success : $fail);
    }

    /**
     * 支付宝同步返回处理
     *
     */
    public function returnUrl(){
        $order_type = $_GET['extra_common_param'];
        if($order_type == 'vr_order') {
            $act = 'member_vr_order';
        }else {
            exit();
        }

        $out_trade_no = $_GET['out_trade_no'];
        $trade_no = $_GET['trade_no'];
        $url = 'http://www.mxhhw.com/shop/index.php?act='.$act;

        //对外部交易编号进行非空判断
        if(!preg_match('/^\d{18}$/',$out_trade_no)) {
            $this->error('参数错误', $url, 1);
        }

        if ($order_type == 'vr_order') {
            $result = $this->getVrOrderInfo($out_trade_no);
            if(!$result['state']) {
                $this->error($result['msg'], $url, 1);
            }
            if ($result['data']['order_state'] != 10) {
                $payment_state = 'success';
            }
        }
        $order_pay_info = $result['data'];
        $api_pay_amount = $result['data']['api_pay_amount'];

        if ($payment_state != 'success'){
            //取得支付方式
            $result = $this->getPaymentInfo('alipay');
            if (!$result['state']) {
                $this->error($result['msg'], $url, 1);
            }
            $payment_info = $result['data'];

            //创建支付接口对象
            $payment_api  = new alipay($payment_info,$order_pay_info);

            //返回参数判断
            // $verify = $payment_api->return_verify();
            // if(!$verify) {
            //     $this->error('支付数据验证失败', $url, 1);
            // }

            //取得支付结果
            $pay_result = $payment_api->getPayResult($_GET);
            if (!$pay_result) {
                $this->error('非常抱歉，您的订单支付没有成功，请您后尝试', $url, 1);
            }

            //更改订单支付状态
            if($order_type == 'vr_order') {
                $result = $this->updateVrOrder($out_trade_no, $payment_info['payment_code'], $order_pay_info, $trade_no);
            }
            if (!$result['state']) {
                $this->error('支付状态更新失败', $url, 1);
            }

            //支付成功后跳转
            if ($order_type == 'vr_order') {
                $pay_ok_url = 'http://localhost/shopnc/index.php?act=buy_virtual&op=pay_ok&order_sn='.$out_trade_no.'&order_id='.$order_pay_info['order_id'].'&order_amount='.ncPriceFormat($api_pay_amount);
                redirect($pay_ok_url);
            }
        } 
    }

    /**
     * 支付成功后修改虚拟订单状态
     */
    public function updateVrOrder($out_trade_no, $payment_code, $order_info, $trade_no) {
        $post['payment_code'] = $payment_code;
        $post['trade_no'] = $trade_no;
        return $this->changeOrderStatePay($order_info, 'system', $post);
    }

    /**
     * 支付订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $post
     * @return array
     */
    public function changeOrderStatePay($order_info, $role, $post) {
        try {

            $model_vr_order = M('vr_order');
            $model_vr_order->startTrans();

            //更新订单状态
            $update_order = array();
            $update_order['order_state'] = 20;
            $update_order['payment_time'] = $post['payment_time'] ? strtotime($post['payment_time']) : time();
            $update_order['payment_code'] = $post['payment_code'];
            $update_order['trade_no'] = $post['trade_no'];
            $update = $model_vr_order->where(array('order_id'=>$order_info['order_id']))->save($update_order);
            if (!$update) {
                throw new Exception('订单更新失败');
            }

            //发放兑换码
            $vr_code = $this->addOrderCode($order_info);
            if (!$vr_code) {
                throw new Exception('兑换码发送失败');
            }

            // 支付成功发送买家消息
            // $param = array();
            // $param['code'] = 'order_payment_success';
            // $param['member_id'] = $order_info['buyer_id'];
            // $param['param'] = array(
            //         'order_sn' => $order_info['order_sn'],
            //         'order_url' => urlShop('member_vr_order', 'show_order', array('order_id' => $order_info['order_id']))
            // );
            // QueueClient::push('sendMemberMsg', $param);

            // 支付成功发送店铺消息
            // $param = array();
            // $param['code'] = 'new_order';
            // $param['store_id'] = $order_info['store_id'];
            // $param['param'] = array(
            //         'order_sn' => $order_info['order_sn']
            // );
            // QueueClient::push('sendStoreMsg', $param);

            //发送兑换码到手机
            $sendSMS = new SendTemplateSMS();
            $sendSMS->send_template_SMS($order_info['buyer_phone'],array($vr_code,'30'),1);

            $model_vr_order->commit();
            return callback(true,'更新成功');

        } catch (Exception $e) {
            $model_vr_order->rollback();
            return callback(false,$e->getMessage());
        }
    }
}