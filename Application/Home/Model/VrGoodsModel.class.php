<?php
namespace Home\Model;
use Think\Model;
class VrGoodsModel extends Model {

	//取出二级或三级分类的所有商品ID(如有三级以下分类需再增加判断)
	public function getGoodsIdByCatId($catId){
		$catModel = D('GoodsClass');
		$children = $catModel ->getChildren($catId);
        $parent = $catModel->parentPath($catId);
		if(!empty($children)){
			$children[] = $catId;
			$gids = $this -> field('goods_id') -> where(array(
				'gc_id_2' => array('in',$children),
			))->select();
			$id = array();
			foreach ($gids as $k => $v)
			{
				if(!in_array($v['goods_id'], $id))
					$id[] = $v['goods_id'];
			}
			return $id;
		}else{
			$children[] = $catId;
			$gids = $this -> field('goods_id') -> where(array(
				'gc_id_3' => array('in',$children),
			))->select();
			$id = array();
			foreach ($gids as $k => $v)
			{
				if(!in_array($v['goods_id'], $id))
					$id[] = $v['goods_id'];
			}
			return $id;
		}
	}


	//取一个分类下的商品及分页(二级或三级)
	public function cat_search(){
        if(I('get.gc_id')){                       
            $catId = I('get.gc_id');
            $arr=explode('-',$catId);
            $gid=$arr[0];
            $where['gc_id_2'] = $gid;
        }
        if(I('get.gc_id3')){                       
            $catId3= I('get.gc_id3');
            $arr=explode('-',$catId3);
            $gid=$arr[0];
            $where['gc_id_3'] = $gid;
        }
        switch (I('odby')){
            case 'xl':
                $p = "goods_salenum";
                break;
            case 'jg':
                $p = "goods_promotion_price";
                break;
            case 'hp':
                $p = "evaluation_count";
                break;
            case 'sj':
                $p = "goods_edittime";
                break; 
            default:
                $p = "goods_id";
        }
        $where['goods_state'] = 1;
        $where['goods_verify'] = 1;
        //取出商品总数

        $gdata = $this -> where($where) -> select();
        $count = count($gdata);
        $page = new \Think\Page($count, 2);
        // 配置翻页的样式
        $data['page'] = $page->show();
        $data['data'] = $this ->field('goods_id,goods_commonid,goods_name,goods_promotion_price,goods_price,goods_marketprice,goods_salenum,evaluation_count,gc_id_1,gc_id_2,gc_id_3')-> where($where) ->limit($page->firstRow.','.$page->listRows) ->order(array($p => "desc")) ->select();      
		return $data;
	}


//顶部搜索关键字及分页
    public function key_search(){
        $key = I('get.name');
        // $term['a.goods_commend'] = 1;
        // $term['a.is_virtual'] = 1;
        // if(!empty($key)){        
        //     $where['b.goods_name'] = array('like',"%$key%");
        //     $where['address'] = array('like',"%$key%");
        //     $where['message'] = array('like',"%$key%");
        //     $where['_logic'] = 'or';
        //     $term['_complex'] = $where;
        // }
        //根据关键字(商品名称，地址，信息窗口取出相应商品ID)
        $goodsaddr = M('VrGoodsAddress');
        $goods_Id = $goodsaddr ->field('GROUP_CONCAT(DISTINCT goods_commonid) gid')
        ->where(array(
            'goods_name' => array('exp', " LIKE '%$key%' OR address LIKE '%$key%' OR message LIKE '%$key%'"),
        ))
        ->find();      
        $goods_id = $this -> field('GROUP_CONCAT(DISTINCT goods_commonid) gids')
        ->where(array(
            'goods_name' => array('exp', " LIKE '%$key%'"),
        ))
        ->find();        
        if(empty($goods_Id['gid']) && empty($goodsid['gids'])){
            $data['count'] = 0;
            if(empty($key)){
                $data = null;
            }
            return $data;
        }else{
            //分割成数组 
            $goodsId = explode(',',$goods_Id['gid']);
            $goodsid = explode(',',$goods_id['gids']);
            $gid = array_merge($goodsId,$goodsid);
            $gsid = array_unique($gid);
            $data['count'] = count($gsid);
        }
        //return $goodsId;
        $page = new \Think\Page($data['count'], 2);  //设置页面商品显示个数
        // 配置翻页的样式
        $data['page'] = $page->show();
        $where1['goods_id'] = array('in',$gsid);
        $where1['goods_state'] = 1;
        $where1['goods_verify'] = 1;
        switch (I('odby')){
            case 'xl':
                $p = "goods_salenum";
                break;
            case 'jg':
                $p = "goods_promotion_price";
                break;
            case 'hp':
                $p = "evaluation_count";
                break;
            case 'sj':
                $p = "goods_edittime";
                break; 
            default:
                $p = "goods_id";
        }
         $data['data'] = $this ->field('goods_id,goods_commonid,goods_promotion_price,goods_name,goods_price,goods_marketprice,goods_salenum,evaluation_count,gc_id_1,gc_id_2,gc_id_3')-> where($where1) ->limit($page->firstRow.','.$page->listRows)
        ->order(array($p => "desc")) ->select(); 
        // $gadata['data'] = $this->alias('a')
        // ->field('a.goods_id,a.goods_commonid,a.goods_name,a.goods_price,a.goods_marketprice,a.goods_salenum,a.evaluation_count,a.gc_id_1,a.gc_id_2,a.gc_id_3')
        // ->join('RIGHT JOIN __VR_GOODS_ADDRESS__ b 
        //        ON a.goods_commonid = b.goods_commonid')
        // ->where($term)       
        // ->select();
        // //二维数组去重
        // foreach ($gadata['data'] as $k=>$v){
        //   $v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        //   $temp[$k]=$v;
        // }
        // $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组 
        // foreach ($temp as $k => $v){
        //     $array=explode(',',$v); //再将拆开的数组重新组装
        //     //下面的索引根据自己的情况进行修改即可
        //     $data['data'][$k]['goods_id'] =$array[0];
        //     $data['data'][$k]['goods_commonid'] =$array[1];
        //     $data['data'][$k]['goods_name'] =$array[2];
        //     $data['data'][$k]['goods_price'] =$array[3];
        //     $data['data'][$k]['goods_marketprice'] =$array[4];
        //     $data['data'][$k]['goods_salenum'] =$array[5];
        //     $data['data'][$k]['evaluation_count'] =$array[6];
        //     $data['data'][$k]['gc_id_1'] =$array[7];
        //     $data['data'][$k]['gc_id_2'] =$array[8];
        //     $data['data'][$k]['gc_id_3'] =$array[9];
        // }        
        // foreach ($data['data'] as $k => $v) {
        //      $goodsclass['gc_id_2'][] = $v['gc_id_2'];
        //      $goodsclass['gc_id_3'][] = $v['gc_id_3'];
        // }
        // //数组去重 
        // $data['gc_id_2'] = array_unique($goodsclass['gc_id_2']);
        // $data['gc_id_3'] = array_unique($goodsclass['gc_id_3']);      
        if(empty($key)){
            $data = null;
        }
        return $data;
    }




    /**
     * 获取推荐限时折扣商品
     * @param int $count 推荐数量
     * @return array 推荐限时活动列表
     *
     */
    public function getXianshiGoodsCommendList() {
        $condition = array();
        //是否正常状态
        $condition['state'] = 1;
      //  $condition['start_time'] = array('lt', time());
      //  $condition['end_time'] = array('gt', time());
        $xianshi_list = $this->getXianshiGoodsExtendList($condition, null, 'xianshi_recommend desc', '*');
        return $xianshi_list;
    }

    /**
     * 读取限时折扣商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 限时折扣商品列表
     *
     */
    public function getXianshiGoodsExtendList($condition, $page=null, $order='', $field='*') {
        $xianshi_goods_list = $this->getXianshiGoodsList($condition, $page, $order, $field);
        if(!empty($xianshi_goods_list)) {
            for($i=0, $j=count($xianshi_goods_list); $i < $j; $i++) {
                $xianshi_goods_list[$i] = $this->getXianshiGoodsExtendInfo($xianshi_goods_list[$i]);
            }
        }
        return $xianshi_goods_list;
    }

    /**
     * 读取限时折扣商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 限时折扣商品列表
     *
     */
     
    public function getXianshiGoodsList($condition, $page=null, $order='', $field='*') {
        $xianshiGoods = M('PXianshiGoods');
        return $xianshi_goods_list = $xianshiGoods->field($field)->where($condition)->page($page)->order($order)->select();
    }

   
      /**
     * 获取限时折扣商品扩展信息
     * @param array $xianshi_info
     * @return array 扩展限时折扣信息
     *
     */
    public function getXianshiGoodsExtendInfo($xianshi_info) {
       // $xianshi_info['goods_url'] = urlShop('goods', 'index', array('goods_id' => $xianshi_info['goods_id']));
       // $xianshi_info['image_url'] = cthumb($xianshi_info['goods_image'], 60, $xianshi_info['store_id']);
        //$xianshi_info['xianshi_price'] = ncPriceFormat($xianshi_info['xianshi_price']);
        $xianshi_info['xianshi_discount'] = number_format($xianshi_info['xianshi_price'] / $xianshi_info['goods_price'] * 10, 1).'折';
       
        return $xianshi_info;
    }
    
}   














