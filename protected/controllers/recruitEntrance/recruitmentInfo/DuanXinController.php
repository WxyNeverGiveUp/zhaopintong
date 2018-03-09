<?php

/**
 * Created by PhpStorm.
 * User: 郑文华
 * Date: 2017/7/18
 * Time: 15:31
 */
Yii::import('ext.muzhi.MuZhiSdk',true);
class duanXinController  extends Controller{
    public  $code = '';
     //发送验证码
     public function actionSendCode(){

         $tel = $_GET['tel'];
         //生成验证码
         for($i=0;$i<4;$i++){
          $this->code .= rand(0,9);
         }

         $content = '你的验证码是 '.$this->code;
         $model = CompanyLoginUser::model();
         $sql = "select * from  {{company_login_user}} where phone = ".$tel;
         $modelInfo = $model->findBySql($sql);
         if($modelInfo) {
             send_sms($content,$tel);
             //传给前端，由前端进行验证码的验证
             echo "right|".$this->code;
         }
         else{
             $message = '手机号无效或者该手机号为未注册的手机号';
             echo $message;
         }
     }

     //验证找回密码的表单
    public function actionVerify()
    {
      $tel = $_POST['username'];
      $password = $_POST['password'];
      $passwordAgain = $_POST['passwordAgain'];

        $sql = "select * from  {{company_login_user}} where phone = '".$tel."'";
        $modelInfo = CompanyLoginUser::model()->findBySql($sql);
        //判断两次密码是否一致
       if($password == $passwordAgain){
           //更新信息，设置新的密码
              $modelInfo->password = md5($password);
              $modelInfo->save();
              $message = '密码修改成功，请重新登陆';
           print $message;
       }else {
           $message = '两次密码不一致，请重新输入';
           print $message;
       }
        $this->smarty->display('recruitEntrance/user/login.html');
    }

}





