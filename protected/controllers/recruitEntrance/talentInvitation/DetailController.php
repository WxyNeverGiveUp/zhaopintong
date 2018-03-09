<?php
/**
 * Created by PhpStorm.
 * User: shihao
 * Date: 17-7-25
 * Time: 下午7:44
 */

class DetailController extends Controller
{
    public function actionDetail($user_id)
    {
        //$user_id = $_GET['id'];
        // 获取联系方式
        $ContactInfo = $this->actionGetContactInfo($user_id);
        if($ContactInfo['cellphone']==null&&$ContactInfo['phone']==null&&$ContactInfo['qq']==null&&$ContactInfo['email']==null&&$ContactInfo['wechat']==null){
            $ContactInfo=null;
        }

        // 获取基本信息
        $BasicInfo = UserDetail::model()->getList($user_id);
        // 现居地信息
        $live=$BasicInfo->current_live;
        if($live=='0')
        {
            $live=null;
        }

        // 获取简历信息
        $ResumeInfo = Resume::model()->with('degree')->getList($user_id);
        // 判断该用户是否有简历内容
        if ($ResumeInfo) {
            $resume_id = $ResumeInfo->id;
            // 获取实习经历信息
            $WorkExperienceInfo = WorkExperience::model()->getList($resume_id);
            // 获取项目经历信息
            $projectExperienceInfo = ProjectExperience::model()->getList($resume_id);
            // 获取获奖（证书或荣誉）信息
            $schoolAwardsInfo = SchoolAwards::model()->getList($resume_id);
            // 获取校内职务信息
            $schoolDutyInfo = SchoolDuty::model()->getList($resume_id);
            // 获取语言能力信息
            $languagesAbilityInfo = LanguagesAbility::model()->with('language_type')->getList($resume_id);
            // 获取IT技能信息
            $itSkillInfo = ItSkill::model()->findAllByAttributes(array('resume_id'=>$resume_id));
            // 获取培训经历信息
            $trainingExperienceInfo = TrainingExperience::model()->getList($resume_id);
            // 获取附件
            $attachmentInfo = ResumeAttachment::model()->findAllByAttributes(array('resume_id'=>$resume_id));
        }else{
            $resume_id = null;
            $WorkExperienceInfo = "";
            $schoolAwardsInfo = "";
            $schoolDutyInfo = "";
            $languagesAbilityInfo = "";
            $itSkillInfo = "";
            $trainingExperienceInfo = "";
            $projectExperienceInfo = "";
            $attachmentInfo = "";
        }

        // 获取求职经历信息
        $jobExperienceInfo = JobExperience::model()->getList($user_id);

        // 获取教育经历信息
        $StudyExperienceInfo=StudyExperience::model()->with('position_degree')->getList($user_id);

        // 获取在校学历或工作职位
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

        // 获取学历
        $sql="select max(position_degree_id) as num from {{study_experience}} where user_id='".$user_id."'";
        $result=StudyExperience::model()->findBySql($sql);
        $degree=Degree::model()->findByAttributes(array('id'=>$result->num))->name;

        // 获取专长
        $sql = "select  special_performance from {{user_special_performance}}
        where user_id ='".$user_id."'";
        $special =UserSpecialPerformance::model()->findAllBySql($sql);
        $this->smarty->assign('live',$live);
        $this->smarty->assign('identify',$identify);
        $this->smarty->assign('degree',$degree);
        $this->smarty->assign('special',$special);
        $this->smarty->assign('user_id',$user_id);
        $this->smarty->assign('resume_id',$resume_id);
        $this->smarty->assign('BasicInfo', $BasicInfo);
        $this->smarty->assign('ResumeInfo',$ResumeInfo);
        $this->smarty->assign('ContactInfo',$ContactInfo);
        $this->smarty->assign('WorkExperienceInfo',$WorkExperienceInfo);
        $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);
//        $this->smarty->assign('isActivated',User::model()->findByPk($user_id)->is_activated);
//        $this->smarty->assign('isLeague',User::model()->findByPk($user_id)->is_league);
        $this->smarty->assign('schoolAwardsInfo',$schoolAwardsInfo);
        $this->smarty->assign('schoolDutyInfo',$schoolDutyInfo);
        $this->smarty->assign('languagesAbilityInfo',$languagesAbilityInfo);
        $this->smarty->assign('itSkillInfo',$itSkillInfo);
        $this->smarty->assign('trainingExperienceInfo',$trainingExperienceInfo);
        $this->smarty->assign('projectExperienceInfo',$projectExperienceInfo);
        $this->smarty->assign('attachmentInfo',$attachmentInfo);
        $this->smarty->assign('jobExperienceInfo',$jobExperienceInfo);
        $this->smarty->display('recruitEntrance/talentInvitation/talentInvitation-viewResume.html');

    }

    public function actionGetContactInfo($user_id){
        $type = User::model()->findByAttributes(array('id'=>$user_id));
        $arr['cellphone'] = $type->cellphone;
        $arr['phone'] = $type->phone;
        $arr['qq'] = $type->qq;
        $arr['wechat'] = $type->wechat;
        $arr['email'] = $type->email;
        return $arr;
    }

    public function filters()
    {
        return array(
            array(
                'application.filters.RecruitmentFilter',
            ),
        );
    }
}