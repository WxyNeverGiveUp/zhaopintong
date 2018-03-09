<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/13
 * Time: 17:46
 */

class InterviewExperienceController extends Controller{
    public function actionList($id){
        $company = Company::model()->findByPk($id);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $criteria = new CDbCriteria;
        $criteria->condition='company_id=:companyId AND is_ok=1';
        $criteria->params=array(':companyId'=>$id);
        $count = InterviewExperience::model()->count($criteria);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('count',$count);
        $current="current";
        $this->smarty->assign('interviewExperience',$current);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/interviewExperience/experience-new.html');
    }

    public function actionJson($id){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria;
        $criteria->condition='company_id=:companyId AND is_ok=1';
        $criteria->params=array(':companyId'=>$id);
        $list_all = InterviewExperience::model()->findAll($criteria);
        $pageSize = 5;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $criteria->with = array('userdetail','ispraise','praiseCount');
        $list= InterviewExperience::model()->findAll($criteria);
        // 重组不同表的数据
        foreach ($list as $key => $value) {
            if(isset($value->id))
                $interviewExperience[$key]['experienceId'] = $value->id;
            else
                $interviewExperience[$key]['experienceId'] = "无";
            if(isset($value->userdetail->head_url))
                $interviewExperience[$key]['imgLinks'] = $value->userdetail->head_url;
            else
                $interviewExperience[$key]['imgLinks'] = "无";
            if(isset($value->is_public)){
                if($value->is_public==0)
                    $interviewExperience[$key]['name'] = '匿名用户';
                else
                $interviewExperience[$key]['name'] = $value->userdetail->realname;
            }
            else
                $interviewExperience[$key]['name'] = "无";
            if(isset($value->city))
                $interviewExperience[$key]['location'] = $value->city;
            else
                $interviewExperience[$key]['location'] = "无";
            if(isset($value->interview_position))
                $interviewExperience[$key]['position'] = $value->interview_position;
            else
                $interviewExperience[$key]['position'] = "无";
            if(isset($value->is_getjob))
                $interviewExperience[$key]['isAccept'] = $value->is_getjob;
            else
                $interviewExperience[$key]['isAccept'] = "无";
            if(!empty($value->ispraise))
                $interviewExperience[$key]['isPraise'] = 1;
            else
                $interviewExperience[$key]['isPraise'] = 0;
            if(isset($value->interview_date))
                $interviewExperience[$key]['interviewTime'] = $value->interview_date;
            else
                $interviewExperience[$key]['interviewTime'] = "无";
            if(isset($value->interview_round))
                $interviewExperience[$key]['interviewRounds'] = $value->interview_round;
            else
                $interviewExperience[$key]['interviewRounds'] = "无";
            if(isset($value->description))
                $interviewExperience[$key]['experience'] = $value->description;
            else
                $interviewExperience[$key]['experience'] = "无";
            if(isset($value->addtime))
                $interviewExperience[$key]['shareTime'] = $value->addtime;
            else
                $interviewExperience[$key]['shareTime'] = "无";
            if(isset($value->praiseCount))
                $interviewExperience[$key]['praiseNum'] = $value->praiseCount;
            else
                $interviewExperience[$key]['praiseNum'] = "无";
        }
        if($_GET['sortId']==1&&$interviewExperience!=null) {
            usort($interviewExperience, function ($a, $b) {
                return $b['praiseNum'] - $a['praiseNum'];
            });
        }
        $listJson='{"data":'.CJSON::encode($interviewExperience).',"dataCount":"'.$recordCount.'"}';
        print $listJson;
    }

    public function actionCreate($id){
        $company = Company::model()->findByPk($id);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $current="current";
        $this->smarty->assign('interviewExperience',$current);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/interviewExperience/share-new.html');
    }

    public function actionAdd($id,$is_public=0){
        $experience = new InterviewExperience();
        $experience->interview_position = $_POST['interviewPosition'];
        //TODO:cityId未设置
        $experience->city = $_POST['city'];
        //$experience->interview_date = date("Y-m-d H:i:s",strtotime($_POST['interviewDate']));
        $years = $_POST['years'];
        $months = $_POST['months'];
        $days= $_POST['days'];
        if($months<10)
            $months = '0'.$months;
        if($days<10)
            $days = '0'.$days;
        $experience->interview_date = date("Y-m-d",strtotime($years.'-'.$months.'-'.$days));
        $experience->interview_round = $_POST['rounds'];
        $experience->description = $_POST['description'];
        $experience->is_getjob = $_POST['isGetjob'];
        $experience->addtime = date('Y-m-d H:i:s',time());
        $is_public = 1;
        $experience->is_public = $is_public;
        $experience->company_id = $id;
        //TODO:从session获取、验证；
        $experience->user_id = Yii::app()->session['user_id'];
        $experience->save();
        $this->redirect($this->createUrl("company/interviewExperience/list/id/".$experience->company_id));
    }

    public function actionToEdit($id){
        $experience = InterviewExperience::model()->findByPk($id);
        $company = Company::model()->findByPk($experience->company_id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('experience', $experience);
        $this->smarty->display('interviewExperience/edit.html');
    }

    public function actionEdit($id){
        $experience = InterviewExperience::model()->findByPk($id);
        $experience->interview_position = $_POST['interviewPosition'];
        $experience->city_id = $_POST['city'];
        $experience->interview_date = $_POST['interviewDate'];
        $experience->interview_round = $_POST['interviewRound'];
        $experience->description = $_POST['description'];
        $experience->is_getjob = $_POST['isGetjob'];
        $experience->is_public = $_POST['isPublic'];

        $experience->save();
        $this->redirect($this->createUrl("company/interviewExperience/list/id/".$experience->company_id));
    }

    public function actionDel($id){
        $experience = InterviewExperience::model()->findByPk($id);
        $companyId = $experience->company_id;
        if(!empty($experience))
            $experience->delete();
        $this->redirect($this->createUrl("company/interviewExperience/list/id/".$companyId));
    }

    public function actionPraise($experienceId,$isPraise){
        $re = InterviewExperience::model()->findByPk($experienceId);
        if($re==null){
            //$this->actionList($experienceId);
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $reUser = InterviewExperienceUser::model()->find(array(
            'condition' => 'interview_id=:interviewId AND user_id=:userId',
            'params' => array(':interviewId'=>$experienceId,':userId'=>$userId),
        ));

        if($isPraise==1) {
            $interviewUser = new InterviewExperienceUser();
            $interviewUser->interview_id= $experienceId;
            $interviewUser->user_id = $userId;
            $interviewUser->save();
        }
        else{
            $reUser->delete();
        }
        $number = InterviewExperienceUser::model()->count(array(
            'condition' => 'interview_id=:inId',
            'params' => array(':inId'=>$experienceId),
        ));
        $listJson='{"code":"0","data":{"praiseNum":"'.$number.'"}}';
        print $listJson;
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + create,add,praise')
        );
    }
}