<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function login($username = null, $password = null, $verify = null){
    	/* 读取数据库中的配置 */
    	$config	=	S('DB_CONFIG_DATA');
    	if(!$config){
    		$config	=	D('Config')->lists();
    		S('DB_CONFIG_DATA',$config);
    	}
    	C($config); //添加配置
    	    	
        if(IS_POST){
            /* 检测验证码 TODO: */
            if(C('WEB_SITE_VERIFY') && !check_verify($verify)){
                $this->error('验证码输入错误！');
            }

			/* 登录用户 */
			$User = D('Common/User');
			if($User->login($username, $password, 'admin_login')){ //登录用户
				//TODO:跳转到登录前页面
				$this->success('登录成功！', U('Index/index'));
			} else {
				$this->error($User->getError());
			}
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('Common/User')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}
