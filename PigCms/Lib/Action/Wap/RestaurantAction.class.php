<?php
class RestaurantAction extends StoreAction{
	public function __construct(){
		parent::__construct();
	}
	
	public function _initialize()
	{
		parent::_initialize();
	}
	
	//分享
	public function share(){
		$share 	= new WechatShare($this->wxuser['appid'],$this->wxuser['appsecret'],$this->token,$this->wecha_id);
		$share->hideOutShare();
		$this->shareScript=$share->getSgin();
		$this->assign('shareScript',$this->shareScript);
	}
	

	/**
	 * 商城首页
	 */
	public function cats()
	{
		parent::cats();
	}
	
	/**
	 * 商品详情页面
	 */
	public function goodsDetail(){
		$this->assign("metaTitle","商品详情页面");
		$this->assign("metaContent","主人，您正在查看");
		
		$this->display();
	}
	
	/**
	 * 获取所有菜单类目
	 */
	public function menus(){
		$company = M('Company')->where("`token`='{$this->token}' AND `isbranch`=0")->find();
		$cid = $this->_cid = isset($_GET['cid']) ? intval($_GET['cid']) : $company['id'];
		$parentid = isset($_GET['parentid']) ? intval($_GET['parentid']) : 0;
		
		$data = $this->product_cat_model->where(array('token' => $this->token, 'cid' => $cid))->order("sort ASC, id DESC")->select();
		$json_data = array();
		
		foreach($data as $value){
			$item = array();
			$item['id'] 	= $value['id'];
			$item['pid'] 	= $value['parentid'];
			$item['platform_id'] = '1';
			$item['shop_id'] = $value['cid'];
			$item['name'] 	= $value['name'];
			$item['intro'] 	= $value['des'];
			$item['icon'] 	= $value['logourl'];
			$item['sort'] 	= $value['sort'];
			$item['type'] 	= 0;
			$item['status'] = 1;
			$itme['token']  = $value['token'];
			
			$json_data[$value['id']] = $item;
		}
		
		foreach($json_data as $v){
			unset($json_data[$v['id']]['pid']);
			if($v['pid'] != 0){
				$json_data[$v['pid']]['children'][] = $json_data[$v['id']];
				unset($json_data[$v['id']]);
			}
		}
		
		$data = array();
		foreach ($json_data as $y){
			$data[] = $y;
		}
		
		ksort($json_data, 'sort');
		
		$menu_info = array();
		$menu_info['status'] 	= true;
		$menu_info['code'] 		= 0;
		$menu_info['msg'] 		= "";
		$menu_info['data'] 		= $data;
		
		exit(json_encode($menu_info));
		
	}
	
	/**
	 * 获取菜单对应的商品
	 */
	public function menu_goods(){
		$where = array('token' => $this->token, 'cid' => $this->_cid, 'groupon' => 0, 'dining' => 0, 'status' => 0);
		if ($this->_isgroup) {
			$relation = M("Product_relation")->where(array('token' => $this->token, 'cid' => $this->_cid))->select();
			$gids = array();
			foreach ($relation as $r) {
				$gids[] = $r['gid'];
			}
			if ($gids) $where['gid'] = array('in', $gids);
			$where['cid'] = $this->mainCompany['id'];
		}
		//$where = array('token' => $this->token, 'cid' => $this->_cid);
		if (isset($_GET['catid'])) {
			$catid = intval($_GET['catid']);
			$where['catid'] = $catid;
		}
		$page = isset($_GET['page']) && intval($_GET['page']) > 1 ? intval($_GET['page']) : 1;
		$pageSize = isset($_GET['pagesize']) && intval($_GET['pagesize']) > 1 ? intval($_GET['pagesize']) : 100;
		
		$method = isset($_GET['method']) && ($_GET['method']=='DESC' || $_GET['method']=='ASC') ? $_GET['method'] : 'DESC';
		$orders = array('time', 'discount', 'price', 'salecount');
		$order = isset($_GET['order']) && in_array($_GET['order'], $orders) ? $_GET['order'] : 'time';
		$start = ($page-1) * $pageSize;
		$products = $this->product_model->where($where)->order("sort ASC, " . $order.' '.$method)->limit($start . ',' . $pageSize)->select();
		
		$this->createGoods2Json($products);
	}
	
	/**
	 * 获取单个商品信息
	 */
	public function goodsInfo(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where = array('token' => $this->token, 'id' => $id);
		$product = $this->product_model->where($where)->find();
		
		$goods = $this->createGoodsItem($product);
		//获取商品展示图片
		$productimage = M('Product_image')->field('image')->where(array('pid'=>$id))->select();
		$imgs = array();
		foreach ($productimage as $key=>$value){
			$imgs[] = $value['image'];
		}
		$goods['img'] = $imgs;
		
		$goods['goods'] = $goods;
		$goods['spec'][]  = $this->createSpecItem($product);
		if(empty($goods)){
			exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'','data'=>$goods)));
		}
		exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'','data'=>$goods)));
	}
	
	/**
	 * 创建商品json格式
	 * @param unknown $products
	 */
	private function createGoods2Json($products){
		$goods = $this->createGoodsData($products);
		if(empty($goods)){
			exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'','data'=>$goods)));
		}
		exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'','data'=>$goods)));
	}
	
	/**
	 * 创建商品数据
	 * @param unknown $products
	 * @return multitype:
	 */
	private function createGoodsData($products){
		$goods = array();
		$spec  = array();
		foreach ($products as $key=>$value){
			$goods['goods'][] = $this->createGoodsItem($value);
			$goods['spec'][$value['id']][]  = $this->createSpecItem($value);
		}
		
		return $goods;
	}
	
	/**
	 * 创建商品项
	 */
	private function createGoodsItem($value){
		$item['id'] = $value['id'];
		$item['disabled'] 	= intval($value['num']) > 0 ? 0 : 1; //显示是否卖光了
		$item['need_wait'] 	= 0;//显示是否需要等待
		$item['time_cost'] 	= 0;
		$item['type_id'] 	= $value['catid'];
		$item['title'] 		= $value['name'];
		$item['cover'] 		= $value['logourl'];
		$item['img'] 		= '';
		$item['intro'] 		= $value['intro'];
		$item['sub_title'] 	= '';
		$item['recommend'] 	= $value['recommend'];	//推荐
		$item['comment_number'] = $value['comment_number'];//评论数
		$item['liked'] 		= $value['liked'];		//喜欢数
		$item['is_new']    	= $value['is_new'];		//是否新品
		$item['goods_id']  	= $value['id'];
		$item['cid'] 	   	= $value['cid'];
		$item['sort'] 	   	= $value['sort'];
		$item['gid'] 	   	= $value['id'];
		$item['storeid'] 	= $value['storeid'];
		$item['salecount'] 	= $value['salecount'];
		$item['dining'] 	   	= $value['dining'];
		$item['fakemembercount'] = $value['fakemembercount'];//销量基数
		$item['num'] 	   	= $value['num'];//库存
		$item['status'] 	= $value['status'];
		
		$product_user = M('product_user')->where(array('pid'=>$item['id']))->find();
		if($product_user != false){
			$item['is_liked'] 		= $product_user['is_liked'] == "false"?false:true;	//是否已经添加喜欢
			$item['is_collected'] 	= $product_user['is_collected'] == "false"?false:true;//是否收藏
		}
		
		return $item;
	}
	
	/**
	 * 创建商品规格项
	 */
	private function createSpecItem($value){
		$spec_item['id'] 				= $value['id'];
		$spec_item['spec_id'] 			= 0;
		$spec_item['price'] 	   		= $value['price'];
		$spec_item['original_price']	= $value['oprice'];
		
		return $spec_item;
	}
	
	/**
	 * 获取会员优惠卷(代金卷)
	 */
	public function memberCoupons(){
		$type 		= 2;//代金卷
		$is_use 	= $this->_get('is_use','intval')?$this->_get('is_use','intval'):'0';
		$now=time();
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$type,'is_use'=>"$is_use");
		
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_id,coupon_type,add_time,is_use')->select();
		
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'cardid'=>$value['cardid'],'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_coupon')->where($cwhere)->field('pic,statdate,enddate as expires_date,title,price')->find();
			if($cinfo['expires_date']>$now && $cinfo['statdate']<$now){
				$data[$key] = array_merge($value,$cinfo);
			}else{
				unset($data[$key]);
			}
		}
		if($data){
			exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'','data'=>$data)));
		}else{
			exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'','data'=>'')));
		}
		
	}
	
	/**
	 * 添加喜欢
	 */
	public function addlike(){
		if(isset($_POST['gid'])){
			$pid = $_POST['gid'];
			//保存该商品喜欢次数
			$where = array('id'=>$pid);
			$this->product_model->where($where)->setInc('liked',1);
			
			//保存用户已喜欢信息
			$where = array('token'=>$this->token, 'pid'=>$pid, 'wecha_id'=>$this->wecha_id);
			$product_user_result = M('product_user')->where($where)->find();
			if($product_user_result != false){
				M('product_user')->where($where)->save(array('is_liked'=>'true'));
			}else{
				$where = array('token'=>$this->token, 'cid'=>$this->_cid, 'pid'=>$pid, 'wecha_id'=>$this->wecha_id, 'is_liked'=>'true');
				M('product_user')->add($where);
			}
			exit(json_encode(array('status' => true, 'code' => 0, 'msg'=>'','data'=>[])));
		}
	}
	
	/**
	 * 添加收藏
	 */
	public function addCollect(){
		if(isset($_POST['gid'])){
			$pid = $_POST['gid'];
				
			//保存用户已收藏信息
			$where = array('token'=>$this->token, 'pid'=>$pid, 'wecha_id'=>$this->wecha_id);
			$product_user_result = M('product_user')->where($where)->find();
			
			if($product_user_result != false){
				M('product_user')->where($where)->save(array('is_collected'=>'true'));
			}else{
				$where = array('token'=>$this->token, 'cid'=>$this->_cid, 'pid'=>$pid, 'wecha_id'=>$this->wecha_id, 'is_collected'=>'true');
				M('product_user')->add($where);
			}
			exit(json_encode(array('status' => true, 'code' => 0, 'msg'=>'','data'=>[])));
		}
	}
	
	/**
	 * 删除收藏
	 */
	public function delCollect(){
		//删除用户已收藏信息
		$where = array('token'=>$this->token, 'pid'=>$pid, 'wecha_id'=>$this->wecha_id);
		$product_user_result = M('product_user')->where($where)->save('is_collected', false);
		exit(json_encode(array('status' => true, 'code' => 0, 'msg'=>'','data'=>[])));
	}
	
	/**
	 * 获取评论
	 */
	public function getcomment(){
		//{"status":1,"data":[{"nickname":"\u56db\u6d41","avator":"http:\/\/static.weiwubao.com\/download\/avator\/member_oFq_CjqTcqleeyewjfEMzxIakjsk.jpg","id":"12017","mp_id":"800194","item":"repast","item_id":"3186","member_id":"2070882","content":"\u5f97\u5230\u7684","create_date":"1426840767","hidden":"1","reply_id":"0","ip":"119.39.102.169","verify":"0","date":"2015-03-20"}],"quantity":1,"page_total":1,"total":"1"}
		$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
		$offset = 10;
		$start = ($page - 1) * $offset;
		$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
		$where = array('token' => $this->token, 'pid' => $pid, 'isdelete' => 0);
		$product_model = M("Product_comment");
		$count = $product_model->where($where)->count();
	
		$comment = $product_model->where($where)->order('id desc')->limit($start, $offset)->select();
		
		$data = array();
		$item = array();
		foreach ($comment as &$com) {
			$item['member_id']	= $com['memberid']; //用户信息表里的id
			$userinfo = M('Userinfo')->where(array('id'=>$item['member_id']))->find();
			if($userinfo != false){
				$item['avator']		= $userinfo['portrait'];//头像
				$item['nickname']	= $userinfo['wechaname'];
			}
			
			$item['id']		= $com['id'];
			$item['mp_id']		= $com['mp_id'];//平台id
			$item['item']	= 'repast';
			$item['item_id']	= $com['pid'];
			$item['content']	= htmlspecialchars_decode($com['content']);
			$item['create_date']= $com['dateline'];//substr($com['wecha_id'], 0, 7) . "****";
			$item['date']		= date("Y-m-d H:i", $com['dateline']);
			$item['hidden']		= '1';
			$item['reply_id']	= '0';
			$item['verify']		= '0';
			$item['wecha_id'] = $this->wecha_id;
			
			$data[] = $item;
		}
		$totalPage = ceil($count / $offset);
		$page = $totalPage > $page ? intval($page + 1) : 0;
		
		exit(json_encode(array('status' => true, 'data' => $data, 'quantity'=>count($comment),'page_total'=>$totalPage, 'total'=>$count)));
	}
	
	/**
	 * 保存评论
	 */
	public function postcomment(){
 		$cartid = isset($_POST['cartid']) && intval($_POST['cartid'])? intval($_POST['cartid']) : 0;
		$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
		$detailid = isset($_POST['detailid']) ? intval($_POST['detailid']) : 0;
		
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		
// 		$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
// 		if ($cartObj = M("product_cart")->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'id' => $cartid))->find()){
// 			if ($cartObj['paid'] == 0 && $alipayConfig['open']) {
// 				$this->error("您暂时还不能评论该商品");
// 			}
// 			$data = array();
// 			if ($product = M("Product")->where(array('id' => $pid, 'token' => $this->token))->find()) {
// 				if ($detailid) {
// 					$products = unserialize($cartObj['info']);
// 					$result = $this->getCat($products);
// 					foreach ($result[0] as $row) {
// 						foreach ($row['detail'] as $d) {
// 							if ($d['id'] == $detailid) {
// 								$str = $row['colorTitle'] && $d['colorName'] ? $row['colorTitle'] . ":" . $d['colorName'] : '';
// 								$str .= $row['formatTitle'] && $d['formatName'] ? ", " . $row['formatTitle'] . ":" . $d['formatName'] : '';
// 								$data['productinfo'] = $str;
// 							}
// 						}
// 					}
// 				}
// 			} else {
// 				$this->error("此产品可能下架了，暂时无法对其评论");
// 			}
// 		} else {
// 			$this->error("您还没有购买此商品，暂时无法对其评论");
// 		}
		
		//获取会员信息
		$member_info = array();
		if($_POST['mid']){
			$cartObj['truename'] = "test name";
			$cartObj['tel'] = "12312312";
		}
		//替换表情标签
		$content = preg_replace('/\[E:(\d+)\]/','<img src="./tpl/Wap/default/common/restaurant/asset/vlink/emoticons/$1.gif"/>',$_POST['content']);
		
		$comment = M("Product_comment");
		$data['cartid'] = $cartid;
		$data['pid'] = $pid;
		$data['detailid'] = $detailid;
		//$data['score'] = $_POST['score'];
		$data['content'] = htmlspecialchars(stripslashes($content));
		$data['token'] = $this->token;
		$data['wecha_id'] = $wecha_id;
		$data['truename'] = $cartObj['truename'];
		$data['tel'] 	  = $cartObj['tel'];
		$data['__hash__'] = $_POST['__hash__'];
		$data['dateline'] = time();
		$data['memberid'] = $_POST['mid'];
		
		unset($data['__hash__']);
		
		$c_id = $comment->add($data);
		if (false !== $c_id) {
			$date = date("Y-m-d H:i", $data['dateline']);
			$this->product_model->where(array('id'=>$pid))->setInc('comment_number');
			//{"status":1,"content":"a","c_id":12532,"date":"2015-04-05"}
			exit(json_encode(array('status' => 1, 'content' => $content, 'c_id'=>$c_id, 'date'=>$date)));
		} else {
			exit(json_encode(array('status' => 0)));
		}
	}
	
	/**
	 * 待付款页面
	 */
	public function orderCheck(){
		$this->assign("metaTitle","待付款订单");
		$this->assign("metaContent","主人，您有订单未付款，喜欢就赶紧下手哟!");
		
		$alipay = new AlipayAction();
		$pay_setting = $alipay->getPaySetting();
		$this->assign('pay_setting',$pay_setting);
		$this->display();
	}
	
	/**
	 * 我的收藏页面
	 */
	public function orderCollect(){
		$this->assign("metaTitle","我的收藏");
		$this->assign("metaContent","主人，您收藏了不少东西哟，赶紧来看看吧!");
		$this->display();
	}
	
	/**
	 * 查询用户收藏列表
	 */
	public function memberCollectList(){
		//{"status":true,"code":0,"msg":"","data":{"goods":[{"id":"668","shop_id":"26","goods_id":"221","disabled":"0","need_wait":"0","production_number":"2","time_cost":"0","is_make":"1","status":"1","created_at":"1413545600","updated_at":"1427089513","deleted_at":null,"title":"综合莓果班吉饼","sub_title":"Mix Berry Pancake","cover":"http:\/\/static.weiwubao.com\/upload\/image\/800194\/20150322\/320x320_201503221605481.jpg","img":[null],"intro":"新鲜的草莓搭上酸甜的莓果，酸甜清爽，再配上特制的奶油香软的班吉饼，味蕾与视觉的盛宴。","recommend":"35","is_new":"1","liked":"19"},{"id":"922","shop_id":"26","goods_id":"864","disabled":"0","need_wait":"0","production_number":"2","time_cost":"0","is_make":"1","status":"1","created_at":"1415448543","updated_at":"1415498678","deleted_at":null,"title":"法式鸡腿汉堡配薯条","sub_title":"French Chicken Burger With Fries","cover":"http:\/\/static.weiwubao.com\/upload\/image\/800194\/20141231\/320x320_201412311635111.jpg","img":[null],"intro":"整只新鲜手枪鸡腿去骨，采用法式芥末酱料腌制，加入洋葱、番茄和酸黄瓜，与鲜脆生菜和香滑沙拉酱融合，搭配大块儿手工薯条和色拉菜，口感丰富，绝对让你食指大动！","recommend":"15","is_new":"1","liked":"11"},{"id":"1479","shop_id":"26","goods_id":"1389","disabled":"1","need_wait":"0","production_number":"2","time_cost":"0","is_make":"1","status":"1","created_at":"1418713815","updated_at":"1423999623","deleted_at":null,"title":"野兽卡——200元面值","sub_title":"BEAST","cover":"http:\/\/static.weiwubao.com\/upload\/image\/800194\/20141216\/320x320_201412161504261.jpg","img":[null],"intro":"微信充值200元，即可获得：\r\n免费原味拿铁券1张。","recommend":"20","is_new":"1","liked":"16"},{"id":"4240","shop_id":"26","goods_id":"3186","disabled":"0","need_wait":"0","production_number":"2","time_cost":"0","is_make":"1","status":"1","created_at":"1426325463","updated_at":"1427089435","deleted_at":null,"title":"10点半欧陆套餐","sub_title":"Europe Set Meal","cover":"http:\/\/static.weiwubao.com\/upload\/image\/800194\/20150322\/320x320_201503221602521.jpg","img":[null],"intro":"颠覆传统风格，采用多种食材搭配，德式肉肠、香煎小蘑菇、香嫩培根卷、再配以煎鸡蛋、菊豆和法棍，均衡营养搭配，不再单一的美味享受。","recommend":"35","is_new":"1","liked":"14"},{"id":"4561","shop_id":"26","goods_id":"3405","disabled":"0","need_wait":"0","production_number":"2","time_cost":"0","is_make":"1","status":"0","created_at":"1427438191","updated_at":"1428310203","deleted_at":null,"title":"BG本周精选套餐","sub_title":"Set Meal Of The Week","cover":"http:\/\/static.weiwubao.com\/upload\/image\/800194\/20150327\/320x320_201503271432051.jpg","img":[null],"intro":"法式奶冻+水果班吉饼，本周精选套餐组合。","recommend":"35","is_new":"1","liked":"3"}],"spec":{"668":[{"id":"670","spec_id":"0","name":"","price":"45.00","original_price":"45.00"}],"922":[{"id":"911","spec_id":"0","name":"","price":"45.00","original_price":"45.00"}],"1479":[{"id":"1535","spec_id":"0","name":"","price":"200.00","original_price":"200.00"}],"4240":[{"id":"4188","spec_id":"0","name":"","price":"39.00","original_price":"39.00"}],"4561":[{"id":"4370","spec_id":"0","name":"","price":"63.00","original_price":"63.00"}]}}}
		
		//查找用户收藏的商品id集合
		$where = array('token'=>$this->token, 'cid'=>$this->_cid, 'wecha_id'=>$this->wecha_id);
		$ids = M('Product_user')->field('pid')->where($where)->select();
		
		$ids_str = "";
		if($ids != false){
			foreach ($ids as $key=>$value){
				if($value){
					if($key == 0){
						$ids_str .= $value['pid'];
					}else{
						$ids_str .= ','.$value['pid'];
					}
				}
			}
		}
		$map['id']  = array('exp',' IN ('.$ids_str.') ');
		$products = $this->product_model->where($map)->select();
		$this->createGoods2Json($products);
	}
	
	/**
	 * 历史订单页面
	 */
	public function orderList(){
		$this->assign("metaTitle","已购买订单");
		$this->assign("metaContent","主人，您的订单都在这里哟!");
		
		$this->my();
	}
	
	/**
	 * 订单详情页面
	 */
	public function orderDetail(){
		$this->assign("metaTitle","订单详情页面");
		$this->assign("metaContent","主人，您赶紧来看看购买的东西哟!");
		
		$this->myDetail();
	}
	
	/**
	 * 订单计算
	 */
	public function orderCompute(){
		if($_POST){
			$total_price = 0;
			foreach ($_POST['spec'] as $key=>$value){
				if($value){
					$pid = $value['sid'];
					$number = $value['number'];
					
					$where = array('token'=>$this->token, 'id'=>$pid);
					$product_result = $this->product_model->field('price')->where($where)->find();
					if($product_result != false){
						$total_price = $total_price + floatval($product_result['price']) * intval($number);
					}
				}
			}
			
			//如果有优惠卷，则扣掉优惠卷金额
			if(isset($_POST['params']['coupons'])){
				$type 		= 2;//代金卷
				$is_use 	= $this->_get('is_use','intval')?$this->_get('is_use','intval'):'0';
				$now=time();
				$ids = '';
				foreach ($_POST['params']['coupons'] as $key=>$value){
					if($key == 0){
						$ids = $value;
					}else{
						$ids .= ',' + $value ;
					}
				}
				$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$type,'is_use'=>"$is_use",'id'=> array('exp',' IN ('.$ids.') '));
				$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_id,coupon_type,add_time,is_use')->select();
				
				foreach($data as $key=>$value){
					$cwhere 		= array('token'=>$this->token,'cardid'=>$value['cardid'],'id'=>$value['coupon_id']);
					$cinfo			= M('Member_card_coupon')->where($cwhere)->field('statdate,enddate,price')->find();
					if($cinfo['enddate']>$now && $cinfo['statdate']<$now){
						$total_price -= $cinfo['price'];
					}
				}
			}
			
			$data = array('price'=>$total_price, 'promotion'=>array(), 'errors'=>array());
			exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'', 'data'=>$data)));
		}
		exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'', 'data'=>null)));
		//{"status":true,"code":0,"msg":"","data":{"price":238,"promotion":[],"errors":[]}}
	}
	
	/**
	 * 提交订单
	 */
	public function orderSubmit(){
		//paymode 1:在线支付, 2:财付通, 3:货到付款, 4:会员卡支付 , 5:积分兑换
		//paytype alipay:支付宝,weixin:微信支付,tenpay:财付通[wap手机],tenpayComputer:财付通[即时到帐],
		//		  yeepay:易宝支付,allinpay:通联支付, daofu:货到付款, dianfu:到店付款, chinabank:网银在线
		
		//兼容post,get方式，如果是post则要传spec值
		if($this->isAjax()){
			if(!isset($_POST['spec'])){
				exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'订单无法创建', 'data'=>null)));
			}
		}
		
		$paymode = $_POST['params']['paymode'] ? $_POST['params']['paymode'] : $_POST['paymode'];
		$paytype = $_POST['params']['paytype'] ? $_POST['params']['paytype'] : $_POST['paytype'];
		$saveinfo_post = $_POST['params']['saveinfo'] ? $_POST['params']['saveinfo'] : $_POST['saveinfo'];
		
		$_POST['truename']  = $_POST['params']['truename'] ? $_POST['params']['truename'] : $_POST['truename'];
		$_POST['tel']  = $_POST['params']['tel'] ? $_POST['params']['tel'] : $_POST['tel'];
		$_POST['address']  = $_POST['params']['address'] ? $_POST['params']['address'] : $_POST['address'];
		$_POST['score'] 	= $_POST['params']['score'];
		$_POST['orid'] 		= $_POST['params']['orid'];
		$_POST['saveinfo'] 	= $saveinfo_post;
		
		if(IS_GET){
			$paymode = $_GET['paymode'];
			$paytype = $_GET['paytype'];
			$_POST['score'] 	= $_GET['score'];
			$_POST['orid'] 		= $_GET['orid'];
			$_POST['saveinfo'] 	= $_GET['saveinfo'];
			$_POST['truename'] 	= $_GET['truename'];
			$_POST['tel'] 		= $_GET['tel'];
			$_POST['address'] 	= $_GET['address'];
		}
		
		//保存到购物车
		foreach ($_POST['spec'] as $key=>$value){
			if($value){
				$_GET['id'] = $value['id'];
				$_GET['did'] = $value['did'];
				$_GET['count'] = $value['count'];
				$_GET['remark'] = $value['remark'];
				
				$this->addProductToCart();
			}
		}
		
		//保存订单
		$row = array();
		
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		$row['truename'] = $this->_post('truename');
		$row['tel'] = $this->_post('tel');
		$row['address'] = $this->_post('address');
		$row['token'] = $this->token;
		$row['wecha_id'] = $wecha_id;
		$row['paymode'] = isset($paymode) ? intval($paymode) : 0;
		$row['paytype'] = $paytype;
		$row['cid'] = $cid = $this->_isgroup ? $this->mainCompany['id'] : $this->_cid;
		
		//积分
		$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
		$orid = isset($_POST['orid']) ? intval($_POST['orid']) : 0;
		$product_cart_model = D('Product_cart');
		if ($cartObj = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'id' => $orid))->find()) {
			$carts = unserialize($cartObj['info']);
		} else {
			$carts = $this->_getCart();
		}
		
		
		$normal_rt = 0;
		
		$info = array();
		if ($carts){
			$calCartInfo = $this->calCartInfo($carts);
			foreach ($carts as $pid => $rowset) {
				$total = 0;
				$tmp = M('product')->where(array('id' => $pid))->find();//setDec('num', $total);
				if (is_array($rowset)) {
					foreach ($rowset as $did => $ro) {
						$temp = M('Product_detail')->where(array('id' => $did, 'pid' => $pid))->find();//setDec('num', $ro['count']);
						if ($temp['num'] < $ro['count'] && empty($cartObj)) {
							if($this->isAjax()){
								exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'购买的量超过了库存', 'data'=>null)));
							}else{
								$this->error('购买的量超过了库存');
							}
						}
						$total += $ro['count'];
						$price = $this->fans['getcardtime'] ? ($temp['vprice'] ? $temp['vprice'] : $temp['price']) : $temp['price'];
						$info[$pid][$did] = array('count' => $ro['count'], 'price' => $price);
					}
				} else {
					$total = $rowset;
					$price = $this->fans['getcardtime'] ? ($tmp['vprice'] ? $tmp['vprice'] : $tmp['price']) : $tmp['price'];
					$info[$pid] = $rowset . "|" . $price;
				}
				if ($tmp['num'] < $total && empty($cartObj)) {
					if($this->isAjax()){
						exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'购买的量超过了库存', 'data'=>null)));
					}else{
						$this->error('购买的量超过了库存');
					}
				}
			}
				
			$setting = M('Product_setting')->where(array('token' => $this->token, 'cid' => $cid))->find();
			$saveprice = $totalprice = $calCartInfo[1] + $calCartInfo[2];
			
			//如果有优惠卷，则扣掉优惠卷金额
			if(isset($_POST['params']['coupons'])){
				$coupon_type = 2;//代金卷
				$now  =time();
				$ids = '';
				foreach ($_POST['params']['coupons'] as $key=>$value){
					if($key == 0){
						$ids = $value;
					}else{
						$ids .= ',' + $value ;
					}
				}
				
				$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$coupon_type,'is_use'=>"0",'id'=> array('exp',' IN ('.$ids.') '));
				$coupons = M('Member_card_coupon_record')->where($where)->field('id,coupon_id')->select();

				if($coupons != false){
					foreach ($coupons as $key=>$value){
						if($value['coupon_id']){
							//读取价格
							$where 	= array('token'=>$this->token,'id'=> $value['coupon_id']);
							$price_result = M('Member_card_coupon')->where($where)->field('price')->find();
							
							if($price_result != false){
								$totalprice -= intval($price_result['price']);
								
								//标识优惠卷已经使用
								$where 	= array('token'=>$this->token,'id'=> $value['id']);
								$coupon_data['is_use'] = "1";
								$coupon_data['use_time'] = $now;
								M('Member_card_coupon_record')->where($where)->save($coupon_data);
							}
						}
					}
				}
			}
			
			if ($score && $setting && $setting['score'] > 0 && $this->fans['total_score'] >= $score) {
				$s = isset($cartObj['score']) ? intval($cartObj['score']) : 0;
				$totalprice -= ($score + $s) / $setting['score'];
				if ($totalprice <= 0) {
					$score = ($calCartInfo[1] + $calCartInfo[2]) * $setting['score'];
					$totalprice = 0;
					$row['paid'] = 1;
					$row['paymode'] = 5;
				} else {
					$score += $s;
				}
			}
				
			$row['total'] = $calCartInfo[0];
			$row['price'] = $totalprice;
			$row['diningtype'] = 0;
			$row['buytime'] = '';
			$row['tableid'] = 0;
			$row['info'] = serialize($info);
			$row['groupon']=0;
			$row['dining'] = 0;
			$row['score'] = $score;
				
			$row['twid'] = $this->_twid;
			$row['totalprice'] = $saveprice;
			if ($cartObj) {
				//$row['score'] = $cartObj['score'] + $score;
				$normal_rt = $product_cart_model->where(array('id' => $orid))->save($row);
				$orderid = $cartObj['orderid'];
			} else {
				$row['time'] = $time = time();
				$row['orderid'] = $orderid = date("YmdHis") . rand(100000, 999999);
				$normal_rt = $product_cart_model->add($row);
			}
				
				
			//TODO 发货的短信提醒
			if ($normal_rt && empty($orid)) {
				$tdata = $this->getCat($carts);
				$list = array();
				foreach ($tdata[0] as $va) {
					$t = array();
					if (!empty($va['detail'])) {
						foreach ($va['detail'] as $v) {
							$t = array('num' => $v['count'], 'colorName' => $v['colorName'], 'formatName' => $v['formatName'], 'price' => $v['price'], 'name' => $va['name']);
							$list[] = $t;
						}
					} else {
						$t = array('num' => $va['count'], 'price' => $va['price'], 'name' => $va['name']);
						$list[] = $t;
					}
				}
				$company = D('Company')->where(array('token' =>$this->token, 'id' => $cid))->find();
				$op = new orderPrint();
				$msg = array('companyname' => $company['name'], 'companytel' => $company['tel'], 'truename' => $row['truename'], 'tel' => $row['tel'], 'address' => $row['address'], 'buytime' => $row['time'], 'orderid' => $row['orderid'], 'sendtime' => '', 'price' => $row['price'], 'total' => $row['total'], 'list' => $list);
				$msg = ArrayToStr::array_to_str($msg);
				$op->printit($this->token, $this->_cid, 'Store', $msg, 0);
		
				$userInfo = D('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $wecha_id))->find();
				
				if($setting['email_status'] == 1){
					Sms::sendSms($this->token, "您的顾客{$row['truename']}刚刚下了一个订单，订单号：{$orderid}，手机号：{$row['tel']}请您注意查看并处理【".$company['shortname']."】");
				}
			}
		}
		
		if ($normal_rt){
			$product_model = M('product');
			$product_cart_list_model = M('product_cart_list');
			$userinfo_model = M('Userinfo');
			$thisUser = $userinfo_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id))->find();
			if (empty($cartObj)) {
				$crow = array();
				$tdata = $this->getCat($carts);
				foreach ($carts as $k => $c){
					$crow['cartid'] = $normal_rt;
					$crow['productid'] = $k;
					$crow['price'] = $tdata[1][$k]['totalPrice'];//$c['price'];
					$crow['total'] = $tdata[1][$k]['total'];
					$crow['wecha_id'] = $row['wecha_id'];
					$crow['token'] = $row['token'];
					$crow['cid'] = $row['cid'];
					$crow['time'] = $time;
					$product_cart_list_model->add($crow);
						
					//增加销量
					$totalprice || $product_model->where(array('id'=>$k))->setInc('salecount', $tdata[1][$k]['total']);
				}
		
				//删除库存
				foreach ($carts as $pid => $rowset) {
					$total = 0;
					if (is_array($rowset)) {
						foreach ($rowset as $did => $ro) {
							M('Product_detail')->where(array('id' => $did, 'pid' => $pid))->setDec('num', $ro['count']);
							$total += $ro['count'];
						}
					} else {
						if (strstr($rowset, '|')) {
							$a = explode("|", $rowset);
							$total = $a[0];
						} else {
							$total = $rowset;
						}
					}
					$product_model->where(array('id' => $pid))->setDec('num', $total);
				}
				$_SESSION[$this->session_cart_name] = null;
				unset($_SESSION[$this->session_cart_name]);
				
				//保存个人信息
				if ($_POST['saveinfo']) {
					$this->assign('thisUser', $thisUser);
					$userRow = array('tel' => $row['tel'],'truename' => $row['truename'], 'address' => $row['address']);
					if ($thisUser) {
						$userinfo_model->where(array('id' => $thisUser['id']))->save($userRow);
						$userinfo_model->where(array('id' => $thisUser['id'], 'total_score' => array('egt', $score)))->setDec('total_score', $score);
						F('fans_token_wechaid', NULL);
					} else {
						$userRow['token'] = $this->token;
						$userRow['wecha_id'] = $wecha_id;
						$userRow['wechaname'] = '';
						$userRow['qq'] = 0;
						$userRow['sex'] = -1;
						$userRow['age'] = 0;
						$userRow['birthday'] = '';
						$userRow['info'] = '';
		
						$userRow['total_score'] = 0;
						$userRow['sign_score'] = 0;
						$userRow['expend_score'] = 0;
						$userRow['continuous'] = 0;
						$userRow['add_expend'] = 0;
						$userRow['add_expend_time'] = 0;
						$userRow['live_time'] = 0;
						$userinfo_model->add($userRow);
					}
				}
			} else {
				$userinfo_model->where(array('id' => $thisUser['id'], 'total_score' => array('egt', $score - $cartObj['score'])))->setDec('total_score', $score - $cartObj['score']);
				F('fans_token_wechaid', NULL);
			}
		
			if ($totalprice) {
				$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
				if ($this->fans['balance'] > 0 && $row['paymode'] == 4) {
					$url = U('CardPay/pay', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'success' => 1, 'from'=> 'Restaurant', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $totalprice));
					if($this->isAjax()){
						exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'订单生成成功', 'data'=>array('url'=>$url))));
					}else{
						header('Location:'.$url);
					}
					die;
				} elseif ($alipayConfig['open']) {
					$notOffline = $setting['paymode'] == 1 ? 0 : 1;
					$url = U('Alipay/redirect_pay', array('pay_type'=>$paytype, 'token' => $this->token, 'wecha_id' => $this->wecha_id, 'success' => 1, 'from'=> 'Restaurant', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $totalprice, 'notOffline' => $notOffline));
					if($this->isAjax()){
						exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'订单生成成功', 'data'=>array('url'=>$url))));
					}else{
						header('Location:'.$url);
					}
					die;
				}
			}
			$model = new templateNews();
			$model->sendTempMsg('TM00820', array('href' => U('Store/my',array('token' => $this->token, 'wecha_id' => $wecha_id)), 'wecha_id' => $wecha_id, 'first' => '购买商品提醒', 'keynote1' => '订单未支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '购买成功，感谢您的光临，欢迎下次再次光临！'));
			$url = U('Restaurant/orderList',array('token' => $_GET['token'], 'wecha_id' => $wecha_id, 'success' => 1, 'twid' => $this->_twid));
			if($this->isAjax()){
				exit(json_encode(array('status' => true, 'code'=>0, 'msg'=>'订单生成成功', 'data'=>array('url'=>$url))));
			}else{
				header('Location:'.$url);
			}
		} else {
			if($this->isAjax()){
				exit(json_encode(array('status' => false, 'code'=>0, 'msg'=>'订单生成失败', 'data'=>null)));
			}else{
				//$this->error('订单生成失败');
			}
		}
	}
	
	/**
	 * 添加购物车
	 */
	public function addProductToCart()
	{
		$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		$carts = $this->_getCart();
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;//商品的详细id,即颜色与尺寸
		if (isset($carts[$id])) {
			if ($did) {
				if (isset($carts[$id][$did])) {
					$carts[$id][$did]['count'] += $count;
				} else {
					$carts[$id][$did]['count'] = $count;
				}
			} else {
				$carts[$id] += $count;
			}
		} else {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$calCartInfo = $this->calCartInfo();
	}
	
	private function calCartInfo($carts='')
	{
		$totalCount = $totalFee = 0;
		if (!$carts) {
			$carts = $this->_getCart();
		}
		$data = $this->getCat($carts);
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}
	
		return array($totalCount, $totalFee, $data[2]);
	}
	
	private function _getCart()
	{
		if (!isset($_SESSION[$this->session_cart_name])||!strlen($_SESSION[$this->session_cart_name])){
			$carts = array();
		} else {
			$carts=unserialize($_SESSION[$this->session_cart_name]);
		}
		return $carts;
	}
	
	
	/**
	 * 支付成功后的回调函数
	 */
	public function payReturn() {
		$orderid = $_GET['orderid'];
		if ($order = M('Product_cart')->where(array('orderid' => $orderid, 'token' => $this->token))->find()) {
			//TODO 发货的短信提醒
			//if ($order['paid']) {
				$carts = unserialize($order['info']);
				$tdata = $this->getCat($carts);
				$list = array();
				foreach ($tdata[0] as $va) {
					$t = array();
					$salecount = 0;
					if (!empty($va['detail'])) {
						foreach ($va['detail'] as $v) {
							$t = array('num' => $v['count'], 'colorName' => $v['colorName'], 'formatName' => $v['formatName'], 'price' => $v['price'], 'name' => $va['name']);
							$list[] = $t;
							$salecount += $v['count'];
						}
					} else {
						$t = array('num' => $va['count'], 'price' => $va['price'], 'name' => $va['name']);
						$list[] = $t;
						$salecount = $va['count'];
					}
					if ($order['paid']) {
						D("Product")->where(array('id' => $va['id']))->setInc('salecount', $salecount);
					}
				}
	
				if ($order['paid']) {
					if ($order['twid']) {
						$this->savelog(3, $order['twid'], $this->token, $order['cid'], $order['totalprice']);
					}
				}
				
	
				$company = D('Company')->where(array('token' =>$this->token, 'id' => $order['cid']))->find();
				$op = new orderPrint();
				$msg = array('companyname' => $company['name'], 'companytel' => $company['tel'], 'truename' => $order['truename'], 'tel' => $order['tel'], 'address' => $order['address'], 'buytime' => $order['time'], 'orderid' => $order['orderid'], 'sendtime' => '', 'price' => $order['price'], 'total' => $order['total'], 'list' => $list);
				$msg = ArrayToStr::array_to_str($msg, 1);
				$op->printit($this->token, $this->_cid, 'Store', $msg, 1);
				$userInfo = D('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
				
				$company_shortname = $company['shortname'];
				//发送短信提醒
				
				if($order['paid']){
					Sms::sendSms($this->token, "您的顾客{$userInfo['truename']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理【".$company_shortname."】");
					$model = new templateNews();
					$model->sendTempMsg('TM00820', array('href' => C('site_url').U('Restaurant/orderList',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)), 'wecha_id' => $this->wecha_id, 'first' => '购买商品提醒', 'keynote1' => '订单已支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '购买成功，感谢您的光临，欢迎下次再次光临！'));
				}else{
					if(strtolower($order['paytype']) == strtolower('daofu')){
						Sms::sendSms($this->token, "您的顾客{$userInfo['truename']}刚刚对订单号：{$orderid}的订单选择[货到付款]，请您注意查看并处理【".$company_shortname."】");
						$model = new templateNews();
						$model->sendTempMsg('TM00820', array('href' => C('site_url').U('Restaurant/orderList',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)), 'wecha_id' => $this->wecha_id, 'first' => '购买商品提醒', 'keynote1' => '未支付【货到付款】', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '购买成功，感谢您的光临，欢迎下次再次光临！'));
					}else if(strtolower($order['paytype']) == strtolower('dianfu')){
						Sms::sendSms($this->token, "您的顾客{$userInfo['truename']}刚刚对订单号：{$orderid}的订单选择[到店付款]，请您注意查看并处理【".$company_shortname."】");
						$model = new templateNews();
						$model->sendTempMsg('TM00820', array('href' => C('site_url').U('Restaurant/orderList',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)), 'wecha_id' => $this->wecha_id, 'first' => '购买商品提醒', 'keynote1' => '未支付【到店付款】', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '购买成功，感谢您的光临，欢迎下次再次光临！'));
					}
				}
			//}
			
			$this->redirect(U('Restaurant/orderList',array('token' => $this->token,'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)));
		}else{
			exit('订单不存在');
		}
	}
	
}

?>