<?php
namespace Home\Model;
use Think\Model;
class EvaluateStoreModel extends Model {
    /**
     * 根据店铺编号获取店铺评分数据
     *
     * @param int @store_id 店铺编号
     * @param int @sc_id 分类编号，如果传入分类编号同时返回行业对比数据
     */
    public function getEvaluateStoreInfoByStoreID($store_id, $sc_id = 0) {
        $info = array();
        $info['store_credit'] = $this->_getEvaluateStore(array('seval_storeid' => $store_id));
        $info['store_credit_average'] = round((($info['store_credit']['store_desccredit']['credit'] + $info['store_credit']['store_servicecredit']['credit'] + $info['store_credit']['store_deliverycredit']['credit']) / 3), 1);
        $info['store_credit_percent'] = intval($info['store_credit_average'] / 5 * 100);
        return $info;
    }

    /**
     * 获取店铺评分数据
     */
    private function _getEvaluateStore($condition) {
        $result = array();
        $field = 'AVG(seval_desccredit) as store_desccredit,';
        $field .= 'AVG(seval_servicecredit) as store_servicecredit,';
        $field .= 'AVG(seval_deliverycredit) as store_deliverycredit,';
        $field .= 'COUNT(seval_id) as count';
        $info = $this->getEvaluateStoreInfo($condition, $field);
        $result['store_desccredit']['text'] = '描述相符';
        $result['store_servicecredit']['text'] = '服务态度';
        $result['store_deliverycredit']['text'] = '发货速度';
        if(intval($info['count']) > 0) {
            $result['store_desccredit']['credit'] = round($info['store_desccredit'], 1);
            $result['store_servicecredit']['credit'] = round($info['store_servicecredit'], 1);
            $result['store_deliverycredit']['credit'] = round($info['store_deliverycredit'], 1);
        } else {
            $result['store_desccredit']['credit'] = round(5, 1);
            $result['store_servicecredit']['credit'] = round(5, 1);
            $result['store_deliverycredit']['credit'] = round(5, 1);
        }
        return $result;
    }


    /**
     * 获取店铺评分信息
     */
    public function getEvaluateStoreInfo($condition, $field='*') {
        $list = $this->field($field)->where($condition)->find();
        return $list;
    }
}   














