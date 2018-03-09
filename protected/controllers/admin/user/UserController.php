<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/27
 * Time: 上午10:17
 */

class UserController extends Controller {

    /**
     * 列出所有管理员
     */
    public function actionList(){
        $criteria = new CDbCriteria();
        $list_all = Admin::model()->findAll($criteria);

        $pageSize = 10;
        $recordCount = count($list_all);
        $this->smarty->assign('list',$list_all);
        $this->smarty->assign('pageSize',$pageSize);
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/user/list.html');

    }

    /**
     * 分页json
     */
    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria();
        $list_all = Admin::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount =count($list_all);
        $list= Admin::model()->findAll($criteria);  //记录分页

        foreach( $list as $key => $value){
            $listJson[$key]['name'] = $value->name;
        }

        $list2='{"list":'.CJSON::encode($listJson).',"dataCount":"'.$recordCount.'"}';


        print $list2;
    }

    /**
     * 跳转到改密码页面
     */
    public function actionToEdit(){
        $this->smarty->assign('current','system');
        $this->smarty->display( 'admin/user/edit.html' );
    }

    /**
     * 改密码
     */
    public function actionEdit() {
        $password = $_POST['password'];
        $old_password = $_POST['old_password'];

        $user_name = Yii::app()->session['user_name'];

        $user = Admin::model()->find('name=:name', array(':name' => $user_name));

        if( md5( $old_password.$user->salt ) == $user->password ) {

            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $salt = '';

            for ($i = 1; $i < 30; $i++) {
                $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            $user->name = $user_name;
            $user->salt = $salt;
            $user->password = md5($password.$salt);
            $user->save();
            $this->redirect($this->createUrl("admin/user/user/list"));
        }
        else{
            $this->smarty->assign( 'message' , '原始密码错误' );
            $this->smarty->assign('current','system');
            $this->smarty->display( 'admin/user/error.html' );
        }
    }

    /**
     * 跳转到增加管理员页面
     */
    public function actionToAddUser(){
        $this->smarty->assign('current','system');
        $this->smarty->display( 'admin/user/adduser.html' );
    }

    /**
     * 增加管理员
     */
    public function actionAddUser(){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $username = $_POST['username'];
        $password = $_POST['password'];

        $users = Admin::model()->find('name=:name', array(':name' => $username));
        if( $users == null ) {

            $salt = '';

            for ($i = 1; $i < 30; $i++) {
                $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
            }

            $user = new Admin();

            $user->name = $username;
            $user->salt = $salt;
            $user->password = md5($password.$salt);

            $user->save();
            $this->redirect($this->createUrl("admin/user/user/list"));
        }
        else{
            $this->smarty->assign( 'message' , '用户名已经存在' );
            $this->smarty->assign('current','system');
            $this->smarty->display( 'admin/user/error.html' );
        }
    }
}