<?php
    
    Class ModifyUserInfoController extends Controller
    {
    	public function actionIndex()
    	{    	
    		//yii::app()->session['user_id']=1;
    		$image = UserDetail::model()->findByAttributes(array('user_id'=>yii::app()->session['user_id']))->head_url;
            $password = User::model()->findByAttributes(array('id'=>yii::app()->session['user_id']))->password;
            $this->smarty->assign('image',$image);
            $this->smarty->assign('password',$password);
    		$this->smarty->display('job/user-info/modify-info.html');
    	}
    	 /*************************修改头像************************/   
	      public function actionDealImage()
	      {	   
	        // yii::app()->session['user_id']=1;       
	         $model = UserDetail::model()->findByAttributes(array('user_id'=>yii::app()->session['user_id']));	         	              
	         $image = CUploadedFile::getInstanceByName('head_url');         
	         $uploadPath ="assets/uploadFile/image/"; 	         
	         //$im=$image->getName();
	         $fileUrl = $uploadPath.time().'.'.$image->getExtensionName();
	         if($image->saveAs($fileUrl))
	         {	           	             
	             $model->head_url="http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/'.$fileUrl; 	    	                  
	             if($model->save()){
	                 $this->redirect(array('index'));
	             }else{
	                 echo "添加失败";
	            }
	          }else{
	              echo "添加失败";
	          }
	      }

        /*************************修改密码************************/
        public function actionPassword()
        {
        	//yii::app()->session['user_id']=1;       
	        $model = User::model()->findByAttributes(array('id'=>yii::app()->session['user_id']));
            if(!empty($_POST))
            {
                if($model->password==md5($_POST['curentPassword']))
                {
                   $model->password=md5($_POST['newPassword']);
                    if($model->save())
                    {
                         Yii::app()->session->clear();            
                         Yii::app()->session->destroy(); 
                         $this->redirect(array('site/index'));  
                    }
                    else{

                        $message = "密码修改失败";
                        echo"<script>alert('".$message."')</script>";
                        $this->smarty->assign('model',$model);
                        $this->smarty->display('job/user-info/modify-password.html');
                    }

                }
                else
                {                    
 
                     $message = "现有密码错误，请重试";
                     echo"<script>alert('".$message."')</script>";
                     $this->smarty->assign('model',$model);
                     $this->smarty->display('job/user-info/modify-password.html');
                }

            }
        }

    }
?>