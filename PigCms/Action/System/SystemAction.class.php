<?php
class SystemAction extends BackAction{
	
	public function index(){
		$where['display']=1;
		$where['status']=1;
		$order['sort']='asc';
		$nav=M('node')->where($where)->order($order)->select();
		$this->assign('nav',$nav);
		$this->display();
	}
	
	public function menu(){
		if(empty($_GET['pid'])){
			$where['display']=2;
			$where['status']=1;
			$where['pid']=2;
			$where['level']=2;
			$order['sort']='asc';
			$nav=M('node')->where($where)->order($order)->select();
			$this->assign('nav',$nav);
		}
		$this->display();
	}

protected function deldir($dir){
		$result = true;
		$dh = opendir($dir);
		while($file=readdir($dh)){
			if($file!="." && $file!=".."){
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)){
					$result = unlink($fullpath);					
				}else{
					$this->deldir($fullpath);
				}
			}
			rmdir($fullpath);
		}
		closedir($dh);
		return $result;
	}
	public function clear(){
		$this->display();
	}
	public function del(){
		
		$dir = './Conf/logs';
		$r = $this->deldir($dir);
		if($r){
			$this->success('清除成功',U('index'));
		}else{
			$this->error('清除失败，请检查目录权限',U('index'));
		}
	}
	
	public function main(){
	    //授权相关
		
		$version = './Conf/version.php';
        $ver = include($version);
        $ver = $ver['ver'];
		$hosturl= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$updatehost = 'http://updata.lechengwed.com/update.php';
        $updatehosturl = $updatehost.'?a=client_check_time&v='.$ver.'&u='.$hosturl;
        $domain_time = file_get_contents($updatehosturl);

		if($domain_time=='0'){
           $domain_time='[授权版本：终生版]';
		}else{
		   $domain_time='授权版本：(高级版终生使用权)--免费更新服务截止：('.date("Y-m-d",$domain_time).')';
		}

		$this->assign('ver',$ver);
		$this->assign('domain_time',$domain_time);
	
		$this->display();
	}
}