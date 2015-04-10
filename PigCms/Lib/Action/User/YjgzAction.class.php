<?php
class YjgzAction extends UserAction{
	public function index(){
	
		if(IS_POST){
			if($this->token){
				$wxuser = M('wxuser');
				$data = array('yjgz'=>$_POST['yjgz']);
				$result = $wxuser->where("token = '$this->token'")->save($data);
				if($result != false){
					$this->success('保存成功');
				}else{
					$this->error('保存失败');
				}
			}
		}
		
		if($this->token){
			$wxuser = M('wxuser');
			$result = $wxuser->where("token = '$this->token'")->select();
			$this->assign('yjgz' ,$result[0]['yjgz']);
		}
		
		$this->display();
    }
}
?>