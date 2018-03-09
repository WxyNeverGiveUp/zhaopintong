<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
/**
* 
*/
class PositionController extends Controller
{ 
	public function actionPositionList(){
		$Array = array();

		$DegreeInfo = Degree::model()->findAll();
		foreach ($DegreeInfo as $key => $value) {
			$Degree[$key]['id'] = $value->id;
			$Degree[$key]['name'] = $value->name;
		}

		$Source = array(                 
               1=>array('name'=>'东北师大','id'=>1),
			   2=>array('name'=>'六所部属','id'=>2),
			   3=>array('name'=>'互联网','id'=>3),
			);

		$Array['source'] = $Source;
		$Array['degree'] = $Degree;

		$Array = CJSON::encode($Array);
		print($Array);
	}

	public function  actionSearchJson($user_id=0,$page=0,$kind,$searchWord = null, $propertyId = 0, $locationId = 0, $positionTypeId = 0,$degreeId=0,$messageSourceId=0,$majorId=0,$isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0,$heatSort=0,$timeSort=1){
        if ($locationId!='不限') {
            $locationId = City::model()->findByAttributes(array('name'=>$locationId))->id;
        }else{
            $locationId = 0;
        }
        $positionListOnePage = PositionService::getInstance()->search2($page, $kind,$searchWord, $propertyId, $locationId, $positionTypeId,$degreeId,$messageSourceId,$majorId,
            $isJoinBigRecruitment , $isJoinRecruitmentWeek ,$heatSort,$timeSort);
        $dataCount = $positionListOnePage['recordCount'];
        
        $PositionUser = PositionUser::model()->findAllByAttributes(array('user_id'=>$user_id));
        $ResumeUser = ResumeUser::model()->findAllByAttributes(array('user_id'=>$user_id));
        if ($PositionUser!=null) {
            foreach ($PositionUser as $key => $value) {
                foreach ($positionListOnePage['list'] as $k => $v) {
                    if ($v['id'] == $value->position_id) {
                        $positionListOnePage['list'][$k]['collection'] = 1;
                    }
                }
            }
        }
        if ($ResumeUser!=null) {
            foreach ($ResumeUser as $key => $value) {
                foreach ($positionListOnePage['list'] as $k => $v) {
                    if ($v['id'] == $value->position_id) {
                        $positionListOnePage['list'][$k]['sended'] = 1;
                    }else{
                        $positionListOnePage['list'][$k]['sended'] = 0;
                    }
                }
            }
        }

        $SearchJson='{"code":0,"data":'.CJSON::encode($positionListOnePage['list']).',"dataCount":"'.$dataCount.'"}';
        print  $SearchJson;
    }

    public function actionPositionDetail(){
    	$data = json_decode(file_get_contents("php://input"));
		$id = $data->pid;
        $user_id = $data->user_id;
        $Array = array();

        $cri = new CDbCriteria();
        $cri->select = 'id,name,phone,logo';
        $cri->addCondition('t.id=:id');
        $cri->params = array(':id'=>Position::model()->findByPk($id)->company_id);
        $company = Company::model()->with('companytrade','companyproperty','city')->find($cri);
        $companyInfo['id'] = $company->id;
        $companyInfo['name'] = $company->name;
        $companyInfo['phone'] = $company->phone;
        $companyInfo['logo'] = 'www.dsjyw.net/'.$company->logo;
        $city_id = CompanyCity::model()->findByAttributes(array('company_id'=>$company->id))->city_id;
        $companyInfo['city'] = City::model()->findByPk($city_id)->name;
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


        }
            $position =  PositionService::getInstance()->detail($id);
            if ($position['position_source']==1) {
            	$position['position_source'] = '东北师大';
            }else if($position['position_source']==2){
            	$position['position_source'] = '6所部署';
            }else if($position['position_source']==3){
            	$position['position_source'] = '互联网';
            }

            $positionUser = PositionUser::model()->find(array(
                'condition' => 'position_id=:positionId AND user_id=:userId',
                'params' => array(':positionId'=>$position['id'],':userId'=>$user_id),
            ));
            $resumeUser = ResumeUser::model()->find(array(
                'condition' => 'position_id=:positionId AND user_id=:userId',
                'params' => array(':positionId'=>$position['id'],':userId'=>$user_id),
            ));
            $concerned = $positionUser?1:0;
            $sended = $resumeUser?1:0;

            $positionContacts = $position->positioncontacts;

            $Array['position']=$position;
            $Array['company']=$companyInfo;
            $Array['concerned']=$concerned;
            $Array['sended']=$sended;
            $Array['positioncontacts']=$positioncontacts;
            // $Array['user_id'] = $user_id;

            print(CJSON::encode($Array));
     
    }


    public function actionSendResume(){
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;
        $user_id = $data->user_id;
        // $id = 11755;
        // $user_id = 3;
        $positionContact = Position::model()->findByPk($id)->positioncontacts;
     

        // 获取简历id
        $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
 
        $basicInfo = UserDetail::model()->getList($user_id);// 获取基本信息
        $gender = $this->Gender($basicInfo->gender);
        $age = $this->AgeNum($basicInfo->birthday);
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
            $lang[$key]->les_say=$this->changeLangueges($value->les_say);
            $lang[$key]->rea_wri=$this->changeLangueges($value->rea_wri);
            //将数据等级转化为中文等级，能更清晰的表示语言的能力
            //将数据等级转化为中文等级，能更清晰的表示语言的能力
        }
        // 获取求职经历信息
        $JobExperience = JobExperience::model()->getList($user_id);
        // 获取项目经历信息
        $ProjectExperience = ProjectExperience::model()->getList($resume_id);
        foreach ($JobExperience as $key => $value) {
            $JobExperience[$key]->type=$this->changeJobGrade($value->type);
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

        foreach ($StudyExperienceInfo as $key => $value) {
            $StudyExperienceContent.='<tr>
                    <td>'.$value->school_name.'</td>
                    <td class="long">'.substr($value->start_time,0,7).'至'.substr($value->end_time, 0,7).'</td>
                    <td>'.$value->position_degree->name.'</td>
                    <td>'.$value->major_name.'</td>
                </tr>';
        }

        foreach ($model as $key => $value) {
            $modelContent.='<p>'.$value->name.'</p>';
        }

        foreach ($schoolDuty as $key => $value) {
            $schoolDutyContent.='<tr>
                <td>'.$value->duty_name.'</td>
                <td class="long">'.substr($value->start_time, 0,7).'至'.substr($value->end_time, 0,7).'</td>
                <td>'.$value->school.'</td>
            </tr>';
        }

        foreach ($schoolAward as $key => $value) {
            $schoolAwardContent.='<tr>
                <td>'.$value->award_name.'</td>
                <td>'.substr($value->award_time, 0,7).'</td>
                <td>'.$value->school.'</td>
            </tr>';
        }

        foreach ($lang as $key => $value) {
            $langContent.='<tr>
                <td>'.$value->name.'</td>
                <td>'.$value->les_say.'</td>
                <td>'.$value->rea_wri.'</td>
                <td class="longer">'.$value->grade.'</td>
            </tr>';
        }

        foreach ($train as $key => $value) {
            $trainContent.='<tr>
                <td class="longer"><{$training.company_name}>'.$value->company_name.'</td>
                <td class="long">'.substr($value->start_time,0,7).'至'.substr($value->end_time,0,7).'</td>
                <td>'.$value->position_name.'</td>
            </tr>';
        }

        foreach ($ItSkill as $key => $value) {
            $ItSkillContent.=$value->name.',';
        }

        foreach ($ProjectExperience as $key => $value) {
            $ProjectExperienceContent.='<tr>
                <td class="longer"><{$project.project_name}>'.$value->project_name.'</td>
                <td class="long">'.substr($value->start_time, 0,7).'至'.substr($value->end_time,0,7).'</td>
                <td>'.$value->project_role.'</td>
            </tr>';
        }

        foreach ($WorkExperience as $key => $value) {
            $WorkExperienceContent.='<tr>
                <td class="longer">'.$value->company_name.'</td>
                <td class="long">'.substr($value->start_time, 0,7).'至'.substr($value->end_time, 0,7).'</td>
                <td>'.$value->position_name.'</td>
            </tr>';
        }

        foreach ($TrainingExperience as $key => $value) {
            $TrainingExperienceContent.='<tr>
                <td class="long">'.$value->training_name.'</td>
                <td class="long">'.substr($value->start_time, 0,7).'至'.substr($value->end_time, 0,7).'</td>
            </tr>';
        }

        foreach ($JobExperience as $key => $value) {
            $JobExperienceContent.='<tr>
                <td class="longer">'.$value->company_name.'</td>
                <td>'.$value->position_name.'</td>
                <td>'.$value->type.'</td>
                <td>'.substr($value->time, 0,7).'</td>
            </tr>';
        }


        $content = '
        <style type="text/css">
            #skill li{
                display:block;text-align:center;
            }
        </style>
        <div class="my-resume">
        <div class="my-resume-header"></div>
        <div class="content">
        <img src='.$basicInfo->head_url.' class="my-face"/>
        <h1>'.$basicInfo->realname.'</h1>
        <div class="detail">'.$gender.'&nbsp;&nbsp;&nbsp;'.$age.'
        </div>
        <ul class="details">
            <li>';

        if ($identify) {
            $content.='<span class="major"><i></i>'.$identify->name.'-'.$identify->value.'</span>';
        }

        $content.='<span class="education"><i></i>'.$name;

        if ($workEx) {
            $content.=$workEx.'年工作经验'.'&nbsp;&nbsp;&nbsp;'.$basicInfo->account_place.'</span></li>';
        }else{
            $content.='应届毕业生';
        }
        
        $content.=
            '<li>
                <span class="phone"><i>手机号：</i>'.$user->phone.'</span>&nbsp;&nbsp;&nbsp;
                <span class="email"><i>邮箱：</i>'.$user->email.'</span>
            </li>
        </ul>
        <!-- 教育经历模块 start -->
        <div class="module">
            <h2 class="module-title"><span>教育经历</span></h2>
            <table>
                <thead>
                <th>毕业院校</th>
                <th>就读时间</th>
                <th>学历</th>
                <th>专业</th>
                </thead>
                <tbody>
                '.$StudyExperienceContent.'
                </tbody>
            </table>
        </div>
        <!-- 教育经历模块 end -->
        <!-- 证书模块 start -->
        <div class="module">
            <h2 class="module-title"><span>证&nbsp;&nbsp;&nbsp;书</span></h2>
            '.$modelContent.'
        </div>
        <!-- 证书模块 end -->';

        
        if ($schoolDutyContent) {
            $content.=
            '<div class="module">
                <h2 class="module-title"><span>校内职务</span></h2>
                <table>
                    <thead>
                    <th>职务名称</th>
                    <th>任职时间</th>
                    <th>学校</th>
                    </thead>
                    <tbody>
                    '.$schoolDutyContent.'
                    </tbody>
                </table>
            </div>
        <!-- 校内职务模块 end -->';
        }
 
        if ($schoolAwardContent) {
            $content.=
            '<!-- 校内奖励模块 start -->
            <div class="module">
                <h2 class="module-title"><span>校内奖励</span></h2>
                <table>
                    <thead>
                    <th>获奖名称</th>
                    <th>获得时间</th>
                    <th>学校</th>
                    </thead>
                    <tbody>
                    '.$schoolAwardContent.'
                    </tbody>
                </table>
            </div>
            <!-- 校内奖励模块 end -->';
        }
       
        if ($langContent) {
            $content.=
            '<!-- 语言能力模块 start -->
            <div class="module">
                <h2 class="module-title"><span>语言能力</span></h2>
                <table>
                    <thead>
                    <th>语言</th>
                    <th>听说</th>
                    <th>读写</th>
                    <th>等级考试</th>
                    </thead>
                    <tbody>
                    '.$langContent.'
                    </tbody>
                </table>
            </div>
            <!-- 语言能力模块 end -->';
        }
        
        if ($trainContent) {
            $content.=
            '<!-- 实习经历模块 start -->
            <div class="module">
                <h2 class="module-title"><span>实习经历</span></h2>
                <table>
                    <thead>
                    <th>工作公司</th>
                    <th>在职时间</th>
                    <th>职位名称</th>
                    </thead>
                    <tbody>
                    '.$trainContent.'
                    </tbody>
                </table>
            </div>
            <!-- 实习经历模块 end -->';
        }
        
        if ($ItSkillContent) {
            $content.=
            '<!-- IT技能模块 start -->
            <div class="module">
                <h2 class="module-title"><span>IT技能</span></h2>

                <ul id="skill">
                    <li>精通的技能：
                                        <span>
                                        '.$ItSkillContent.'
                                        </span>
                    </li>
                    <li>熟悉的技能：<span>'.$ItDetail->detail.'</span>
                    </li>
                </ul>
            </div>
            <!-- IT技能模块 end -->';
        }
        
        if ($ProjectExperienceContent) {
            $content.=
            '<!-- 项目经验模块 start -->
            <div class="module">
                <h2 class="module-title"><span>项目经验</span></h2>
                <table>
                    <thead>
                    <th>项目名称</th>
                    <th>参与时间</th>
                    <th>担任职务</th>
                    </thead>
                    <tbody>
                    '.$ProjectExperienceContent.'
                    </tbody>
                </table>
            </div>
            <!-- 项目经验模块 end -->';
        }
        
        if ($WorkExperienceContent) {
            $content.=
            '<!-- 工作经历模块 start -->
            <div class="module">
                <h2 class="module-title"><span>工作经历</span></h2>
                <table>
                    <thead>
                    <th>工作单位</th>
                    <th>在职时间</th>
                    <th>职位名称</th>
                    </thead>
                    <tbody>
                    '.$WorkExperienceContent.'
                    </tbody>
                </table>
            </div>
            <!-- 工作经历模块 end -->';
        }
        
        if ($TrainingExperienceContent) {
            $content.=
            '<!-- 培训经历模块 start -->
            <div class="module">
                <h2 class="module-title"><span>培训经历</span></h2>
                <table>
                    <thead>
                    <th>培训名称</th>
                    <th>培训时间</th>
                    </thead>
                    <tbody>
                    '.$TrainingExperienceContent.'
                    </tbody>
                </table>
            </div>
            <!-- 培训经历模块 end -->';
        }
        
        if ($JobExperienceContent) {
            $content.=
            '<!-- 求职经历模块 start -->
            <div class="module">
                <h2 class="module-title"><span>求职经历</span></h2>
                <table>
                    <thead>
                    <th>公司名称</th>
                    <th>职位名称</th>
                    <th>类型</th>
                    <th>时间</th>
                    </thead>
                    <tbody>
                    '.$JobExperienceContent.'
                    </tbody>
                </table>
            </div>
            <!-- 求职经历模块 end -->
            </div>
            </div>
            </div>';
        }
        

        $mail = Yii::App()->mail;
        $mail->IsSMTP();
        
        $email = $positionContact->email;
        // $email = 'chenh034@163.com';
 
        if (filter_var($email,FILTER_VALIDATE_EMAIL)) {
            $email_arr = explode("@",$email);
            $len = count($email_arr);
            $domain = $email_arr[$len-1];
            if (checkdnsrr($domain, 'MX')) {
               $mail->AddAddress($email);
            }else{
                $message = array(
                   'code'=>1,
                   'data'=>'简历发送失败'
                );
                print(CJSON::encode($message));
                return;
            }
        }else{
            $message = array(
                   'code'=>1,
                   'data'=>'简历发送失败'
                );
            print(CJSON::encode($message));
            return;
        }

        $mail->Subject = "简历"; //邮件标题


        $mail->Body = $content;

        $mail->MsgHTML($mail->Body);
        $mail->IsHTML(true);
        if ($mail->send()) {
            $ResumeUser = new ResumeUser();
            $ResumeUser->position_id = $id;
            $ResumeUser->user_id = $user_id;
            if ($ResumeUser->save()) {
                $message = array(
                   'code'=>0,
                   'data'=>$user_id.','.$id
                );
            }
            
        }else{
            $message = array(
                   'code'=>1,
                   'data'=>'简历发送失败'
                );
        }

        print(CJSON::encode($message));
    }

    /*********************性别转换的函数********************************/
      public function Gender($data)
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
     
     public function AgeNum($age)
     {          
         return (date('Y-m-d',time())-$age);
     }   

     public function ChangeLangueges($var)
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
    public function ChangeJobGrade($data)
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
	
}