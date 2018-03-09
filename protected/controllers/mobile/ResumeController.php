<?php

/**
* 简历部分控制器，包括数据查询，编辑，删除
*/
class ResumeController extends Controller
{
	/*
	*我的简历首页
	*/

	public function filters(){
        // return array('checkAuth');
    }

    public function actionTest(){
    	$User = User::model()->findAllBySql('select * from {{User}} order by id desc limit 5');

    	print_r($User);
    }


	public function actionIndex(){
		$data = json_decode(file_get_contents("php://input"));
		$user_id = $data->user_id;
		$resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;

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
		 $value->les_say=$this->ChangeLangueges($value->les_say);  //将数据等级转化为中文等级，能更清晰的表示语言的能力
		 $value->rea_wri=$this->ChangeLangueges($value->rea_wri);//将数据等级转化为中文等级,能更清晰的表示语言的能力                
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
		$value->type=$this->ChangeJobGrade($value->type);                
		}

		// 获取教育经历
		$StudyExperienceInfo =StudyExperience::model()->with('position_degree')->getList($user_id); 
		foreach ($StudyExperienceInfo as $key => $value) {
			$StudyExperienceInfo[$key]->position_degree_id = $value->position_degree->name;
		}


		$ItDetail = ItSkillsDetail::model()->findByAttributes(array('resume_id'=>$resume_id));
		//获取附件信息
		$file=ResumeAttachment::model()->findAllByAttributes(array('resume_id'=>$resume_id));      
		$school_name = StudyExperience::model()->findAllBySql("select school_name from {{study_experience}} 
		where user_id=".$user_id);       

             
		$current="current";
         


		$array['school'] = $school_name;
		$array['resume'] = $current;
		$array['file'] = $file; //附件信息
		$array['ItDetail'] = $ItDetail;//it的详细信息
		$array['ItSkill'] = $ItSkill;  //it技能的信息
		$array['WorkExperience'] = $WorkExperience;  //工作经历的信息
		$array['train'] = $train;           //实习经历的信息   
		$array['ProjectExperience'] = $ProjectExperience;     //项目经验的信息      
		$array['TrainingExperience'] = $TrainingExperience;  //培训经历的信息 
		$array['JobExperience'] = $JobExperience;      //求职经历的信息
		$array['languages'] = $lang;     //语言能力的信息
		$array['award'] = $schoolAward; //校内奖励的信息
		$array['school'] = $schoolDuty; //校内职务的信息
		$array['model'] = $model;  //证书的信息
		$array['ResumeInfo'] = $ResumeInfo;  // 获取简历信息
		$array['StudyExperienceInfo'] = $StudyExperienceInfo;  //教育经历的信息
		$array['contact'] = $user;       //手机与邮箱的信息
		$array['workEx'] = $workEx;  //工作经验年数的信息
		$array['basicInfo'] =  $basicInfo;  //用户的基本信息信息             
		
		$array = CJSON::encode($array);
		print($array);
	}
   

    /*
    *
    */
	public function DayId($year,$month){
        
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
   
    /*
    *
    */
    public function ChangeYear($data){
        $data=$data+5;
        for($i = 1980;$i<=$data;$i++){
            $arr[]=$i;
        }
        return $arr;
    }
    

    /*
    *
    */
    public function DataArray($dataOne,$dataTwo,$num,$array){

        if($dataOne||$dataTwo)
        {
             $array[]=$num;
        }         
        return $array;
    }

    public function LookProvinceToId($data){
        $province_id = City::model()->findByAttributes(array('id'=>$data))->province_id;
        $cityName = City::model()->findAllByAttributes(array('province_id'=>$province_id));
        return $cityName;
    }

    public function LookProvinceToName($data){
        $province_id = City::model()->findByAttributes(array('name'=>$data))->province_id;;
        $cityName = City::model()->findAllByAttributes(array('province_id'=>$province_id));
        return $cityName;
    }

    /*****在语言能力那里的一个听写等级的转换函数************************************/
    public function ChangeLangueges($var){
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
