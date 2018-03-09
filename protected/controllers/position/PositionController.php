<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-18
 * Time: 下午5:09
 */



class PositionController extends Controller{

    public function actionList(){
        if(isset($_GET['kind'])){
            $kind = $_GET['kind'];
            $this->smarty->assign('kind',$kind);
        }
        else{
            $this->smarty->assign('kind',2);
        }
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        $positionTypeList = CacheService::getInstance()->positionType();
        $propertyList  = CacheService::getInstance()->companyProperty();
        $positionSpecialtyList = CacheService::getInstance()->positionSpecialty();
        $degreeList = CacheService::getInstance()->degree();
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('positionTypeList',$positionTypeList);
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('positionSpecialtyList', $positionSpecialtyList);
        $this->smarty->display('position/educational.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria();
        //$criteria -> condition = ('is_discarded=0');
        $list_all = Position::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= Position::model()->findAll($criteria);  //记录分页
        $list2='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    public function  actionSearchJson($page=0, $kind,$searchWord = null, $propertyId = 0, $locationId = 0, $positionTypeId = 0,$degreeId=0,$messageSourceId=0,$majorId=0,
                                      $isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0,$heatSort=0,$timeSort=1){
        $positionListOnePage = PositionService::getInstance()->search2($page, $kind,$searchWord, $propertyId, $locationId, $positionTypeId,$degreeId,$messageSourceId,$majorId,
            $isJoinBigRecruitment , $isJoinRecruitmentWeek ,$heatSort,$timeSort);
        $dataCount = $positionListOnePage['recordCount'];
        $SearchJson='{"code":0,"data":'.CJSON::encode($positionListOnePage['list']).',"dataCount":"'.$dataCount.'"}';
        print  $SearchJson;
    }

    public function actionConcern($jobId,$isCollect){
        $position = Position::model()->findByPk($jobId);
        if($position==null){
            $this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $positionUser = PositionUser::model()->find(array(
            'condition' => 'position_id=:positionId AND user_id=:userId',
            'params' => array(':positionId'=>$jobId,':userId'=>$userId),
        ));

        if($isCollect==1) {
            $positionUserOne = new PositionUser();
            $positionUserOne->position_id=$jobId;
            $positionUserOne->user_id = $userId;
            $positionUserOne->save();
        }
        else{
            $positionUser->delete();
        }
        $list='{"code":0,"data":""}';
        print $list;
    }


    public  function  actionDetail($id){
        $company = Company::model()->with('companytrade','companyproperty','city')->findByPk(Position::model()->findByPk($id)->company_id);
        if($company!=null) {
            if (!empty($company->city)) {
                $cityList = '';
                $i = 0;
                $count = count($company->city);
                foreach ($company->city as $city) {
                    $i++;
                    if ($i === $count) {
                        $cityList .= $city->name;
                    } else {
                        $cityList .= $city->name . ',';
                    }
                }
                $company['city'] = $cityList;
            } else
                $company['city'] = "无";

            $concernedNum = CompanyUser::model()->count(array(
                'condition' => 'company_id=:companyId',
                'params' => array(':companyId' => $company->id),
            ));

            $this->smarty->assign('concernedNum', $concernedNum);
            $companyUser = CompanyUser::model()->find(array(
                'condition' => 'company_id=:companyId AND user_id=:userId',
                'params' => array(':companyId'=>$company->id,':userId'=>Yii::app()->session['user_id']),
            ));
            $concerned = $companyUser?1:0;
            $this->smarty->assign('concernedNum',$concernedNum);
            $this->smarty->assign('concerned',$concerned);
            $this->smarty->assign('company', $company);

        }
            $position =  PositionService::getInstance()->detail($id);
        $positionContacts = $position->positioncontacts;
        $this->smarty->assign('positionContacts', $positionContacts);
        $this->smarty->assign('position', $position);
        $current="current";
        $this->smarty->assign('po',$current);
        $this->smarty->assign('isActivated',User::model()->findByPk(Yii::app()->session['user_id'])->is_activated);
        $this->smarty->assign('isLeague',User::model()->findByPk(Yii::app()->session['user_id'])->is_league);
        $this->smarty->display('company/post/job-detail.html');
    }

    public  function  actionPositionType(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'PTmore')
                $List = CacheService::getInstance()->positionType();
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($List).'}';
        print  $SearchJson;
    }

    public  function  actionPositionSpecialty(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Mmore')
                $List = CacheService::getInstance()->positionSpecialty();
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($List).'}';
        print  $SearchJson;
    }

    public  function  actionProperty(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Pmore')
                $List = CacheService::getInstance()->companyProperty();
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($List).'}';
        print  $SearchJson;
    }

    public  function  actionLocation(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Lmore')
                $cityList = CacheService::getInstance()->province();
            else
                $cityList = CacheService::getInstance()->city()[$ss];
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($cityList).'}';
        print  $SearchJson;
    }

    public function actionToSendResume($id){
        $positionContact = Position::model()->findByPk($id)->positioncontacts;
        //yii::app()->session['user_id'] = 1;

        // 从session中获取用户id
        $user_id = yii::app()->session['user_id'];

        // 获取简历id
        $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
        $Letter = ApplicationLetter::model()->findAllByAttributes(array('resume_id'=>$resume_id));
        $this->smarty->assign('app_letter',$Letter);

        $this->smarty->assign('positionContact', $positionContact);
        //$content = Yii::app()->runController('job/resume/myResumeIndexWord');
        //$this->smarty->assign('content', $content);
        $user_id=yii::app()->session['user_id'];
        $basicInfo = UserDetail::model()->getList($user_id);// 获取基本信息
        $gender=Yii::app()->runController('job/resume/gender/data/'.$basicInfo->gender);//进行性别的转换
        $age = Yii::app()->runController('job/resume/ageNum/age/'.$basicInfo->birthday);//进行年龄的转换
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
            $value->les_say=Yii::app()->runController('job/resume/changeLangueges/var/'.$value->les_say); //将数据等级转化为中文等级，能更清晰的表示语言的能力
            $value->rea_wri=Yii::app()->runController('job/resume/changeLangueges/var/'.$value->rea_wri);//将数据等级转化为中文等级，能更清晰的表示语言的能力
        }
        // 获取求职经历信息
        $JobExperience = JobExperience::model()->getList($user_id);
        // 获取项目经历信息
        $ProjectExperience = ProjectExperience::model()->getList($resume_id);
        foreach ($JobExperience as $key => $value) {
            $value->type=Yii::app()->runController('job/resume/changeJobGrade/data/'.$value->type);
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
        $this->smarty->assign('id',$id);
        $this->smarty->display('position/sendResume.html');
    }

    public  function  actionSendResume(){
        $email = $_POST['contactEmail'];
        if(isset($_POST['letter'])&&$_POST['letter']==0) {
            if($_POST['addLetter']!=null)
                $letterContent = $_POST['addLetter'];
            else
                $letterContent = '无';
        }
        elseif(isset($_POST['letter'])&&$_POST['letter']!=0) {
                $letterContent = $_POST['addLetter'];
        }
        else{
            if(isset($_POST['addLetter'])&&$_POST['addLetter']!=null&&$_POST['addLetter']!=''){
                $letterContent = $_POST['addLetter'];
            }
            else
                $letterContent = '无';
        }

        $file = CUploadedFile::getInstanceByName('url');
        if($file!=null){
            //文件保存路径
            $uploadPath ="assets/uploadFile/";

            //获取文件后缀名
            $extName = $file->getExtensionName();
            //给文件重命名
            $fileName = time().'.'.$extName;
            //保存文件
            $fileUrl = $uploadPath.$fileName;
            $file->saveAs($fileUrl);
        }
        $mail = Yii::App()->mail;
        $mail->IsSMTP();

        // $verify = Yii::app()->emailVerify;

        // $randval = substr(md5(uniqid(rand())), 0, 6);
        // $verify->from = $randval;
        // if ($verify->check_mail($email) === true) {
        //     $mail->AddAddress($email);
        // } else {
        //     $this->redirect('err');
        // }
        
        if (filter_var($email,FILTER_VALIDATE_EMAIL)) {
            $email_arr = explode("@",$email);
            $len = count($email_arr);
            $domain = $email_arr[$len-1];
            if (checkdnsrr($domain, 'MX')) {
               $mail->AddAddress($email);
            }else{
                $this->redirect('err');
            }
        }else{
            $this->redirect('err');
        }

        $mail->Subject = "简历"; //邮件标题


        $content = '求职信:'."<br/>";
        //if($addLetter==null||$addLetter=='')
            $contentNew = $content.$letterContent;
        //else
            //$contentNew = $content.'无';
        $mail->Body = $contentNew."<br/>"."<br/>"."<br/>简历:<br/>".$_POST['content']; //邮件内容
        if($file!=null){
            $mail->AddAttachment(Yii::getPathOfAlias('webroot').'/assets/uploadFile/'.$fileName);
        }
        $mail->MsgHTML($mail->Body);
        $mail->IsHTML(true);
        if ($mail->send()) {
            $position = Position::model()->findByPk($_POST['positionId']);
            $position->resume_num = $position->resume_num+1;
            $position->save();
            $this->redirect($this->createUrl("job/position"));
        }

    }

    public function actionListBigRecruit(){
        if(isset($_GET['kind'])){
            $kind = $_GET['kind'];
            $this->smarty->assign('kind',$kind);
        }
        else{
            $this->smarty->assign('kind',2);
        }
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        $positionTypeList = CacheService::getInstance()->positionType();
        $propertyList  = CacheService::getInstance()->companyProperty();
        $positionSpecialtyList = CacheService::getInstance()->positionSpecialty();
        $degreeList = CacheService::getInstance()->degree();
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('positionTypeList',$positionTypeList);
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('positionSpecialtyList', $positionSpecialtyList);
        $this->smarty->assign('isBig',1);
        $this->smarty->display('position/bigRecruitment.html');
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + concern,toSendResume')
        );
    }

    public function actionErr(){
        $errMsg = "对不起，邮件发送地址无效";
        $this->smarty->assign('errMsg',$errMsg);
        $this->smarty->display('position/err.html');
    }

    public function actionTest(){

       phpinfo();

    }
} 
