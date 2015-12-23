<?php

namespace Addons\YouaskService\Controller;
use Addons\YouaskService\Controller\BaseController;

class YouaskServiceController extends BaseController{
	function _initialize() {
		parent::_initialize ();
		
		// 子导航
		$action = strtolower ( _ACTION );
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '工号管理';
		$res ['url'] = addons_url (  'YouaskService://YouaskService/lists'  );
		$res ['class'] = $action == 'lists'   ? 'cur' : '';
		$nav [] = $res;	

		$res ['title'] = '客服分组';
		$res ['url'] = addons_url (  'YouaskService://Group/lists'  );
		$res ['class'] = ($controller == "group" && $action == 'lists') ? 'cur' : '';
		$nav [] = $res;			
			
		$res ['title'] = '客服在线状态';
		$res ['url'] = addons_url ( 'YouaskService://YouaskService/kfzxstate' );
		$res ['class'] = $action == 'kfzxstate' ? 'cur' : '';
		$nav [] = $res;		
				
		$this->assign ( 'sub_nav', $nav );
	
	}


	protected $model;
	public function __construct() {
		parent::__construct ();
		$this->model = M('model')->getByName ( 'youaskservice_user' );
		$this->model || $this->error ( '模型不存在！' );
		$this->assign ( 'model', $this->model );
	}
			
	/********************************基本管理*******************************************/
	//客服在线状态
	public function kfzxstate(){
		header("Content-type: text/html; charset=utf-8"); 
		$token = get_token();
		$list_grids=array();
		$list_grids[] = array("title"=>"客服编号","field"=>"kf_id");
		$list_grids[] = array("title"=>"昵称","field"=>"name");
		$list_grids[] = array("title"=>"帐号","field"=>"kf_account");
		$list_grids[] = array("title"=>"正在等待接入的用户","field"=>"accepted_case");
		$list_grids[] = array("title"=>"最大自动接入数","field"=>"auto_accept");
		$list_grids[] = array("title"=>"在线状态","field"=>"status");
		
		$this->assign ( 'list_grids', $list_grids );
		
		$access_token = $this->getaccess_token();	
		$url_get = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$access_token;				
		$json = $this->curlGet($url_get);		
		$json =json_decode($json);
		
		$kf_onlinelists = $json->kf_online_list;
		//补充昵称		
		foreach	($kf_onlinelists as $value ) {
			$userinfo = M('youaskservice_user')->where(array("token"=>$token,"kfid"=>$value->kf_id,"userName"=>$value->kf_account))->find();
			if($userinfo ){
				$value->name = $userinfo["name"];
			}
		}
		$this->assign ( 'list_data', $kf_onlinelists );
		$this->display ("zxstatelists");
	}
	
	//同步客服工号
	public function tongbugonghao($istis=true){	
		header("Content-type: text/html; charset=utf-8"); 
		$token = get_token();
	
		$access_token = $this->getaccess_token();
		$url_get = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$access_token;				
		$json = $this->curlGet($url_get);		
		$json =json_decode($json);		
		if (!$json->errmsg) {
            
        } else {
			$this->error((('同步客服工号发生错误：错误代码' . $json->errcode) . ',微信返回错误信息：') . $json->errmsg);
        }
		M('youaskservice_user')->where(array("token"=>$token))->delete();
		$kflists = $json->kf_list; 
		foreach	($kflists as $value ) {
			$users = M('youaskservice_user');
			$users->create();
			$users->token =	$token;
			$users->name = $value->kf_nick;
			$users->kfid = $value->kf_id;
			$users->userName = $value->kf_account;
			$users->add();
		}
		if($istis){
			$this->success ( '同步成功！' );
		}
	}	
	
	
	
	
	/**
	 * 显示指定模型列表数据
	 */
	public function lists() {
		// 使用提示
		$tongbugonghaoUrl = U('tongbugonghao');
		$normal_tips = '工号管理：工号自动从微信平台获取,如有修改请&nbsp;<a href="'.$tongbugonghaoUrl.'">同步工号信息</a><br/>注意:<br/>&nbsp;&nbsp;&nbsp;&nbsp; 客服帐号@微信别名<br/>&nbsp;&nbsp;&nbsp;&nbsp; 微信别名如有修改，旧帐号使用旧的微信别名，新增的帐号使用新的微信别名 ';
		$this->assign ( 'normal_tips', $normal_tips );
			
		$this->assign ( 'check_all', false );
	
		$token = get_token();
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据		                                
		// 解析列表规则
		$fields = array ();
		$grids = preg_split ( '/[;\r\n]+/s', $this->model ['list_grid'] );
		foreach ( $grids as &$value ) {
			// 字段:标题:链接
			$val = explode ( ':', $value );
			// 支持多个字段显示
			$field = explode ( ',', $val [0] );
			$value = array (
					'field' => $field,
					'title' => $val [1] 
			);
			if (isset ( $val [2] )) {
				// 链接信息
				$value ['href'] = $val [2];
				// 搜索链接信息中的字段信息
				preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
					$fields [] = $match [1];
				}, $value ['href'] );
			}
			if (strpos ( $val [1], '|' )) {
				// 显示格式定义
				list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
			}
			foreach ( $field as $val ) {
				$array = explode ( '|', $val );
				$fields [] = $array [0];
			}
		}
		// 过滤重复字段信息
		$fields = array_unique ( $fields );
		// 关键字搜索
		$map ['token'] = $token;
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
					'like',
					'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		$row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];
		
		// 读取模型数据列表		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
				
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		if($count==0){
			$this->tongbugonghao(false);
			$count = M ( $name )->where ( $map )->count ();
		}
		
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();		
		
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		
		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->meta_title = $this->model ['title'] . '列表';
		
		
		$this->display ();		
	}
	
	public function add() {			
		if (IS_POST) {
			// 自动补充token
			$_POST ['token'] = get_token ();
						
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $micsetid = $Model->add ()) {					
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {			
			$micset_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $micset_fields );		
			
			$this->meta_title = '新增' . $this->model ['title'];
			$this->display ('add');			
		}
	}
	
	public function edit() {
		// 获取模型信息
		$id = I ( 'id', 0, 'intval' );			
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {	
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );			
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}			
						
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $this->model ['title'];
			$this->display ( 'edit');
		}
	}
	
	public function del() {
		$ids = I ( 'id', 0 );
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据!' );
		}
		
		$Model = M ( get_table_name ( $this->model ['id'] ) );
		$map = array (
				'id' => array (
						'in',
						$ids 
				) 
		);
		$map ['token'] = get_token ();		
		
		if ($Model->where ( $map )->data (array("isdelete"=>1))->save()) {
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	
	//批量启用或停用管理
	public function plqtguanli() {
		$ids = I ( 'id', 0 );
		$type = I ( 'get.type', 0, 'intval' );
		
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据!' );
		}
		
		$Model = M ( get_table_name ( $this->model ['id'] ) );
		$map = array (
				'id' => array (
						'in',
						$ids 
				) 
		);
		$map ['token'] = get_token ();		
		
		if ($Model->where ( $map )->data (array("state"=>$type))->save()) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败！' );
		}
	}
	/********************************微信客服设置*******************************************/
	
	function config() {
		// 使用提示
		$normal_tips = '微信客服是指微信公众平台自带的多客服系统，只有认证服务号才有此功能。<br/>
微信客服启用方法，必须满足两个条件：<br/>&nbsp;&nbsp;&nbsp;&nbsp;1、有客服处于登录状态；<br/>&nbsp;&nbsp;&nbsp;&nbsp;2、下面的人工客服状态处于打开中。<br/>这样系统在回答不上来的时候就会切换到客服模式。<br/>在客服模式下系统将不再做任何自动回复。
如需彻底关闭微信客服：必须让客服全部下线，并且在下面关掉客服状态';
		$this->assign ( 'normal_tips', $normal_tips );
		
		if (IS_POST) {
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, $_POST ['config'] );
			
			if ($flag !== false) {
				$this->success ( '保存成功');
			} else {
				$this->error ( '保存失败' );
			}
			exit ();
		}
		
		parent::config ();
	}	
	
		
	/********************************其他操作*******************************************/
	
}
