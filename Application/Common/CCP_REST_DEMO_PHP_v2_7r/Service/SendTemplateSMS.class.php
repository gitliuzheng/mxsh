<?php
// +----------------------------------------------------------------------
// | Author: liuzhen <799771885@qq.com>
// +----------------------------------------------------------------------
// | Date: 2016-07-06 17:39
// +----------------------------------------------------------------------
namespace Common\CCP_REST_DEMO_PHP_v2_7r\Service;
use Common\CCP_REST_DEMO_PHP_v2_7r\SDK\CCPRestSDK;
/**
 * 容联云短信平台
 */
class SendTemplateSMS {
	//主帐号
	private $accountSid= '8aaf070855b647ab0155bdd40fc309cd';

	//主帐号Token
	private $accountToken= 'b7e8db013ad44fe785a99c8db85ac974';

	//应用Id
	private $appId='8aaf070855b647ab0155bdd4102209d3';

	//请求地址，格式如下，不需要写https://
	private $serverIP='app.cloopen.com';

	//请求端口 
	private $serverPort='8883';

	//REST版本号
	private $softVersion='2013-12-26';


	/**
	  * 发送模板短信
	  * @param to 手机号码集合,用英文逗号分开
	  * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
	  * @param $tempId 模板Id
	  */       
	public function send_template_SMS($to,$datas,$tempId)
	{
	     // 初始化REST SDK
	     //global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
	     $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);
	     $rest->setAccount($this->accountSid,$this->accountToken);
	     $rest->setAppId($this->appId);
	    
	     // 发送模板短信
	     echo "Sending TemplateSMS to $to <br/>";
	     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
	     if($result == NULL ) {
	         echo "result error!";
	         break;
	     }
	     if($result->statusCode!=0) {
	         echo "error code :" . $result->statusCode . "<br>";
	         echo "error msg :" . $result->statusMsg . "<br>";
	         //TODO 添加错误处理逻辑
	     }else{
	         echo "Sendind TemplateSMS success!<br/>";
	         // 获取返回信息
	         $smsmessage = $result->TemplateSMS;
	         echo "dateCreated:".$smsmessage->dateCreated."<br/>";
	         echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
	         //TODO 添加成功处理逻辑
	     }
	}
}