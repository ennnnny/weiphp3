<?php

namespace Addons\Comment\Controller;

use Home\Controller\AddonsController;

class CommentController extends AddonsController {
	function lists() {
		$this->assign ( 'add_button', false );
		$model = $this->getModel ( 'comment' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		// 搜索条件
// 		$nickname = I ( 'nickname' );
		
// 		$map ['token'] = $fmap ['token'] = get_token ();
		$map = $this->_search_map ( $model, $fields );
// 		if (! empty ( $nickname )) {
// 			$fmap ['nickname'] = array (
// 					'like',
// 					"%$nickname%" 
// 			);
// 			$follow_ids = D ( 'Comment/Follow' )->where ( $fmap )->getFields ( 'id' );
// 			if (empty ( $follow_ids )) {
// 				$map ['follow_id'] = 0;
// 			} else {
// 				$map ['follow_id'] = array (
// 						'in',
// 						$follow_ids 
// 				);
// 			}
// 		}
		if (I ( 'sports_id' )) {
			$map['aim_table']='sports';
			$map ['aim_id'] = I ( 'sports_id' );
		}
		if (I ( 'lzwg_id' )) {
			$map['aim_table']='lzwg';
			$map ['aim_id'] = I ( 'lzwg_id' );
		}
// 		empty ( $map ) || session ( 'common_condition', $map );
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		$map['uid']=$this->mid;
// 		dump($map);
		$list_data ['list_data'] = D ( 'Comment' )->where ( $map )->order ( 'id desc' )->page ( $page, $row )->select ();
// 		lastsql();
		/* 查询记录总数 */
		$count = D ( 'Comment' )->where ( $map )->count ();
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$sportsDao=D('Addons://Sports/Sports');
		foreach ( $list_data ['list_data'] as &$v ) {
			if($v['aim_table']=='sports'){
				$sports=$sportsDao->getInfo($v['aim_id']);
				$v['sports']=$sports['vs_team'];
				$param['id']=$v['id'];
				$param['is_audit']=$v['is_audit'];
			}
			$url=addons_url('Comment://Comment/change_audit',$param);
			$v['is_audit']=$v['is_audit']==0?'<a href="'.$url.'">未审核</a>':'审核通过';
			$v['content']=parseComment($v['content']);
			$follow = get_followinfo ( $v ['follow_id'] );
			$v = array_merge ( $follow, $v );
			$v['content'] = '<span class="selectRange" data-url='.addons_url('Comment://Comment/addBlack').'>'.$v['content'].'</span>';
		}
		$this->assign ( $list_data );
		//dump($list_data);
		
		$this->display ();
	}
	function addBlack(){
		$postData['value'] = I('post.word');
		$data['result'] = 'success';
		if(!empty($postData['value'])){
			$sensitiveStr = C ( 'SENSITIVE_WORDS' );
			$sensitiveArr = explode ( ',', $sensitiveStr );
			foreach ( $sensitiveArr as $v ) {
				if($v === $postData['value']){
					$data['result'] = 'fail';
					$data['msg'] = '关键词已经存在!';
					$this -> ajaxReturn($data,'JSON');
					break;
					exit;
				}
			}
			$postData['value'] = $sensitiveStr.','.$postData['value'];
			$res = M('config')->where("name = 'SENSITIVE_WORDS'")->save($postData);
			if($res){
				S ( 'DB_CONFIG_DATA',null);
				$data['result'] = 'success';
				$data['msg'] = '添加成功!';
				$this -> ajaxReturn($data,'JSON');
			}
			//dump($sensitiveStr);	
		}
	}
	function change_audit(){
		$is_audit = I ( 'is_audit' );
		$id = I ( 'id' );
		$data ['is_audit'] = $is_audit == 0 ? '1' : '0';
		M('comment')->where(array('id'=>$id))->setField('is_audit',$data['is_audit']);
		$this->success ( '修改成功' );
	}
}
