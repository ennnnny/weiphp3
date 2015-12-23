<?php
        	
namespace Addons\YouaskService\Model;
use Home\Model\WeixinModel;
        	
/**
 * YouaskService的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		
		$config = getAddonConfig ( 'YouaskService' ); // 获取后台插件的配置参数	
		$content = $dataArr["Content"];
		//判断微信用户发送的消息中的关键词
		$token = get_token();	
       		//addWeixinLog ("进入", 0);
		//直接进入人工操作
		if($config["zrg"] == $content){			
			//$this->transmitService($dataArr);			
			//判断是否有在线的客服,电脑优先
			$zxlists = $this->kfzxstate();
			$len = sizeof($zxlists);
			
			if($len !=0){
				$kfaccount = $zxlists[rand(0,($len-1))];
				$this->transmitServiceZD($dataArr,$kfaccount);
			}else{
				$this->transmitService($dataArr);
			}
			exit();
		}	   
	 
        $keywords=M('youaskservice_keyword');
		$keywordsValue = $keywords->where(" token='".$token."' and instr('".$content."',msgkeyword) >0 ")->order("id desc")->select();           
              
		//查询匹配方式
		foreach($keywordsValue as $v){
			
			$ispass  =false;
			//多项匹配,只取第一项
			switch($v["msgkeyword_type"]){				
				case "0":
					if($v["msgkeyword"] == $content){
						$ispass  =true;
					}
					break;
				case "1":
					if($v["msgkeyword"] == substr($content,strlen($v["msgkeyword"]))){
						$ispass  =true;
					}
					break;
				case "2":
					if($v["msgkeyword"] == substr($content,-strlen($v["msgkeyword"]))){
						$ispass  =true;
					}
					break;
				case "3":					
					//发送到客服0:指定人员 1:指定客服组					
					$ispass  =true;					
					break;				
			}
			
			if($ispass){
				if($v["zdtype"] == 0){
					$this->transmitServiceZD($dataArr,$v["msgkfaccount"]);
					exit();
				}else{
					//查询组
					$group =  M("youaskservice_group")->where(array("token"=>$token,"id"=>$v["kfgroupid"]))->find();
					if($group){
						$kfgroup = unserialize($group["groupdata"]);
						$len = sizeof($kfgroup);
						$kfaccount = $kfgroup[rand(0,($len-1))];
						$this->transmitServiceZD($dataArr,$kfaccount);
						exit();
					}
				}	
			}	
				
		}			
	} 
	
	//客服在线状态
	public function kfzxstate(){
		header("Content-type: text/html; charset=utf-8"); 
				
		$access_token = $this->getaccess_token();	
		$url_get = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$access_token;				
		$json = $this->curlGet($url_get);			
		$json =json_decode($json);
		
		$kf_onlinelists = $json->kf_online_list;
		$kflist =array();
		//补充昵称		
		foreach	($kf_onlinelists as $value ) {
			$kflist[] = $value->kf_account;			
		}
					

		return $kflist;
	}
	
	//获取微信认证
	function getaccess_token(){
		return get_access_token();
	}
	
	function curlGet($url, $method = 'get', $data = '')
    {		
        $ch = curl_init();
        $headers = array('Accept-Charset: utf-8');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }
	
	//回复多客服消息
    private function transmitService($object)
    {
		if($config["model"] == 1){
			$xmlTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[transfer_customer_service]]></MsgType>
	</xml>";
			$result = sprintf($xmlTpl, $object["FromUserName"], $object["ToUserName"], time());
			echo $result;
		}
    }
	
	
	//回复多客服消息(指定客服)
    private function transmitServiceZD($object,$kf)
    {
		$config = getAddonConfig ( 'YouaskService' ); // 获取后台插件的配置参数	
		if($config["model"] == 1){	
			$xmlTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[transfer_customer_service]]></MsgType>
	<TransInfo>
			<KfAccount>%s</KfAccount>
	</TransInfo>
	</xml>";
			$result = sprintf($xmlTpl, $object["FromUserName"], $object["ToUserName"], time(),$kf);
			echo $result;
		}
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
}
        	