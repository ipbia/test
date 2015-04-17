<?php
class MyewmAction extends UserAction{
	public function index(){
		$id=$this->_get('hid','intval');
		$token=$this->_get('token');
		$info=$this->generateQRfromGoogle('http://'.$_SERVER['HTTP_HOST'].'/index.php?g=Wap%26m=Discount%26a=index%26token='.$token.'%26hid='.$id);
		
		$this->assign('info',$info);
		$this->display();
	}
	
	public	function generateQRfromGoogle($chl)
	{
		return '<img src="http://api.k780.com:88/?app=qr.get&data='.$chl.'&level=L&size=10"/>';
		//return '<img src="https://chart.googleapis.com/chart?cht=qr&chld=H&chs=500x500&chl='.$chl.'"/>';
		//return '<img src="http://qr.liantu.com/api.php?bg=ffffff&fg=000000&gc=000000&el=l&w=400&m=10&text='.$chl.'"/>';
	}
	
	public	function generateQRForAjax()
	{
		$chl = "";
		if(isset($_POST['chl'])){
			$chl = $_POST['chl'];
		}
		echo '<img src="http://api.k780.com:88/?app=qr.get&data='.$chl.'&level=L&size=10"/>';
	}
	
	
}
	
?>