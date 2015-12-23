<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Model;

use Think\Model;

/**
 * 分类模型
 */
class MaterialModel extends Model {
	protected $tableName = 'material_news';
	
	/**
	 * 获取导航列表，支持多级导航
	 *
	 * @param boolean $field
	 *        	要列出的字段
	 * @return array 导航树
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function getMediaIdByGroupId($group_id) {
		$map ['group_id'] = $group_id;
		$list = $this->where ( $map )->order ( 'id asc' )->select ();
		if (! empty ( $list [0] ['media_id'] ))
			return $list [0] ['media_id'];
			
			// 自动同步到微信端
		foreach ( $list as $vo ) {
			$data ['title'] = $vo ['title'];
			$data ['thumb_media_id'] = empty ( $vo ['thumb_media_id'] ) ? $this->_thumb_media_id ( $vo ['cover_id'] ) : $vo ['thumb_media_id'];
			$data ['author'] = $vo ['author'];
			$data ['digest'] = $vo ['intro'];
			$data ['show_cover_pic'] = 1;
			$data ['content'] = $vo ['content'];
			$data ['content_source_url'] = U ( 'news_detail', array (
					'id' => $vo ['id'] 
			) );
			
			$articles [] = $data;
		}
		
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . get_access_token ();
		$param ['articles'] = $articles;
		
		$res = post_data ( $url, $param );
		if ($res ['errcode'] != 0) {
			return false;
		} else {
			$this->where ( $map )->setField ( 'media_id', $res ['media_id'] );
			return $res ['media_id'];
		}
	}
	function _thumb_media_id($cover_id) {
		$cover = get_cover ( $cover_id );
		$driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		if ($driver != 'Local' && ! file_exists ( SITE_PATH . $cover ['path'] )) { // 先把图片下载到本地
			
			$pathinfo = pathinfo ( SITE_PATH . $cover ['path'] );
			mkdirs ( $pathinfo ['dirname'] );
			
			$content = wp_file_get_contents ( $cover ['url'] );
			$res = file_put_contents ( SITE_PATH . $cover ['path'], $content );
			if ($res) {
				return '';
			}
		}
		
		$path = $cover ['path'];
		if (! $path) {
			return '';
		}
		
		$param ['type'] = 'thumb';
		$param ['media'] = '@' . realpath ( SITE_PATH . $path );
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token ();
		$res = post_data ( $url, $param, true );
		
		if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
			return '';
		}
		
		$map ['cover_id'] = $cover_id;
		$map ['manager_id'] = $this->mid;
		$this->where ( $map )->setField ( 'thumb_media_id', $res ['media_id'] );
		
		return $res ['media_id'];
	}
}
