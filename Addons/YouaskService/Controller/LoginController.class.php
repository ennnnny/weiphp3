<?php

namespace Addons\YouaskService\Controller;
use Addons\YouaskService\Controller\ChatBaseController;

class LoginController extends ChatBaseController{		
	
	/********************************基本管理*******************************************/
		
	public function index(){
		if(IS_POST){
			//查询是否开启了客服系统
			$YouaskServiceconfig = getAddonConfig ( 'YouaskService' ); // 获取后台插件的配置参数	
			if($YouaskServiceconfig['state'] == 1){		
				if(session('YouaskService_userId') != ""){
					$this->success('您已经登录过,现在为你转到主页',addons_url ( 'YouaskService://Index/index' ));
				}else{
					$userName=I ( 'post.userName','');
					$data['userPwd']=I ( 'post.userPwd','');
					if($userName=='' || $data['userPwd']==''){
						$this->error('帐号必须填写');
					}			
					if((!strpos($userName,'@') === FALSE)){
							$user=explode('@',$userName);
							$data['userName']=$user[0];
							$data['token']=$user[1];
							if($data['userName']==false || $data['token']==false){
								$this->error('帐号格式不正确');
							}
					}else{
						$this->error('帐号格式错误');
					}
					$data["isdelete"] = 0;
					$back=M('youaskservice_user')->where($data)->find();
					if($back!=false){
						if($back['state'] == 0){
							$this->error('抱歉,您的帐号已被停用!');
						}			
						session('YouaskService_userId',$back['id']);
						session('YouaskService_name',$back['name']);
						session('YouaskService_token',$data['token']);
						session('YouaskService_userName',$back['userName']);
						
						$this->success('登录成功',addons_url ( 'YouaskService://Index/index' ));
					}else{
						$this->error('您的登录信息错误<br />请核实后再登录');
					}
				}
			}else{
				$this->error('抱歉,客服系统已关闭!');
			}
		}else{	
			
			$this->display(T ( 'Addons://YouaskService@default/YouaskService/Chat/default/login' ));
		}
	
	}
	
}
