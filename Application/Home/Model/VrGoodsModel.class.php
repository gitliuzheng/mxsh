<?php
namespace Home\Model;
use Think\Model;
class VrGoodsModel extends Model {

    const STATE1 = 1;       // 出售中
    const STATE0 = 0;       // 下架
    const STATE10 = 10;     // 违规
    const VERIFY1 = 1;      // 审核通过
    const VERIFY0 = 0;      // 审核失败
    const VERIFY10 = 10;    // 等待审核

    /**
     * 由ID取得在售单个虚拟商品信息
     * @param unknown $goods_id
     * @param string $field 需要取得的缓存键值, 例如：'*','goods_name,store_name'
     * @return array
     */
    public function getVirtualGoodsOnlineInfoByID($goods_id) {
        $goods_info = $this->getGoodsInfoByID($goods_id);
        //return $goods_info['is_virtual'] == 1 && $goods_info['virtual_indate'] >= time() ? $goods_info : array();
        return $goods_info['is_virtual'] == 1  ? $goods_info : array();
    }

    /**
     * 取得商品详细信息
     * 如果未找到，则缓存所有字段
     * @param int $goods_id
     * @param string $fields 需要取得的缓存键值, 例如：'*','goods_name,store_name'
     * @return array
     */
    public function getGoodsInfoByID($goods_id) {
        $goods_info = $this->getGoodsInfo(array('goods_id'=>$goods_id));
        return $goods_info;
    }


    /**
     * 获取单条商品SKU信息
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsInfo($condition, $field = '*') {
        $model_VrGoods = D("VrGoods");
        return $model_VrGoods->field($field)->where($condition)->find();
    }


    /**
     * 出售中的商品数量
     *
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonOnlineCount($condition) {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonCount($condition);
    }


    /**
     * 获得商品数量
     *
     * @param array $condition
     * @param string $field
     * @return int
     */
    public function getGoodsCommonCount($condition) {
        return $this->table(C('DB_PREFIX') . 'vr_goods_common')->where($condition)->count();
    }


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
        //分割成数组 
        $goodsId = explode(',',$goods_Id['gid']);
        $goodsid = explode(',',$goods_id['gids']);
        $gid = array_merge($goodsId,$goodsid);
        $gsid = array_unique($gid);
        
        $data['count'] = count($gsid);
        //return $goodsId;
        $page = new \Think\Page($data['count'], 2);  //设置页面商品显示个数
        // 配置翻页的样式
        $data['page'] = $page->show();
        $where1['goods_id'] = array('in',$gsid);
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
}   














