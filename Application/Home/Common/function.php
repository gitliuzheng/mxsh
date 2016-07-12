<?php
/**
 * 从当前URL中去掉某个参数之后的URL
 *11
 * @param unknown_type $param
 */
function filterUrl($param)
{
	// 先取出当前的URL地址
	$url = $_SERVER['PHP_SELF'];
	// 正则去掉某个参数
	$re = "/\/$param\/[^\/]+/";
	return preg_replace($re, '', $url);
}


/**
 * 从当前URL中去掉某个参数和分页页码之后的URL
 *
 * @param unknown_type $param,$p
 */
function fUrl($param,$p)
{
	// 先取出当前的URL地址
	$url = $_SERVER['PHP_SELF'];
	// 正则去掉某个参数
	$re = "/\/$param\/[^\/]+/";
	$pe = "/\/$p\/[^\/]+/";
	$u =  preg_replace($re, '', $url);
	return  preg_replace($pe, '', $u);
}

/**
* 价格格式化
*
* @param int	$price
* @return string	$price_format
*/
function ncPriceFormat($price) {
	$price_format	= number_format($price,2,'.','');
	return $price_format;
}

/**
 * 规范数据返回函数
 * @param unknown $state
 * @param unknown $msg
 * @param unknown $data
 * @return multitype:unknown
 */
function callback($state = true, $msg = '', $data = array()) {
    return array('state' => $state, 'msg' => $msg, 'data' => $data);
}

/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

/**
 * 取得订单支付类型文字输出形式
 *
 * @param array $payment_code
 * @return string
 */
function orderPaymentName($payment_code) {
    return str_replace(
            array('offline','online','alipay','tenpay','chinabank','predeposit'),
            array('货到付款','在线付款','支付宝','财付通','网银在线','站内余额支付'),
            $payment_code);
}