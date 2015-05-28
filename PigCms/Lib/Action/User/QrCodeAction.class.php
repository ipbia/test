<?php
class QrCodeAction extends BaseAction{
	
	public	function generateQRForAjax()
	{
		$chl = 'http://'.$_SERVER['HTTP_HOST'];
		if(isset($_POST['chl'])){
			$chl .= $_POST['chl'];
		}
		echo '<img src="http://api.k780.com:88/?app=qr.get&data='.urlencode($chl).'&level=L&size=10"/>';
	}
	
	
}
	
?>