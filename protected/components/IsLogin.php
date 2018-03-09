<?php
class IsLogin {
	
	public function __construct(){
		if(!isset($_SESSION)){
		    session_start();
		}
		if(isset($_SESSION['RID'])){
			return;
		}else{
			echo '<script>
			var path = window.location.protocol + "//" + window.location.host+window.document.location.pathname;
			window.top.location.href=path+"?r=login/index"
			</script>';
		}
	}
}

?>