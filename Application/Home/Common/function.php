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


