<?php
class AlipayAction extends UserAction{
	public function index(){
		if (C('agent_version')){
			$group=M('User_group')->field('id,name,price')->where('price>0 AND agentid='.$this->agentid)->select();
		}else {
			$group=M('User_group')->field('id,name,price')->where('price>0')->select();
		}
		$user=M('User_group')->field('price')->where(array('id'=>session('gid')))->find();
		$this->assign('group',$group);
		$this->assign('user',$user);
		//读取配置        
                $user_token=M('Wxuser')->field('token')->where(array('uid'=>session('uid')))->find();
		$alipay_config_db=M('Alipay_config');
		$alipayConfig=$alipay_config_db->where(array('token'=>$user_token['token']))->find();   
                //print_r($alipayConfig);exit;
                $this->assign('paytype',$alipayConfig['paytype']);
		$this->display();
	}
	public function vip(){
     $wxusers=M('Wxuser')->where(array('uid'=>$this->user['id']))->select();

//		if (C('agent_version')){
//			$group=M('User_group')->field('id,name,price')->where('price>0 AND agentid='.$this->agentid)->select();
//		}else {
//			$group=M('User_group')->field('id,name,price')->where('price>0')->select();
//		}
    //$group=M('User_group')->field('id,name,price')->where('price>0 AND agentid='.$wxusers['0']['agentid'])->select();
    $group=M('User_group')->field('id,name,price')->where('price>0 AND agentid='.$this->agentid)->select();
		$user=M('User_group')->field('price')->where(array('id'=>session('gid')))->find();
		$this->assign('group',$group);
		$this->assign('user',$user);
		$this->display();
	}
	public function vip_post (){
		$month=intval($_POST['num']);
		//检查费用
		$groupid=intval($_POST['gid']);
		$thisGroup=M('User_group')->where(array('id'=>$groupid))->find();
		$needFee=intval($thisGroup['price'])*$month;
		$moneyBalance=$this->user['moneybalance'];
		///
		$wxusers=M('Wxuser')->where(array('uid'=>$this->user['id']))->select();
		//
		if ($this->isAgent){
			$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
			$price=$pricebyMonth*count($wxusers)*$month;
			if ($price>$this->thisAgent['moneybalance']){
				$this->error('请联系您的站长处理');
			}
		}
		//
		if ($needFee<$moneyBalance||$needFee==$moneyBalance){
			//
			$users_db=D('Users');
			//$users_db->where(array('id'=>$this->user['id']))->save(array('viptime'=>$this->user['viptime']+$month*30*24*3600,'status'=>1,'gid'=>$groupid));
			$users_db->where(array('id'=>$this->user['id']))->save(array('viptime'=>time()+$month*30*24*3600,'status'=>1,'gid'=>$groupid));
			//
			$gid=$groupid+1;
			$functions=M('Function')->where('gid<'.$gid)->select();
			$str='';
			if ($functions){
				$comma='';
				foreach ($functions as $f){
					$str.=$comma.$f['funname'];
					$comma=',';
				}
			}
			//
			$token_open_db=M('Token_open');
			
			if ($wxusers){
				foreach ($wxusers as $wxu){
					$token_open_db->where(array('token'=>$wxu['token']))->save(array('queryname'=>$str));
				}
			}
			$indent=array();
			$indent['id']=time();
			//
			$spend=0-$needFee;
			M('Indent')->data(array('uid'=>session('uid'),'month'=>$month,'title'=>'购买服务','uname'=>$this->user['username'],'gid'=>$groupid,'create_time'=>time(),'indent_id'=>$indent['id'],'price'=>$spend,'status'=>1))->add();
			M('Users')->where(array('id'=>$this->user['id']))->setDec('moneybalance',intval($needFee));
			//
			if ($this->isAgent){
				$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
				if ($wxusers){
				$price=$pricebyMonth*count($wxusers)*$month;
				M('Agent')->where(array('id'=>$this->thisAgent['id']))->setDec('moneybalance',$price);
				M('Agent_expenserecords')->add(array('agentid'=>$this->thisAgent['id'],'amount'=>(0-$price),'des'=>$this->user['username'].'(uid:'.$this->user['id'].')延期'.$month.'个月，共'.count($wxusers).'个公众号','status'=>1,'time'=>time()));
				}
			}
			//
			$this->success('处理成功，请退出重新登陆才会生效',U('User/Index/index',array('pubs'=>1)));
		}else{
			$this->success('余额不足，请先充值',U('User/Alipay/index',array('pubs'=>1)));
		}
	}
	public function redirectPost(){
		if($this->_post('price')==false||$this->_post('uname')==false)$this->error('价格和用户名必须填写');
		//price ,uname,uid,groupid,num 月
		$url=str_replace('.cn','.com',C('site_url'));
		header('Location:'.$url.'/index.php?g=User&m=Alipay&a=post&price='.$this->_post('price').'&uname='.$this->_post('uname').'&uid='.session('uid').'&groupid='.$this->_post('group').'&num='.$this->_post('num'));
	}
	public function post(){
		if($this->_post('price')==false||$this->_post('uname')==false)$this->error('价格和用户名必须填写');
		import("@.ORG.Alipay.AlipaySubmit");
		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//商品数量
		$quantity = "1";
		//物流费用
		$logistics_fee = "0.00";
		//必填，即运费
		//物流类型
		$logistics_type = "EXPRESS";
		//必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		//物流支付方式
		$logistics_payment = "SELLER_PAY";
		//必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
		//收货人姓名
		$receive_name = $_POST['WIDreceive_name'];
		//如：张三
		//收货人地址
		$receive_address = $_POST['WIDreceive_address'];
		//如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
		//收货人邮编
		$receive_zip = $_POST['WIDreceive_zip'];
		//如：123456
		//收货人电话号码
		$receive_phone = $_POST['WIDreceive_phone'];
		//如：0571-88158090
		//收货人手机号码
		$receive_mobile = $_POST['WIDreceive_mobile'];
		//如：13312341234
		//服务器异步通知页面路径
		$notify_url = C('site_url').U('User/Alipay/notify');
		//需http://格式的完整路径，不能加?id=123这类自定义参数
		//页面跳转同步通知页面路径
		$return_url = C('site_url').U('User/Alipay/return_url');
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		//卖家支付宝帐户
		$seller_email =trim(C('alipay_name'));
		 //商户订单号
		$out_trade_no = $this->user['id'].'_'.time();
		//商户网站订单系统中唯一订单号，必填
		//订单名称
		$subject ='充值vip'.$this->_post('group').'会员'.$this->_post('num').'个月';
		//必填
		//付款金额
		$total_fee =(int)$_POST['price'];

        $body = 'vip高级会员服务费';
        //商品展示地址
        $show_url = C('site_url').U('Home/Index/price');
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
		$body = $subject;
		$data=M('Indent')->data(			
		array('uid'=>session('uid'),'month'=>intval($this->_post('num')),'title'=>$subject,'uname'=>$this->_post('uname'),'gid'=>$this->_post('gid'),'create_time'=>time(),'indent_id'=>$out_trade_no,'price'=>$total_fee))->add();
		$show_url = rtrim(C('site_url'),'/');

	if(trim(C('service')) == 'create_direct_pay_by_user'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),//"create_direct_pay_by_user",
					"partner" =>trim(C('alipay_pid')),
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"seller_email"	=> $seller_email,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"anti_phishing_key"	=> $anti_phishing_key,
					"exter_invoke_ip"	=> $exter_invoke_ip,
					"_input_charset"	=>trim(strtolower('utf-8'))
			);
		}else if(trim(C('service')) == 'trade_create_by_buyer'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),
					"partner" => trim(C('alipay_pid')),
					"seller_email" => $seller_email,
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"price"	=> $total_fee,
					"quantity"	=> $quantity,
					"logistics_fee"	=> $logistics_fee,
					"logistics_type"	=> $logistics_type,
					"logistics_payment"	=> $logistics_payment,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"receive_name"	=> $receive_name,
					"receive_address"	=> $receive_address,
					"receive_zip"	=> $receive_zip,
					"receive_phone"	=> $receive_phone,
					"receive_mobile"	=> $receive_mobile,
					"_input_charset"	=> trim(strtolower('utf-8'))
			);
		}else if (trim(C('service')) == 'create_partner_trade_by_buyer'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),
					"partner" => trim(C('alipay_pid')),
					"seller_email" => $seller_email,
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"price"	=> $total_fee,
					"quantity"	=> $quantity,
					"logistics_fee"	=> $logistics_fee,
					"logistics_type"	=> $logistics_type,
					"logistics_payment"	=> $logistics_payment,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"receive_name"	=> $receive_name,
					"receive_address"	=> $receive_address,
					"receive_zip"	=> $receive_zip,
					"receive_phone"	=> $receive_phone,
					"receive_mobile"	=> $receive_mobile,
					"_input_charset"	=> trim(strtolower('utf-8'))
			);
		}

		//建立请求
		$alipaySubmit = new AlipaySubmit($this->setconfig());
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
	}
	public function recharge(){
		if($this->_post('price')==false||$this->_post('uname')==false)$this->error('价格和用户名必须填写');
		import("@.ORG.Alipay.AlipaySubmit");
		//支付类型
		$payment_type = "1";
		//商品数量
		$quantity = "1";
		//必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
		//物流费用
		$logistics_fee = "0.00";
		//必填，即运费
		//物流类型
		$logistics_type = "EXPRESS";
		//必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		//物流支付方式
		$logistics_payment = "SELLER_PAY";
		//必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
		//收货人姓名
		$receive_name = $_POST['WIDreceive_name'];
		//如：张三
		//收货人地址
		$receive_address = $_POST['WIDreceive_address'];
		//如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
		//收货人邮编
		$receive_zip = $_POST['WIDreceive_zip'];
		//如：123456
		//收货人电话号码
		$receive_phone = $_POST['WIDreceive_phone'];
		//如：0571-88158090
		//收货人手机号码
		$receive_mobile = $_POST['WIDreceive_mobile'];
		//如：13312341234
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = C('site_url').U('User/Alipay/notify');
		//需http://格式的完整路径，不能加?id=123这类自定义参数
		//页面跳转同步通知页面路径
		$return_url = C('site_url').U('User/Alipay/charge_return');
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		//卖家支付宝帐户
		$seller_email =trim(C('alipay_name'));
		//商户订单号
		$out_trade_no = $this->user['id'].'_'.time();
		//商户网站订单系统中唯一订单号，必填
		//订单名称
		$subject ='充值';
		//必填
		//付款金额
		$total_fee =intval($_POST['price']);

		$body = '会员充值';
		//商品展示地址
		$show_url = C('site_url').U('Home/Index/price');
		//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

		//防钓鱼时间戳
		$anti_phishing_key = "";
		//若要使用请调用类文件submit中的query_timestamp函数

		//客户端的IP地址
		$exter_invoke_ip = "";
		//非局域网的外网IP地址，如：221.0.0.1
		$body = $subject;
		$data=M('Indent')->data(
		array('uid'=>session('uid'),'month'=>0,'title'=>$subject,'uname'=>$this->_post('uname'),'gid'=>0,'create_time'=>time(),'indent_id'=>$out_trade_no,'price'=>$total_fee))->add();
		$show_url = rtrim(C('site_url'),'/');

		if(trim(C('service')) == 'create_direct_pay_by_user'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),//"create_direct_pay_by_user",
					"partner" =>trim(C('alipay_pid')),
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"seller_email"	=> $seller_email,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"anti_phishing_key"	=> $anti_phishing_key,
					"exter_invoke_ip"	=> $exter_invoke_ip,
					"_input_charset"	=>trim(strtolower('utf-8'))
			);
		}else if(trim(C('service')) == 'trade_create_by_buyer'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),
					"partner" => trim(C('alipay_pid')),
					"seller_email" => $seller_email,
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"price"	=> $total_fee,
					"quantity"	=> $quantity,
					"logistics_fee"	=> $logistics_fee,
					"logistics_type"	=> $logistics_type,
					"logistics_payment"	=> $logistics_payment,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"receive_name"	=> $receive_name,
					"receive_address"	=> $receive_address,
					"receive_zip"	=> $receive_zip,
					"receive_phone"	=> $receive_phone,
					"receive_mobile"	=> $receive_mobile,
					"_input_charset"	=> trim(strtolower('utf-8'))
			);
		}else if (trim(C('service')) == 'create_partner_trade_by_buyer'){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => trim(C('service')),
					"partner" => trim(C('alipay_pid')),
					"seller_email" => $seller_email,
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"price"	=> $total_fee,
					"quantity"	=> $quantity,
					"logistics_fee"	=> $logistics_fee,
					"logistics_type"	=> $logistics_type,
					"logistics_payment"	=> $logistics_payment,
					"body"	=> $body,
					"show_url"	=> $show_url,
					"receive_name"	=> $receive_name,
					"receive_address"	=> $receive_address,
					"receive_zip"	=> $receive_zip,
					"receive_phone"	=> $receive_phone,
					"receive_mobile"	=> $receive_mobile,
					"_input_charset"	=> trim(strtolower('utf-8'))
			);
		}
		
		
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($this->setconfig());
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
	}
	public function setconfig(){
		$alipay_config['partner']		= trim(C('alipay_pid'));
		//安全检验码，以数字和字母组成的32位字符
		$alipay_config['key']			= trim(C('alipay_key'));
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('MD5');
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\wxcms\\Lib\\ORG\\Alipay\\cacert.pem';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';		
		return $alipay_config;
	}
	public function add(){
		$this->index();
	}
	public function charge_return (){
		import("@.ORG.Alipay.AlipayNotify");
		$alipayNotify = new AlipayNotify($this->setconfig());
		$verify_result = $alipayNotify->verifyReturn();	
		if($verify_result) {
			$out_trade_no = $this->_get('out_trade_no');
			//支付宝交易号
			$trade_no =  $this->_get('trade_no');
			//交易状态
			$trade_status =  $this->_get('trade_status');
			if( $this->_get('trade_status') == 'TRADE_FINISHED' ||  $this->_get('trade_status') == 'TRADE_SUCCESS') {
				$indent=M('Indent')->where(array('indent_id'=>$out_trade_no))->find();
				if($indent!=false){
					if($indent['status']==1){$this->error('该订单已经处理过,请勿重复操作');}
					$result = M('Users')->where(array('id'=>$indent['uid']))->setInc('money',intval($indent['price']));
					$result = M('Users')->where(array('id'=>$indent['uid']))->setInc('moneybalance',intval($indent['price']));
					$back=M('Indent')->where(array('id'=>$indent['id']))->setField('status',1);
					if($back!=false){
						$this->success('充值成功',U('User/Index/index',array('pubs'=>1)));
					}else{
						$this->error('充值失败,请在线客服,为您处理',U('User/Index/index',array('pubs'=>1)));
					}
				}else{
					$this->error('订单不存在',U('User/Index/index',array('pubs'=>1)));

				}
			}else {
			  $this->error('充值失败，请联系官方客户',array('pubs'=>1));
			}
		}else {
			$this->error('不存在的订单',array('pubs'=>1));
		}
	}
	//同步数据处理
	public function return_url (){
		import("@.ORG.Alipay.AlipayNotify");
		$alipayNotify = new AlipayNotify($this->setconfig());
		$verify_result = $alipayNotify->verifyReturn();	
		if($verify_result) {
			$out_trade_no = $this->_get('out_trade_no');
			//支付宝交易号
			$trade_no =  $this->_get('trade_no');
			//交易状态
			$trade_status =  $this->_get('trade_status');
			if( $this->_get('trade_status') == 'TRADE_FINISHED' ||  $this->_get('trade_status') == 'TRADE_SUCCESS') {
				$indent=M('Indent')->where(array('indent_id'=>$out_trade_no))->find();
				if($indent!=false){
					if($indent['status']==1){$this->error('该订单已经处理过,请勿重复操作');}
					M('Users')->where(array('id'=>$indent['uid']))->setInc('money',intval($indent['price']));
					M('Users')->where(array('id'=>$indent['uid']))->setInc('moneybalance',intval($indent['price']));
					$back=M('Indent')->where(array('id'=>$indent['id']))->setField('status',1);
					if($back!=false){
						$month=intval($indent['month']);
						//检查费用
						$groupid=intval($indent['gid']);
						$thisGroup=M('User_group')->where(array('id'=>$groupid))->find();
						$needFee=intval($thisGroup['price'])*$month;
						$moneyBalance=$this->user['moneybalance']+$indent['price'];
						if ($needFee<$moneyBalance){
							//
							$users_db=D('Users');
							$users_db->where(array('id'=>$indent['uid']))->save(array('viptime'=>$this->user['viptime']+$month*30*24*3600,'status'=>1,'gid'=>$indent['gid']));
							//
							$gid=$indent['gid']+1;
							$functions=M('Function')->where('gid<'.$gid)->select();
							$str='';
							if ($functions){
								$comma='';
								foreach ($functions as $f){
									$str.=$comma.$f['funname'];
									$comma=',';
								}
							}
							//
							$token_open_db=M('Token_open');
							$wxusers=M('Wxuser')->where(array('uid'=>$indent['uid']))->select();
							if ($wxusers){
								foreach ($wxusers as $wxu){
									$token_open_db->where(array('token'=>$wxu['token']))->save(array('queryname'=>$str));
								}
							}
							//
							$spend=0-$needFee;
							M('Indent')->data(array('uid'=>session('uid'),'month'=>$month,'title'=>'购买服务','uname'=>$this->user['username'],'gid'=>$groupid,'create_time'=>time(),'indent_id'=>$indent['id'],'price'=>$spend,'status'=>1))->add();
							M('Users')->where(array('id'=>$indent['uid']))->setDec('moneybalance',intval($needFee));
							//
							$this->success('充值成功并购买成功',U('User/Index/index',array('pubs'=>1)));
						}else{
							$this->success('充值成功但您的余额不足',U('User/Index/index',array('pubs'=>1)));
						}
					}else{
						$this->error('充值失败,请在线客服,为您处理',U('User/Index/index',array('pubs'=>1)));
					}
				}else{
					$this->error('订单不存在',U('User/Index/index',array('pubs'=>1)));

				}
			}else {
			  $this->error('充值失败，请联系官方客户',array('pubs'=>1));
			}
		}else {
			$this->error('不存在的订单',array('pubs'=>1));
		}
	}
	public function notify(){
		import("@.ORG.Alipay.alipay_notify");
		$alipayNotify = new AlipayNotify($this->setconfig());
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
				
	}
        
        
        
        
        
        
    public function kuaiqian(){//余额 moneybalance
		if($this->_post('price')==false||$this->_post('uname')==false)$this->error('价格和用户名必须填写');

		$kuaiqian_account =trim(C('kuaiqian_account'));
                $kuaiqian_key =trim(C('kuaiqian_key'));

		//$kuaiqian_account ='1002334024301';
                //$kuaiqian_key ='7QSANBBTYKAIT934';
                
		//商户订单号
		$out_trade_no = $this->user['id'].'_'.time();
		//商户网站订单系统中唯一订单号，必填
		//订单名称
		$subject ='充值';
		//必填
		//付款金额
		$total_fee =intval($_POST['price']);

		$data=M('Indent')->data(
                    array('uid'=>session('uid'),
                          'month'=>0,
                          'title'=>$subject,
                          'uname'=>$this->_post('uname'),
                          'gid'=>0,
                          'create_time'=>time(),
                          'indent_id'=>$out_trade_no,
                          'price'=>$total_fee)
                )->add();
                $get_para = 'zhuanru_money=' . $total_fee;
                $get_para .= '&uid=' . session('uid');
                $get_para .= '&kuaiqian_account=' . $kuaiqian_account;
                $get_para .= '&kuaiqian_key=' . $kuaiqian_key;
                $get_para .= '&out_trade_no=' . $out_trade_no;
                //echo $get_para.'2';exit;
                header("location:/commer_url_kq.php?" . $get_para);
	}        
        public function kuaiqian_charge_return (){
$arr = $_REQUEST; 
if ($arr['ext1'] != '') {	
	list($out_trade_no, $userid) = explode('-', $arr['ext1'], 2);//0-base64 1-normal
	$out_trade_no = base64_decode($out_trade_no);
	list($type, $account, $yh_price, $num, $tid, $slt_son) = explode('-', $out_trade_no);//flb ->  F-1-$zhuanch_money-1-1-1- uid
	list($arr['total_fee'], $arr['trade_no']) = explode('@', $arr['ext2']);// 费用以分为单位@订单号 total_fee为元
	if (!$userid) $userid = session('uid');if (!$userid) $userid = 0;
	//$userid $yh_price $arr['trade_no']			
        
        
        //程序处理
        $out_trade_no = $arr['trade_no'];
        $indent=M('Indent')->where(array('indent_id'=>$out_trade_no))->find();
        if($indent!=false){
                if($indent['status']==1){$this->error('该订单已经处理过,请勿重复操作');}
                M('Users')->where(array('id'=>$indent['uid']))->setInc('money',intval($indent['price']));
                M('Users')->where(array('id'=>$indent['uid']))->setInc('moneybalance',intval($indent['price']));
                $back=M('Indent')->where(array('id'=>$indent['id']))->setField('status',1);
                if($back!=false){
                        $this->success('充值成功',U('User/Index/index'));
                }else{
                        $this->error('充值失败,请在线客服,为您处理',U('User/Index/index',array('pubs'=>1)));
                }
        }else{
              $this->error('订单不存在',U('User/Index/index',array('pubs'=>1)));

        }        
} else {
	die('error');
}
echo '<result>1</result><redirecturl>http://'.$_SERVER['SERVER_NAME'].'/index.php?g=User&m=Alipay&a=index</redirecturl>';
sleep(1);echo '充值成功';sleep(1);header("location:/index.php?g=User&m=Alipay&a=index");
	}	
}



?>