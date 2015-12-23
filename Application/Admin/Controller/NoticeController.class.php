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
 * 公告管理
 */
class NoticeController extends AdminController {
	
	function lists() {
		//$title  =   I('title',"");
       // $map['nickname']    =   array('like', '%'.(string)$title.'%');
		$list = M ( 'SystemNotice')->selectPage(15);
        $this->assign('_list', $list);
		$this->display ();
	}
	
	/**
     * 新增公告
     * @author jacyxie <51daxigua@gmail.com>
     */
    public function add(){
		$postUrl = U('add');
		$this ->assign('postUrl',$postUrl);
        if(IS_POST){
			$_POST['create_time'] = time();
            $Model = D('SystemNotice');
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
            $this->assign('info',array('id'=>I('id')));
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
            $Model = D('SystemNotice');
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
            $info = M('SystemNotice')->field(true)->find($id);
            
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
        if(M('SystemNotice')->where($map)->delete()){
             $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
