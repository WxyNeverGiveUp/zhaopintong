<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
/**
*  app首页模块
*/
class IndexController extends Controller
{
	/*
    *app首页
    *包含信息：焦点图； 最新公告；
	*/
	public function actionIndex(){
		$Array = array();
		$cri = new CDbCriteria();
		$cri->select = 'id,type_id,title,add_time';
		$cri->order = 't.id desc';
		$cri->limit = 6;
		$cri->addCondition('is_tea_recruitment=0');
		$announceModel = Announcement::model();
		$announceInfo = $announceModel->with('type')->findAll($cri);


        // 由于涉及到多个表的联查，字段分布在AR对象的不同属性，直接将AR对象转为json无法获得所有字段
        // 将查询信息重组为一个数组
		foreach ($announceInfo as $key => $value) {
			$Array['announce'][$key]['id'] = $value->id;
			$Array['announce'][$key]['type'] = $value->type->name;
			$Array['announce'][$key]['title'] = $value->title;
			$Array['announce'][$key]['time'] = substr($value->add_time, 0,10);
			$Array['announce'][$key]['add_time'] = $value->add_time;
		}

		$imageModel = IndexImage::model();
		$imageInfo = $imageModel->findAll();

		foreach ($imageInfo as $key => $value) {
			$Array['image'][$key]['id'] = $value->id;
			$Array['image'][$key]['path'] = 'http://www.dsjyw.net/'.$value->path;
			$Array['image'][$key]['url'] = $value->url;
			$Array['image'][$key]['number'] = $value->number;
		}
		$Array['imageSize'] = count($imageInfo);

		$Array = CJSON::encode($Array);
		print_r($Array);
        
		
	}

	/*
	*公告列表
	*点击查看更多后进入
    *每次请求返回20条数数据
	*/

	public function actionAnnounceList(){
		$Array = array();
		// $offset = $HTTP_RAW_POST_DATA['offset'];
		$data = json_decode(file_get_contents("php://input"));
		$offset = $data->offset;
		// $offset = $_POST['offset'];

        $cri = new CDbCriteria();
		$cri->select = 'id,type_id,title,add_time';
		$cri->order = 't.id desc';
		$cri->limit = 25;
		$cri->offset = $offset;
		$cri->addCondition('is_tea_recruitment=0');


		$announceModel = Announcement::model();
		$announceInfo = $announceModel->with('type')->findAll($cri);
        

        // 由于涉及到多个表的联查，字段分布在AR对象的不同属性，直接将AR对象转为json无法获得所有字段
        // 将查询信息重组为一个数组
		foreach ($announceInfo as $key => $value) {
			$Array['announce'][$key]['id'] = $value->id;
			$Array['announce'][$key]['type'] = $value->type->name;
			$Array['announce'][$key]['title'] = $value->title;
			$Array['announce'][$key]['add_time'] = $value->add_time;
			$Array['announce'][$key]['time'] = substr($value->add_time, 0,10);
		}
		$Array['num'] = count($announceInfo);

		$Array = CJSON::encode($Array);
		print($Array);
	}


	/*
	*刷新公告页
	*/
	public function actionAnnounceRefresh(){
		$Array = array();
		$data = json_decode(file_get_contents("php://input"));
		$first_id = $data->first-1;

		$announceModel = Announcement::model();
		$announceInfo = $announceModel->findAll(array(
                        'select'=>'id,type_id,title,add_time',
                        'condition'=>'id>:first_id',
                        'order'=>'id desc',
                        'params'=>array(':first_id'=>$first_id)
			));
		foreach ($announceInfo as $key => $value) {
			$Array['announce'][$key]['id'] = $value->id;
			$Array['announce'][$key]['type'] = $value->type->name;
			$Array['announce'][$key]['title'] = $value->title;
			$Array['announce'][$key]['add_time'] = $value->add_time;
			$Array['announce'][$key]['time'] = substr($value->add_time, 0,10);
		}
		$Array['first_id'] = $data->first;
		$Array = CJSON::encode($Array);
		print($Array);
	}

	/*
	*教师招考列表
	*每次请求返回20条数据
	*/
	public function actionTeacherRecruit(){
		$Array = array();
		$data = json_decode(file_get_contents("php://input"));
		$offset = $data->offset;

        $cri = new CDbCriteria();
		$cri->select = 'id,title,add_time';
		$cri->order = 't.id desc';
		$cri->limit = 25;
		$cri->offset = $offset;
		$cri->addCondition('is_tea_recruitment=1');

		$announceModel = Announcement::model();
		$teacherRecruitInfo = $announceModel->with('city')->findAll($cri);

        
        // 由于涉及到多个表的联查，字段分布在AR对象的不同属性，直接将AR对象转为json无法获得所有字段
        // 将查询信息重组为一个数组
		foreach ($teacherRecruitInfo as $key => $value) {
			if ($value->city!=null) {
				$Array['teacher'][$key]['id'] = $value->id;
				$Array['teacher'][$key]['city'] = $value->city->name;
				$Array['teacher'][$key]['title'] = $value->title;
				$Array['teacher'][$key]['add_time'] = $value->add_time;
				$Array['teacher'][$key]['time'] = substr($value->add_time, 0,10);
			}
			
		}

		$Array = CJSON::encode($Array);
		print_r($Array);
	}

	public function actionTeacherRefresh(){
		$Array = array();
		$data = json_decode(file_get_contents("php://input"));
		$first_id = $data->first-1;

		$announceModel = Announcement::model();
		$announceInfo = $announceModel->with('city')->findAll(array(
                        'select'=>'id,type_id,title,add_time',
                        'condition'=>'t.id>:first_id and is_tea_recruitment=1',
                        'order'=>'t.id desc',
                        'params'=>array(':first_id'=>$first_id)
			));
		foreach ($announceInfo as $key => $value) {
			$Array['teacher'][$key]['id'] = $value->id;
			$Array['teacher'][$key]['city'] = $value->city->name;
			$Array['teacher'][$key]['title'] = $value->title;
			$Array['teacher'][$key]['add_time'] = $value->add_time;
			$Array['teacher'][$key]['time'] = substr($value->add_time, 0,10);
		}
		$Array['first_id'] = $data->first;
		$Array = CJSON::encode($Array);
		print($Array);
	}

	

	/*
	*公告与教师招考的详细页面
	*/
    public function actionAnnounceDetail(){
    	$data = json_decode(file_get_contents("php://input"));
    	$id = $data->aid;
    	$announceInfo = Announcement::model()->findByPk($id);
    	$announceInfo = CJSON::encode($announceInfo);

    	print($announceInfo);

    }






	/*
    *宣讲会列表页面
    *返回宣讲会类型和行业的关联信息
	*/
	public function actionRecruitList(){
		$Array = array();
         
        // 宣讲会类型
		$type = array(
			   1=>array('name'=>'视频宣讲','id'=>1),
			   2=>array('name'=>'实地宣讲','id'=>2),
			   3=>array('name'=>'外地宣讲','id'=>3),
			);
        
        // 查询行业表，获得行业信息
		$tradeModel = CompanyTrade::model();
		$tradeInfo = $tradeModel->findAll();
		foreach ($tradeInfo as $key => $value) {
			$Array['trade'][$key]['name'] = $value->name;
			$Array['trade'][$key]['id'] = $value->id;
		}
		// 拼接数组
		$Array['type'] = $type;

		$Array = CJSON::encode($Array);

		print_r($Array);

	}



	/*
	*宣讲会列表页面
	*根据ajax请求参数返回相应宣讲会数据
	*每次请求返回10条数据
	*/
	// public function actionRecruitJson(){
	// 	// 获取参数
	// 	$token = Yii::app()->request->getPost('token');
	// 	$offset = Yii::app()->request->getPost('offset');
	// 	$type = Yii::app()->request->getPost('type');
	// 	$trade = Yii::app()->request->getPost('trade');
	// 	$time = Yii::app()->request->getPost('time'); //参数为0则代表无此限制条件

	// 	// 强制类型转换
	// 	$offset = (int)$offset;
	// 	$type = (int)$type;
	// 	$trade = (int)$trade;
		
	// 	// 通过token获取用户id
	// 	// $user_id = Yii::app()->cache()->get($token);
 //        $user_id = 5786;

	// 	$Array = array();


	// 	$cri = new CDbCriteria();
	// 	$cri->limit = 10;
	// 	$cri->offset = $offset;
	// 	$cri->select = 'id,name,time';
	// 	$cri->order = 't.id desc';
	// 	$params = array();
	// 	if ($type) {
	// 		$cri->addCondition("type= :type");
	// 		$params[':type'] = $type;
	// 	}
	// 	if ($time) {
	// 		$cri->addCondition("date_format(time,'%Y-%m-%d')= :time");
	// 		$params[':time'] = $time;
	// 	}
	// 	if ($trade) {
	// 		$cri->addCondition("company.trade_id= :trade_id");
	// 		$params[':trade_id'] = $trade;
	// 	}
	// 	$cri->params = $params;

	// 	$recruitModel = CareerTalk::model();
	// 	$recruitInfo = $recruitModel->with('company','user')->findAll($cri);
        
 //        // 由于涉及到多个表的联查，字段分布在AR对象的不同属性，直接将AR对象转为json无法获得所有字段
 //        // 将查询信息重组为一个数组
	// 	foreach ($recruitInfo as $key => $value) {
	// 		$Array[$key]['id'] = $value->id;
	// 		$Array[$key]['name'] = $value->name;
	// 		$Array[$key]['time'] = $value->time;

	// 		// 判断当该宣讲会是否被当前用户关注
	// 		if (!empty($value->user)) {
	// 			foreach ($value->user as $k => $v) {
	// 				if ($v->user_id == $user_id) {
	// 					$Array[$key]['is_focus'] = 1;
	// 				}else{
	// 					$Array[$key]['is_focus'] = 0;
	// 				}
					
	// 			}
	// 		}else{
	// 			$Array[$key]['is_focus'] = 0;
	// 		}
	// 	}

 //        $Array = CJSON::encode($Array);
	// 	print_r($Array);
	// }

	public function actionRecruitJson($user_id=0,$page=0,$searchWord=null,$timeId=0,$preachTypeId=0,$industryId=0,$id=0,$userId=0,$isFollow=0,$follow=0){
		$isFollow = 0;
        $dataCount = count(CTService::getInstance()->searchForFront($searchWord,$timeId,$industryId,$preachTypeId,0,$id,$userId,$isFollow));
        // $dataCount = 1;
        $list = CTService::getInstance()->searchForFront($searchWord,$timeId,$industryId,$preachTypeId,$page,$id,$userId,$isFollow);
        if ($follow==1&&$user_id!=0) {
        	$CompanyUser = CompanyUser::model()->findAllByAttributes(array('user_id'=>$user_id));
	        foreach ($list as $key => $value) {
	        	foreach ($CompanyUser as $k => $v) {
	        		if ($v->company_id!=$value['company_id']) {
	        			unset($list[$key]);
	        		}
	        	}
	        }
        }
        
        foreach ($list as $key => $value) {
            if(isset($value['id']))
                $careerTalk[$key]['id'] = $value['id'];
            else
                $careerTalk[$key]['id'] = "0";
            if(isset($value['time'])){
                $time = $value['time'];
                $month = date('m',strtotime($time));
                if ($month<10) {
                	$month = substr($month, 1,1);
                }
                
                $day = date('d',strtotime($time));
                $weekArray = array("日", "一", "二", "三", "四", "五", "六");
                $week = "周" . $weekArray[date("w", strtotime($time))];
                $careerTalk[$key]['month'] = $month;
                $careerTalk[$key]['date'] = $day;
                $careerTalk[$key]['week'] = $week;
                $careerTalk[$key]['time'] = date('H:i',strtotime($time));
            }
            else{
                $careerTalk[$key]['month'] = "无";
                $careerTalk[$key]['date'] = "无";
                $careerTalk[$key]['week'] = "无";
                $careerTalk[$key]['time'] = "无";
            }
            if(isset($value['name']))
                $careerTalk[$key]['company'] = $value['name'];
            else
                $careerTalk[$key]['company'] = "无";
            if(isset($value['preachType'])){
                if($value['preachType']==1)
                $careerTalk[$key]['preachType'] = '视频宣讲';
                elseif($value['preachType']==2)
                    $careerTalk[$key]['preachType'] = '实地宣讲';
                elseif($value['preachType']==3)
                    $careerTalk[$key]['preachType'] = '外地宣讲';
                else
                    $careerTalk[$key]['preachType'] = '外地宣讲';
            }
            else
                $careerTalk[$key]['preachType'] = "无";
            if(isset($value['location']))
                $careerTalk[$key]['location'] = $value['location'];
            else
                $careerTalk[$key]['location'] = "无";
            if(isset($value['isOverdue']))
                $careerTalk[$key]['isOverdue'] = $value['isOverdue'];
            else
                $careerTalk[$key]['isOverdue'] = "无";
            //if(isset($value['isEnroll'])){
                $caUser = CareerTalkUser::model()->find(array(
                    'condition' => 'career_talk_id=:id AND user_id=:userId',
                    'params' => array(':id'=>$value['id'],':userId'=>$user_id),
                ));
                $concerned = $caUser?1:0;
                $careerTalk[$key]['isEnroll'] = $concerned;
        }
        $searchJson = '{"code":0,"data":'.json_encode($careerTalk).',"dataCount":'.$dataCount.',"timeId":'.$timeId.'}';
        print $searchJson;
    }
   
    /*
    *用户关注宣讲会
    *在t_career_talk表中添加一条记录
    */
	public function actionFocusRecruit(){
		$token = Yii::app()->request->getPost('token');
		$id = Yii::app()->request->getPost('id');

		$id = (int)$id;

		// 通过token，从缓存中获取当前用户的id
		$user_id = Yii::app()->cache->get($token);

		$CareerTalkUser = new CareerTalkUser();
		$CareerTalkUser->career_talk_id = $id;
		$CareerTalkUser->user_id = $user_id;

		if ($CareerTalkUser->save()) {
			$message = array(
                   'code'=>0,
                   'data'=>array(
               	             'id'=>$CareerTalkUser->attributes('career_talk_id'),
                             'user_id'=>$CareerTalkUser->attributes('user_id')
                   	)
				);
		}else{
			$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据修改失败')
				);
		}

		$message = CJSON::encode($message);

		print_r($message);
	}

    

    /*
    *用户取消关注宣讲会
    *在t_career_talk表中删除这条记录
    */
    public function actionCancelFocusRecruit(){
    	$token = Yii::app()->request->getPost('token');
    	$id = Yii::app()->request->getPost('id');

    	$user_id = Yii::app()->cache->get($token);

    	$CareerTalkUser = CareerTalkUser::model();

    	$rs = $CareerTalkUser->deleteAllByAttributes(array('user_id'=>$user_id,'career_talk_id'=>$id));

    	if ($rs) {
			$message = array(
                   'code'=>0,
                   'data'=>array(
               	             'id'=>$id,
                             'user_id'=>$user_id
                   	)
				);
		}else{
			$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据删除失败')
				);
		}

		$message = CJSON::encode($message);

		print_r($message);
    }



    public function actionTest(){
    	$model = Announcement::model();

    	$cri = new CDbCriteria();
    	$cri->select = 'id,title';
    	$cri->order = 'id desc';
    	$cri->limit = 10;

    	$Infos = $model->findAll($cri);

    	$this->render('test',array('Infos'=>$Infos));
    }



}