 <?php
             /*********这是编辑job/my_resume/edit-resume.html的控制器*****************************/

     Class ResumeController extends Controller
     {
        /******************************简历首页*********************************************/
        public function actionIndex()
        {

             $user_id=yii::app()->session['user_id'];
             $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
             Yii::app()->session['resume_id']=$resume_id;  
             $basicInfo = UserDetail::model()->getList($user_id);// 获取基本信息                            
             $arr = array("刚刚工作","一","二","三","四","五","六","七","八","在校生");  //用来表示工作经验的年数
             if($basicInfo->work_experience<1){  //当满足这个条件时，说明该用户还没有工作经历
                 $workEx='';
             } 
             else
             {
                $workEx = $arr[$basicInfo->work_experience-1]; 
             }               
             $user = User::model()->findByAttributes(array('id'=>$user_id)); // 获取联系方式             
             $ResumeInfo = Resume::model()->with('degree')->getList($user_id);// 获取简历信息                       
             $sql = "select t_languages_ability.id,type_id,les_say,point,rea_wri,grade,t_languages_type.name as name from {{languages_ability}},{{languages_type}}
             where (t_languages_ability.type_id= t_languages_type.id) and resume_id=".$resume_id;
             $lang = LanguagesAbility::model()->findAllBySql($sql);               
             foreach($lang as $key => $value) {  
                 $value->les_say=$this->actionChangeLangueges($value->les_say);  //将数据等级转化为中文等级，能更清晰的表示语言的能力
                 $value->rea_wri=$this->actionChangeLangueges($value->rea_wri);//将数据等级转化为中文等级，能更清晰的表示语言的能力                
            }
             $sql ="select id,name from {{certificate}} where resume_id=".$resume_id;                    
             //证书的信息
             $model = Certificate::model()->findAllBySql($sql);             
             // 获取IT技能信息
             $ItSkill = ItSkill::model()->findAllByAttributes(array('resume_id'=>$resume_id));            
              //校内职务的信息
             $schoolDuty = SchoolDuty::model()->findAllByAttributes(array('resume_id'=>$resume_id));             
            //获取校内奖励
             $schoolAward = SchoolAwards::model()->findAllByAttributes(array('resume_id'=>$resume_id));          
             // 获取实习经历信息
             $train = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>1));//getList($user_id);               
             // 获取项目经历信息
             $ProjectExperience = ProjectExperience::model()->getList($resume_id);             
                          
             // 获取培训经历信息
             $TrainingExperience = TrainingExperience::model()->getList($resume_id); 


              
             // 获取工作经历信息
             $WorkExperience = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>0));
             // 获取求职经历信息
             $JobExperience = JobExperience::model()->getList($user_id);
              foreach($JobExperience as $key=>$value){
                $value->type=$this->actionChangeJobGrade($value->type);                
             }
             // 获取教育经历
             $StudyExperienceInfo =StudyExperience::model()->with('position_degree')->getList($user_id); 





             $ItDetail = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>$resume_id));
             //获取附件信息
             $file=ResumeAttachment::model()->findAllByAttributes(array('resume_id'=>$resume_id));      
             $school_name = StudyExperience::model()->findAllBySql("select school_name from {{study_experience}} 
                where user_id=".Yii::app()->session['user_id']);       
             //if(empty(Yii::app()->session['array']))
             //{
                $editArray = $this->actionDataArray($basicInfo,"","0",$editArray);/***********/
                $editArray = $this->actionDataArray($user->email,$user->phone,"1",$editArray);/*********/
                $editArray = $this->actionDataArray($StudyExperienceInfo,"","2",$editArray);/*********/ 
                $editArray = $this->actionDataArray($model,"","3",$editArray);/*********/
                $editArray = $this->actionDataArray($schoolDuty,"","4",$editArray);/*********/
                $editArray = $this->actionDataArray($schoolAward,"","5",$editArray);/*********/
                $editArray = $this->actionDataArray($lang,"","6",$editArray);/*********/
                $editArray = $this->actionDataArray($train,"","7",$editArray);/*********/  
                $editArray = $this->actionDataArray($ItSkill,"","8",$editArray);/*********/
                $editArray = $this->actionDataArray($ProjectExperience,"","9",$editArray);/*********/ 
                $editArray = $this->actionDataArray($WorkExperience,"","10",$editArray);/*********/ 
                $editArray = $this->actionDataArray($TrainingExperience,"","11",$editArray);/*********/ 
                $editArray = $this->actionDataArray($JobExperience,"","12",$editArray);/*********/  
                $editArray = $this->actionDataArray($file,"","13",$editArray);/*********/ 
                Yii::app()->session['array']=implode(",",$editArray);
            // }              
             $current="current";
             $time = date('y-m-d h:i:s',time());  
             $Year = date('Y',strtotime($time));
             $Month = date('M',strtotime($time));
             $var = $this->actionChangeYear($Year);            
             $trade = CompanyTrade::model()->findAll();
             $positionType = PositionType::model()->findAll();
             $province = Province::model()->findAll();
             $zhuye = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));
             $juzhu = $this->actionLookProvinceToName($basicInfo->current_live);
             $hukou = $this->actionLookProvinceToName($basicInfo->account_place);            
             $timeDay = $this->actionDayId($Year,$Month);  
             $this->smarty->assign('zhuye',$zhuye);          
             $this->smarty->assign('day',$timeDay);
             $this->smarty->assign('pro',$province);
             $this->smarty->assign('hukou',$hukou);
             $this->smarty->assign('juzhu',$juzhu);
             $this->smarty->assign('type',$positionType);
             $this->smarty->assign('trade',$trade);
             $this->smarty->assign('schooldd',$school_name);
             $this->smarty->assign('time',$var);
             $this->smarty->assign('resume',$current);
             $this->smarty->assign('file',$file); //附件信息
             $this->smarty->assign('ItDetail',$ItDetail);//it的详细信息
             $this->smarty->assign('ItSkill',$ItSkill);  //it技能的信息
             $this->smarty->assign('WorkExperience',$WorkExperience);  //工作经历的信息
             $this->smarty->assign('train',$train);           //实习经历的信息   
             $this->smarty->assign('ProjectExperience',$ProjectExperience);     //项目经验的信息      
             $this->smarty->assign('TrainingExperience',$TrainingExperience);  //培训经历的信息 
             $this->smarty->assign('JobExperience',$JobExperience);      //求职经历的信息
             $this->smarty->assign('languages',$lang);     //语言能力的信息
             $this->smarty->assign('award',$schoolAward); //校内奖励的信息
             $this->smarty->assign('school',$schoolDuty); //校内职务的信息
             $this->smarty->assign('model',$model);  //证书的信息
             $this->smarty->assign('ResumeInfo',$ResumeInfo);  // 获取简历信息
             $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);  //教育经历的信息
             $this->smarty->assign('contact',$user);       //手机与邮箱的信息
             $this->smarty->assign('workEx',$workEx);  //工作经验年数的信息
             $this->smarty->assign('basicInfo', $basicInfo);  //用户的基本信息信息             
             $this->smarty->display('job/my_resume/edit-resume.html');
        }


         public function actionDayId($year,$month)
         {
            
            if($month=='4'||$month=='6'||$month=='9'||$month=='11')//4,6,9,11月有30天
            {               
                 $array = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30];                
            }
            else if($month=='2') //当时间为二月时，要判断平年与闰年
            {
                if($year%400==0||$year%4==0&&$year%100!=0)
                {
                   $array=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29]; 
                }
                else
                {
                    $array=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,28]; 
                    
                }
            }
            else                     //这是一个月有31天的情况1,3,5,7,8,10,12                          
            {
                 $array=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,31]; 
                  
            }
                return $array;
        }


        public function actionLookProvinceToId($data){
            $province_id = City::model()->findByAttributes(array('id'=>$data))->province_id;
            $cityName = City::model()->findAllByAttributes(array('province_id'=>$province_id));
            return $cityName;
        }

        public function actionLookProvinceToName($data){
            $province_id = City::model()->findByAttributes(array('name'=>$data))->province_id;;
            $cityName = City::model()->findAllByAttributes(array('province_id'=>$province_id));
            return $cityName;
        }

       
        public function actionChangeYear($data){
            $data=$data+5;
            for($i = 1980;$i<=$data;$i++){
                $arr[]=$i;
            }
            return $arr;
        }
                      
        /**********************判断一个数是否存在，然后返回特定的数组***********************/
        public function actionDataArray($dataOne,$dataTwo,$num,$array)
        {
            if($dataOne||$dataTwo)
            {
                 $array[]=$num;
            }         
            return $array;
        }
        /*************************返回总体数组的json格式**********************************/
        public function actionFinishTime()
        {
            print '{"code":0,"data":['.Yii::app()->session['array'].']}'; 
        }
                 
        /*****在语言能力那里的一个听写等级的转换函数************************************/
        public function actionChangeLangueges($var)
        {
            if($var=='1')
            {
               $var='一般';
            }else if($var=='2'){
               $var='良好';
            }else if($var=='3'){
                $var='精通';
            }
            return $var;
        }
        /*****在求职经历那里的一个求职等级的转换函数**********************************/
        public function actionChangeJobGrade($data)
        {
             if ($data=='0') {
                    $data= '参加笔试';
                }else if($data=='1'){
                    $data= '参加面试 ';
                }else if($data=='2'){
                    $data= '拿到Offer';
                }
                return $data;
        }
              
         /******************去掉日期月与日（小于10时）前零的函数***********************/
        public function actionData($data)
        {             
            $id= substr($data,0,1);             
            if($id==0)
            {
                $da=substr($data,1,1);
            }else{
                $da = $data;
            }
            return $da; 

        }

        /***********************************基本信息的json*********************************/
     	public function actionBasicInfo()
     	{
     		$user_id =Yii::app()->session['user_id'];
            //$user_id = 1;
     		$user = UserDetail::model()->findByAttributes(array('user_id'=>$user_id));
            if($user->work_experience==null)
                $user->work_experience=0;
     		$year = date('Y',strtotime($user->birthday));
     		$mon = date('m',strtotime($user->birthday));
     		$d= date('d',strtotime($user->birthday));
            $month=$this->actionData($mon);
            $day=$this->actionData($d);   
            $current_live = $this->actionChange($user->current_live);
            $account_place = $this->actionChange($user->account_place);  
            $current=$this->actionChangeCity($current_live->province_id);
            $account=$this->actionChangeCity($account_place->province_id);  
            if(empty($current_live->province_id)&&empty($account_place->province_id)&&empty($user->birthday))
            {
                $json='{"code":"0","data":{"birthYear":2015,"birthMonth":12,
               "birthDay":30,"name":"'.$user->realname.'",
               "hukouCity":0,"liveCity":0,
               "hukouProvince":0, "liveProvince":0,
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }else if(empty($account_place->province_id)&&isset($user->birthday)&&isset($current_live->province_id))
            {
                $json='{"code":"0","data":{"birthYear":'.$year.',"birthMonth":'.$month.',
               "birthDay":'.$day.',"name":"'.$user->realname.'",
               "hukouCity":0,"liveCity":'.($current_live->id-$current+1).',
               "hukouProvince":0, "liveProvince":'.($current_live->province_id).',
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }else if(empty($current_live->province_id)&&isset($user->birthday)&&isset($account_place->province_id))
            {
                $json='{"code":"0","data":{"birthYear":'.$year.',"birthMonth":'.$month.',
               "birthDay":'.$day.',"name":"'.$user->realname.'",
               "hukouCity":'.($account_place->id-$account+1).',"liveCity":0,
               "hukouProvince":'.($account_place->province_id).', "liveProvince":0,
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }
            else if(empty($current_live->province_id)&&empty($account_place->province_id)&&isset($user->birthday))
            {
               $json='{"code":"0","data":{"birthYear":'.$year.',"birthMonth":'.$month.',
               "birthDay":'.$day.',"name":"'.$user->realname.'",
               "hukouCity":0,"liveCity":0,
               "hukouProvince":0, "liveProvince":0,
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }   
            else if(empty($account_place->province_id)&&empty($user->birthday)&&isset($current_live->province_id))
            {
                $json='{"code":"0","data":{"birthYear":2015,"birthMonth":12,
               "birthDay":30,"name":"'.$user->realname.'",
               "hukouCity":0,"liveCity":'.($current_live->id-$current+1).',
               "hukouProvince":0, "liveProvince":'.($current_live->province_id).',
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }
            else if(isset($account_place->province_id)&&empty($user->birthday)&empty($current_live->province_id))
            {
                $json='{"code":"0","data":{"birthYear":'.$year.',"birthMonth":'.$month.',
               "birthDay":'.$day.',"name":"'.$user->realname.'",
               "hukouCity":'.($account_place->id-$account+1).',"liveCity":0,
               "hukouProvince":'.($account_place->province_id).', "liveProvince":0,
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }
           else{
                 $json='{"code":"0","data":{"birthYear":'.$year.',"birthMonth":'.$month.',
               "birthDay":'.$day.',"name":"'.$user->realname.'",
               "hukouCity":'.($account_place->id-$account+1).',"liveCity":'.($current_live->id-$current+1).',
               "hukouProvince":'.($account_place->province_id).', "liveProvince":'.($current_live->province_id).',
                "workExperience":'.$user->work_experience.',
                "presentLoc":"'.$user->current_live.'"}}';
            }
             print $json;      		 
     	}
        /******************************通过市的名字找市的信息的函数**************************/
        public function actionChange($city)
        {
            $model = City::model()->findByAttributes(array('name'=>$city));
            return $model; 
        }
 
        /*************************通过省的Id找第一个在特定省排在最前边的市Id的函数********************/
        public function actionChangeCity($id)
        {
            $model = City::model()->findByAttributes(array('province_id'=>$id))->id;
            return $model; 
        }



        /*****************************年的json函数,可以在这里增加年数****************************/
     	public function actionYear()
     	{
     	     $json='{"code":"0","data":[2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,
             2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990,
             1989,1988,1987,1986,1985,1984,1983,1982,1981,1980]}';
             print $json; 
     	}
        
        /************************************StudySpecialty的json函数*****************************/
     	public function actionMajor()
     	{
     		$major = StudySpecialty::model()->findAll();

     		print '{"code":0,"data":'.CJSON::encode($major).'}';	

     	}

         /**********************************通过年与月获取日的json函数*******************************/
     	public function actionDay()
     	{
     		$year = $_GET['year'];
            $month = $_GET['month'];
            if($month=='4'||$month=='6'||$month=='9'||$month=='11')//4,6,9,11月有30天
            {               
                 $json='{"code":"0","data":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,
                 15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]}';
                 print $json; 
            }
            else if($month=='2') //当时间为二月时，要判断平年与闰年
            {
                if($year%400==0||$year%4==0&&$year%100!=0)
                {
                    $json='{"code":"0","data":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,
                    15,16,17,18,19,20,21,22,23,24,25,26,27,28,29]}';
                    print $json; 
                }
                else
                {
                    $json='{"code":"0","data":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,
                    15,16,17,18,19,20,21,22,23,24,25,26,27,28]}';
                    print $json; 
                }
            }
            else                     //这是一个月有31天的情况1,3,5,7,8,10,12                          
            {
                 $json='{"code":"0","data":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,
                 15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31]}';
                 print $json; 
            }
     	}

        /*********************************职位类型的json函数******************************/
        public function actionPosition()
        {
            $position = PositionType::model()->findAll();
            $json='{"code":0,"data":'.CJSON::encode($position).'}';
            print $json;
        }

         /********************************电话与邮箱的json函数******************************/
        public function actionPhoneEmail()
        {
            $user_id = yii::app()->session['user_id'];
            $sql = "select email,cellphone from {{user}} where id=".$user_id;
            $user = User::model()->findBySql($sql);
            $json='{"code":0,"data":{"phone":"'.$user->cellphone.'","email":"'.$user->email.'"}}';
            print $json;
        }
        
         /******************************StudySpecialty类型的json函数**********************/
        public function actionStudySpecialty()
        {
            $specialty = StudySpecialty::model()->findAll();
            $json='{"code":0,"data":'.CJSON::encode($specialty).'}';
            print $json;
        }
         /********************************行业的json函数***********************************/
        public function actionCompanyTrade()
        {
            $specialty = CompanyTrade::model()->findAll();
            $json='{"code":0,"data":'.CJSON::encode($specialty).'}';
            print $json;
        }
         /*****语言的json函数，如何ajax请求id存在，则返回该id的语言类型的名字，否则返回所有的语言类型****/
        public function actionLanguage()
        {    

             if(isset($_GET['Lid']))
             {
                $name=LanguagesType::model()->findByPk($_GET['Lid'])->name;
                print '{"code":0,"data":"'.$name.'"}';
             }else{
                $lan = LanguagesType::model()->findAll();
                $json='{"code":0,"data":'.CJSON::encode($lan).'}';
                print $json;
             }
             
        }

        /*****语言能力的json函数，它与删除有关，有多个等级与分数，如果删除特定的等级分数，就要从数据库里删掉它。
        如果数据库的特定行的等级等为零时，返回'{"code":"0"}'
        ****/
        public function actionDealLanguageShan()
        {            
            $locate=$_GET['examId'];
            $id=$_GET['currentEditId'];
            $model=LanguagesAbility::model()->findByAttributes(array('id'=>$id)); 
            $arr=$this->actionCountArray($model->grade);             
            $model->grade=$this->actionArrray($model->grade,$locate);            
            $model->point=$this->actionArrray($model->point,$locate);   
            $model->grade_id=$this->actionArrray($model->grade_id,$locate); 
                         
            if($model->save())
            {                
                if($arr==1){
                      print '{"code":"1"}';
                }
                else{
                     print '{"code":"0"}';
                 }                   
            }

        }

        /*****将转来的数据通过逗号分成数组，然后通过特定的位置去掉数组中的特定元素，再组成一个通过逗号连接的字符串****/
        public function actionArrray($data,$locate)
        {
            $arr=explode(",",$data);
            array_splice($arr,$locate,1);
            return implode(',',$arr);
        }
        /*****将变量分为数组，然后返回它的个数****/
        public function actionCountArray($data)
        {
            $arr=explode(",",$data);
            return count($arr);
        }
         /*****返回特定用户证书的json****/
        public function actionCertificate()
        {
            $user_id = Yii::app()->session['user_id'];
            $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;   
            $sql ="select id,name from {{certificate}} where resume_id=".$resume_id;       
            $model = Certificate::model()->findAllBySql($sql);
            $json='{"code":0,"data":'. CJavaScript::jsonEncode($model).'}';
            print $json;           
        }
        
        /*****返回所有专业类型的json****/
        public function actionCompanyTradess()
        {
            $model = StudySpecialty::model()->findAll();
            $json='{"code":0,"data":'.CJSON::encode($model).'}';
            print $json;
        }

         /****通过在教育经历里查出特定用户的毕业学校，然后返回特定用户的所有就读学校的json函数****/
        public function actionSchool()
        {
            $user_id = yii::app()->session['user_id'];             
            $model = StudyExperience::model()->findAllBySql("select school_name as school from {{study_experience}} 
                where user_id=".$user_id);
            $json='{"code":0,"data":'.json_encode($model).'}';
            print $json;
        }
        /*****可先判断是否ajax请求中是否存在省ID,如有，返回该省的所有市，如果没有将所有省都返回的json函数****/
        public function actionProvinceCity()
        {
            $id = $_GET['id'];
           if(isset($id))
           {
                 $model = City::model()->findAllByAttributes(array('province_id'=>$id));
                 $json='{"code":0,"data":'.CJSON::encode($model).'}';
                 print $json;               
           }else{
                
                $model = Province::model()->findAll();
                $json='{"code":0,"data":'.CJSON::encode($model).'}';
                print $json;
           }           
        }

         
        /**************************联系方式表单的提交*****************************/
        public function actionContract()
        {
            $user_id=yii::app()->session['user_id'];
            $user = User::model()->findByAttributes(array('id'=>$user_id));
            $user->cellphone=$_POST['phone'];
            $user->email=$_POST['email'];
            if($user->save())
            {
                 $this->redirect(array('index'));
             }else{
                echo "编辑失败";
             }
        }
        /*************************拼接求职经历json函数***************************/
        public function actionJob($id)
        {
              $model = JobExperience::model()->findByAttributes(array('id'=>$id));             
              $year = date('Y',strtotime($model->time));
              $mon = date('m',strtotime($model->time));              
              $month=$this->actionData($mon);
              $json='{"code":"0","data":{"applyYear":'.$year.',"applyMonth":'.$month.',
              "applyCompanyName":"'.$model->company_name.'","applyPosiName":"'.$model->position_name.'",
              "applyType":'.$model->type.'}}';
              print $json;                 
        }
        /*****删除求职经历的json函数****/
        public function actionDeleteJob($id)
        {
           if(JobExperience::model()->deleteByPk($id))
            {
                  print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            } 
        }
         /*****培训经历的json函数，通过ajax请求中带有的特定id，查出特定的求职经历json****/
        public function actionTrainingExperience($id)
        {
              // 获取培训经历信息
              $train= TrainingExperience::model()->findByAttributes(array('id'=>$id));              
              $startYear = date('Y',strtotime($train->start_time));
              $start_mon = date('m',strtotime($train->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($train->end_time));
              $end_mon = date('m',strtotime($train->end_time));              
              $endMonth=$this->actionData($end_mon);
              $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
              "endYear":'.$endYear.',"endMonth":'.$endMonth.',
              "trainName":"'.$train->training_name.'","trainContent":"'.$train->training_content.'",
              "trainAgency":"'.$train->training_organization.'"}}';
              print $json;  
        }
         /*****删除培训经历的json函数，通过ajax请求中带有的特定id，删除特定培训经历的json****/
        public function actionDeleteTrainingExperience($id)
        {
            if(TrainingExperience::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }

        /*****项目经历的json函数，通过ajax请求中带有的特定id，查出特定项目经历的json****/
        public function actionProjectExperience($id)
        {
             // 获取项目经历信息
              $project = ProjectExperience::model()->findByAttributes(array('id'=>$id)); 
              $startYear = date('Y',strtotime($project->start_time));
              $start_mon = date('m',strtotime($project->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($project->end_time));
              $end_mon = date('m',strtotime($project->end_time));              
              $endMonth=$this->actionData($end_mon);
              $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
              "endYear":'.$endYear.',"endMonth":'.$endMonth.',
              "projectName":"'.$project->project_name.'","teamSize":'.$project->team_size.',
              "projectSum":"'.$project->project_profile.'",
              "projectRole":"'.$project->project_role.'","projectAchievement":"'.$project->project_results.'"}}';
              print $json;              
        }


        /*****删除项目经验的json函数，通过ajax请求中带有的特定id，删除特定项目经验的json****/
        public function actionDeleteProjectExperience($id)
        {
            if(ProjectExperience::model()->deleteByPk($id))
            {
                 print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }

        /*****工作经验的json函数，通过ajax请求中带有的特定id，查出特定工作经验的json****/
        public function actionWorkExperience($id)
        {
             // 获取工作经历信息
              $work = WorkExperience::model()->findByAttributes(array('id'=>$id));               
              $provinceId = City::model()->findByAttributes(array('id'=>$work->city_id))->province_id;
              $city = $this->actionChangeCity($provinceId)-1;
              $startYear = date('Y',strtotime($work->start_time));
              $start_mon = date('m',strtotime($work->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($work->end_time));
              $end_mon = date('m',strtotime($work->end_time));              
              $endMonth=$this->actionData($end_mon); 
              if($work->salary){
                      $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                      "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                      "workCompany":"'.$work->company_name.'","posiName":"'.$work->position_name.'",
                      "province":'.($provinceId).',
                      "city":'.($work->city_id-$city).',"department":"'.$work->department.'",
                      "industry":'.$work->trade_id.',"posiType":'.$work->position_type_id.',
                      "salary":'.$work->salary.',"reportTo":"'.$work->report_object.'",
                      "description":"'.$work->experience_describe.'",
                      "subordinateNum":'.$work->subordinates_num.',"posiLevel":'.$work->level.'}}';
              }else{

                      $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                      "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                      "workCompany":"'.$work->company_name.'","posiName":"'.$work->position_name.'",
                      "province":'.($provinceId).',
                      "city":'.($work->city_id-$city).',"department":"'.$work->department.'",
                      "industry":'.$work->trade_id.',"posiType":'.$work->position_type_id.',
                      "salary":0,"reportTo":"'.$work->report_object.'",
                      "description":"'.$work->experience_describe.'",
                      "subordinateNum":'.$work->subordinates_num.',"posiLevel":'.$work->level.'}}';
              }
              
              print $json; 
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


        public function actionLookResumeId($model,$id){
             $resume_id = $model::model()->findByPk($id)->resume_id;
             return $resume_id;
        }

        public function actionLookUserId($model,$id){
             $user_id = $model::model()->findByPk($id)->user_id;
             return $user_id;
        }

        /***************删除工作经验的json函数，通过ajax请求中带有的特定id，查出特定工作经验的json**********/
        public function actionDeleteWorkExperience($id)
        {
            $resume_id=$this->actionLookResumeId('WorkExperience',$id);
            $this->actionCommen($resume_id,Yii::app()->session['resume_id']);
            if(WorkExperience::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
         /*****实习经验的json函数，通过ajax请求中带有的特定id，查出特定实习经验的json****/
        public function actionTraining($id)
        {
             // 获取实习经历信息
              $work = WorkExperience::model()->findByAttributes(array('id'=>$id));               
              $provinceId = City::model()->findByAttributes(array('id'=>$work->city_id))->province_id;
              $city = $this->actionChangeCity($provinceId)-1;
              $startYear = date('Y',strtotime($work->start_time));
              $start_mon = date('m',strtotime($work->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($work->end_time));
              $end_mon = date('m',strtotime($work->end_time));              
              $endMonth=$this->actionData($end_mon); 
              if($work->salary)
              {
                 $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                  "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                  "internCompany":"'.$work->company_name.'","posiName":"'.$work->position_name.'",
                  "province":'.$provinceId.',
                  "city":'.($work->city_id-$city).',"department":"'.$work->department.'",
                  "industry":'.$work->trade_id.',"posiType":'.$work->position_type_id.',
                  "salary":'.$work->salary.',"reportTo":"'.$work->report_object.'",
                  "description":"'.$work->experience_describe.'"}}';
              }else{
                  $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                  "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                  "internCompany":"'.$work->company_name.'","posiName":"'.$work->position_name.'",
                  "province":'.$provinceId.',
                  "city":'.($work->city_id-$city).',"department":"'.$work->department.'",
                  "industry":'.$work->trade_id.',"posiType":'.$work->position_type_id.',
                  "salary":0,"reportTo":"'.$work->report_object.'",
                  "description":"'.$work->experience_describe.'"}}';
              }
              
              print $json; 
        }
        /*****删除实习经验的json函数，通过ajax请求中带有的特定id，查出特定实习经验的json****/
        public function actionDeleteTraining($id)
        {
            $resume_id=$this->actionLookResumeId('WorkExperience',$id);
            $this->actionCommen($resume_id,Yii::app()->session['resume_id']);
            if(WorkExperience::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
        /*****返回语言能力编辑时的json形式****/
        public function actionLanguages($id)
        {              
              $lang = LanguagesAbility::model()->findByAttributes(array('id'=>$id));                 
              $json='{"code":"0","data":{"language":'.$lang->type_id.',"talkListen":'.$lang->les_say.',
              "readWrite":'.$lang->rea_wri.',"exam":['.$lang->grade_id.'],
              "grade":['.$lang->point.']}}';
              print $json;  
        }
         /*****删除语言能力编辑时的json形式****/
        public function actionDeleteLanguage($id)
        {

            if(LanguagesAbility::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
        /*******通过ajax返回的特定语言类型的id，返回特定的等级考试****/
        public function actionExam($id)
        {
             $model=LanguagesExam::model()->findAllByAttributes(array('language_type_id'=>$id));              
             print'{"code":0,"data":'.CJSON::encode($model).'}';                            
        }
        /*****编辑时校内奖励的json形式函数****/
        public function actionAward($id)
        {
              $award = SchoolAwards::model()->findByAttributes(array('id'=>$id));                         
              $awardYear = date('Y',strtotime($award->award_time));
              $start_mon = date('m',strtotime($award->award_time));
              $awardMonth=$this->actionData($start_mon);              
              $json='{"code":"0","data":{"awardYear":'.$awardYear.',"awardMonth":'.$awardMonth.',              
              "school":"'.$award->school.'","schoolAward":"'.$award->award_name.'",
             "description":"'.$award->explain.'"}}';
              print $json;  
        }
        /*****删除校内奖励时，返回的json形式函数****/
        public function actionDeleteAward($id)
        {             
            if(SchoolAwards::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }  
        /*****编辑时返回的教育经历的json函数，通过特定的id****/       
        public function actionEducation($id)
        {            
              $tudy =StudyExperience::model()->findByAttributes(array('id'=>$id));              
              $startYear = date('Y',strtotime($tudy->start_time));
              $start_mon = date('m',strtotime($tudy->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($tudy->end_time));
              $end_mon = date('m',strtotime($tudy->end_time));              
              $endMonth=$this->actionData($end_mon);
              $id=$tudy->study_specialty_id;  
              $Lid=StudySpecialty::model()->findByAttributes(array('id'=>$id))->parent_id;             
              $value=StudySpecialty::model()->findByAttributes(array('parent_id'=>$Lid))->id;
              if($tudy->gpa==null)
                  $tudy->gpa='0';
              if(empty($Lid))
              {
                  $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                  "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                  "school":"'.$tudy->school_name.'","majorName":"'.$tudy->major_name.'",
                  "majorClassify":'.$tudy->study_specialty_id.',"majorId":0,"subId":0,
                  "degree":'.($tudy->position_degree_id).',"GPA":'.$tudy->gpa.',"ranking":'.$tudy->rank.',
                  "majorDescription":"'.trim($tudy->study_describe).'"}}';
              }
              else
              {
                  $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
                  "endYear":'.$endYear.',"endMonth":'.$endMonth.',
                  "school":"'.$tudy->school_name.'","majorName":"'.$tudy->major_name.'",
                  "majorClassify":'.$tudy->study_specialty_id.',"majorId":'.$Lid.',"subId":'.($id-$value+1).',
                  "degree":'.($tudy->position_degree_id).',"GPA":'.$tudy->gpa.',"ranking":'.$tudy->rank.',
                  "majorDescription":"'.trim($tudy->study_describe).'"}}';
              }                      
              print $json;   
        }
         /*****删除教育经历的json函数，通过特定的id****/ 
        public function actionDeleteEducation($id)
        {
            $user_id=$this->actionLookUserId('StudyExperience',$id);
            $this->actionCommen($user_id,Yii::app()->session['user_id']);
            if(StudyExperience::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
        
        /*****编辑校内职务时，返回的json形式函数，通过ajax请求，获取特定的校内职务的id****/
        public function actionSchoolDuty($id)
        {
              $duty = SchoolDuty::model()->findByAttributes(array('id'=>$id));
              $startYear = date('Y',strtotime($duty->start_time));
              $start_mon = date('m',strtotime($duty->start_time));
              $startMonth=$this->actionData($start_mon);
              $endYear = date('Y',strtotime($duty->end_time));
              $end_mon = date('m',strtotime($duty->end_time));              
              $endMonth=$this->actionData($end_mon);            
               $json='{"code":"0","data":{"startYear":'.$startYear.',"startMonth":'.$startMonth.',
              "endYear":'.$endYear.',"endMonth":'.$endMonth.',
              "school":"'.$duty->school.'","schoolPosition":"'.$duty->duty_name.'",
              "achievement":"'.$duty->duty_performance.'"}}';
              print $json; 
        }
        /*****删除校内职务时，返回的json形式函数****/
        public function actionDeleteSchoolDuty($id)
        {
            if(SchoolDuty::model()->deleteByPk($id))
            {
                print '{"code":"0"}';//删除成功时，返回的形式。
            }else{
                 print '{"code":"1"}';
            }
        }

        /*****提交IT技能时，通过前台发来新增的IT技能，进行与数据库里原有的字段进行拼接，然后返回整个字段给前台显示****/
        public function actionPutSkill()
        {                           
             $arr=explode(',',$_GET['skills']); //获取前台发过来用逗号隔开的数据，将其分成数组             
             
                 foreach ($arr as $key => $value) {  //进行循环，将数组插入数据库
                    if(!empty($value))
                    {
                       $result = ItSkill::model()->findByAttributes(array('resume_id'=>Yii::app()->session['resume_id'],'name'=>$value));
                        if(empty($result))
                        {
                          $skill= new ItSkill();
                          $skill->name=$value; 
                          $skill->resume_id= Yii::app()->session['resume_id'];
                          $skill->save();
                        } 
                    }
                    
                 }
                
             /*if(!empty($_GET['detail'])) 
             {*/
                 $ItDetail = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>Yii::app()->session['resume_id']));  
                 if($ItDetail)  //判断IT信息的详细信息是否存在。
                 {
                     $ItDetail->detail=self::$cleanService->clean($_GET['detail']);
                     $ItDetail->save();
                 }else
                 {                 
                     $ItDetail = new ItSkillsDetail();
                     $ItDetail->resume_id= Yii::app()->session['resume_id'];
                     $ItDetail->detail=self::$cleanService->clean($_GET['detail']);
                     $ItDetail->save();
                 }         
             //}                           
             $ski = ItSkill::model()->findAllByAttributes(array('resume_id'=>Yii::app()->session['resume_id']));             
             if($ski)  //将从数据库里查出来的所有IT数据进行拼接。
             {
                foreach ($ski as $key => $value) 
                {
                   $sk = $sk.",".$value->name;                  
                }
             }                
               $skills=$this->actionChar($sk);                
              $json='{"code":0,"data":{"skills":"'.$skills.'","detail":"'.self::$cleanService->clean($_GET['detail']).'"}}';
              print $json; 
        }
        /*****这是一个去掉变量前逗号的函数****/
        public function actionChar($sk)
        {             
            $char= substr($sk,0,1);
            $len = strlen($sk);            
            if($char==',')
            {
                $da=substr($sk,1,$len-1);
            }else{
                $da = $sk;
            }
            return $da; 

        }

        /*****给前台返回一个数据库里已选的IT技能，通过resume_id***/
        public function actionSelectSkill()
        {
                         
             $skill = ItSkill::model()->findAllByAttributes(array('resume_id'=>Yii::app()->session['resume_id']));
             print'{"code":0,"data":'.CJSON::encode($skill).'}';
        }

        /*****通过resume_id删除IT的详细信息***/
        public function actionDetailSkill()
        {
             $skill = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>Yii::app()->session['resume_id']))->detail;
             print'{"code":0,"data":"'.self::$cleanService->clean($skill).'"}';
        }
        /*****通过特定的技能ID，删除IT信息***/
        public function actionDeleteSkill($id)
        {
            if(ItSkill::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
         /*****通过特定的证书ID，删除特定的证书信息***/
        public function actionDeleteCertificate($id)
        {
            if(Certificate::model()->deleteByPk($id))
            {
                print '{"code":"0"}';
            }else{
                 print '{"code":"1"}';
            }
        }
         /*****通过特定的resume_id，添加提交的多个证书信息***/
        public function actionEditCertificate()
        {
             $resume_id=Yii::app()->session['resume_id'];            
             foreach ($_POST['certificate'] as $key => $value){
                if(!empty($value))
                {
                      $result=Certificate::model()->findByAttributes(array('resume_id'=>$resume_id,'name'=>self::$cleanService->clean($value)));
                      if(empty($result))
                      {
                          $model = new Certificate();
                          $model->name=self::$cleanService->clean($value);
                          $model->resume_id=$resume_id;
                          $model->save();
                      } 
                }                 
             }
             $this->redirect(array('index'));
        }
        
       /*****通过特定的user_id，添加提交的教育信息***/
        public function actionAddStudyExperience()
        {
             $user_id=yii::app()->session['user_id'];
            if (isset($_POST['StudyExperience'])) 
            {                
                $model = new StudyExperience();
                $_POST['StudyExperience']['start_time']=$this->actionChangeTime('StudyExperience','start_time');
                $_POST['StudyExperience']['end_time']=$this->actionChangeTime('StudyExperience','end_time');
                $_POST['StudyExperience']['study_describe']=trim($_POST['StudyExperience']['study_describe']);
                foreach($_POST['StudyExperience'] as $k=>$val){
                    $_POST['StudyExperience'][$k] = self::$cleanService->clean($val);
                }
                $model->attributes = $_POST['StudyExperience'];                 
                $model->user_id = $user_id;
                if ($model->save()) {
                    $this->redirect(array('index'));
                }else{
                    echo "添加失败";
                }
            }
        }
        /***********************拼接时间的函数*************************/  
        public function actionChangeTime($data,$time)
        {
              $time= $_POST[$data][$time]['year'].'-'.
              $_POST[$data][$time]['month'].'-01';
              return $time;         
        }

       /*************************编辑教育经验************************/
        public function actionEditStudyExperience()
        {
            $user_id=yii::app()->session['user_id'];
            if (isset($_POST['StudyExperience'])) 
            {                                          
                $_POST['StudyExperience']['start_time']=$this->actionChangeTime('StudyExperience','start_time');
                $_POST['StudyExperience']['end_time']=$this->actionChangeTime('StudyExperience','end_time');
                $model = StudyExperience::model()->findByAttributes(array('id'=>$_POST['id']));
                $_POST['StudyExperience']['study_describe']=trim($_POST['StudyExperience']['study_describe']);
                foreach($_POST['StudyExperience'] as $k=>$val){
                    $_POST['StudyExperience'][$k] = self::$cleanService->clean($val);
                }
                $model->attributes = $_POST['StudyExperience']; 
                if ($model->save()) {
                    $this->redirect(array('index'));
                }else{
                    echo "添加失败";
                }
            }
        }


         /*************************添加实习经验************************/
          public function actionWorkExperienceAdd(){ 
                $resume_id=yii::app()->session['resume_id']; 
                 //yii::app()->session['user_id']=1;      
                $model = new WorkExperience();
                if (isset($_POST['WorkExperience'])){
                  $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
                  $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
                    foreach($_POST['WorkExperience'] as $k=>$val){
                        $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
                    }
                  $model->attributes = $_POST['WorkExperience'];                 
                  $model->resume_id = $resume_id;
                  $model->work_type=1;
                  $model->user_id =  yii::app()->session['user_id'];                  
                  if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
              }       
                 
        } 
          /*************************编辑实习经验************************/ 
          public function actionEditTrainEx(){ 
                $resume_id=yii::app()->session['resume_id'];  
                 //yii::app()->session['user_id']=1;                     
                if (isset($_POST['WorkExperience'])){                  
                  $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
                  $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
                  $model = WorkExperience::model()->findByAttributes(array('id'=>$_POST['id']));
                    foreach($_POST['WorkExperience'] as $k=>$val){
                        $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
                    }
                  $model->attributes = $_POST['WorkExperience'];                                
                  $model->resume_id = $resume_id;
                  $model->work_type=1;
                  $model->user_id =  yii::app()->session['user_id'];                  
                  if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
              }       
                 
        } 
       
         /*************************添加工作经验************************/ 
          public function actionAddWorkExperience(){ 
                $resume_id=yii::app()->session['resume_id'];  
                 //yii::app()->session['user_id']=1;      
                $model = new WorkExperience();
                if (isset($_POST['WorkExperience'])){
                  $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
                  $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
                    foreach($_POST['WorkExperience'] as $k=>$val){
                        $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
                    }
                  $model->attributes = $_POST['WorkExperience'];                 
                  $model->resume_id = $resume_id;
                  $model->work_type=0;
                  $model->user_id =  yii::app()->session['user_id'];                  
                  if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
              }       
                 
          }

         /*************************编辑工作经验************************/ 
          public function actionEditWorkExperience(){ 
                $resume_id=yii::app()->session['resume_id'];  
                // yii::app()->session['user_id']=1;                      
                if (isset($_POST['WorkExperience'])){
                  $_POST['WorkExperience']['start_time']=$this->actionChangeTime('WorkExperience','start_time');
                  $_POST['WorkExperience']['end_time']=$this->actionChangeTime('WorkExperience','end_time');
                  $model = WorkExperience::model()->findByAttributes(array('id'=>$_POST['id']));
                    foreach($_POST['WorkExperience'] as $k=>$val){
                        $_POST['WorkExperience'][$k] = self::$cleanService->clean($val);
                    }
                  $model->attributes = $_POST['WorkExperience'];                 
                  $model->resume_id = $resume_id;
                  $model->work_type=0;
                  $model->user_id =  yii::app()->session['user_id'];                  
                  if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
              }       
                 
          }

         /*************************添加语言能力，要拼接等级和分数************************/ 

         public function actionDealLanguages()
         {

                $resume_id=yii::app()->session['resume_id'];                                    
                if (isset($_POST['languages'])){                            
                  for($i=0;$i<count($_POST['grade']);$i++)  
                  {  
                      $exam = $this->actionChangeGrade($_POST['grade'][$i]);                   
                      $grade.=",".$exam."(".$_POST['point'][$i].")"; 
                      $id.=",".$this->actionChangeId($_POST['languages']['type_id'],$exam); 
                      $point.=",".$_POST['point'][$i];
                  }                                                                                              
                  $model = new LanguagesAbility();
                  $model->attributes = $_POST['languages']; 
                  $model->resume_id = $resume_id; 
                  $model->grade=$this->actionChar($grade);
                  $model->point=$this->actionChar($point); 
                  $model->grade_id=$this->actionChar($id);                  
                 if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
            }
         }
        /*************************算出特定语言等级的相对位置************************/ 
         public function actionChangeId($id,$name)
         {
             $model=LanguagesExam::model()->findByAttributes(array('language_type_id'=>$id,'exam_name'=>$name))->id;
             $mod=LanguagesExam::model()->findByAttributes(array('language_type_id'=>$id))->id;
             return $model-$mod;

         }         
         /*************************编辑语言能力，要拼接等级和分数************************/ 
         public function actionEditLanguageAbility()
         {

               $resume_id=yii::app()->session['resume_id'];                                    
              if (isset($_POST['languages'])){    
                  $model = LanguagesAbility::model()->findByAttributes(array('id'=>$_POST['id']));                                                                                                                                  
                  $model->attributes = $_POST['languages']; 
                  $model->resume_id = $resume_id; 
                  for($i=0;$i<count($_POST['grade']);$i++)  
                  {    
                     $exam = $this->actionChangeGrade($_POST['grade'][$i]);                                        
                     $id.=",".$this->actionChangeId($_POST['languages']['type_id'],$exam);                    
                     $grade.=",".$exam."(".$_POST['point'][$i].")"; 
                  } 
                  $model->grade=$this->actionChar($grade); 
                 // $model->grade=implode(',',$_POST['grade']); 
                  $model->point=implode(',',$_POST['point']);  
                  $model->grade_id=$this->actionChar($id); 
                 if ($model->save()) {
                      $this->redirect(array('index'));
                  }else{
                      echo "添加失败";
                  }
            }
         }
         public function actionChangeGrade($data){
              $model = LanguagesExam::model()->findByAttributes(array('id'=>$data))->exam_name;
              return $model;
         }

     /*************************添加校内奖励************************/ 

      public function actionDealAward()
      {
         $resume_id=yii::app()->session['resume_id'];              
         $model = new SchoolAwards();
         if (isset($_POST['award'])){            
              $_POST['award']['award_time']=$this->actionChangeTime('award','award_time');
             foreach($_POST['award'] as $k=>$val){
                 $_POST['award'][$k] = self::$cleanService->clean($val);
             }
              $model->attributes = $_POST['award']; 
              $model->resume_id = $resume_id;  
               if ($model->save()) {
                  $this->redirect(array('index'));
              }else{
                  echo "添加失败";
              }
         }

      }

      /*************************编辑校内奖励************************/ 
      public function actionEditAward()
      { 
            $resume_id=yii::app()->session['resume_id']; 
            $model = SchoolAwards::model()->findByAttributes(array('id'=>$_POST['id']));          
            $_POST['award']['award_time']=$this->actionChangeTime('award','award_time');
            foreach($_POST['award'] as $k=>$val){
              $_POST['award'][$k] = self::$cleanService->clean($val);
            }
            $model->attributes = $_POST['award']; 
            $model->resume_id = $resume_id;  
            if($model->save()) {
                $this->redirect(array('index'));
            }else{
                echo "添加失败";
            }
         
      }
      
       /*************************添加校内职务************************/ 
      public function actionDealSchoolDuty()
      {         
         $resume_id=yii::app()->session['resume_id'];              
         $model = new SchoolDuty();
         if (isset($_POST['duty'])){              
            $_POST['duty']['start_time']=$this->actionChangeTime('duty','start_time');
            $_POST['duty']['end_time']=$this->actionChangeTime('duty','end_time');
             foreach($_POST['duty'] as $k=>$val){
                 $_POST['duty'][$k] = self::$cleanService->clean($val);
             }
            $model->attributes = $_POST['duty'];                 
            $model->resume_id = $resume_id;                            
            if ($model->save()) {
                $this->redirect(array('index'));
            }else{
                 echo "添加失败";
            }
         }      
    }

      /*************************编辑校内职务************************/ 
      public function actionEditSchoolDuty()
      {
         
         $resume_id=yii::app()->session['resume_id'];   
         $model = SchoolDuty::model()->findByAttributes(array('id'=>$_POST['id']));                              
              // 拼接时间
              $_POST['duty']['start_time']=$this->actionChangeTime('duty','start_time');
              $_POST['duty']['end_time']=$this->actionChangeTime('duty','end_time');
              foreach($_POST['duty'] as $k=>$val){
              $_POST['duty'][$k] = self::$cleanService->clean($val);
              }
              $model->attributes = $_POST['duty'];                 
              $model->resume_id = $resume_id;                            
              if ($model->save()) {
                  $this->redirect(array('index'));
              }else{
                  echo "添加失败";
              }               
    }

    /*************************添加项目经验************************/ 
    public function actionDealProject()
    {
         $resume_id=yii::app()->session['resume_id'];              
         $model = new ProjectExperience();
         if (isset($_POST['project'])){
          // 拼接时间           
          $_POST['project']['start_time']=$this->actionChangeTime('project','start_time');
          $_POST['project']['end_time']=$this->actionChangeTime('project','end_time');
             foreach($_POST['project'] as $k=>$val){
                 $_POST['project'][$k] = self::$cleanService->clean($val);
             }
          $model->attributes = $_POST['project'];                 
          $model->resume_id = $resume_id;                            
          if ($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
      }      
    }

    /*************************编辑项目经验************************/ 
    public function actionEditProject()
    {

         $resume_id=yii::app()->session['resume_id'];   
         $model = ProjectExperience::model()->findByAttributes(array('id'=>$_POST['id']));                                
          $_POST['project']['start_time']=$this->actionChangeTime('project','start_time');
          $_POST['project']['end_time']=$this->actionChangeTime('project','end_time');
          foreach($_POST['project'] as $k=>$val){
            $_POST['project'][$k] = self::$cleanService->clean($val);
          }
          $model->attributes = $_POST['project'];                 
          $model->resume_id = $resume_id;                            
          if ($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
         
    }


   /*************************添加培训经历************************/  
   public function actionAddTrainingExperience()
    {
         $resume_id=yii::app()->session['resume_id'];              
         $model = new TrainingExperience();
         if (isset($_POST['train'])){          
          $_POST['train']['start_time']=$this->actionChangeTime('train','start_time');
          $_POST['train']['end_time']=$this->actionChangeTime('train','end_time');
             foreach($_POST['train'] as $k=>$val){
                 $_POST['train'][$k] = self::$cleanService->clean($val);
             }
          $model->attributes = $_POST['train'];                 
          $model->resume_id = $resume_id;                            
          if ($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
      }      
    }

       /*************************编辑培训经历************************/  
       public function actionEditTrainingExperience()
        {
             $resume_id=yii::app()->session['resume_id'];                                          
             if (isset($_POST['train'])){
              // 拼接时间
              $model = TrainingExperience::model()->findByAttributes(array('id'=>$_POST['id'])); 
              $_POST['train']['start_time']=$this->actionChangeTime('train','start_time');
              $_POST['train']['end_time']=$this->actionChangeTime('train','end_time');
                 foreach($_POST['train'] as $k=>$val){
                     $_POST['train'][$k] = self::$cleanService->clean($val);
                 }
              $model->attributes = $_POST['train'];                 
              $model->resume_id = $resume_id;                            
              if ($model->save()) {
                  $this->redirect(array('index'));
              }else{
                  echo "添加失败";
              }
          }      
        }


    /*************************添加求职经历************************/  
   public function actionAddJobExperience()
    {
         $user_id=yii::app()->session['user_id'];              
         $model = new JobExperience();
         if (isset($_POST['job'])){
          $_POST['job']['time']=$this->actionChangeTime('job','time');
             foreach($_POST['job'] as $k=>$val){
                 $_POST['job'][$k] = self::$cleanService->clean($val);
             }
          $model->attributes = $_POST['job'];                 
          $model->user_id = $user_id;                            
          if ($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
      }      
    }

      /*************************编辑求职经历************************/   
   public function actionEditJobExperience()
    {
         $user_id=yii::app()->session['user_id'];                        
         if (isset($_POST['job'])){
          // 拼接时间
          $model = JobExperience::model()->findByAttributes(array('id'=>$_POST['id'])); 
           $_POST['job']['time']=$this->actionChangeTime('job','time');
             foreach($_POST['job'] as $k=>$val){
                 $_POST['job'][$k] = self::$cleanService->clean($val);
             }
            $model->attributes = $_POST['job'];                 
            $model->user_id = $user_id;                            
          if($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
      }      
    }
    
     
     /*************************编辑基本信息************************/   
     public function actionBasicInfoPut()
     { 
          $user_id=yii::app()->session['user_id'];
          $model=UserDetail::model()->findByAttributes(array('user_id'=>$user_id));//current_live
          $city_id=$this->actionChangeName($_POST['BasicInfo']['account_place']);
          $_POST['BasicInfo']['birthday'] =
                    $_POST['BasicInfo']['birthday']['year'].'-'.
                    $_POST['BasicInfo']['birthday']['month'].'-'.
                     $_POST['BasicInfo']['birthday']['day'];
         foreach($_POST['BasicInfo'] as $k=>$val){
             $_POST['BasicInfo'][$k] = self::$cleanService->clean($val);
         }
           $model->attributes = $_POST['BasicInfo'];
           $model->city_id= $city_id;
           //$model->account_place = City::model()->findByPk($city_id)->name;
           if ($model->save()) {
              $this->redirect(array('index'));
          }else{
              echo "添加失败";
          }
     }

      /******************************通过市的名字找市的信息的函数**************************/
    public function actionChangeName($city)
    {
        $model = City::model()->findByAttributes(array('name'=>$city))->id;
        return $model; 
    }
              
    /*************************添加附件************************/   
      public function actionDealFile()
      {
         $resume_id=yii::app()->session['resume_id'];           
         $fileObj = CUploadedFile::getInstanceByName('file');
         $uploadPath ="assets/uploadFile/file/";
          //if($fileObj){
              $fileUrl = $uploadPath.time().'.'.$fileObj->getExtensionName();
             if($fileObj->saveAs($fileUrl))
             {
                 $model=new ResumeAttachment();
                 $model->name=$fileObj->getName();
                 $model->url="http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/'.$fileUrl;
                 $model->resume_id=$resume_id;
                  $model->size=ceil($fileObj->getSize()/1024);
                 if ($model->save()){
                     $this->redirect(array('index'));
                 }else{
                     echo "添加失败";
                }
             }else{
                  echo "添加失败";
             }
          //}else{
              //$message = "附件格式不支持，请重新上传";
              //echo"<script>alert('".$message."')</script>";
              //$this->actionIndex();
          //}
      }

          /*************************删除附件************************/   
          public function actionDeleteFile($id)
          {
              $model=ResumeAttachment::model()->deleteByPk($id);
              if($model)
              {
                   print '{"code":"0"}'; 

              }else{
                  print '{"code":"1"}'; 
              } 
          }

        /*********************性别转换的函数********************************/
          public function actionGender($data)
          {
               if($data=='0')
               {
                 $data="女";
               }else{
                 $data="男";
               }
               return $data;
          }
         /*********************通过时间计算出年龄的转换函数**********************/
         
         public function actionAgeNum($age)
         {          
             return (date('Y-m-d',time())-$age);
         }        

      /********************我的简历job/my_resume/my_resume.html的显示*************************/
      public function actionMyResumeIndex()
      {
         $user_id=yii::app()->session['user_id'];
         $basicInfo = UserDetail::model()->getList($user_id);// 获取基本信息
         $gender=$this->actionGender($basicInfo->gender);//进行性别的转换
         $age = $this->actionAgeNum($basicInfo->birthday);//进行年龄的转换
         $arr = array("零","一","二","三","四","五","六","七","八");  //用来表示工作经验的年数
         if($basicInfo->work_experience<=1)  //当满足这个条件时，说明该用户还没有工作经历
         {
             $workEx=null;
         }else
         {
            $workEx = $arr[$basicInfo->work_experience-1]; 
         }               
         $user = User::model()->findByAttributes(array('id'=>$user_id)); // 获取联系方式
         $ResumeInfo = Resume::model()->with('degree')->getList($user_id);// 获取简历信息
         // 获取教育经历
         $StudyExperienceInfo =StudyExperience::model()->with('position_degree')->
                                               getList($user_id); 
          /************************获取最大学历**********************/
          $sql="select max(position_degree_id) as num from {{study_experience}} where user_id='".$user_id."'";
          $result=StudyExperience::model()->findBySql($sql);
         // var_dump($result);
          $name=Degree::model()->findByAttributes(array('id'=>$result->num))->name;
         // var_dump($name);
         $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
         Yii::app()->session['resume_id']=  $resume_id;  
         $sql ="select id,name from {{certificate}} where resume_id=".$resume_id;                    
         //证书的信息
         $model = Certificate::model()->findAllBySql($sql);
         //校内职务的信息
         $schoolDuty = SchoolDuty::model()->findAllByAttributes(array('resume_id'=>$resume_id));

         $schoolAward = SchoolAwards::model()->findAllByAttributes(array('resume_id'=>$resume_id));
         //查询语言能力的信息。
         $sql = "select t_languages_ability.id,type_id,les_say,point,rea_wri,grade,t_languages_type.name as name from {{languages_ability}},{{languages_type}}
         where (t_languages_ability.type_id= t_languages_type.id) and resume_id=".$resume_id;
         $lang = LanguagesAbility::model()->findAllBySql($sql);   

         foreach ($lang as $key => $value) {  
             $value->les_say=$this->actionChangeLangueges($value->les_say);  //将数据等级转化为中文等级，能更清晰的表示语言的能力
             $value->rea_wri=$this->actionChangeLangueges($value->rea_wri);//将数据等级转化为中文等级，能更清晰的表示语言的能力
             
        }
         // 获取求职经历信息
         $JobExperience = JobExperience::model()->getList($user_id);
         // 获取项目经历信息
         $ProjectExperience = ProjectExperience::model()->getList($resume_id);
         foreach ($JobExperience as $key => $value) {
            $value->type=$this->actionChangeJobGrade($value->type); 
         }
         // 获取培训经历信息
         $TrainingExperience = TrainingExperience::model()->getList($resume_id); 

         // 获取实习经历信息
         $train = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>1));//getList($user_id); 
         // 获取工作经历信息
         $WorkExperience = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>0));
          // 获取IT技能信息
         $ItSkill = ItSkill::model()->findAllByAttributes(array('resume_id'=>$resume_id));
         $ItDetail = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>$resume_id));
          
         /****************获取当前身份*****************/            
        $study="select school_name as name,major_name as value from {{study_experience}}
        where sign=1 and user_id='".$user_id."'";
        $identify=StudyExperience::model()->findBySql($study);
        if(empty($identify))
        {
            $work="select company_name as name,position_name as value from {{work_experience}}
            where sign=1 and user_id='".$user_id."'";
            $identify =WorkExperience::model()->findBySql($work);
            if(empty($identify))
            {
                $identify="";
            }
        } 
         //获取附件信息
         $file=ResumeAttachment::model()->findByAttributes(array('resume_id'=>$resume_id));
         $this->smarty->assign('file',$file);
         $this->smarty->assign('ItDetail',$ItDetail);//it的详细信息
         $this->smarty->assign('ItSkill',$ItSkill);  //it技能的信息
         $this->smarty->assign('WorkExperience',$WorkExperience);  //工作经历的信息
         $this->smarty->assign('train',$train);           //实习经历的信息   
         $this->smarty->assign('ProjectExperience',$ProjectExperience);     //项目经验的信息      
         $this->smarty->assign('TrainingExperience',$TrainingExperience);  //培训经历的信息 
         $this->smarty->assign('JobExperience',$JobExperience);      //求职经历的信息
         $this->smarty->assign('languages',$lang);     //语言能力的信息
         $this->smarty->assign('award',$schoolAward); //校内奖励的信息
         $this->smarty->assign('school',$schoolDuty); //校内职务的信息
         $this->smarty->assign('model',$model);  //证书的信息
         $this->smarty->assign('ResumeInfo',$ResumeInfo);  // 获取简历信息
         $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);  //教育经历的信息
         $this->smarty->assign('contact',$user);      //手机与邮箱的信息
         $this->smarty->assign('workEx',$workEx);  //工作经验年数的信息
         $this->smarty->assign('basicInfo',$basicInfo);  //用户的基本信息信息
         $this->smarty->assign('gender',$gender);  //用户性别信息
         $this->smarty->assign('age',$age);  //用户年龄信息
         $this->smarty->assign('identify',$identify);//用户当前身份
         $this->smarty->assign('name',$name);//学历
         $this->smarty->display('job/my_resume/my_resume.html');
      }
      /***我的简历job/my_resume/my_resume_word.html的显示,专门为导出word去掉了简历页面中没必要显示内容*****/
     public function actionMyResumeIndexWord()
      {
         $user_id=yii::app()->session['user_id'];
         $basicInfo = UserDetail::model()->getList($user_id);// 获取基本信息
         $gender=$this->actionGender($basicInfo->gender);//进行性别的转换
         $age = $this->actionAgeNum($basicInfo->birthday);//进行年龄的转换
         $arr = array("零","一","二","三","四","五","六","七","八");  //用来表示工作经验的年数
         if($basicInfo->work_experience<=1)  //当满足这个条件时，说明该用户还没有工作经历
         {
             $workEx=null;
         }else
         {
            $workEx = $arr[$basicInfo->work_experience-1]; 
         }               
         $user = User::model()->findByAttributes(array('id'=>$user_id)); // 获取联系方式
         $ResumeInfo = Resume::model()->with('degree')->getList($user_id);// 获取简历信息
         // 获取教育经历
         $StudyExperienceInfo =StudyExperience::model()->with('position_degree')->
                                               getList($user_id); 
          /************************获取最大学历**********************/
          $sql="select max(position_degree_id) as num from {{study_experience}} where user_id='".$user_id."'";
          $result=StudyExperience::model()->findBySql($sql);
         // var_dump($result);
          $name=Degree::model()->findByAttributes(array('id'=>$result->num))->name;
         // var_dump($name);
         $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
         Yii::app()->session['resume_id']=  $resume_id;  
         $sql ="select id,name from {{certificate}} where resume_id=".$resume_id;                    
         //证书的信息
         $model = Certificate::model()->findAllBySql($sql);
         //校内职务的信息
         $schoolDuty = SchoolDuty::model()->findAllByAttributes(array('resume_id'=>$resume_id));

         $schoolAward = SchoolAwards::model()->findAllByAttributes(array('resume_id'=>$resume_id));
         //查询语言能力的信息。
         $sql = "select t_languages_ability.id,type_id,les_say,point,rea_wri,grade,t_languages_type.name as name from {{languages_ability}},{{languages_type}}
         where (t_languages_ability.type_id= t_languages_type.id) and resume_id=".$resume_id;
         $lang = LanguagesAbility::model()->findAllBySql($sql);   

         foreach ($lang as $key => $value) {  
             $value->les_say=$this->actionChangeLangueges($value->les_say);  //将数据等级转化为中文等级，能更清晰的表示语言的能力
             $value->rea_wri=$this->actionChangeLangueges($value->rea_wri);//将数据等级转化为中文等级，能更清晰的表示语言的能力
             
        }
         // 获取求职经历信息
         $JobExperience = JobExperience::model()->getList($user_id);
         // 获取项目经历信息
         $ProjectExperience = ProjectExperience::model()->getList($resume_id);
         foreach ($JobExperience as $key => $value) {
            $value->type=$this->actionChangeJobGrade($value->type); 
         }
         // 获取培训经历信息
         $TrainingExperience = TrainingExperience::model()->getList($resume_id); 

         // 获取实习经历信息
         $train = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>1));//getList($user_id); 
         // 获取工作经历信息
         $WorkExperience = WorkExperience::model()->findAllByAttributes(array('user_id'=>$user_id,'work_type'=>0));
          // 获取IT技能信息
         $ItSkill = ItSkill::model()->findAllByAttributes(array('resume_id'=>$resume_id));
         $ItDetail = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>$resume_id));
          
         /****************获取当前身份*****************/            
        $study="select school_name as name,major_name as value from {{study_experience}}
        where sign=1 and user_id='".$user_id."'";
        $identify=StudyExperience::model()->findBySql($study);
        if(empty($identify))
        {
            $work="select company_name as name,position_name as value from {{work_experience}}
            where sign=1 and user_id='".$user_id."'";
            $identify =WorkExperience::model()->findBySql($work);
            if(empty($identify))
            {
                $identify="";
            }
        } 
         //获取附件信息
         $file=ResumeAttachment::model()->findByAttributes(array('resume_id'=>$resume_id));
         $this->smarty->assign('file',$file);
         $this->smarty->assign('ItDetail',$ItDetail);//it的详细信息
         $this->smarty->assign('ItSkill',$ItSkill);  //it技能的信息
         $this->smarty->assign('WorkExperience',$WorkExperience);  //工作经历的信息
         $this->smarty->assign('train',$train);           //实习经历的信息   
         $this->smarty->assign('ProjectExperience',$ProjectExperience);     //项目经验的信息      
         $this->smarty->assign('TrainingExperience',$TrainingExperience);  //培训经历的信息 
         $this->smarty->assign('JobExperience',$JobExperience);      //求职经历的信息
         $this->smarty->assign('languages',$lang);     //语言能力的信息
         $this->smarty->assign('award',$schoolAward); //校内奖励的信息
         $this->smarty->assign('school',$schoolDuty); //校内职务的信息
         $this->smarty->assign('model',$model);  //证书的信息
         $this->smarty->assign('ResumeInfo',$ResumeInfo);  // 获取简历信息
         $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);  //教育经历的信息
         $this->smarty->assign('contact',$user);      //手机与邮箱的信息
         $this->smarty->assign('workEx',$workEx);  //工作经验年数的信息
         $this->smarty->assign('basicInfo',$basicInfo);  //用户的基本信息信息
         $this->smarty->assign('gender',$gender);  //用户性别信息
         $this->smarty->assign('age',$age);  //用户年龄信息
         $this->smarty->assign('identify',$identify);//用户当前身份
         $this->smarty->assign('name',$name);//学历
         $this->smarty->display('job/my_resume/my_resume_word.html');
      }


   /*********************将我的简历导出成word的形式**********************/
      public function actionExportToWord()
      {
          ob_start(); //打开缓冲区
          echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
         <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
         <xml><w:WordDocument><w:View>Print</w:View></xml>
         </head>';
          echo $this->actionMyResumeIndexWord();
          Header("Cache-Control: public");
          Header("Content-type: application/octet-stream");
          Header("Accept-Ranges: bytes");
          if (strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')) {
              Header('Content-Disposition: attachment; filename=简历.doc');
          }
          else if (strpos($_SERVER["HTTP_USER_AGENT"],'Firefox')) {
              Header('Content-Disposition: attachment; filename=简历.doc');
          }
          else{
              Header('Content-Disposition: attachment; filename=简历.doc');
          }
          Header("Pragma:no-cache");
          Header("Expires:0");
          ob_end_flush();//输出全部内容到浏览器

      }
 }
?>