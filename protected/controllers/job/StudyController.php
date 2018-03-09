<?php
/**
* 功能：实现报名宣讲会与短信定制
*/
Yii::import('ext.muzhi.MuZhiSdk',true);
      class StudyController extends Controller 
      {

          public function actionIndex()
            {
              //yii::app()->session['user_id']=1;
               $userId=yii::app()->session['user_id'];
               $this->smarty->assign('userId',$userId);
               $current="current";
               $this->smarty->assign('phone',$current);
               $this->smarty->display('job/signup_preach/signup_preach.html');
            }            
          public function actionDel($id)
      	    {                   
                  $userId=yii::app()->session['user_id'];                 
      	    	    $dorm=CareerTalkUser::model()->deleteAll('user_id=:userId and career_talk_id=:id',
                    array(':userId'=>$userId,':id'=>$id));
                  if($dorm>0)
                  {
                     $json='{"code":0,"data":""}';
                     print $json;    
                  }                
      	    }

          public function actionMessage()
          {
              if(User::model()->findByPk(Yii::app()->session['user_id'])->cellphone==null)
                  $this->actionToAddPhone();
              else
                  $this->actionShowPhone();
          }

          public function actionSendMessage()
          {
              $userList = User::model()->findAll(array('condition' => 'cellphone is not null and is_activated=1 and is_league=2'));
              $careerList = CareerTalk::model()->findAll(array(
                  'condition' => "str_to_date(time,'%Y-%m-%d')=:date AND type=2 AND(is_front_input=0 OR is_ok=1)",
                  'params' => array(':date'=>date('Y-m-d',time())),
                  'order'=>'time'
              ));
              $text = array();
              $text[0] = '今日宣讲会：';
              $text1 = array();
              $text1[0] = '今日宣讲会：';
              $i = 0;
              foreach ($careerList as $key => $value) {
                  if($key==count($careerList)-1){
                      if(mb_strlen($text[$i].=$value->name.',时间:'.date('Y-m-d H:i',strtotime($value->time)).',地点:'.$value->place)+8<=300)
                      $text1[$i] .=$value->name.',时间:'.date('Y-m-d H:i',strtotime($value->time)).',地点:'.$value->place;
                      else {
                          $i++;
                          $text1[$i] .= $value->name . ',时间:' . date('Y-m-d H:i',strtotime($value->time)) . ',地点:' . $value->place;
                      }
                  }
                  else {
                      if(mb_strlen($text[$i].=$value->name.',时间:'.date('Y-m-d H:i',strtotime($value->time)).',地点:'.$value->place . ';')+8<=300)
                          $text1[$i] .= $value->name . ',时间:' . date('Y-m-d H:i',strtotime($value->time)) . ',地点:' . $value->place . ';';
                      else {
                          $i++;
                          $text1[$i] .= $value->name . ',时间:' . date('Y-m-d H:i',strtotime($value->time)) . ',地点:' . $value->place . ';';
                      }
                  }
              }
              if($careerList!=null) {
                  foreach ($userList as $key => $value) {
                      foreach ($text1 as $val) {
                          send_sms($val,$value->cellphone);
                      }
                  }
              }
          }
          public function actionToAddPhone()
            {
               $current="current";
               $this->smarty->assign('message',$current);
               $this->smarty->display('job/phone/add-phone.html');
            }
          public function actionAddPhone()
            {
                if(!empty($_POST['cellphone']))
                {
                     $id = yii::app()->session['user_id'];                                
                     $msg = User::model()->findByPk($id);
                     $msg ->cellphone= $_POST['cellphone']; 
                     if($msg->save())
                     {
                        $msg->refresh();
                        $this->redirect($this->createUrl("job/study/message"));
                     } 
                 }              
            }

          public function actionShowPhone()
            {
                $id=yii::app()->session['user_id'];                            
                $msg = User::model()->findByPk($id);
                $cellphone = $msg->cellphone;
                $this->smarty->assign('cellphone',$cellphone);
                $current="current";
                $this->smarty->assign('message',$current);
                $this->smarty->display('job/phone/change-phone.html');
            }

          public function actionUpdate()
            {
                $id=yii::app()->session['user_id'];                           
                $msg = User::model()->findByPk($id);
                if($_POST['cellphone']){
                    $msg->cellphone = $_POST['cellphone'];
                    if($msg->save())
                    {
                       $msg->refresh();
                       $this->redirect($this->createUrl("job/study/message"));
                    }                  
                }
            }
          public function actionDelPhone()
            {
                $id =Yii::app()->session['user_id'];
                $msg =User::model()->findByPk($id);
                if(!empty($msg)){
                    $msg->cellphone=null;
                    if($msg->save())
                    {
                        print '{"code":"0"}'; 
                    }else{
                         print '{"code":"1"}'; 
                    }                                    
                }
            }

          public function filters() {
              return array(
                  array('application.controllers.filters.SessionCheckFilter + message')
              );
          }

      }
?>