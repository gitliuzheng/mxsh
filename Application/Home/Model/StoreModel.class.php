<?php
namespace Home\Model;
use Think\Model;
class StoreModel extends Model {
    /**
     * 通过店铺编号查询店铺信息
     *
     * @param int $store_id 店铺编号
     * @return array
     */
    public function getStoreInfoByID($store_id) {
        $store_info = $this->getStoreInfo(array('store_id' => $store_id));
        return $store_info;
    }


    public function getStoreOnlineInfoByID($store_id) {
        $store_info = $this->getStoreInfoByID($store_id);
        if(empty($store_info) || $store_info['store_state'] == '0') {
            return array();
        } else {
            return $store_info;
        }
    }


    /**
     * 查询店铺信息
     *
     * @param array $condition 查询条件
     * @return array
     */
    public function getStoreInfo($condition) {
        $store_info = $this->where($condition)->find();
        if(!empty($store_info)) {
            if(!empty($store_info['store_presales'])) $store_info['store_presales'] = unserialize($store_info['store_presales']);
            if(!empty($store_info['store_aftersales'])) $store_info['store_aftersales'] = unserialize($store_info['store_aftersales']);

            //商品数
            $model_VrGoods = D('VrGoods');
            $store_info['goods_count'] = $model_VrGoods->getGoodsCommonOnlineCount(array('store_id' => $store_info['store_id']));

            //店铺评价
            $model_EvaluateStore = D('EvaluateStore');
            $store_evaluate_info = $model_EvaluateStore->getEvaluateStoreInfoByStoreID($store_info['store_id'], $store_info['sc_id']);
            
            $store_info = array_merge($store_info, $store_evaluate_info);
        }
        return $store_info;
    }
}   














