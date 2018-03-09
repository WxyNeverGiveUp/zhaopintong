<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-7
 * Time: 下午6:30
 */

class IndexController extends Controller{
    public function actionAdminIndex(){
        if(isset($_SESSION['RID']) && $_SESSION['RID']==2){
            $this->smarty->display('index/adminIndex.html');
        }
        else {
            $this->smarty->display("common/error.html");
        }
    }
    public function actionIndex(){
        if(isset($_SESSION['RID']) && $_SESSION['RID']==1){
            $this->smarty->display('index/index.html');
        }
        else {
            $this->smarty->display("common/error.html");
        }
    }

    public function actionTop(){
        if(isset($_SESSION['RID'])){
            $this->smarty->display('index/index_top.html');
        }
        else {
            $this->smarty->display("common/error.html");
        }
    }

    public function actionLeft(){
        if(isset($_SESSION['RID']) && $_SESSION['RID']==2){
            $this->smarty->display('index/index_left.html');
        }
        elseif(isset($_SESSION['RID']) && $_SESSION['RID']==1){
            $this->smarty->display('index/index_left2.html');
        }
        else {
            $this->smarty->display("common/error.html");
        }
    }

    public function actionRight(){
        if(isset($_SESSION['RID'])){
            $this->smarty->display('index/index_right.html');
        }
        else {
            $this->smarty->display("common/error.html");
        }
    }
} 