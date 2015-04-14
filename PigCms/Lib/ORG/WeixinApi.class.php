<?php
class WeixinApi
{	
	private $appId		= '';
	private $appSecret	= '';
	public $error 		= array();
	public $token 		= '';
	public $wecha_id 		= '';
	
	public function __construct($appId,$appSecret,$token,$wecha_id)
	{
		$this->appId		= $appId;
		$this->appSecret	= $appSecret;
		$this->token		= $token;
		$this->wecha_id		= $wecha_id;
	}
	
	/**
	 * 获取公众号对应的access_token
	 * @param unknown $token
	 * @param unknown $appid
	 * @param unknown $appSecret
	 */
	public function getAccessToken(){
		
		$now 	= time();
		//先查找公众号对应数据库，是否有保存或过期
		$access_token_data 	= M('Wxuser')->where(array('token'=>$this->token))->field('access_token,expires_in')->find();
		if( (empty($access_token_data['access_token']) || empty($access_token_data['expires_in']) ) || ($access_token_data['access_token']!='' && $access_token_data['expires_in']!='' && $access_token_data['expires_in'] < $now ) ){
			$tokenData 	= $this->getToken();
			if($tokenData['errcode']){
				$this->error['token_error'] 	= array('errcode'=>$tokenData['errcode'],'errmsg'=>$tokenData['errmsg']);
			}else{
				M('Wxuser')->where(array('token'=>$this->token))->save(array('access_token'=>$tokenData['access_token'],'expires_in'=>$now+$tokenData['expires_in']));
				$access_token 	= $tokenData['access_token'];
			}
		}else{
			$access_token 		= $access_token_data['access_token'];
		}
		
		return $access_token;
	}
	
	public function getError(){
		dump($this->error);
	}
	
	//获取token
	private function  getToken(){
		$url 	= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
		return $this->https_request($url);
	}
	
	//https请求（支持GET和POST）
	protected function https_request($url, $data = null)
	{
		$curl = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		$errorno= curl_errno($curl);
		if ($errorno) {
			return array('curl'=>false,'errorno'=>$errorno);
		}else{
			$res = json_decode($output,1);
	
			if ($res['errcode']){
				return array('errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']);
			}else{
				return $res;
			}
		}
		curl_close($curl);
	}
}
?>

