<?php
require_once(Yii::getPathOfAlias('application.vendor.smarty').DIRECTORY_SEPARATOR.'Smarty.class.php');
define('SMARTY_VIEW_DIR', Yii::getPathOfAlias('application.views.smarty'));

class CSmarty extends Smarty {
   
    const DS = DIRECTORY_SEPARATOR;
   
    function __construct() {
        parent::__construct();
        $this->template_dir = SMARTY_VIEW_DIR.self::DS.'templates';
        $this->compile_dir = SMARTY_VIEW_DIR.self::DS.'templates_c';
        $this->caching = false;
        $this->cache_dir = SMARTY_VIEW_DIR.self::DS.'cache';
        $this->left_delimiter  =  '<{';
        $this->right_delimiter =  '}>';
        $this->cache_lifetime = 3600;
    }

    function init() {}
   
}


?>