<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 首页显示，商品详情页显示、记录用户浏览记录
 * zhangkuan
 */
class IndexController extends CommonController {

//首页
    public function index(){
    	$goodsModel = D('GoodsClass');
    	$floorData = $goodsModel->floorData();
    	$this->assign(array(
    		'floorData' => $floorData,
    		));
        $this->display();
    }

//商品详情页 
    public function deal(){
        $id = I('get.id');
        $goods = D('VrGoods');     
        $term['a.goods_id'] = array('eq',$id);       
        $data = $goods -> where(array('goods_id' => array('eq',$id),))->find();
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
        $this->assign(array(
            'gcname' => $gcname,
            'data' => $data,
            'gcaddress' => $gcaddress,
            'count' => $count,
        ));
        $this->display();
    }

// ajax获取cookie值
    public function displayHistory()
    {
        $id = I('get.id');
        $data = isset($_COOKIE['display_history']) ? unserialize($_COOKIE['display_history']) : array();
        // 把最新浏览的这件商品放到数组中的第一个位置上
        array_unshift($data, $id);
        $data = array_unique($data);
        if(count($data) > 5)
            $data = array_slice($data, 0, 5);
        setcookie('display_history', serialize($data), time() + 30 * 86400, '/');
        // 再根据商品的ID取出商品的详细信息
        $goods = D('VrGoods'); 
        $data = implode(',', $data);
        $gData = $goods->field('goods_id,goods_name,goods_price,goods_promotion_price')->where(array(
            'goods_id' => array('in', $data),            
        ))->order("FIELD(goods_id,$data)")->select();        
        echo json_encode($gData);
    }

//地址信息
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
   
}