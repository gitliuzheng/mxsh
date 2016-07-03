<?php
namespace Home\Controller;
use Think\Controller;
/*
* 公共控制器类，所有控制器都继承该控制器
*/
class CommonController extends Controller{
	protected $is_cookie_login = 0;
	protected $vr_member_id = '';
	protected $vr_member_name = '';

	public function _initialize(){
		if ($this->checkCookie()){
			$this->is_cookie_login = 1;

			$model_member = M('Member');
			$member_info = $model_member->where(array("member_id"=>$this->vr_member_id))->find();

			if(!empty($member_info)){
				$this->vr_member_name = $member_info['member_name'];
			}
		}

		$this->assign('is_cookie_login', $this->is_cookie_login);
		$this->assign('vr_member_id', $this->vr_member_id);
		$this->assign('vr_member_name', $this->vr_member_name);
	}

	/*
	* 检测Cookie中是否有包含for_vr_member_id的键
	*/
	public function checkCookie(){
		$flag = false;
		if (is_array($_COOKIE) && count($_COOKIE) > 0){
		    foreach ($_COOKIE as $key => $value) {
		        if (strstr($key, 'for_vr_member_id')){
		        	$flag = true;
		        	$this->vr_member_id = $value;
		        }
		    }
		}

		return $flag;
	}
}