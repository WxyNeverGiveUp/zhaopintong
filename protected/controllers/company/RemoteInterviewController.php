<?php
/**
 * Created by PhpStorm.
 * User: lixiang
 * Date: 2015/7/22
 * Time: 17:04
 */

class RemoteInterviewController extends Controller{
    public function actionList($id){
        $this->smarty->assign('companyId',$id);
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
        $this->smarty->assign('remote',$current);
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
        $this->smarty->display('company/remoteInterview/remote-interview-new.html');
    }
    public function actionJson($id){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria();
        $criteria->condition='company_id=:companyId';
        $criteria->params=array(':companyId'=>$id);
        $list_all = RemoteInterview::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $data= RemoteInterview::model()->with('enroll')->findAll($criteria);  //记录分页
        //$data = Position::model()->with('collection')->findAll($cri);
        foreach ($data as $key => $value) {
            if (isset($value->id))
                $interview[$key]['id'] = $value->id;
            else
                $interview[$key]['id'] = "无";
            if (isset($value->day)) {
                $month = date('m', strtotime($value->day));
                $day = date('d', strtotime($value->day));
                $weekArray = array("日", "一", "二", "三", "四", "五", "六");
                $week = "周" . $weekArray[date("w", strtotime($value->day))];
                $interview[$key]['month'] = $month;
                $interview[$key]['date'] = $day;
                $interview[$key]['week'] = $week;
            } else {
                $interview[$key]['month'] = "无";
                $interview[$key]['date'] = "无";
                $interview[$key]['week'] = "无";
            }
            if (isset($value->start_time) && isset($value->end_time))
                $interview[$key]['hour'] = $value->start_time . '-' . $value->end_time;
            else
                $interview[$key]['hour'] = "无";
            if (isset($value->theme))
                $interview[$key]['remoteInfo'] = $value->theme;
            else
                $interview[$key]['remoteInfo'] = "无";
            if (isset($value->place))
                $interview[$key]['location'] = $value->place;
            else
                $interview[$key]['location'] = "无";
            if (!empty($value->enroll))
                $interview[$key]['isEnroll'] = 1;
            else
                $interview[$key]['isEnroll'] = 0;
            if (isset($value->day) && isset($value->start_time)) {
               $monthDay = $value->day . ' ' . $value->start_time;
               if (strtotime($monthDay) < strtotime(date('Y-m-d H:i', time())))
                $interview[$key]['isOverdue'] = 1;
               else
                   $interview[$key]['isOverdue'] = 0;
            }
            else
                $interview[$key]['location'] = "无";
        }
        $listJson='{"code":0,"data":'.CJSON::encode($interview).',"dataCount":"'.$recordCount.'"}';
        print $listJson;
    }

    public function actionEnroll(){
        $remoteId = $_GET['remoteId'];
        $isEnroll = $_GET['isEnroll'];
        $isEnter = $_GET['isEnter'];
        $re = RemoteInterview::model()->findByPk($remoteId);
        if($re==null){
            //$this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $reUser = RemoteInterviewUser::model()->find(array(
            'condition' => 'interview_id=:interviewId AND user_id=:userId',
            'params' => array(':interviewId'=>$remoteId,':userId'=>$userId),
        ));

        if($isEnroll==1) {
            $interviewUser = new RemoteInterviewUser();
            $interviewUser->interview_id= $remoteId;
            $interviewUser->user_id = $userId;
            $interviewUser->save();
        }
        elseif ($isEnter==1) {
            $interviewUser = new RemoteInterviewUser();
            $interviewUser->interview_id= $remoteId;
            $interviewUser->user_id = $userId;
            $interviewUser->save();
        }
        else{
            $reUser->delete();
        }
        $list='{"code":0,"data":""}';
        print $list;
    }

    public function  actionDetail($id){
        $company = Company::model()->findByPk(RemoteInterview::model()->findByPk($id)->company_id);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$company->id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$company->id),
        ));
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $remoteInterview = RemoteInterview::model()->findByPk($id);
        if(strtotime(date("Y-m-d",time()))>strtotime($remoteInterview->day)){
            $this->smarty->assign('isOverdue',1);
        }
        else{
            $this->smarty->assign('isOverdue',0);
        }
        $this->smarty->assign('remoteInterview',$remoteInterview);
        $remoteInterviewUsers = RemoteInterviewUser::model()->findAllByAttributes(array('interview_id'=>$id));
        $number = count($remoteInterviewUsers);
        $this->smarty->assign('remoteInterviewUsers',$remoteInterviewUsers);
        $this->smarty->assign('number',$number);
        $current="current";
        $this->smarty->assign('remote',$current);
        $remoteInterviewUser = RemoteInterviewUser::model()->find(array(
            'condition' => 'interview_id=:interviewId AND user_id=:userId',
            'params' => array(':interviewId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $remoteInterviewUser?1:0;
        $this->smarty->assign('concerned',$concerned);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$company->id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$company->id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/remoteInterview/remote-detail.html');
    }

    public function  actionJsonList($id){
        $remoteInterviewUsers = RemoteInterviewUser::model()->with('userdetail')->findAllByAttributes(array('interview_id'=>$id));
        foreach ($remoteInterviewUsers as $key => $value) {
            if (isset($value->user_id))
                $reInterviewUsers[$key]['userId'] = $value->user_id;
            else
                $reInterviewUsers[$key]['userId'] = "无";
            if (isset($value->userdetail->head_url))
                $reInterviewUsers[$key]['head_url'] = $value->userdetail->head_url;
            else
                $reInterviewUsers[$key]['head_url'] = "无";
        }
        $listJson='{"code":0,"data":'.CJSON::encode($reInterviewUsers).',"dataCount":"'.count($reInterviewUsers).'"}';
        print $listJson;
    }

    public function actionIsRightTime($id){
        if(!isset(yii::app()->session['user_id']))
            $listJson='{"code":1}';
        else{
            $remote = RemoteInterview::model()->findByPk($id);
            /*$day = $remote->day;
            $startTime = $remote->start_time;
            if(date('Y-m-d',strtotime($day))>date('Y-m-d',time()))
                $listJson='{"code":2,"data":'.'"'.date('Y-m-d H:i',strtotime($startTime)-900).'"}';
            elseif(date('Y-m-d',strtotime($day))==date('Y-m-d',time())){
                if(date('H:i',strtotime($startTime)-900)>date('H:i',time()))
                    $listJson = '{"code":2,"data":'.'"'.date('Y-m-d H:i',strtotime($startTime)-900).'"}';
                else
                    $listJson = '{"code":0,"data":'.'"'.$remote->url.'"}';
            }
            else*/
                $listJson = '{"code":0,"data":'.'"'.$remote->url.'"}';
        }
        print $listJson;
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + enroll')
        );
    }
}