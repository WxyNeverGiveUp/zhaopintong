<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/24
 * Time: 下午2:27
 */

class AboutUsController extends Controller {

    /**
     * 前台页面显示关于我们
     */
    function actionAboutUs(){
        $list = AboutUs::model()->findAll();
        $this->smarty->assign('list',$list);
        $this->smarty->display('aboutUs/about-us-index.html');
    }
}