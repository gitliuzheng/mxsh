<?php
namespace Home\Controller;
use Think\Controller;
use Common\CCP_REST_DEMO_PHP_v2_7r\Service\SendTemplateSMS;
/**
 * 首页显示，商品详情页显示、记录用户浏览记录、用户评价分页
 * zhangkuan
 */
class IndexController extends CommonController {

    /*
    * 首页
    */
    public function index(){
        //限时折扣
        $model_xianshi_goods = D('VrGoods');
        $xianshi_item = $model_xianshi_goods->getXianshiGoodsCommendList();
       // print_r($xianshi_item);
       // exit;
        $this->assign('xianshi_item', $xianshi_item);
        $this->display();
    }

    /*
    * 商品详情页
    */ 
    public function deal(){
        $id = I('get.id');
        $goods = D('VrGoods');           
        $data = $goods -> where(array('goods_id' => array('eq',$id),'goods_state' => array('eq',1),'goods_verify' =>array('eq',1),))->find();
        $goodsModel = D('GoodsClass'); 
        //获取此商品各级分类名称
        for($a=1;$a<=3;$a++){
            $gcname[$a] =  $goodsModel->where(array('gc_id' => array('eq',$data['gc_id_'.$a]),))->find();
        }       
        //获取此商户地址
        $goodsAddress = D('VrGoodsAddress');
        $gcaddress = $goodsAddress -> where(array('goods_commonid' => array('eq',$id),))->select();
        //取出商品对应的地址总条数
        if(count($gcaddress)%3 != 0){
         $count =intval(count($gcaddress)/3)+1; //计算记录数
        }else{
            $count = count($gcaddress)/3;
        } 
        //取评价
        $evaluate_goods = M('EvaluateGoods');
        $temp = $evaluate_goods -> where(array('geval_goodsid' => array('eq',$id),)) -> count();       
        //取出评价总条数
        if($temp%3 != 0){
         $evaluate_count =intval($temp/3)+1; //计算记录数
        }else{
            $evaluate_count = $temp/3;
        }                 
        //取评价图示数据
        for ($i=5; $i >= 1 ; $i--) { 
            $num[$i] = $evaluate_goods -> where(array('geval_goodsid' => array('eq',$id),'geval_scores' => array('eq',$i),)) -> count();
            //转换成各分值的百分比
            $favourable[$i] = round(($num[$i]/$temp)*100).'%';
        }
        $total = $num[5]/$temp*5+$num[4]/$temp*4+$num[3]/$temp*3+$num[2]/$temp*2+$num[1]/$temp*1;
        $total_favourable = round(($total/5)*100).'%';
        //print_r($data);
        //exit;
        $this->assign(array(
            'gcname' => $gcname,
            'data' => $data,
            'gcaddress' => $gcaddress,
            'count' => $count,
            'evaluate_count' => $evaluate_count,
            'temp' => $temp,
            'favourable' => $favourable,
            'num' => $num,
            'total' => $total,
            'total_favourable' => $total_favourable
        ));
        $this->display();
    }



    /*
    * 商品详情页收藏
    */
    public function favorites(){
        $login = $this->is_cookie_login;
        $member_id = $this->vr_member_id;
        //判断是否登陆，是否已收藏
        $login = 1;
        $member_id = 6;
        if($login){
            $favorites = M('favorites');
            $fwhere['member_id'] = $member_id;
            $fwhere['fav_id'] = I('id');
            $nufavorites = $favorites -> where($fwhere) ->find();
            if($nufavorites){
                echo json_encode('你已经收藏过该商品');
                die;
            }
            $vrGoods = M('VrGoods');
            $where['goods_id'] = I('id');
            $data = $vrGoods -> where($where) ->find();
            $temp['fav_id'] = $data['goods_id'];
            $temp['fav_time'] = time();
            $temp['goods_name'] = $data['goods_name'];
            $temp['gc_id'] = $data['gc_id'];
            $temp['goods_price'] = $data['log_price'];
            $temp['member_id'] = $member_id;
            $favorites-> data($temp) ->add();
            $vrGoods -> where($where) ->setInc('goods_collect',1);
            $dataNum = $vrGoods -> where($where) -> find();
            $num = $dataNum['goods_collect'];
            
            echo json_encode($num);
        }else{
            echo json_encode(0);
        }
    }

    /*
    * ajax获取cookie值
    */
    public function displayHistory(){
        $id = I('get.id');
        $data = isset($_COOKIE['display_history']) ? unserialize($_COOKIE['display_history']) : array();
        // 把最新浏览的这件商品放到数组中的第一个位置上
        if($id){
        array_unshift($data, $id);
    }
        $data = array_unique($data);
        if(count($data) > 5)
            $data = array_slice($data, 0, 5);
        setcookie('display_history', serialize($data), time() + 30 * 86400, '/');
        // 再根据商品的ID取出商品的详细信息
        $goods = D('VrGoods'); 
        $data = implode(',', $data);
        if(!$data){
            echo json_encode(0);
            die;
        }
        $gData = $goods->field('goods_id,goods_name,goods_price,goods_promotion_price')->where(array(
            'goods_id' => array('in', $data),            
        ))->order("FIELD(goods_id,$data)")->select();        
        echo json_encode($gData);
    }

    /*
    * ajax清除cookie浏览记录
    */
    public function deldisplayHistory(){
        $data = unserialize($_COOKIE['display_history']);
        foreach ($data as $key => $value) {
            unset($data[$key]);
        }
        cookie("display_history",serialize($data),50000);
        echo true;
    }

    /*
    * 地址信息
    */
    public function addrajax(){
        $id = I('get.id');
        $page = I('get.page');
        $goodsAddress = D('VrGoodsAddress');
        $gcaddress = $goodsAddress -> where(array('goods_commonid' => array('eq',$id),))->limit(3*$page-3,3)->select();     
        echo json_encode($gcaddress);
    }

    /*
    * 删除冗余资源文件
    * liuzhen 2016-06-27 11:41
    */
    public function listDir(){
        $indexConetnet = file_get_contents('E:\php-workspace\mxsh\Application\Home\View\Search\category.html');

        $dir = 'E:\php-workspace\mxsh\Public\category_files';
        if(is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                while (($file = readdir($dh)) !== false)
                {
                    if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
                    {
                        echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
                        listDir($dir."/".$file."/");
                    }
                    else
                    {
                        if($file!="." && $file!="..")
                        {
                            $isExist = '';
                            if (strstr($indexConetnet, $file)){
                                $isExist = '******************************';
                            }

                            echo $file.$isExist."<br>";
                            // if (!strstr($indexConetnet, $file)){
                            //     unlink($dir.'\\'.$file);
                            // }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }


    /*
    * 商品详情页评价分页
    */
    public function evaajax(){
        $id = I('get.id');
        $page = I('get.page');
        $evaluate_goods = M('EvaluateGoods');
        $de = I('get.de');
        if($de){
            if($de == 'time'){
                $temp = $evaluate_goods -> where(array('geval_goodsid' => array('eq',$id),))->limit(3*$page-3,3)->order('geval_addtime desc')->select();
            }else{
                $temp = $evaluate_goods -> where(array('geval_goodsid' => array('eq',$id),))->limit(3*$page-3,3)->order('geval_scores desc')->select();
            }
        } 
        echo json_encode($temp);
    }

    /*
    * 测试发送模板短信
    * liuzhen 2016-07-06 15:58
    */
    public function sendSMS(){
        $sendSMS = new SendTemplateSMS();
        $sendSMS->send_template_SMS("15827323295",array('8888','30'),1);
    }



   
}