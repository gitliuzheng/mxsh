<?php
namespace Home\Model;
use Think\Model;
class GoodsClassModel extends Model {


//首页楼层数据、导航条的数据(按优先级排序)
	public function floorData(){
		//判断是否存在缓存，如存大直接取
		$floorData = S('floorData');
		if($floorData)
			return $floorData;
		else{
			//取楼层的顶级分类(按优先级排序)
			$ret = $this -> where(array(
					'gc_parent_id' => array('eq',0),
					'gc_show' => array('eq',1),
					'gc_virtual' => array('eq',1),
				))->order('gc_sort ASC')->select();
			$goods = D('VrGoods');
			foreach ($ret as $k => $v) {
			 	//取楼层的二级分类
			 	$ret[$k]['recSubCat'] = $this -> where(array(
			 			'gc_parent_id' => array('eq',$v['gc_id']),
			 			'gc_show' => array('eq',1),
			 			'gc_virtual' => array('eq',1),
			 		))->order('gc_sort ASC')->select();
			 	//取楼层二级分类下的所有商品(包含扩展分类)
			 	foreach ($ret[$k]['recSubCat'] as $k1 => &$v1) {
			 		$gids = $goods->getGoodsIdByCatId($v1['gc_id']);	
			 		if(!empty($gids)){			
			 		$v1['goods'] = $goods->field('goods_id,goods_commonid,goods_name,goods_price,goods_marketprice,goods_salenum,evaluation_count')->where(array(
			 			'goods_id' => array('in',$gids),
			 			))->limit(4)->select();		//前台商品显示limit()几个	
					}
					//取三级分类
					$v1['threeCat'] = $this -> where(array(
			 			'gc_parent_id' => array('eq',$v1['gc_id']),
			 			'gc_show' => array('eq',1),
			 			'gc_virtual' => array('eq',1),
			 		))->select();
			 	}
			}
			//数据缓存一天
			S('floorData', $ret, 86400);
			return $ret;
		}				
	}


//一个分类的所有子分类ID
	public function getChildren($catId){
		$data = $this -> select();
		return $this -> _getChildren($data,$catId,true);
	}

//递归从数据中找子分类
	public function _getChildren($data,$catId,$isClear=false){
		static $_ret = array();
		if($isClear){
			$_ret = array();
		}
		foreach ($data as $k => $v) {
			if($v['gc_parent_id'] == $catId){
				$_ret[] = $v['gc_id'];
				$this -> _getChildren($data,$v['gc_id']);
			}
		}
		return $_ret;
	}


//所有父级分类
	public function parentPath($catId){
		static $ret = array();
		$info = $this -> field('gc_id,gc_name,gc_parent_id') -> find($catId);
		$ret[] = $info;
		if($info['gc_parent_id'] > 0){
			$this -> parentPath($info['gc_parent_id']);
		}
		return $ret;
	}	
}










