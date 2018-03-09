<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
// include(dirname(__FILE__)."/../extensions/phpqrcode/phpqrcode.php");
class Controller extends CController
{

	//加入smarty模板引擎
	protected  $smarty = '';
    protected  static $cleanService = null;
    protected   $positionService = null;
    protected   $companyService = null;
	public  function init($route){
		$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		$this->smarty = Yii::app()->smarty;
        $this->smarty->assign('url',$route);
        $data = Yii::app()->webConstants->getWebSite().$route;
        $this->smarty->assign('data',$data);
        

        // 生成的文件名
        // $filename = 'qrCode.png';
        // // 纠错级别：L、M、Q、H
        // $errorCorrectionLevel = 'L';
        // // 点的大小：1到10
        // $matrixPointSize = 4;
        // QRcode::png($data,$filename,$errorCorrectionLevel,$matrixPointSize,2);
		$this->smarty->assign('website', Yii::app()->webConstants->getWebSite());
		$this->smarty->assign('staticWebsite', Yii::app()->webConstants->getStaticWebsite());
		$this->smarty->assign('ueditorWebsite', Yii::app()->webConstants->getUeditorWebsite());
		$this->smarty->assign('tempWebsite', Yii::app()->webConstants->getTempWebsite());
        $this->smarty->assign('site', Yii::app()->webConstants->getSite());
        $this->smarty->assign('wulisite', Yii::app()->webConstants->getWulisite());
        $this->smarty->assign('user',yii::app()->session['username']);
        if(isset(Yii::app()->session['user_id'])) {
            $this->smarty->assign('isActivated', User::model()->findByPk(Yii::app()->session['user_id'])->is_activated);
            $this->smarty->assign('isLeague', User::model()->findByPk(Yii::app()->session['user_id'])->is_league);
        }
        self::$cleanService = CleanService::getInstance();
        $this->positionService = PositionService::getInstance();
        $this->companyService = CompanyService::getInstance();    
	}


    /**
     * @param $filterChain
     * 这个filter用于拦截未登录的访问
     */
    public function filterPreComment( $filterChain ){

        if( !isset(  Yii::app()->session['user_name'] ) ){
            $this->redirect($this->createUrl('admin/login/login/toLogin'));
        }
        $filterChain->run();
    }

    /**
     * @return array
     * 后台页面的访问需要经过filterPreComment这个filter
     */
    public function filters(){
        //     header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        $url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        if(strpos($url, "admin")&&!strpos($url,"admin/login/login/toLogin")&&!strpos($url,"admin/login/login/login") ) {
            return array('preComment');
        }
    }

    public function actionStatus($data){
        $sql="select sign from {{study_experience}} where user_id='".$data."'";
        $result=StudyExperience::model()->findBySql($sql);
        if(!$result){
            $sqll = "select sign from {{work_experience}} where user_id='".$data."'";
            $result = WorkExperience::model()->findBySql($sqll);
            if($result){
                return true;
            }
        }else{
            return true;
        }
    }

    /*
    * api权限验证
    */
    public function token($jy_token){
        $time = date('Ymd',time());
        $md_time = md5($time);

        $y1 = substr(date('Y'), 0,2);
        $y2 = substr(date('Y'), 2,2);
        $month = date('m');
        $day = date('d');

        $offset[] = 20;
        $offset[] = 13;
        $offset[] = 9;
        $offset[] = 3;
        $offset[] = (substr($y1, 0,1)?$y1:substr($y1,1,1));
        $offset[] = (substr($y2, 0,1)?$y2:substr($y2,1,1));
        $offset[] = (substr($month, 0,1)?$month:substr($month,1,1));
        $offset[] = (substr($day, 0,1)?$day:substr($day,1,1));

        $token = '';
        foreach ($offset as $key => $value) {
            $token.=substr($md_time, $value-1,1);
        }
        $token = strtoupper($token);
        
        if ($token == $jy_token) {
            return 0;
        }else{
            return 1;
        }

    }
    public function sendtoken(){
        $time = date('Ymd',time());
        $md_time = md5($time);

        $y1 = substr(date('Y'), 0,2);
        $y2 = substr(date('Y'), 2,2);
        $month = date('m');
        $day = date('d');

        $offset[] = 20;
        $offset[] = 13;
        $offset[] = 9;
        $offset[] = 3;
        $offset[] = (substr($y1, 0,1)?$y1:substr($y1,1,1));
        $offset[] = (substr($y2, 0,1)?$y2:substr($y2,1,1));
        $offset[] = (substr($month, 0,1)?$month:substr($month,1,1));
        $offset[] = (substr($day, 0,1)?$day:substr($day,1,1));

        $token = '';
        foreach ($offset as $key => $value) {
            $token.=substr($md_time, $value-1,1);
        }
        
        return strtoupper($token);

    }
}
