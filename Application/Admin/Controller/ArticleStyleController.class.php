<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 图文样式编辑
 */
class ArticleStyleController extends AdminController {
	
	function lists() {
		//$title  =   I('title',"");
       // $map['nickname']    =   array('like', '%'.(string)$title.'%');
		$list = M ( 'article_style')->selectPage(15);
		$groupModel = M('article_style_group');
		foreach($list['list_data'] as &$v){
			$gInfo = $groupModel -> find($v['group_id']);
			$v['group_name'] = $gInfo['group_name'];
		}
        $this->assign('_list', $list);
		$this->display ();
	}
	
	function groupList(){
		$list = M ( 'article_style_group')->selectPage(15);
        $this->assign('_list', $list);
		$this->display ();
	}
	
	/**
     * 新增样式
     * @author jacyxie <51daxigua@gmail.com>
     */
    public function add(){
		$postUrl = U('add');
		$this ->assign('postUrl',$postUrl);
		if(IS_POST){
			$Model = D('article_style');
            $data = $Model->create();
			if($data){
                $id = $Model->add();
                if($id){
                    $this->success('新增成功', U('lists'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
			//$info = M('article_style')->field(true)->find($id);
			$info['group_list'] = $this -> _get_group_list();
            $this->assign('info',$info);
            $this->display('edit');
        }
    }
	
	/**
     * 编辑配置
     * @author jacyxie <51daxigua@gmail.com>
     */
    public function edit($id = 0){
		$postUrl = U('edit',array('id'=>$id));
		$this ->assign('postUrl',$postUrl);
        if(IS_POST){
			$Model = D('article_style');
            $data = $Model->create();
            if($data){
                if($Model->save()!== false){
                   $this->success('更新成功', U('lists'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('article_style')->field(true)->find($id);
            $info['group_list'] = $this -> _get_group_list();
			foreach($info['group_list'] as &$v){
				if($v['id'] == $info['grooup_id']){
					$v['selected'] = 1;
				}else{
					$v['selected'] = 0;
				}
			}
            if(false === $info){
                $this->error('获取公告信息错误');
            }
            $this->assign('info', $info);
            $this->display();
        }
    }
	public function del(){
        $id = array_unique((array)I('id',0));
	
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('article_style')->where($map)->delete()){
             $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
	
	function groupAdd(){
		$postUrl = U('groupAdd');
		$this ->assign('postUrl',$postUrl);
        if(IS_POST){
			$Model = D('article_style_group');
            $data = $Model->create();
			if($data){
                $id = $Model->add();
                if($id){
                    $this->success('新增成功', U('groupList'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
			$info = M('article_style_group')->field(true)->find(I('id'));
            $this->assign('info',$info );
            $this->display();
        }
	}
	function groupEdit(){
		$id = I('id',0,intval);
		$postUrl = U('groupEdit',array('id'=>$id));
		$this ->assign('postUrl',$postUrl);
        if(IS_POST){
            $Model = D('article_style_group');
            $data = $Model->create();
            if($data){
                if($Model->save()!== false){
                   $this->success('更新成功', U('groupList'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('article_style_group')->field(true)->find($id);
            
            if(false === $info){
                $this->error('获取分组信息错误');
            }
            $this->assign('info', $info);
            $this->display('groupAdd');
        }
	}
	
	public function groupDel(){
        $id = array_unique((array)I('id',0));
	
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('article_style_group')->where($map)->delete()){
             $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
	function _get_group_list(){
		return  M('article_style_group') -> select();
	}
}
