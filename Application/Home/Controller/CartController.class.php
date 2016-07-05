<?php
namespace Home\Controller;
use Think\Controller;

class CartController extends CommonController {

    /**
     * 购物车首页
     */
    public function index(){
        $this->assign("cart",unserialize(cookie("cart")));
        $this->display();
    }


    //添加COOKIE购物车数据到数据库
    public function addCartToDb(){
        $cookie_cart = unserialize(cookie("cart"));
        print_r($cookie_cart);die;
    }

    //添加到购物车
    public function addCart(){
        //登录就放到数据库
        if($this->vr_member_id){
            $model_cart = D("cart");
            //查询该商品是否已经放到数据库里
            $data = array();
            $data['store_id'] = 1;
            $data['buyer_id'] = $this->vr_member_id;
            $data['goods_id'] = $_GET['goods_id'];
            $res = $model_cart->where($data)->find();
            if($res){
                $data = array();
                $data['cart_id'] = $res['cart_id'];
                $data['goods_num'] = $res['goods_num'] + $_GET['goods_num'];
                $model_cart->save($data);

            }else{
                $data = array();
                $data['buyer_id'] = $this->vr_member_id;
                $data['store_id'] = 1;
                $data['store_name'] = "官方店铺";
                $data['goods_id'] = $_GET['goods_id'];
                $data['goods_name'] = $_GET['goods_name'];
                $data['goods_price'] = $_GET['goods_price'];
                $data['goods_num'] = $_GET['goods_num'];
                $data['goods_image'] = '1_04423412434387147.png';
                $model_cart->data($data)->add();
            }



        }else{
            //cookie("cart",null);die;
            $goods_id = I('get.goods_id');
            $cookie_cart = unserialize(cookie("cart"));

            //根据商品的ID取出商品的详细信息
            $model_goods = D('VrGoods');
            $goods_info = $model_goods->field('goods_id,goods_name,goods_price,goods_promotion_price')->where(array('goods_id'=>$goods_id))->find();

            //是否存在购物车
            if($this->issetCart()){
                //该商品是否在购物车里
                if($this->GoodsInCart($goods_id)){
                    //修改商品点击数量
                    $this->changeCartGoodsCount($goods_id,$cookie_cart);
                    //修改商品的小计
                    $this->changeCartGoodsTotal($goods_id,$cookie_cart);

                }else{
                    //添加该商品到购物车
                    $goods_info['url'] = U("Home/Index/deal/id/".$goods_id);
                    $goods_info['goods_num'] = $_GET['goods_num'];
                    $goods_info['goods_total'] = $goods_info['goods_num'] * $goods_info['goods_price'];
                    $index = $cookie_cart ? count($cookie_cart) : 0;
                    $cookie_cart[$index] = $goods_info;

                }


            }else{
                //添加该商品到购物车
                $goods_info['url'] = U("Home/Index/deal/id/".$goods_id);
                $goods_info['goods_num'] = $_GET['goods_num'];
                $goods_info['goods_total'] = $goods_info['goods_num'] * $goods_info['goods_price'];
                $index = $cookie_cart ? count($cookie_cart) : 0;
                $cookie_cart[$index] = $goods_info;


            }
            cookie("cart",serialize($cookie_cart),50000);
            echo json_encode(unserialize(cookie("cart")));
        }


    }


    //添加商品到购物车
    public function addGoodsToCart($goods_id,$cookie_cart){
        $goods_info['url'] = U("Home/Index/deal/id/".$goods_id);
        $goods_info['goods_count'] = 1;
        $goods_info['goods_total'] = $goods_info['goods_price'];
        $index = $cookie_cart ? count($cookie_cart) : 0;
        $cookie_cart[$index] = $goods_info;
        cookie("cart",serialize($cookie_cart),50000);
    }




    //判断是否存在购物车
    public function issetCart(){
        if(cookie("cart")){
            return true;
        }else{
            return false;
        }
    }

    //查询商品是否在购物车里
    public function GoodsInCart($goods_id){
        $cookie_cart = unserialize(cookie("cart"));
        foreach($cookie_cart as $key => $val){
            if($val['goods_id'] == $goods_id){
                return true;
            }
        }
    }

    //删除购物车某个商品
    public function delCart(){
        $cookie_cart_index = I('get.cookie_cart_index');
        $cookie_arr_new = "";
        $cookie_cart = unserialize(cookie("cart"));
        foreach($cookie_cart as $key => $val){
            if($key != $cookie_cart_index){
                $cookie_arr_new[] = $val;
            }
        }
        cookie("cart",serialize($cookie_arr_new),50000);

        echo json_encode($cookie_arr_new);

    }

    //修改商品数量
    public function changeCartGoodsCount($goods_id,&$cookie_cart){
        foreach($cookie_cart as $key => &$val){
            if($val['goods_id'] == $goods_id){
                $val['goods_num'] = $val['goods_num'] + $_GET['goods_num'];
            }
        }
    }

    //求商品的小计
    public function changeCartGoodsTotal($goods_id,&$cookie_cart){
        foreach($cookie_cart as $key => &$val){
            if($val['goods_id'] == $goods_id){
                $val['goods_total'] = $val['goods_num'] * $val['goods_price'];
            }
        }
    }









   
}