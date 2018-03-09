<?php 
/**
* *************************************求职通模块控制器**************************************
*/
class JobController extends Controller
{   
    /***********我的主页job/home/mine.html与person.html***************/
    public function actionIndex()
    {
        header('Content-Type:text/html; charset=utf-8;');
        $user_id=$this->actionEditUserId($_GET['user_id'],yii::app()->session['user_id']);
        if($user_id==null)
            Yii::app()->runController('site/notLogin');
        $ContactInfo = $this->actionLinkPeople($_GET['user_id']); // 获取联系方式

       // var_dump($ContactInfo);
        if($ContactInfo['cellphone']==null&&$ContactInfo['phone']==null&&$ContactInfo['qq']==null&&$ContactInfo['email']==null&&$ContactInfo['wechat']==null){
           unset($ContactInfo);
        }
        $BasicInfo = UserDetail::model()->getList($user_id);// 获取基本信息    
        $live=$BasicInfo->current_live;

        if($live=='0')
        {
          $live=null;
        }
        //var_dump($live);
        $ResumeInfo = Resume::model()->with('degree')->getList($user_id);// 获取简历信息
        // 获取教育经历
        $StudyExperienceInfo=StudyExperience::model()->with('position_degree')->
            getList($user_id);
        /*******************获取当前身份****************************/
        $study="select school_name as name,major_name as value from {{study_experience}}
        where sign=1 and user_id='".$user_id."'";
        $identify=StudyExperience::model()->findAllBySql($study);
        if(empty($identify))
        {
            $work="select company_name as name,position_name as value from {{work_experience}}
            where sign=1 and user_id='".$user_id."'";
            $identify =WorkExperience::model()->findAllBySql($work);
            if(empty($identify))
            {
                $identify="";
            }
        }
        if ($ResumeInfo) { //判断该用户是否有简历内容
            $resume_id = $ResumeInfo->id;
            // 获取实习经历信息
            $WorkExperienceInfo = WorkExperience::model()->getList($resume_id);
        }else{
            $resume_id = null;
            $WorkExperienceInfo = "";
        }
        /************************获取最大学历**********************/
        $sql="select max(position_degree_id) as num from {{study_experience}} where user_id='".$user_id."'";
        $result=StudyExperience::model()->findBySql($sql);
        $name=Degree::model()->findByAttributes(array('id'=>$result->num))->name;
        /***********************获取专长*************************/
        $sql = "select  special_performance from {{user_special_performance}} 
        where user_id ='".$user_id."'";
        $special =UserSpecialPerformance::model()->findAllBySql($sql);   
        $current="current";             
        $this->smarty->assign('color',$current);
        $this->smarty->assign('live',$live);
        $this->smarty->assign('identify',$identify);
        $this->smarty->assign('name',$name);//学历
        $this->smarty->assign('special',$special);
        $this->smarty->assign('user_id',$user_id);
        $this->smarty->assign('resume_id',$resume_id);
        $this->smarty->assign('BasicInfo', $BasicInfo);
        $this->smarty->assign('ResumeInfo',$ResumeInfo);
        $this->smarty->assign('ContactInfo',$ContactInfo);
        $this->smarty->assign('WorkExperienceInfo',$WorkExperienceInfo);
        $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);
        $this->smarty->assign('isActivated',User::model()->findByPk($user_id)->is_activated);
        $this->smarty->assign('isLeague',User::model()->findByPk($user_id)->is_league);
        $this->actionSelectIndex($_GET['user_id'],yii::app()->session['user_id']);
       
    }
     public function actionChinese($data){         
          switch($data){
                case 0: return 仅自己可见;
                break;
                case 1: return 仅校友可见;
                break;
                case 2: return 全部人可见;
                break;                
           }
     }
     public function actionChangeVar($var){
          switch($var){
                case 0: return "self";
                break;
                case 1: return "schoolmate";
                break;
                case 2: return "all";
                break;                
           }
     }
     public function actionLinkPeople($data){
      if($data){
          $ContactInfo = $this->actionSeeUserId($data);
      }else{
          $ContactInfo = User::model()->getList(Yii::app()->session['user_id']); // 获取联系方式
      }
          return $ContactInfo;
     }
     public function actionTestData(){
       $user_id=237;
       Yii::app()->session['user_id']=282;       
       $res = $this->actionSeeUserId($user_id);
       echo Yii::app()->session['user_id'];
       var_dump($res);
    }
    public function actionChangeType($user_id,$type,$data){
        switch($type){
                case 0: return;
                break;
                case 1: return $this->actionLast($user_id,$data);
                break;
                case 2: return $data;
                break;                
           }
    }
   
    public function actionSeeUserId($user_id){
         $type = User::model()->findByAttributes(array('id'=>$user_id));
         $array = explode(',',$type->is_visible);         
         $arr['cellphone'] = $this->actionChangeType($user_id,$array[0],$type->cellphone);
         $arr['phone'] = $this->actionChangeType($user_id,$array[1],$type->phone);
         $arr['qq'] = $this->actionChangeType($user_id,$array[2],$type->qq);
         $arr['wechat'] = $this->actionChangeType($user_id,$array[3],$type->wechat);
         $arr['email'] = $this->actionChangeType($user_id,$array[4],$type->email);    
         return $arr;        
    }
    public function actionLookSchool($data){
       $model = StudyExperience::model()->findAllByAttributes(array('user_id'=>$data));
       return $model;
    }
    public function actionEqualData($data,$dataa){
      foreach($data as $v){
        foreach($dataa as $va){
            if($v['school_name']==$va['school_name']){
                return 1;
                exit;
            }
        }
      }
    }
    public function actionLast($user_id,$data){
        $otherSchool = $this->actionLookSchool($user_id);
        $selfSchool = $this->actionLookSchool(Yii::app()->session['user_id']);
        $result = $this->actionEqualData($selfSchool,$otherSchool);
        if($result){
           return $data;
        }else{
          return;
        }
    }
    public function actionChangData($data){
        return explode(",",$data);
    }
    
    public function actionViewIndex()
    {

        $user_id=$this->actionEditUserId($_GET['user_id'],yii::app()->session['user_id']);

            $ContactInfo = $this->actionLinkPeople($_GET['user_id']); // 获取联系方式
            if ($ContactInfo['cellphone'] == null && $ContactInfo['phone'] == null && $ContactInfo['qq'] == null && $ContactInfo['email'] == null && $ContactInfo['wechat'] == null) {
                unset($ContactInfo);
            }
            $BasicInfo = UserDetail::model()->getList($user_id);// 获取基本信息
            $live = $BasicInfo->current_live;
            if ($live == 0) {
                $live = null;
            }
            $ResumeInfo = Resume::model()->with('degree')->getList($user_id);// 获取简历信息
            // 获取教育经历
            $StudyExperienceInfo = StudyExperience::model()->with('position_degree')->
            getList($user_id);
            /*******************获取当前身份****************************/
            $study = "select school_name as name,major_name as value from {{study_experience}}
        where sign=1 and user_id='" . $user_id . "'";
            $identify = StudyExperience::model()->findAllBySql($study);
            if (empty($identify)) {
                $work = "select company_name as name,position_name as value from {{work_experience}}
            where sign=1 and user_id='" . $user_id . "'";
                $identify = WorkExperience::model()->findAllBySql($work);
                if (empty($identify)) {
                    $identify = "";
                }
            }
            if ($ResumeInfo) { //判断该用户是否有简历内容
                $resume_id = $ResumeInfo->id;
                // 获取实习经历信息
                $WorkExperienceInfo = WorkExperience::model()->getList($resume_id);
            } else {
                $resume_id = null;
                $WorkExperienceInfo = "";
            }
            /************************获取最大学历**********************/
            $sql = "select max(position_degree_id) as num from {{study_experience}} where user_id='" . $user_id . "'";
            $result = StudyExperience::model()->findBySql($sql);
            $name = Degree::model()->findByAttributes(array('id' => $result->num))->name;
            /***********************获取专长*************************/
            $sql = "select  special_performance from {{user_special_performance}}
        where user_id ='" . $user_id . "'";
            $special = UserSpecialPerformance::model()->findAllBySql($sql);
            $current = "current";
            $this->smarty->assign('color', $current);
            $this->smarty->assign('live', $live);
            $this->smarty->assign('identify', $identify);
            $this->smarty->assign('name', $name);//学历
            $this->smarty->assign('special', $special);
            $this->smarty->assign('user_id', $user_id);
            $this->smarty->assign('resume_id', $resume_id);
            $this->smarty->assign('BasicInfo', $BasicInfo);
            $this->smarty->assign('ResumeInfo', $ResumeInfo);
            $this->smarty->assign('ContactInfo', $ContactInfo);
            $this->smarty->assign('WorkExperienceInfo', $WorkExperienceInfo);
            $this->smarty->assign('StudyExperienceInfo', $StudyExperienceInfo);
            $this->smarty->assign('isActivated', User::model()->findByPk($user_id)->is_activated);
            $this->smarty->assign('isLeague', User::model()->findByPk($user_id)->is_league);
            $this->actionSelectIndex($_GET['user_id'], yii::app()->session['user_id']);
    }
           
    /*****获取user_id的值，有两种情况：一是通过get方式转来的；二是在session里的值********/
    public function actionEditUserId($data,$session)
    {
       if(isset($data))
       {
           $user_id=$data;
       }else{
           $user_id=$session;
       }
       return  $user_id;
    }
   /**********************判断是否本人，执行相应的页面*****************************/
    public function actionSelectIndex($data,$session)
    {
       if(isset($data)&&$data!=$session)
       {
               $this->smarty->display('job/home/person.html');
       }else
       {
           if(Yii::app()->session['username']==null)
               Yii::app()->runController('site/notLogin');
           else
               $this->smarty->display('job/home/mine.html');
       }      
    }
    /*******************显示编辑基本信息*************************/
    public function actionBasicInfoIndex($user_id)
    {  
        header("content-type:text/html;charset=utf-8");   
        $this->actionCommen($user_id,yii::app()->session['user_id']);   
        //$user_id= yii::app()->session['user_id'];     
        $sql = "SELECT id,school_name,major_name,sign FROM {{study_experience}} WHERE user_id='".$user_id."'";
        $StudyExperience = StudyExperience::model()->findAllBySql($sql);
        $attribute=array('user_id'=>$user_id);
        $userDetail = UserDetail::model()->findByAttributes($attribute);     
        $WorkExperienceInfo = WorkExperience::model()->findAllByAttributes($attribute);        
        $resume_id = Resume::model()->findByAttributes($attribute)->id;
        $current="current";
        $workNum=$userDetail->work_experience;
        $workCount=array(0,1,2,3,4,5,6,7,8,9);
        $this->smarty->assign('workCount',$workCount);
        $this->smarty->assign('workNum',$workNum);
        $this->smarty->assign('color',$current);
        $this->smarty->assign('user_id',$user_id);
        $this->smarty->assign('UserDetail',$userDetail);
        $this->smarty->assign('StudyExperienceInfo',$StudyExperience);
        $this->smarty->assign('WorkExperienceInfo',$WorkExperienceInfo);
        $this->smarty->assign('resume_id',$resume_id);
        $this->smarty->assign('user',User::model()->findByPk($user_id));
        $this->smarty->display('job/home/edit_info.html'); 
     
    }    
   /*******************判断操作是否是当前用户*************************/
    public function actionCommen($data,$session)
    {
        header('Content-Type:text/html; charset=utf-8;');
       if(empty($data)||$data!=$session){
             echo "<script>alert('不能进行非法操作')</script>";  
             exit();
       }
    }
    /*******************提交编辑基本信息*************************/
    public function actionBasicInfoEdit($user_id){                                     
            header("content-type:text/html;charset=utf-8");  
            $this->actionCommen($user_id,yii::app()->session['user_id']); 
            //$user_id= yii::app()->session['user_id'];        
            $BasicInfo=UserDetail::model()->findByAttributes(array('user_id'=>$user_id));     
              
            $BasicInfo->realname=self::$cleanService->clean($_POST['realname']);
            $BasicInfo->gender=$_POST['gender'];
            $BasicInfo->city_id=$_POST['city_id'];             
            $BasicInfo->account_place=$this->actionChange($_POST['city_id']);
            $BasicInfo->work_experience=$_POST['work_experience'];
            $BasicInfo->files_url=$_POST['files_url'];

            //$BasicInfo->save();
            if(!empty($_POST['sign']))
            {
                    $status =$_POST['sign'];          
                    $sign= substr($status,-1);
                    $id= substr($status,0,-1);
                    WorkExperience::model()->updateAll(array('sign'=>'0'),'user_id=:uid',array(':uid'=>$user_id));
                    StudyExperience::model()->updateAll(array('sign'=>'0'),'user_id=:uid',array(':uid'=>$user_id));             
                if($sign=='1')
                {
                     $work=WorkExperience::model()->findByPk($id);
                     $work->sign=1;
                     $work->save();            
                     $this->actionEditSave($BasicInfo->save());            
                }
                else if($sign=='0')
                {             
                    $study = StudyExperience::model()->findByPk($id);
                    $study->sign=1;
                    $study->save();                              
                    $this->actionEditSave($BasicInfo->save()); 
                }
            }             
            else
            {
                 $this->actionEditSave($BasicInfo->save()); 
            }
                    
    }

    public function actionMergeType($data){
        return implode(',',$data);
    }

   
    /******************************通过市的名字找市的信息的函数**************************/
    public function actionChange($city)
    {
        $model = City::model()->findByAttributes(array('id'=>$city))->name;
        return $model; 
    }
    /*******一个公共的判断是否保存的函数，执行成功就跳到主页***********/
    public function actionEditSave($data)
    {
         if($data)
         {
             $this->redirect(array('index'));
         }else{
              echo "编辑失败";
         }
    }

    
    /*******************编辑联系方式****************************/
    public function actionContactInfoEdit($id){
          header("content-type:text/html;charset=utf-8");              
          $this->actionCommen($id,yii::app()->session['user_id']);
          $ContactInfo = $this->LoadModel($id,'User');
          if (isset($_POST['ContactInfo'])) {          
             $ContactInfo->attributes = $_POST['ContactInfo'];                   
             $ContactInfo->is_visible = $this->actionMergeType($_POST['is_visible']);                             
             $this->actionEditSave($ContactInfo->save());           
          }

          $is_visible = User::model()->findByAttributes(array('id'=>yii::app()->session['user_id']))->is_visible;
          $array = explode(',',$is_visible);
          for($i=0;$i<5;$i++){
             $chinese[]=$this->actionChinese($array[$i]);
             $dataValue[]=$this->actionChangeVar($array[$i]);
          }               
          $current="current";
          $this->smarty->assign('array',$array);
          $this->smarty->assign('chinese',$chinese);
          $this->smarty->assign('data',$dataValue);
          $this->smarty->assign('color',$current);              
          $this->smarty->assign('ContactInfo',$ContactInfo);               
          $this->smarty->display('job/home/contact_info_edit.html');        
    }

    /*******************编辑关于我************************/
    public function actionAboutMeEdit($user_id){
         header("content-type:text/html;charset=utf-8");
         $this->actionCommen($user_id,yii::app()->session['user_id']);
              $AboutMe = $this->LoadModelByUser($user_id,'UserDetail');
              $AboutMe->about_me = trim($AboutMe->about_me);
              if (isset($_POST['AboutMe'])) {
                  $AboutMe->about_me = self::$cleanService->clean(trim($_POST['AboutMe']['about_me']));
                  $this->actionEditSave($AboutMe->save()); 
              }else{
                  $cri = new CDbCriteria();
                  $cri->select = 'user_id,about_me';
                  $cri->limit = 2;
                  $cri->order = 'id DESC';
                  $list = UserDetail::model()->findAll($cri);
                  $current="current";
                  $this->smarty->assign('color',$current);
                  $this->smarty->assign('ShowMore',$list);
                  $this->smarty->assign('AboutMe',$AboutMe);
                  $this->smarty->display('job/home/about_me.html');
            }        
    }

    /************通过省的Id找第一个在特定省排在最前边的市Id的函数**************/
      public function actionChangeCity($id)
      {
          $model = City::model()->findByAttributes(array('province_id'=>$id))->id;
          return $model; 
      }

      public function actionPrintBasic()
      {
         //yii::app()->session['user_id']=129;
         $id=UserDetail::model()->findByAttributes(array('user_id'=>yii::app()->session['user_id']))->city_id;
         if($id)
         {
             $provinceId=City::model()->findByAttributes(array('id'=>$id))->province_id;
             $value=$this->actionChangeCity($provinceId);
             $json='{"code":0,"data":{"provinceId":'.$provinceId.',"cityId":'.($id-$value+1).'}}';
             print $json;
         }
         else
         {
             print '{"code":1}';
         }         
      }
      
    /*********添加专长的json，通过ajax转来专长的名字，将专长的id返回去********/
     public function actionAddSpecialPerformance($specialtyContent){      
        $user_id=yii::app()->session['user_id'];
        $sql="select id from {{user_special_performance}} 
        where user_id='".$user_id."' and  special_performance='".$specialtyContent."'";
        $SpecialType = UserSpecialPerformance::model()->findAllBySql($sql);       
        if(empty($SpecialType))
        {
            $spec = new UserSpecialPerformance();
            $spec->user_id =  $user_id;
            $spec->special_performance = self::$cleanService->clean($specialtyContent);
            $spec->save();
            $id = $spec->attributes['id'];            
        }else{         
           $id = $SpecialType['0']->id;         
        }      
        //$json='{"code":0,"data":{"specialtyId":"'.$id.',"'.'"content:"'.$spec->special_performance.'}}';
         $json='{"code":0,"data":{"specialtyId":'.$id.',"content":"'.$spec->special_performance.'"'.'}}';
        print $json;       
     }

     /********************删除我的专长json***********************/
    public function actionDeleteSpecialPerformance($specialtyId)
    {           
          $DelModel = UserSpecialPerformance::model()->deleteByPk($specialtyId);
           if($DelModel)
           {
                $json='{"code":0}';
                print $json;   
           }else
           {
                $json='{"code":1}';
                print $json; 
           }     
    }
    

    /******************省与市交互的json************************/      
      public function actionProvinceCity()
      {
          $id = $_GET['id'];
         if(isset($id))
         {
               $sql= "select id, name from  
               {{param_city}} where province_Id=".$id;
               $model = City::model()->findAllBySql($sql);
                $json='{"code":0,"data":'.CJSON::encode($model).'}';
                print $json;             
         }else{
              
              $model = Province::model()->findAll();
              $json='{"code":0,"data":'.CJSON::encode($model).'}';
              print $json;
         }           
      }


     /******************专业类别交互的json************************/      
      public function actionStudySpecialty()
      {
          $id = $_GET['id'];
         if(isset($id))
         {
              $model = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>$id));
              $json='{"code":0,"data":'.CJSON::encode($model).'}';
              print $json;            
         }else{
              
              $model = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));             
              $json='{"code":0,"data":'.CJSON::encode($model).'}';
              print $json;
         }           
      }
      
    /***********************编辑专长******************/
    public function actionSpecialEdit($user_id)
    {
        $this->actionCommen($user_id,yii::app()->session['user_id']);
        $SpecialPerformance = UserSpecialPerformance::model()->findAllByAttributes(array('user_id'=>$user_id));
        $current="current";
        $this->smarty->assign('color',$current);
        $this->smarty->assign('SpecialPerformance',$SpecialPerformance);
        $this->smarty->display('job/home/edit_specialty.html');            
    }
  
    /************************添加工作经历************************/
    public function actionWorkExperienceAdd($resume_id){  
           $user_id=Resume::model()->findByPk($resume_id)->user_id;        
           $this->actionCommen($user_id,yii::app()->session['user_id']);
                 $model = new WorkExperience();
                if (isset($_POST['WorkExperience'])) {
                     if($_POST['sign'])
                     {
                          $this->actionTestGao($user_id);
                          $model->sign=1;
                     }                  
                    $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
                    $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
                    foreach($_POST['WorkExperience'] as $k=>$val){
                       $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
                    }
                    $model->attributes = $_POST['WorkExperience'];
                   // $model->trade_id = $_POST['trade_id'];
                    //$model->position_type_id = $_POST['position_type_id'];
                    $model->resume_id = $resume_id;
                    $model->user_id =  yii::app()->session['user_id'];
                    if (isset($_POST['up_to_now'])) { //end_time 为 至今
                        $now = time();
                        $model->end_time = date('Y-m-d',$now);
                    }                  
                    $this->actionEditSave($model->save()); 
              }       
              // 所属行业关联      
              $Trade = CompanyTrade::model()->findAll();
               // 职位类别关联
              $PositionType = PositionType::model()->findAll();              
              $current="current";
              $year = $this->actionYear();
              $month = $this->actionMonth();
              $this->smarty->assign('year',$year);
              $this->smarty->assign('month',$month);
              $this->smarty->assign('color',$current);
              $this->smarty->assign('Trade',$Trade);
              $this->smarty->assign('PositionType',$PositionType);
              $this->smarty->assign('resume_id',$resume_id);
              $this->smarty->display('job/home/work_experience_add.html');           
    }

    public function actionMonth()
    {
         $arr=array("1","2","3","4","5","6","7","8","9","10","11","12");
         return $arr;
    }

    public function actionYear()
    {
         $arr=array("2019","2018","2017","2016","2015","2014","2013","2012","2011","2010","2009","2008","2007","2006","2005",
         "2004","2003","2002","2001","2000","1999","1998","1997","1996","1995","1994","1993","1992","1991","1990",
         "1989","1988","1987","1986","1985","1984","1983","1982","1981","1980");
         return $arr;
    }

    /***********************拼接时间的函数*************************/  
    public function actionChangeTime($data,$time)
    {
          $time= $_POST[$data][$time]['year'].'-'.
          $_POST[$data][$time]['month'].'-01';
          return $time;         
    }
    /***********************当前身份用到的函数*************************/ 
    public function actionTestGao($user_id)
    {          
         WorkExperience::model()->updateAll(array('sign'=>'0'),'user_id=:uid',array(':uid'=>$user_id));
         StudyExperience::model()->updateAll(array('sign'=>'0'),'user_id=:uid',array(':uid'=>$user_id));
         
    }

    /***************************删除工作经历****************/
    public function actionWorkExperienceDel($id){
        //yii::app()->session['user_id']=1;
         $resume_id=WorkExperience::model()->findByPk($id)->resume_id;  
         $user_id = Resume::model()->findByPk($resume_id)->user_id;       
         $this->actionCommen($user_id,yii::app()->session['user_id']);
         $this->DelModel($id,'WorkExperience');          
    }

    /**************************编辑工作经历****************/
    public function actionWorkExperienceEdit($id){
         $resume_id=WorkExperience::model()->findByPk($id)->resume_id;  
         $user_id = Resume::model()->findByPk($resume_id)->user_id;  
         $this->actionCommen($user_id,yii::app()->session['user_id']);
         $WorkExperience = $this->LoadModel($id,'WorkExperience');
        if (isset($_POST['WorkExperience'])) 
        {
           if($_POST['sign'])
            {
                $this->actionTestGao($user_id);
                $WorkExperience->sign=1;
            } 
            $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
            $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
            foreach($_POST['WorkExperience'] as $k=>$val){
                $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
            }
            $WorkExperience->attributes = $_POST['WorkExperience'];               
             $this->actionEditSave($WorkExperience->save()); 
        } 
          // 所属行业关联      
        $Trade = CompanyTrade::model()->findAll();
        // 职位类别关联
        $PositionType = PositionType::model()->findAll();   
        $tradeId=$WorkExperience->trade_id;   
        $positionId=$WorkExperience->position_type_id;    
        $Edit_time['start_year'] = substr($WorkExperience->start_time,0,4);
        $Edit_time['start_month'] = substr($WorkExperience->start_time,5,2);
        $Edit_time['end_year'] = substr($WorkExperience->end_time,0,4);
        $Edit_time['end_month'] = substr($WorkExperience->end_time,5,2);
        $current="current";
        $year = $this->actionYear();
        $month = $this->actionMonth();
        $this->smarty->assign('year',$year);
        $this->smarty->assign('month',$month);
        $this->smarty->assign('color',$current);
        $this->smarty->assign('tradeId',$tradeId);
        $this->smarty->assign('positionId',$positionId);
        $this->smarty->assign('Trade',$Trade);
        $this->smarty->assign('PositionType',$PositionType);
        $this->smarty->assign('Edit_time',$Edit_time);
        $this->smarty->assign('WorkExperience',$WorkExperience); 
        $this->smarty->display('job/home/work_experience_edit.html');
       
    }

    /**********************添加教育经历*********************/
    public function actionStudyExperienceAdd($user_id){
            $this->actionCommen($user_id,yii::app()->session['user_id']);
            if (isset($_POST['StudyExperience'])){
                $studyEx = new StudyExperience(); 
                if($_POST['sign'])
                {
                    $this->actionTestGao($user_id);
                    $studyEx->sign=1;
                }               
                $_POST['StudyExperience']['start_time']=$this->actionChangeTime('StudyExperience','start_time');
                $_POST['StudyExperience']['end_time']=$this->actionChangeTime('StudyExperience','end_time');
                foreach($_POST['StudyExperience'] as $k=>$val){
                    $_POST['StudyExperience'][$k] = self::$cleanService->clean($val);
                }
                $studyEx->attributes = $_POST['StudyExperience'];
                $studyEx->study_specialty_id = $_POST['study_specialty_id'];
                $studyEx->position_degree_id = $_POST['position_degree_id'];
                $studyEx->user_id = $user_id;
                $this->actionEditSave($studyEx->save()); 
            }
            $year = $this->actionYear();
            $Degree = Degree::model()->findAll();
            //$StudyParentType = $this->StudyParentType();
            $StudySpecialty = StudySpecialty::model()->findAll();
            $StudyParentType=StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));
            $current="current";
            $year = $this->actionYear();
            $month = $this->actionMonth();
            $this->smarty->assign('year',$year);
            $this->smarty->assign('month',$month);
            $this->smarty->assign('color',$current);
            $this->smarty->assign('user_id',$user_id);
            $this->smarty->assign('Degree',$Degree);
            $this->smarty->assign('studyParentType',$StudyParentType);
            $this->smarty->assign('StudySpecialty',$StudySpecialty);
            $this->smarty->assign('StudyParentType',$StudyParentType);
            $this->smarty->display('job/home/study_experience_add.html');
       
    }
   
    /*********************** 删除教育经历*********************/
    public function actionStudyExperienceDel($id){
        $user_id = StudyExperience::model()->findByPk($id)->user_id;
        $this->actionCommen($user_id,yii::app()->session['user_id']);
        $this->DelModel($id,'StudyExperience');         
    }

    /*********************编辑教育经历********************/
    public function actionStudyExperienceEdit($id){
         $user_id = StudyExperience::model()->findByPk($id)->user_id;
          $this->actionCommen($user_id,yii::app()->session['user_id']);
              $StudyExperience = $this->LoadModel($id,'StudyExperience');             
              if (isset($_POST['StudyExperience'])) {
                  if($_POST['sign'])
                  {
                     $this->actionTestGao($user_id);
                     $StudyExperience->sign=1;
                  } 
                  $_POST['StudyExperience']['start_time']=$this->actionChangeTime('StudyExperience','start_time');
                  $_POST['StudyExperience']['end_time']=$this->actionChangeTime('StudyExperience','end_time');
                  foreach($_POST['StudyExperience'] as $k=>$val){
                      $_POST['StudyExperience'][$k] = self::$cleanService->clean($val);
                  }
                  $StudyExperience->attributes = $_POST['StudyExperience'];
                  $StudyExperience->position_degree_id = $_POST['position_degree_id'];
                  $StudyExperience->study_specialty_id = $_POST['study_specialty_id'];
                  $this->actionEditSave($StudyExperience->save()); 
              } 
              $degreeId=$StudyExperience->position_degree_id;
              $childId=$StudyExperience->study_specialty_id;  
              $parentId=StudySpecialty::model()->findByAttributes(array('id'=>$childId))->parent_id;              
              $Degree = Degree::model()->findAll();
              $StudySpecialty = StudySpecialty::model()->findAll();
              $StudyParentType=StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));
              $Edit_time['start_year'] = substr($StudyExperience->start_time,0,4);
              $Edit_time['start_month'] = substr($StudyExperience->start_time,5,2);
              $Edit_time['end_year'] = substr($StudyExperience->end_time,0,4);
              $Edit_time['end_month'] = substr($StudyExperience->end_time,5,2);
              $current="current";
              $this->smarty->assign('color',$current);
              $year = $this->actionYear();
              $month = $this->actionMonth();
              $this->smarty->assign('year',$year);
              $this->smarty->assign('month',$month);
              $this->smarty->assign('parentId',$parentId);
              $this->smarty->assign('childId',$childId);
              $this->smarty->assign('Degree',$Degree);
              $this->smarty->assign('degreeId',$degreeId);
              $this->smarty->assign('StudySpecialty',$StudySpecialty);
              $this->smarty->assign('StudyParentType',$StudyParentType);
              $this->smarty->assign('StudyExperience',$StudyExperience);
              $this->smarty->assign('Edit_time',$Edit_time);
              $this->smarty->display('job/home/study_experience_edit.html');
      
    }

    
  
	/**
     * 简历展示
     */
   
	public function actionResume(){
		//yii::app()->session['user_id'] = 1;

		// 从session中获取用户id
		$user_id = yii::app()->session['user_id'];

		// 获取简历id
		$resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
        
        // 获取基本信息
		$BasicInfo = UserDetail::model()->getList($user_id);
        if ($BasicInfo->gender) {
            $BasicInfo->gender = '男';
        }else{
            $BasicInfo->gender = '女';
        }
        
        // 获取联系方式
        $Contact = User::model()->getList($user_id);

        // 获取教育经历
        $StudyExperience =StudyExperience::model()->getList($user_id);
      
        // 获取证书信息
        $Certificate = Certificate::model()->getList($resume_id);
        
        // 获取校内职务信息
        $SchoolDuty = SchoolDuty::model()->getList($resume_id);

        // 获取校内奖励信息
        $SchoolAward = SchoolAwards::model()->getList($resume_id);

        // 获取语言能力信息
        $LanguagesAbility = LanguagesAbility::model()->getList($resume_id);
        
        // 获取实习经历信息
        $WorkExperience = WorkExperience::model()->getList($user_id);

        // 获取IT技能信息
        $ItSkill = ItSkill::model()->getList($resume_id);

        // 获取项目经历信息
        $ProjectExperience = ProjectExperience::model()->getList($resume_id);

        // 获取培训经历信息
        $TrainingExperience = TrainingExperience::model()->getList($resume_id);

        // 获取求职经历信息
        $JobExperience = JobExperience::model()->getList($resume_id);
        foreach ($JobExperience as $key => $value) {
            if ($value->type) {
                $value->type = '拿到offer';
            }else{
                $value->type = '参加笔试';
            }
        }
        
        // 数据渲染至前端视图
        $this->smarty->assign('BasicInfo',$BasicInfo);
        $this->smarty->assign('ContactInfo',$Contact);
        $this->smarty->assign('StudyExperience',$StudyExperience);
        $this->smarty->assign('Certificate',$Certificate);
        $this->smarty->assign('SchoolDuty',$SchoolDuty);
        $this->smarty->assign('SchoolAward',$SchoolAward);
        $this->smarty->assign('LanguagesAbility',$LanguagesAbility);
        $this->smarty->assign('WorkExperience',$WorkExperience);
        $this->smarty->assign('ItSkill',$ItSkill);
        $this->smarty->assign('ProjectExperience',$ProjectExperience);
        $this->smarty->assign('TrainingExperience',$TrainingExperience);
        $this->smarty->assign('JobExperience',$JobExperience);
        $this->smarty->display('job/resume.html');
		
	}
    
    /**
     * 添加简历内容
     */
    public function actionResumeAdd($type){
        // 判断是否是ajax请求
        if (1) {
            // 确认添加的内容属于哪个模块
            $addType = $type;
            $this->AddModel($addType);

        } else{
            echo "request is wrong";
        }
    }

    public function actionTest(){
        $this->render('test');
    }

    /**
     * 修改简历内容
     */
    public function actionResumeEdit($id,$type){
        if (yii::app()->request->isAjaxRequest) {
            $editTpye = $type;
            if ($editTpye =='BasicInfo') {
                $model = UserDetail::model()->findByPk($id);
                    $model->attributes = $_POST['BasicInfo'];
                    if ($model->save()) {
                        echo 1;
                    }else{
                        echo 0;
                    }
            }else if($editTpye == 'Contact') {
                $model = User::model()->findByPk($id);
                    $model->attributes = $_POST['User'];
                    if ($model->save()) {
                        echo 1;
                    }else{
                        echo 0;
                    }
            }else{
                $this->EditModel($id,$editTpye);
            }
            
        }else{
            echo "request is wrong";
        }
    }

    /**
     * 已关注单位
     */
    public function actionComFocus($page){
        // $user_id = yii::app()->session['user_id'];
        $user_id = 1;
        $count = CompanyUser::model()->countByAttributes(array('user_id'=>$user_id));
        if ($count) {
            $company_id = CompanyUser::model()->pages($page,$user_id);
            $companyModel = Company::model();
            $cri = new CDbCriteria();
            $cri->addinCondition('id',$company_id);
            $company = $companyModel->findAll($cri);
           

            $companyjson = '{"list":'.CJSON::encode($company).',"dataCount":'.$count.'}';
        }else{

            $companyjson = '{"dataCount":'.$count.'}';
        }
        print_r($companyjson);
    }

    /**
     * 添加关注单位
     */
    public function actionAddComFocus($id){
        if (1) {
            $user_id = yii::app()->session['user_id'];
            $user_id = 1;
            $model = new CompanyUser();
            $model->company_id = $id;
            $model->user_id = $user_id;
            if ($model->save()) {
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    /**
     * 取消关注单位
     */
    public function actionCancelComFocus($id){
        if (1) {
            $model = CompanyUser::model();
            if ($model->deleteByPk($id)) {
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    /**
     * 已关注职位
     */
    public function actionToPosFocus()
    {
           $this->smarty->display('job/collect_position/collect_position.html');
    }
    public function actionPosFocus(){
          $page=5;
          // $user_id = yii::app()->session['user_id'];
          $user_id = 1;
          $count = PositionUser::model()->countByAttributes(array('user_id'=>$user_id));
          if ($count) {
              $position_id = PositionUser::model()->pages($page,$user_id);
              $positionModel = Position::model();
              $cri = new CDbCriteria();
              $cri->addinCondition('id',$position_id);
              $position = $positionModel->findAll($cri);
              $positionjson = '{"list":'.CJSON::encode($position).',"dataCount":'.$count.'}';
          }else{

               //$positionjson = '{"dataCount":'.$count.'}';
               $positionjson = '{"code":"0","data":""}';
          }
          print_r($positionjson);
    }

    
    
    /**
     * 添加关注职位
     */
    public function actionAddPosFocus($id){
        if (yii::app()->request->isAjaxRequest) {
            $user_id = yii::app()->session['user_id'];
            $user_id = 1;
            $model = new PositionUser();
            $model->position_id = $id;
            $model->user_id = $user_id;
            if ($model->save()) {
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    /**
     * 取消关注职位
     */
    public function actionCancelPosFocus($id){
        if (yii::app()->request->isAjaxRequest) {
            $model = PositionUser::model();
            if ($model->deleteByPk($id)) {
                echo 1;
            }else{
                echo 0;
            }
        }
    }


     /**
       * 求职信信息
       */
      public function actionLetterIndex()
      {
            $user_id = yii::app()->session['user_id'];
            $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
            $Letter = ApplicationLetter::model()->findAllByAttributes(array('resume_id'=>$resume_id));
            $current="current";
            $this->smarty->assign('letter',$current);
            $this->smarty->assign('app_letter',$Letter);
            $this->smarty->display('job/application_letter/add_application.html');
      }

    /**
     * 添加职信信息
     */
    public function actionAddLetter(){
          $user_id = yii::app()->session['user_id'];
          if($_POST)
          {       
              $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
              $model = new ApplicationLetter();
              $model->title = self::$cleanService->clean($_POST['title']);
              $model->content = self::$cleanService->clean($_POST['content']);
              $model->resume_id = $resume_id;
              if ($model->save()) {
                  $this->redirect(array('letterindex'));
              } 
         }
          $current="current";
            $this->smarty->assign('letter',$current);
           $this->smarty->display('job/application_letter/add_other.html');
    }


    /**
     * 去修改求职信信息
     */
    public function actionToLetterEdit($id){
             $current="current";
             $this->smarty->assign('letter',$current);
             $model = $this->LoadModel($id,'ApplicationLetter');       
             $this->smarty->assign('model',$model);
             $this->smarty->display('job/application_letter/edit_letter.html');       
    }
    

     /**
     * 提交修改求职信信息
     */
    public function actionLetterEdit($id)
    {
           // $user_id = 1;
            $user_id = yii::app()->session['user_id'];
            $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
            $model = $this->LoadModel($id,'ApplicationLetter');           
            $model->title = self::$cleanService->clean($_POST['title']);
            $model->content = self::$cleanService->clean($_POST['content']);
            $model->resume_id = $resume_id;
            if ($model->save()) {
                  $this->redirect(array('letterindex'));
            }else{
                echo "编辑失败";
            }
         
    }

    /**
     * 删除求职信信息
     */
    public function actionLetterDelet($id){
      
          $model = ApplicationLetter::model();
          if ($model->deleteByPk($id)) {
               $this->redirect(array('letterindex'));
          }else{
              echo "删除失败";
          }
    }

    // 省份,城市联动
    public function actionCity(){
        $province_id = $_POST['province_id'];
        // $province_id = 7;
        $model = City::model();
        $list = $model->findAllByAttributes(array('province_id'=>$province_id));
        $list = CJSON::encode($list);
        print_r($list);
    }
    
    public function Trade(){
        // 所属行业关联
        $TradeModel = CompanyTrade::model()->findAll();
        $Trade[] = '请选择';
        foreach ($TradeModel as $key => $value) {
            $Trade[$value->id] = $value->name;
        }
        return $Trade;
    }

    public function Province(){
        // 行省
        $ProvinceModel = Province::model()->findAll();
        $Province[] = '请选择';
        foreach ($ProvinceModel as $key => $value) {
            $Province[$value->id] = $value->name;
        }
        return $Province;
    }

    public function PositionType(){
        // 职位类别关联
        $PositionTypeModel = PositionType::model()->findAll();
        $PositionType[] = '请选择';
        foreach ($PositionTypeModel as $key => $value) {
            $PositionType[$value->id] = $value->name;
        }
        return $PositionType;
    }

    public function StudySpecialty(){
        // 学位类别关联
        $StudySpecialtyModel = StudySpecialty::model()->findAll();
        $StudySpecialty[] = '请选择';
        foreach ($StudySpecialtyModel as $key => $value) {
            $StudySpecialty[$value->id] = $value->name;
        }
        return $StudySpecialty;
    }

    public function StudyParentType(){
        // 学位父类
        $StudyParentTypeModel = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));
        $StudyParentType[] = '请选择';
        foreach ($StudyParentTypeModel as $key => $value) {
            $StudyParentType[$value->id] = $value->name;
        }
        return $StudyParentType;
    }

    public function Degree(){
        // 学历关联
        $DegreeModel = Degree::model()->findAll();
        foreach ($DegreeModel as $key => $value) {
            $Degree[$value->id] = $value->name;
        }
        return $Degree;
    }

    public function LoadModel($id,$model){
        $model = $model::model()->findByPk($id);
        return $model;
    }

    public function LoadModelByUser($user_id,$model){
        $model = $model::model()->findByAttributes(array('user_id'=>$user_id));
        return $model;
    }

    public function EditModel($id,$type){
        $model = $type::model()->findByPk($id);
        $model->attributes = $_POST[$type];
        if ($model->save()) {
            echo 1;
        }else{
            echo 0;
        }
    }

    public function DelModel($id,$model){
        $DelModel = $model::model();
        if ($DelModel->deleteByPk($id)) {
            $this->redirect(array('index'));
        }
    }

    public function AddModel($type){
        $model = new $type();
        $model->attributes = $_POST;
        if ($model->save()) {
            echo 1;
        }else{
            echo 0;
        }
    }
    public function filters() {
       return array(
           array('application.controllers.filters.SessionCheckFilter - viewIndex')
       );
    }
}
?>