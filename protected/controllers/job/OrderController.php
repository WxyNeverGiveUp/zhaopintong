<?php 
/**
* 
*/

// 订阅信息的邮件发送
// 在linux设置crontab 每半小时执行此文件，
// 查找时间戳小于当前时间戳并且未发送的订阅信息并发送邮件
class OrderController extends Controller
{
	
	public function actionIndex(){
	//	echo time();die();
        
       // $sql="select name from {{position}}";
        //$posName = Position::model()->findAllBySql($sql);
        //$this->smarty->assign('posName',$posName);
        $current="current";
        $this->smarty->assign('list',$current);
		$this->smarty->display('job/job_subscription/job_subscription.html');
	}

	public function actionAddOrder(){
		$model = new PositionOrder();
		$user_id = Yii::app()->session['user_id'];
			$model->re_time= date('y-m-d',time());
			$model->re_email = $_POST['email'];
            $place = City::model()->find(
                array(
                    'condition' => 'name LIKE :keyword',
                    'params' => array(':keyword'=>'%'.$_POST['place'].'%'),
                ))->id;
            $model->city_id = $place;
            $model->position_name = $_POST['position'];
			$model->user_id = $user_id;
			$model->circle = $_POST['circle'];
			if ($model->save()) {
				$this->redirect(array('list'));
			}else{
				echo "添加失败";
			}
	}

	public function actionMail(){
		$now = time();
		$cri = new CDbCriteria();
		$PositionModel = Position::model();
		$OrderModel = PositionOrder::model();
        
        //$cri->addCondition('has_send = 0');
        //$cri->addCondition('re_time <= :now');
        //$cri->params[':now'] = $now;
		$OrderInfo = $OrderModel->findAll($cri);
        $nowDay = date('y-m-d',time());
        $data = array();
		if (sizeof($OrderInfo)) {
			foreach ($OrderInfo as $key => $value) {
                if (round((strtotime($nowDay) - strtotime($value->re_time)) / 3600 / 24) % $value->circle == 0){
                    $city_id = $value->city_id;
                    $position_name = $value->position_name;
                    $sql = "SELECT * FROM `t_position` WHERE (city_id=$city_id)
				       AND (`name` LIKE '%$position_name%')";
                    $pos = $PositionModel->findAllBySql($sql);
                    $data[] = array('id' => $value->id, 're_email' => $value->re_email, 'content' => $pos);
               }
			}
		}
        
		foreach ($data as $key => $value) {
            $mail = Yii::App()->mail;
            $re_email = $value['re_email'];
            $content = '';
             foreach($value['content'] as $position){
                 $content .= $position->name.',职位详情请点击'."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/position/position/detail/id/'.$position->id."\r\n";
             }
			$mail->IsSMTP();
			$mail->AddAddress("$re_email");
			$mail->Subject = "您的职位订阅信息"; //邮件标题
			$mail->Body ="$content"; //邮件内容

			if ($mail->send()) {
				$OrderSend = PositionOrder::model()->findByPk($value['id']);
				//$OrderSend->has_send = 1;
				if ($OrderSend->save()) {
					echo "sucess";
				}else{
					echo "发送失败";
				}
			}else{
				echo "error";
			}
		}

	}



	public function actionMtest(){
		$mail = Yii::App()->mail;
		// $mail->IsSendmail();
		// $mail->SMTPSecure = 'tls';

		$mail->IsSMTP();


		$mail->AddAddress('710358590@qq.com');

		$mail->Subject = "密码修改通知"; //邮件标题

		$mail->Body ="您的密码已经修改成123456"; //邮件内容

		if ($mail->send()) {
			echo "sucess";
		}
	}



    public function actionPosition()
    {
        $sql1 = 'select DISTINCT t_position.name as posName from t_position';
        $listPT = Yii::app()->db->createCommand($sql1)->queryAll();
        $SearchJson='{"code":0,"data":'.json_encode($listPT).',"dataCount":"'.count($listPT).'"}';
        print  $SearchJson;
    }

    public  function  actionList(){
        $orderList = PositionOrder::model()->with('city')->findAllByAttributes(array('user_id'=>Yii::app()->session['user_id']));
        $this->smarty->assign('jobOrderList',$orderList);
        $current="current";
        $this->smarty->assign('list',$current);
        $this->smarty->display('job/job_subscription/edit-subscribe.html');
    }

    public function actionDel($id)
    {
        $order = PositionOrder::model()->findByPk($id);
        if(!empty($order)){
            $order->delete();
        }
            $json='{"code":0}';
            print $json;
    }

    /*public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter')
        );
    }*/


    



}
 ?>