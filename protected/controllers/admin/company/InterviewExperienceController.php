<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/13
 * Time: 17:46
 */

class InterviewExperienceController extends Controller{
    public function actionList(){
        $experienceList  = InterviewExperience::model()->findAll();
        $this->smarty->assign('recordCount',count($experienceList));
        $this->smarty->assign('current','interviewExperience');
        $this->smarty->display('admin/company/interviewExperience/list.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria;
        $list_all = InterviewExperience::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria->order = 'is_ok,addtime DESC';
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= InterviewExperience::model()->findAll($criteria);  //记录分页
        $list2='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }
    public function actionCheck($id){
        $interviewExperience = InterviewExperience::model()->findByPk($id);
        $interviewExperience->is_ok=1;
        $interviewExperience->save();
        $this->actionList();
    }
    public function actionDel($id)
    {
        $experience = InterviewExperience::model()->findByPk($id);
        if (!empty($experience)){
            $experience->delete();
            InterviewExperienceUser::model()->deleteAllByAttributes(array('interview_id'=>$id));
            $this->actionList();
        }
    }
}