<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Class SearchController
 * @package Home\Controller
 * 分类搜索，关键字搜索
 */
class SearchController extends CommonController {    
    public function category(){ 
//判断是否有key值，有则是关键字搜索
        if(I('get.key')){
            $goodsModel = D('VrGoods');
            $data = $goodsModel->key_search();  
            $this -> assign(array(
                'page' => $data['page'],
                'data' => $data['data'],
                'gcount' => $data['count'],
            ));        
            $this->display();          
        }
 //导航类搜索                          
        $catId = I('get.gc_id');
        $arr=explode('-',$catId);
        $gid=$arr[0];
        $goods = D('GoodsClass');
        //取二级分类下的所有三级分类
        $gClass = $goods -> where(array('gc_parent_id' => array('eq',$gid),))->select();
        //取虚拟商品顶级分类
        $oneClass = $goods -> where(array('gc_parent_id' => array('eq',0),'gc_virtual' => array('eq',1),)) ->find();
        //取虚拟商品二级分类
        $fClass = $goods -> where(array('gc_parent_id' => array('eq',$oneClass['gc_id']),))->select();
        $goodsModel = D('VrGoods');
        $data = $goodsModel->cat_search();
        $this->assign(array(
            'gClass' => $gClass,
            'fClass' => $fClass,
        ));    
        $this -> assign(array(
            'page' => $data['page'],
            'data' => $data['data'],
        ));         
        $this->display();
    }


}

