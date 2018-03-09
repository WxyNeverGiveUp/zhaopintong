<?php

 /*************************实现忘记密码的功能*************************************/
   class ForgetPassController extends Controller
   {
      public function actionToEmail($email)
      {
          include("protected/extensions/phpmailer/sendl.php");   //导入发送邮件类
          $em = User::model()->findByAttributes(array('email'=>$email));
          $user_id=$em->id;
          $UserDetail = UserDetail::model()->findByAttributes(array('user_id'=>$user_id));
          if($em)
          {
                $gao = new smtpclass();  
                //创建发送邮件的对象
                $emailbody =$UserDetail->realname."你好！ 
                <br/>请点击以下链接，并根据页面提示完成密码重设<br/>     
                <a href='"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/"."user/forgetPass/toSetPass/email/".$email."'
                target= '_blank'>"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/user/forgetPass/toSetPass</a><br/>
                如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问。";             
                 //发送邮件         
                $gao->senduserMail("smtp.ym.163.com",25,"noreplay@dsjyw.net","jy123456","noreplay@dsjyw.net",$email,"东北高师就业联盟网找回密码","'".$emailbody."'","HTML");
                if($gao)
                {
                    print '{"code":"0"}';    
                }else
                {
                     print '{"code":"1"}'; 
                }
                
          }
          else
          {
              print '{"code":"1"}';   
          }
      }

      public function actionToSetPass($email)
      {
           
           $this->smarty->assign('email',$email);
           $this->smarty->display('user/edetail.html');                  	    
      }

      public function actionSetPass($email)
      {      	   
      	  $model = User::model()->findByAttributes(array('username'=>$email));
          if($model)
          {
            header('Content-Type:text/html; charset=utf-8;');
                $model->password = md5(trim($_POST['password']));                            
                if($model->save())
                {
                   $error_message = '密码设置成功';
                   echo "<script>alert('".$error_message."')</script>";
                   $this->redirect(array('site/index'));
                }else
                {
                   $error_message = '密码设置失败';
                   echo "<script>alert('".$error_message."')</script>";
                   $this->redirect(array('site/index'));
                }          
          }else
          {
                $error_message = '用户名不存在';
                echo "<script>alert('".$error_message."')</script>";
                $this->redirect(array('site/index'));
          }       
   }
}
?>