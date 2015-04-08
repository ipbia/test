<?php
class WapAction extends BaseAction {
	public $token;
	public $wecha_id;
	public $fans;
	public $homeInfo;
	public $bottomeMenus;
	public $wxuser;
	public $user;
	public $group;
	public $company;
	public $shareScript;
	public $sign;
protected function _initialize() {
		parent::_initialize();
		
		$this->token = $this->_get('token');
		$this->assign('token', $this->token);
		
		
		if ($this->token && !preg_match("/^[0-9a-zA-Z]{3,42}$/", $this->token)) {
			exit('error token');
		}
		$this->wxuser = S('wxuser_' . $this->token);
		if (!$this->wxuser || 1) {
			$this->wxuser = D('Wxuser')->where(array('token' => $this->token))->find();
			S('wxuser_' . $this->token, $this->wxuser);
		}
		$this->assign('wxuser', $this->wxuser);
/*		
///////////////////////////////////////////////////////////////////////////////////////		
	if ($this->wxuser['winxintype'] == 3 && !isset($_GET['code']) && !isset($_GET['oneid'])) {
			$customeUrl = $this->siteUrl . $_SERVER['REQUEST_URI'];
			
			$customeUrl_arr = explode('&', $customeUrl);
			foreach ($customeUrl_arr as $key=>$val){
				if(!strstr($val, 'wecha_id')){
					$customeUrl_arr2[] = $val;
				}
			}
			$customeUrl = implode('&', $customeUrl_arr2);
			$scope = 'snsapi_base';
			if (isset($_GET['diymenu'])) {
				$scope = 'snsapi_base';
			}
			$oauthUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->wxuser['appid'] . '&redirect_uri=' . urlencode($customeUrl) . '&response_type=code&scope=' . $scope . '&state=oauth#wechat_redirect';
			header('Location:' . $oauthUrl);
			exit();
		}
//////////////////////////////////////////////////////////////////////////////////////
 */
		
		if ((!$_GET['wecha_id'] || urldecode($_GET['wecha_id']) == '{wechat_id}') &&$_GET['wecha_id'] != 'no' && $this->wxuser['winxintype'] == 3 && !isset($_GET['code'])) {
			$customeUrl = $this->siteUrl . $_SERVER['REQUEST_URI'];
			$scope = 'snsapi_base';
			if (isset($_GET['diymenu'])) {
				$scope = 'snsapi_base';
			}
			$oauthUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->wxuser['appid'] . '&redirect_uri=' . urlencode($customeUrl) . '&response_type=code&scope=' . $scope . '&state=oauth#wechat_redirect';
			header('Location:' . $oauthUrl);
			exit();
		}
		
		if (isset($_GET['code']) && isset($_GET['state']) && ($_GET['state'] == 'oauth')) {
			
			$rt = $this->curlGet('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->wxuser['appid'] . '&secret=' . $this->wxuser['appsecret'] . '&code=' . $_GET['code'] . '&grant_type=authorization_code');
			$jsonrt = json_decode($rt, 1);
			$openid = $jsonrt['openid'];
			$access_token = $jsonrt['access_token'];
			$_GET['wecha_id'] = $openid;
			$this->wecha_id = $openid;
			if (!$openid) {
				$this->error('授权不对：' . $jsonrt['errcode'], '#');
				exit();
			}
			
		}else {
		
			$this->wecha_id = $this->_get('wecha_id');
		}
		$this->assign('wecha_id', $this->wecha_id);
		$fansInfo = S('fans_' . $this->token . '_' . $this->wecha_id);
		if (!$fansInfo || 1) {
			$fansInfo = M('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
			$advanceInfo = M('Wechat_group_list')->where(array('token' => $this->token, 'openid' => $this->wecha_id))->find();
			if ($advanceInfo) {
				$fansInfo['nickname'] = $advanceInfo['nickname'];
				if (!$fansInfo['wechaname']) {
					$fansInfo['wechaname'] = $advanceInfo['nickname'];
				}
				$fansInfo['sex'] = $advanceInfo['sex'];
				$fansInfo['province'] = $advanceInfo['province'];
				$fansInfo['city'] = $advanceInfo['city'];
			}
			S('fans_' . $this->token . '_' . $this->wecha_id, $fansInfo);
		}
		$this->fans = $fansInfo;
		$this->assign('fans', $fansInfo);
		$homeInfo = S('homeinfo_' . $this->token);
		if (!$homeInfo || 1) {
			$homeInfo = M('home')->where(array('token' => $this->token))->find();
			S('homeinfo_' . $this->token, $homeInfo);
		}
		$this->homeInfo = $homeInfo;
		$this->assign('homeInfo', $this->homeInfo);
		$catemenu = S('bottomMenus_' . $this->token);
		if (!$catemenu || 1) {
			$catemenu_db = M('catemenu');
			$catemenu = $catemenu_db->where(array('token' => $this->token, 'status' => 1))->order('orderss desc')->select();
			S('bottomMenus_' . $this->token, $catemenu);
		}
		$menures = array();
		if ($catemenu) {
			$res = array();
			$rescount = 0;
			foreach ($catemenu as $val) {
				$val['url'] = $this->getLink($val['url']);
				$res[$val['id']] = $val;
				if ($val['fid'] == 0) {
					$val['vo'] = array();
					$menures[$val['id']] = $val;
					$menures[$val['id']]['k'] = $rescount;
					$rescount++;
				}
			}
			foreach ($catemenu as $val) {
				$val['url'] = $this->getLink($val['url']);
				if ($val['fid'] > 0) {
					array_push($menures[$val['fid']]['vo'], $val);
				}
			}
		}
		$catemenu = $menures;
		$this->bottomeMenus = $catemenu;
		$this->assign('catemenu', $this->bottomeMenus);
		//判断菜单风格
		$radiogroup = $homeInfo['radiogroup'];
		if ($radiogroup == false) {
			$radiogroup = 0;
		}
		$cateMenuFileName = 'tpl/Wap/default/Index_menuStyle' . $radiogroup . '.html';
		$this->assign('cateMenuFileName', $cateMenuFileName);
		$this->assign('radiogroup', $radiogroup);
		$this->user = S('user_' . $this->wxuser['uid']);
		if (!$this->user || 1) {
			$this->user = D('Users')->find(intval($this->wxuser['uid']));
			S('user_' . $this->wxuser['uid'], $this->user);
		}
		$this->assign('user', $this->user);
		$this->group = S('group_' . $this->user['gid']);
		if (!$this->group || 1) {
			$this->group = M('User_group')->where(array('id' => intval($this->user['gid'])))->find();
			S('group_' . $this->user['gid'], $this->group);
		}
		$this->assign('group', $this->group);
		$this->company = S('company_' . $this->token);
		if (!$this->company || 1) {
			$company_db = M('company');
			$this->company = $company_db->where(array('token' => $this->token, 'isbranch' => 0))->find();
			S('company_' . $this->token, $this->company);
		}
		$this->assign('company', $this->company);
		$this->copyright = $this->group['iscopyright'];
		$this->assign('iscopyright',$this->copyright);//是否允许自定义版权
		$this->assign('siteCopyright',C('copyright'));//站点版权信息
		$this->assign('copyright', $this->copyright);
		$signPackage = $this->getSignPackage(); //用的时候开启
		//分享
		$share 	= new WechatShare($this->wxuser['appid'],$this->wxuser['appsecret'],$this->token,$this->wecha_id);
		//$share->getError();
		$this->shareScript=$share->getSgin();
		$this->assign('shareScript',$this->shareScript);
		
}
	
private	function getAccessToken(){ //获取微信access_token
		$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->thisWxUser['appid'].'&secret='.$this->thisWxUser['appsecret'];
		$json=json_decode($this->curlGet($url_get));
		if (!$json->errmsg){
		}else {
			$this->error('获取access_token发生错误：fff错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
		}
		return $json->access_token;
	}
public function getSignPackage() { //获取jsapi相关参数
	$where=array('token'=>$this->token);
	$this->thisWxUser=M('Wxuser')->where($where)->find();
    $jsapiTicket = $this->getJsApiTicket();
    $url = "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=".$jsapiTicket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->thisWxUser['appid'],
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $where=array('token'=>$this->token);
	$data=M('Wxuser')->where($where)->find();
	
	if($data['jsapi_ticket_time']<time()){ //已过期
		
	  $accessToken = $this->getAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken;
      $res = json_decode($this->curlGet($url));
      $ticket = $res->ticket;
      
      if($ticket){
      	 $in_data['id'] = $data['id'];
      	 $in_data['jsapi_ticket'] = $ticket;
      	 $in_data['jsapi_ticket_time'] = 7000 + time();
     	 M('Wxuser')->save($in_data);
      }
      
	}else{
		
	  $ticket = $data['jsapi_ticket'];
	}
    return $ticket;
  }

	public function getLink($url) {
		$url = $url?$url:'javascript:void(0)';
		$urlArr = explode(' ', $url);
		$urlInfoCount = count($urlArr);
		if ($urlInfoCount > 1) {
			$itemid = intval($urlArr[1]);
		}
		//会员卡 刮刮卡 团购 商城 大转盘 优惠券 订餐 商家订单 表单
		if ($this->strExists($url, '刮刮卡')) {
			$link = '/index.php?g=Wap&m=Guajiang&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link .= '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '大转盘')) {
			$link = '/index.php?g=Wap&m=Lottery&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link .= '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '优惠券')) {
			$link = '/index.php?g=Wap&m=Coupon&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link .= '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '刮刮卡')) {
			$link = '/index.php?g=Wap&m=Guajiang&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link .= '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '商家订单')) {
			if ($itemid) {
				$link = $link = '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&hid=' . $itemid;
			}else {
				$link = '/index.php?g=Wap&m=Host&a=Detail&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			}
		}elseif ($this->strExists($url, '万能表单')) {
			if ($itemid) {
				$link = $link = '/index.php?g=Wap&m=Selfform&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '相册')) {
			$link = '/index.php?g=Wap&m=Photo&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Photo&a=plist&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '全景')) {
			$link = '/index.php?g=Wap&m=Panorama&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Panorama&a=item&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '会员卡')) {
			$link = '/index.php?g=Wap&m=Card&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
		}elseif ($this->strExists($url, '商城')) {
			$link = '/index.php?g=Wap&m=Product&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
		}elseif ($this->strExists($url, '订餐')) {
			$link = '/index.php?g=Wap&m=Product&a=dining&dining=1&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
		}elseif ($this->strExists($url, '团购')) {
			$link = '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
		}elseif ($this->strExists($url, '首页')) {
			$link = '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
		}elseif ($this->strExists($url, '网站分类')) {
			$link = '/index.php?g=Wap&m=Index&a=lists&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Index&a=lists&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&classid=' . $itemid;
			}
		}elseif ($this->strExists($url, '图文回复')) {
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, 'LBS信息')) {
			$link = '/index.php?g=Wap&m=Company&a=map&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Company&a=map&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&companyid=' . $itemid;
			}
		}elseif ($this->strExists($url, 'DIY宣传页')) {
			$link = '/index.php/show/' . $this->token;
		}elseif ($this->strExists($url, '婚庆喜帖')) {
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Wedding&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}elseif ($this->strExists($url, '投票')) {
			if ($itemid) {
				$link = '/index.php?g=Wap&m=Vote&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&id=' . $itemid;
			}
		}else {
			$link = str_replace(array('{wechat_id}', '{siteUrl}', '&amp;'), array($this->wecha_id, $this->siteUrl, '&'), $url);
			if (!!(strpos($url, 'tel') === false) && $url != 'javascript:void(0)' && !strpos($url, 'wecha_id=')) {
				if (strpos($url, '?')) {
					$link = $link . '&wecha_id=' . $this->wecha_id;
				}else {
					$link = $link . '?wecha_id=' . $this->wecha_id;
				}
			}
			
		}
		return $link;
	}
	public function strExists($haystack, $needle){
		return !(strpos($haystack, $needle) === FALSE);
	}
	public function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
	
	public function curlPost($url, $data,$showError=1){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if (intval($js['errcode']==0)){
				return array('rt'=>true,'errorno'=>0,'media_id'=>$js['media_id'],'msg_id'=>$js['msg_id']);
			}else {
				if ($showError){
					$this->error('发生了Post错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
				}
			}
		}
	}
}
?>