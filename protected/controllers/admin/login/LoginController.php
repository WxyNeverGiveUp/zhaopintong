<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/27
 * Time: 上午10:16
 */

class LoginController extends Controller {

    /**
     * 跳转到登录页面
     */
    public function actionToLogin(){
        $this->smarty->display('admin/login/login.html');
    }

    /**
     * 登录
     * 使用session来判断是否登录
     * 验证用户名密码之后，保存session来保存登录状态
     */
    public function actionLogin(){
        $user_name = $_POST['username'];
        $password  = $_POST['password'];

        $user = Admin::model()->find('name=:name', array(':name' => $user_name));
        if( md5( $password.$user->salt ) == $user->password){
            if( !isset( $_SESSION) ) {
                session_start();
            }
            Yii::app()->session['user_name'] = $user_name;

            $this->redirect($this->createUrl("admin/company/company/list"));
        }
        else{
            $this->redirect($this->createUrl("admin/login/login/toLogin"));
        }
    }

    /**
     * 登出
     */
    public function actionLogout(){
        unset( Yii::app()->session['user_name'] );
        $this->redirect($this->createUrl("admin/login/login/toLogin"));
    }
}