<?php
class WebConstants{
    
	private $_staticWebsite;
	
	private $_adminStaticWebsite;
	
	private $_ueditorWebsite;
	
	private $_tempWebsite;

    private $_site;

    private $_wulisite;
	
    public function getWebsite(){
    	return "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/";
		//return "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/dsjywnew/new_dsjyw/index.php?r=";
    }
    
    public function getStaticWebsite(){
    	return $this->_staticWebsite;
    }
    
    public function getAdminStaticWebsite(){
    	return $this->_adminStaticWebsite;
    }
    
    public function getUeditorWebsite(){
    	return $this->_ueditorWebsite;
    }
    
    public function getTempWebsite(){
    	return $this->_tempWebsite;
    }

    
    public function getSite(){
         return "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'];
         //return "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/dsjywnew/new_dsjyw/index.php";
    }

    public function getWulisite(){
        return  $this->_wulisite;
    }
    function init() {
    	if($this->_adminStaticWebsite===null)
//     		$this->_adminStaticWebsite=Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.admin.assets'));
    		$this->_adminStaticWebsite=Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.admin.assets'));
    	if($this->_staticWebsite===null)
//     		$this->_staticWebsite='http://'.Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.normal.assets'));
    		$this->_staticWebsite='http://'.Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->baseUrl.'/assets/resources/';
    	if($this->_ueditorWebsite===null)
    		$this->_ueditorWebsite='http://'.Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->baseUrl.'/assets/';
    	if($this->_tempWebsite===null)
    		$this->_tempWebsite='http://'.Yii::app()->request->serverName.':'.Yii::app()->request->serverPort.Yii::app()->baseUrl;
        if($this->_wulisite===null)
            $this->_wulisite=YII::app()->basePath;
    	 
    }
    
}
?>
