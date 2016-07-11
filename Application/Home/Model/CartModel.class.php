<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model {


    /**
     * 购物车列表
     *
     * @param string $type 存储类型 db,cookie
     * @param unknown_type $condition
     * @param int $limit
     */
    public function listCart($type, $condition = array(), $limit = '') {
        if ($type == 'db') {
            $cart_list = $this->where($condition)->limit($limit)->select();
        } elseif ($type == 'cookie') {
            //去除斜杠
            $cart_str = get_magic_quotes_gpc() ? stripslashes(cookie('cart')) : cookie('cart');
            $cart_str = base64_decode(decrypt($cart_str));
            $cart_list = @unserialize($cart_str);
        }

//        $cart_list = is_array($cart_list) ? $cart_list : array();
//        //顺便设置购物车商品数和总金额
//        $this->cart_goods_num =  count($cart_list);
//        $cart_all_price = 0;
//        if(is_array($cart_list)) {
//            foreach ($cart_list as $val) {
//                $cart_all_price	+= $val['goods_price'] * $val['goods_num'];
//            }
//        }
//        $this->cart_all_price = ncPriceFormat($cart_all_price);
        return !is_array($cart_list) ? array() : $cart_list;
    }
}










