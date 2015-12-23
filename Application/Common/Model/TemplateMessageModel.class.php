<?php

namespace Common\Model;

use Think\Model;

/**
 * 微信客服接口操作类
 */
class TemplateMessageModel extends Model {
	protected $tableName = 'user';
	/* 礼包领取通知消息模板 */
	public function replyGiftNotice($uid, $name,$first='',$orderId='',$remark='',$url='',$templateId='') {
		$first == '' && $first='您推荐的爱心分享已被领取';
		$orderId=='' && $orderId=time_format(time(),'YmdHis');
		$remark =='' && $remark='您的好友领取了您推荐的爱心分享，您的人气指数直接爆表！';
	    $param['data']['first']['value']=$first;
	    $param['data']['first']['color']="#E60B43";
		
	    $param['data']['keyword1']['value']=$orderId;
	    $param['data']['keyword1']['color']="#173177";
	    
	    $param['data']['keyword2']['value']=$name;
	    $param['data']['keyword2']['color']="#E60B43";
	    
	    $param['data']['remark']['value']=$remark;
	    $param['data']['remark']['color']="#173177";
	    
	    $templateId=='' && $templateId='VD0sCtsox8YHjFh12XzXRrS-k6-5GN3KMN8McPw0IiY';
		return $this->_replyData ( $uid, $param, $templateId,$url);
	}
	
	/* 礼包领取失败通知消息模板 */
	public function replyGiftFail($uid, $actName,$reason,$giftName='',$remark='',$first='',$url='',$templateId='') {
	   
	    $first == '' && $first='亲爱的用户：';
	    $giftName =='' && $giftName='礼品';
	    $remark =='' && $remark='感谢您的参与!';
	    
	    $param['data']['first']['value']=$first;
	    $param['data']['first']['color']="#173177";
	
	    $param['data']['name']['value']=$actName;
	    $param['data']['name']['color']="#E60B43";
	     
	    $param['data']['giftName']['value']=$giftName;
	    $param['data']['giftName']['color']="#173177";
	     
	    $param['data']['reason']['value']=$reason;
	    $param['data']['reason']['color']="#E60B43";
	    
	    $param['data']['remark']['value']=$remark;
	    $param['data']['remark']['color']="#173177";
	     
	    $templateId=='' && $templateId='JUX4gPYu5BgXj4XLakTvAfMpSmFZoQ_gQ0eKy6MF8wk';
	    return $this->_replyData ( $uid, $param, $templateId,$url);
	}
	
	/* 优惠券领取成功通知 */
	public function replyCouponSuccess($uid, $couponName,$snCode,$endTime,$remark='',$first='',$url='',$templateId='') {
	
	    $first == '' && $first='恭喜您领到一张优惠券！';
	    $remark =='' && $remark='凭兑换码到店使用！';
	     
	    $param['data']['first']['value']=$first;
	    $param['data']['first']['color']="#173177";
	
	    $param['data']['keyword1']['value']=$couponName;
	    $param['data']['keyword1']['color']="#E60B43";
	
	    $param['data']['keyword2']['value']=$snCode;
	    $param['data']['keyword2']['color']="#173177";
	
	    $param['data']['keyword3']['value']=$endTime;
	    $param['data']['keyword3']['color']="#E60B43";
	     
	    $param['data']['remark']['value']=$remark;
	    $param['data']['remark']['color']="#173177";
	
	    $templateId=='' && $templateId='4tFFvlKkiUbVEuK6DHTJWVCrFHntiS-qy_P-BwsY3lM';
	    return $this->_replyData ( $uid, $param, $templateId,$url);
	}
	
	/* 获得代金券通知 */
	public function replyShopCouponSuccess($uid, $couponMoney,$endTime,$remark='',$first='',$url='',$templateId='') {
	
	    $first == '' && $first='尊敬的客户，您已获得'.$couponMoney.' 代金券';
	    $remark =='' && $remark='凭兑换码到店使用！';
	
	    $param['data']['first']['value']=$first;
	    $param['data']['first']['color']="#173177";
	
	    $param['data']['coupon']['value']=$couponMoney;
	    $param['data']['coupon']['color']="#E60B43";
	
	    $param['data']['expDate']['value']=$endTime;
	    $param['data']['expDate']['color']="#173177";
	
	    $param['data']['remark']['value']=$remark;
	    $param['data']['remark']['color']="#173177";
	
	    $templateId=='' && $templateId='WVPA48f6MkeSonU4B2Htqj1I7h99Ksltwvsw3Mbqi_E';
	    return $this->_replyData ( $uid, $param, $templateId,$url);
	}
	
	/* 返现到账通知 */
	public function replyReturnMoney($uid, $money,$content,$remark='',$first='',$url='',$templateId='') {
	
	    $first == '' && $first='尊敬的用户您好，您的一笔返现已到账。';
	    $remark =='' && $remark='感谢你的使用，谢谢！';
	
	    $param['data']['first']['value']=$first;
	    $param['data']['first']['color']="#173177";
	
	    $param['data']['keyword1']['value']=$money;
	    $param['data']['keyword1']['color']="#E60B43";
	
	    $param['data']['keyword2']['value']=$content;
	    $param['data']['keyword2']['color']="#173177";
	
	    $param['data']['remark']['value']=$remark;
	    $param['data']['remark']['color']="#173177";
	
	    $templateId=='' && $templateId='na8JwAd--iYlefDZknhhKOFpmfGF6jSI83o2LL1oKzs';
	    return $this->_replyData ( $uid, $param, $templateId,$url);
	}
	/* 发送回复模板消息到微信平台 */
	function _replyData($uid, $param,$template_id,$jumpUrl='') {
		$map ['token'] = get_token ();
		$map ['uid'] = $uid;
		$param ['touser'] = M ( 'public_follow' )->where ( $map )->getField ( 'openid' );
        $param['template_id']=$template_id;
        $param['url']=$jumpUrl;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . get_access_token ();
		// dump($param);
		// die;
		$result ['status'] = 0;
		$result ['msg'] = '发送失败';
		$res = post_data ( $url, $param );
		
		if ($res ['errcode'] != 0) {
			$result ['msg'] = error_msg ( $res );
		} else {
			$result ['status'] = 1;
			$result ['msg'] = '发送成功';
		}
		return $result;
	}
}
?>
