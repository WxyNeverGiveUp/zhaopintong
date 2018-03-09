<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

/**
* 求职通页面控制器
*/
class JobController extends Controller
{  
   public function filters(){
        // return array('checkAuth');
    }

    public function actionTest(){
    	echo $this->user_id;
    }	

   /*
   *求职通首页
   */
	public function actionIndex(){
        $data = json_decode(file_get_contents("php://input"));
		$user_id = $data->user_id;
        
		$array = array();
        // 查询用户信息
		$UserDetail = UserDetail::model()->findByAttributes(array('user_id'=>$user_id));
        
        // 查询该用户的教育经历，所在院校、专业
		$Experience = StudyExperience::model()->find(array(
                    'order'=>'id desc',
                    'condition'=>'user_id=:user_id',
                    'params'=>array(':user_id'=>$user_id)));
		if ($Experience==null) {
			$Experience = WorkExperience::model()->find(array(
                    'order'=>'id desc',
                    'condition'=>'user_id=:user_id',
                    'params'=>array(':user_id'=>$user_id)));
		}
        
        // 查询该用户关注的宣讲会，单位，职位数目
		$PositionOrderNum = PositionUser::model()->countByAttributes(array('user_id'=>$user_id));

		$CompanyOrderNum = CompanyUser::model()->countByAttributes(array('user_id'=>$user_id));

		$RecruitOrderNum = CareerTalkUser::model()->countByAttributes(array('user_id'=>$user_id));

		$array['name'] = $UserDetail->realname;
		$array['headUrl'] = $UserDetail->head_url;
		$array['school'] = $Experience->school_name;
		$array['major'] = $Experience->major_name;
		$array['PositionOrderNum'] = $PositionOrderNum;
		$array['CompanyOrderNum'] = $CompanyOrderNum;
		$array['RecruitOrderNum'] = $RecruitOrderNum;
      
		$array = CJSON::encode($array);

		print_r($array);



	}

	/*
	*账号信息
	*/
	public function actionUser(){
		$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;

		$UserModel = User::model();
		$UserInfo = $UserModel->findByPk($user_id);
		$UserDetail = UserDetail::model()->find(array(
			      'select'=>'realname,head_url',
                  'condition'=>'user_id=:user_id',
                  'params'=>array(':user_id'=>$user_id)
              ));
        $array['account'] = $UserInfo->username;
        $array['realname'] = $UserDetail->realname;
        $array['headUrl'] = $UserDetail->head_url;

        $array = CJSON::encode($array);

        print($array);

	}




	/*
	*已收藏的职位
	*/
	public function actionPosition(){
		$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $offset = $data->offset;
		$array = array();
		$cri = new CDbCriteria();

		// 查询已收藏的职位id和数量
		$PositionUser = PositionUser::model();
		$PositionUserInfo = $PositionUser->findAllByAttributes(array('user_id'=>$user_id));

		foreach ($PositionUserInfo as $key => $value) {
			$PositionId[] = $value->position_id;
		}

		$PositionModel = Position::model();
		$cri->select = 'id,name,position_source';
		$cri->addInCondition('t.id',$PositionId);
		$cri->limit = 10;
		$cri->offset = $offset;
        $cri->order = 't.id desc';
        
        $PositionInfo = $PositionModel->with('company','city','degree','positionspecialty','brightened')->findAll($cri);
        $num = count($PositionInfo);

        $ResumeUser = ResumeUser::model()->findAllByAttributes(array('user_id'=>$user_id));
        
        if ($ResumeUser!=null) {
            foreach ($ResumeUser as $key => $value) {
                foreach ($PositionInfo as $k => $v) {
                    if ($v->id == $value->position_id) {
                        $array['info'][$k]['sended'] = 1;
                    }else{
                        $array['info'][$k]['sended'] = 0;
                    }  
                }
            }
        }

        $array['num']  = $num;
        foreach ($PositionInfo as $key => $value) {
        	$array['info'][$key]['name'] = $value->name;
        	$array['info'][$key]['id'] = $value->id;
        	$array['info'][$key]['degree'] = $value->degree->name;
        	$array['info'][$key]['city'] = $value->city->name;
        	$array['info'][$key]['company'] = $value->company->name;
        	$array['info'][$key]['positionspecialty'] = $value->positionspecialty->name;
            $array['info'][$key]['brightened1'] = $value->brightened[0]->name;
            $array['info'][$key]['brightened2'] = $value->brightened[1]->name;
        	if ($value->position_source==1) {
        		$array['info'][$key]['position_source'] = '东北师大';
        	}else if($value->position_source==2){
        		$array['info'][$key]['position_sources'] = '6所部属';
        	}else if($value->position_source==3){
        		$array['info'][$key]['position_source'] = '互联网';
        	}
        }

        
        $array = CJSON::encode($array);
        print($array);

	}

    /*
    *收藏职位
    */
    public function actionPositionConcern(){
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $position_id = $data->position_id;
        // $position = Position::model()->findByPk($position_id);
       

        // $positionUser = PositionUser::model()->find(array(
        //     'condition' => 'position_id=:positionId AND user_id=:userId',
        //     'params' => array(':positionId'=>$jobId,':userId'=>$userId),
        // ));

        $positionUserOne = new PositionUser();
        $positionUserOne->position_id=$position_id;
        $positionUserOne->user_id = $user_id;
        if ($positionUserOne->save()) {
           $message = array(
                   'code'=>0,
                   'data'=>''
                );
        }else{
           $message = array(
                   'code'=>0,
                   'data'=>'fail'
                );

        }
         
        print CJSON::encode($message);
    }

	/*
	*取消收藏职位
	*/
	public function actionPositionCancel(){
	    $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
		$id = $data->position_id;



		$PositionUser = PositionUser::model();

		$rs = $PositionUser->deleteAllByAttributes(array('user_id'=>$user_id,'position_id'=>$id));

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

	/*
	*已收藏的单位
	*/
    public function actionCompany(){
    	$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $offset = $data->offset;

    	$array = array();
		$cri = new CDbCriteria();

		// 查询已收藏的职位id
		$CompanyUser = CompanyUser::model();
		$CompanyUserInfo = $CompanyUser->findAllByAttributes(array('user_id'=>$user_id));

		foreach ($CompanyUserInfo as $key => $value) {
			$CompanyId[] = $value->company_id;
		}

		$CompanyModel = Company::model();
		$cri->select = 'id,name';
		$cri->addInCondition('t.id',$CompanyId);
		$cri->limit = 10;
		$cri->offset = $offset;
        
        $CompanyInfo = $CompanyModel->with('companytrade','city','companyproperty')->findAll($cri);
        $num = count($CompanyInfo);
        $Bright = Brightened::model();

        
        $array['num'] = $num;
        foreach ($CompanyInfo as $key => $value) {
        	$array['info'][$key]['name'] = $value->name;
        	$array['info'][$key]['id'] = $value->id;
        	$array['info'][$key]['trade'] = $value->companytrade->name;
        	$array['info'][$key]['city'] = $value->city[0]->name;
        	$array['info'][$key]['property'] = $value->companyproperty->name;
        	$BrightInfo = $Bright->findByAttributes(array('type'=>1,'related_id'=>$value->id));
        	$array['info'][$key]['bright'] = $BrightInfo->name;
        }

        $array = CJSON::encode($array);
        print_r($array);

    }

    /*
    *收藏职位
    */
    public function actionCompanyConcern(){
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $company_id = $data->company_id;

        $companyUserOne = new CompanyUser();
        $companyUserOne->company_id=$company_id;
        $companyUserOne->user_id = $user_id;
        if ($companyUserOne->save()) {
           $message = array(
                   'code'=>0,
                   'data'=>''
                );
        }else{
           $message = array(
                   'code'=>0,
                   'data'=>'fail'
                );

        }
         
        print CJSON::encode($message);
    }

    /*
    *取消关注单位
    */
    public function actionCompanyCancel(){
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
		$id = $data->company_id;

		$CompanyUser = CompanyUser::model();

		$rs = $CompanyUser->deleteAllByAttributes(array('user_id'=>$user_id,'company_id'=>$id));

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


    /*
    *已报名的宣讲会
    */
    public function actionRecruit(){
    

    	$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;

    	$array = array();
		$cri = new CDbCriteria();

		// 查询已收藏的宣讲会id
		$CareerTalkUser = CareerTalkUser::model();
		$CareerTalkUserInfo = $CareerTalkUser->findAllByAttributes(array('user_id'=>$user_id));

		foreach ($CareerTalkUserInfo as $key => $value) {
			$CareerTalkId[] = $value->career_talk_id;
		}

		$CareerTalkModel = CareerTalk::model();
		$cri->select = 'id,name,time,type';
		$cri->addInCondition('t.id',$CareerTalkId);
		$cri->limit = 10;
		$cri->offset = $offset;
        
        $CareerTalkInfo = $CareerTalkModel->findAll($cri);
        $num = count($CareerTalkInfo);

        
        $array['num'] = $num;
        foreach ($CareerTalkInfo as $key => $value) {
        	$time = strtotime($value->time);
        	$month = date('m',$time);

        	$array['info'][$key]['name'] = $value->name;
        	$array['info'][$key]['id'] = $value->id;

        	$array['info'][$key]['month'] = substr($month,0,1)?$month:substr($month, 1,1);
        	$array['info'][$key]['day'] = date('d',$time);
        	$array['info'][$key]['time'] = date('H:i',$time);

        	if ($value->type==1) {
        		$array['info'][$key]['type'] = '视频宣讲会';
        	}else if($value->type==2){
        		$array['info'][$key]['type'] = '实地宣讲会';
        	}else if($value->type==3){
        		$array['info'][$key]['type'] = '外地宣讲会';
        	}

        	switch (date('w',$time)) {
        		case 1:
        			$array['info'][$key]['week'] = '周一';
        			break;
        		case 2:
        			$array['info'][$key]['week'] = '周二';
        			break;
        		case 3:
        			$array['info'][$key]['week'] = '周三';
        			break;
        		case 4:
        			$array['info'][$key]['week'] = '周四';
        			break;
        		case 5:
        			$array['info'][$key]['week'] = '周五';
        			break;
        		case 6:
        			$array['info'][$key]['week'] = '周六';
        			break;
        		case 0:
        			$array['info'][$key]['week'] = '周日';
        			break;
        		
        		default:
        			// 
        			break;
        	}

        }
        $array = CJSON::encode($array);
        print_r($array);
    }

    /*
    *报名宣讲会
    */
    public function actionRecruitConcern(){
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $recruit_id = $data->recruit_id;

        $recruitUserOne = new CareerTalkUser();
        $recruitUserOne->career_talk_id=$recruit_id;
        $recruitUserOne->user_id = $user_id;
        if ($recruitUserOne->save()) {
           $message = array(
                   'code'=>0,
                   'data'=>''
                );
        }else{
           $message = array(
                   'code'=>0,
                   'data'=>'fail'
                );

        }
         
        print CJSON::encode($message);
    }

    /*
    *取消宣讲会报名
    */
    public function actionRecruitCancel(){
		$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $id = $data->id;

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


    /*
    *短信定制页面
    */
    public function actionMessage(){
    

		$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;

		$UserModel = User::model();
		$UserInfo = $UserModel->findByPk($user_id);

		$array['cellphone'] = $UserInfo->cellphone;

		$array = CJSON::encode($array);

		print($array);
    }

    /*
    *添加绑定手机
    */
    public function actionAddcellphone(){

		$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $cellphone = $data->cellphone;

		$UserModel = User::model();
		$UserInfo = $UserModel->findByPk($user_id);

		$UserInfo->cellphone = $cellphone;

		if ($UserInfo->save())  {
			$message = array(
                   'code'=>0,
                   'data'=>array(
               	             'cellphone'=>$cellphone
                   	)
				);
		}else{
			$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据添加失败')
				);
		}

		$message = CJSON::encode($message);
        
		print($message);
    }

    /*
    *删除绑定手机
    */
    public function actionDelcellphone(){
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;

		$UserModel = User::model();
		$UserInfo = $UserModel->findByPk($user_id);

		$UserInfo->cellphone = null;

		if ($UserInfo->save())  {
			$message = array(
                   'code'=>0,
                   'data'=>array(
               	             'cellphone'=>null
                   	)
				);
		}else{
			$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据删除失败')
				);
		}

		$message = CJSON::encode($message);

		print($message);
    }


    /*
    *添加求职信
    */
    public function actionAddLetter(){
    	$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
    	$resume_id = Resume::model()->find(array(
    		     'select'=>'id',
    		     'condition'=>'user_id=:user_id',
    		     'params'=>array(':user_id'=>$user_id)
    		     ))
    	         ->id;

    	$title = $data->title;
    	$content = $data->content;
    	$id = $data->id;

    	if (!$id) {
    		$model = new ApplicationLetter();
    	}else{
    		$id = (int)$id;
    		$model = ApplicationLetter::model()->findByPk($id);
    	}

        // print_r($id);die();
    	$model->title = $title;
    	$model->content = $content;
    	$model->resume_id = $resume_id;

    	if ($model->save()) {
			$message = array(
                   'code'=>0,
                   'data'=>array(
               	             
                   	)
				);
		}else{
			$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据添加失败')
				);
		}

        $message = CJSON::encode($message);
        print($message);

    }

    /*
    *求职信列表
    */
    public function actionApplicationLetter(){
    	$data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
    	$resume_id = Resume::model()->find(array(
    		     'select'=>'id',
    		     'condition'=>'user_id=:user_id',
    		     'params'=>array(':user_id'=>$user_id)
    		     ))
    	         ->id;

    	$model = ApplicationLetter::model();
    	$Info = $model->findAllByAttributes(array('resume_id'=>$resume_id));
        

        $UserDetail = UserDetail::model()->with('city')->findByAttributes(array('user_id'=>$user_id));
        
        // 查询该用户的教育经历，所在院校、专业
        $Experience = StudyExperience::model()->with('position_degree')->find(array(
                    'order'=>'t.id desc',
                    'condition'=>'user_id=:user_id',
                    'params'=>array(':user_id'=>$user_id)));
        // if ($Experience==null) {
        //     $Experience = WorkExperience::model()->with('position_degree')->find(array(
        //             'order'=>'t.id desc',
        //             'condition'=>'user_id=:user_id',
        //             'params'=>array(':user_id'=>$user_id)));
        // }
        
        
        $array['User']['name'] = $UserDetail->realname;
        $array['User']['headUrl'] = $UserDetail->head_url;
        $array['User']['birthday'] = $UserDetail->birthday;
        $array['User']['city'] = $UserDetail->city->name;
        $array['User']['degree'] = $Experience->position_degree->name;
        $array['User']['school'] = $Experience->school_name;
        $array['User']['major'] = $Experience->major_name;
        if ($UserDetail->gender==1) {
            $array['User']['gender'] = '男';
        }else if ($UserDetail->gender==0) {
            $array['User']['gender'] = '女';
        }

    	foreach ($Info as $key => $value) {
    		$array['applicationLetter'][$key]['id'] = $value->id;
    		$array['applicationLetter'][$key]['title'] = $value->title;
    		$array['applicationLetter'][$key]['content'] = $value->content;
    	}

    	$array = CJSON::encode($array);

    	print($array);
    }

    /*
    *编辑求职信
    */
    public function actionEditApplicationLetter(){
        $data = json_decode(file_get_contents("php://input"));
        $id = (int)$data->id;
        $model = ApplicationLetter::model()->findByPk($id);

        print(CJSON::encode($model));
    }

    /*
    *删除求职信
    */
    public function actionDelApplicationLetter(){
    	$data = json_decode(file_get_contents("php://input"));
        $id = (int)$data->id;
    	$model = ApplicationLetter::model()->findByPk($id);

    	if ($model==null) {
    		$message = array(
				   'code' => 0,
				   'data' =>array()
				);
    		$message = CJSON::encode($message);
	    	print($message);
	    	return;
    	}
    	if ($model->delete()) {
    		$message = array(
				   'code' => 0,
				   'data' =>array()
				);
    	}else{
    		$message = array(
				   'code' => 1,
				   'data' =>array('errMsg'=>'数据删除失败')
				);
    	}

    	$message = CJSON::encode($message);
    	print($message);
    }

  


}
