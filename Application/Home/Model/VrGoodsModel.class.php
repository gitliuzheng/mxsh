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
        return $goods_info['is_virtual'] == 1  && $goods_info['virtual_indate'] >= time()   ?  $goods_info : array();
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
        if(empty($key)){
            $data = null;
        }
        return $data;
    }

    /**
     * 更新商品SUK数据
     * @param array $update
     * @param int|array $goods_id
     * @return boolean|unknown
     */
    public function editGoodsById($date, $goods_id) {
        if (empty($goods_id)) {
            return false;
        }

        $condition['goods_id'] = $goods_id;
        $date['goods_edittime'] = time();
        $result = $this->where($condition)->save($date);
        return $result;
    }



    
}   














