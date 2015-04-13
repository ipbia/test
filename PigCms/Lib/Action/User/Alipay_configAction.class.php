<?php
class Alipay_configAction extends UserAction{
	public $alipay_config_db;
        private $_pay_type_arr = array();
        public function _initialize() {
		parent::_initialize();
		$this->alipay_config_db=M('Alipay_config');
		if (!$this->token){
			exit();
		}
                $this->_pay_type_arr = array('weixin', 'alipay', 'tenpay', 'tenpayComputer', 'allinpay', 
                                      'yeepay', 'chinabank', 'daofu', 'dianfu', 'platform');
               // $this->alipay_config_db->where(array('token' => $this->token))->delete();
	}
	public function index(){//print_r($_POST);exit;
		$where['token'] = $this->token;		
                $pay_type_arr = $this->_pay_type_arr;
		if(IS_POST){
                        //'token', 'paytype', 'info' open
                        $db_field = array('service','name', 'pid', 'key', 'partnerkey', 'appsecret', 
                                         'appid','new_appid', 'paysignkey', 'partnerid', 'mchid');		                       
                        $row = array();
                        $row['token'] = $this->token;
                        $row['open']  = $this->_post('is_open');	
			            $row['service'] = strval(trim($_POST['alipay']['service']));
						$row['name'] = strval(trim($_POST['alipay']['name']));
						$row['pid'] = strval(trim($_POST['alipay']['pid']));
						$row['key'] = strval(trim($_POST['alipay']['key']));
						//$data_alipay_config['partnerkey'] = strval(trim($_POST['tenpayComputer']['partnerkey']));
						$row['appsecret'] = strval(trim($_POST['weixin']['appsecret']));
						$row['appid'] = strval(trim($_POST['weixin']['new_appid']));
						$row['partnerkey'] = strval(trim($_POST['weixin']['key']));
						$row['partnerid'] = strval(trim($_POST['tenpayComputer']['partnerid']));
						$row['mchid'] = strval(trim($_POST['weixin']['mchid']));
                        
                        unset($_POST[C('TOKEN_NAME')],$_POST['token'], $_POST['button']);//, $_POST['platform']
                        $row['info'] = serialize($_POST);                       
			
                        $config = $this->alipay_config_db->where($where)->find();
			if ($config){
				$this->alipay_config_db->where($where)->save($row);
			}else {
				$this->alipay_config_db->add($row);
			}
			$this->success('设置成功',U('Alipay_config/index',$where));
                if(0){        
                        foreach ($db_field as $field) {
                            $row[$field]=$this->_post($field);
                            //$row[$field]=$_POST[$pay_type][$field];
                        }
                        //info        
                        foreach ($pay_type_arr as $pay_type) {                                                      
                            $row['paytype'] = $pay_type;
                            $where['paytype'] = $pay_type;
                            $config = $this->alipay_config_db->where($where)->find();
                            //$where=array('token'=>$this->token, 'paytype'=>$pay_type);
                            if (0 && $pay_type == 'weixin') {
                                $_POST[$pay_type]['appid'] = $_POST[$pay_type]['new_appid'];
                            }
                                    
                            if ($this->_post('is_open') == 1) {                                                            
                                foreach ($db_field as $field) {
                                    //$row[$field]=$this->_post($field);
                                    $row[$field]=$_POST[$pay_type][$field];
                                }
                                if ($row['open'] == 0) {
                                    $this->alipay_config_db->where($where)->delete();
                                    continue;
                                }
                                if ($config){                                
                                    $this->alipay_config_db->where($where)->save($row);
                                }else {
                                    $this->alipay_config_db->add($row);
                                }                                
                            } else {
                                //$row['open']=0;
                                 $this->alipay_config_db->where($where)->delete();
                            }
                        }
                    }
		}else{
                        $config = $this->alipay_config_db->where($where)->find();

                        $config_new = array();
                        $config_new['is_open'] = $config['open'];
                        $info_arr = unserialize($config['info']);
                        foreach ($info_arr as $pay_type => $pay_val) {
                            $config_new[$pay_type] = $pay_val;
                        }

			$this->assign('config',$config_new);
			$this->display();
		}
	}
}


?>