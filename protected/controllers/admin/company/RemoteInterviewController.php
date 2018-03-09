<?php
/**
 * Created by PhpStorm.
 * User: lixiang
 * Date: 2015/7/22
 * Time: 17:04
 */

class RemoteInterviewController extends Controller{
    public function actionCreate($id){
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('current','company');
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->display('admin/company/remoteInterview/create.html');
    }
    public function actionAdd(){
        $remote = new RemoteInterview();
        $companyId = $_POST['companyId'];
        $remote->company_id = $companyId;
        $remote->theme = $_POST['theme'];                                              //接受Post过来的值
        $remote->day = $_POST['day'];
        $remote->start_time = $_POST['startHour'].':'.$_POST['startMinute'];
        $remote->end_time = $_POST['endHour'].':'.$_POST['endMinute'];
        $remote->place = $_POST['place'];
        $remote->describe = $_POST['describe'];
        $remote->last_update = Yii::app()->session['user_name'];
        $remote->url = $_POST['url'];
        $remote->save();
        $this->redirect($this->createUrl("admin/company/remoteInterview/listByCompany/id/".$companyId));
    }

    public function actionEdit($id){                                               //编辑功能
        $remote = RemoteInterview::model()->findByPk($id);
        $remote->theme=$_POST['theme'];
        $remote->day=$_POST['day'];
        $remote->start_time = $_POST['startHour'].':'.$_POST['startMinute'];
        $remote->end_time = $_POST['endHour'].':'.$_POST['endMinute'];
        $remote->place=$_POST['place'];
        $remote->describe=$_POST['describe'];
        $remote->last_update = Yii::app()->session['user_name'];
        $remote->url = $_POST['url'];
        $remote->save();
        $this->redirect($this->createUrl("admin/company/remoteInterview/listByCompany/id/".$remote->company_id));
    }
    public function actionToEdit($id){                                             //跳转到Edit页面
        $remote = RemoteInterview::model()->findByPk($id);
        $start = $remote->start_time;
        $end = $remote->end_time;
        $startList = explode(":",$start);
        $endList = explode(":",$end);
        $startHour =$startList[0];
        $startMinute =$startList[1];
        $endHour = $endList[0];
        $endMinute = $endList[1];
        $this->smarty->assign('startHour',$startHour);
        $this->smarty->assign('startMinute',$startMinute);
        $this->smarty->assign('endHour',$endHour);
        $this->smarty->assign('endMinute',$endMinute);
        $this->smarty->assign('remote',$remote);
        $this->smarty->assign('current','company');
        $this->smarty->display('admin/company/remoteInterview/editnew.html');
    }


    public function actionDel($id,$comId){
        $re = RemoteInterview::model()->findByPk($id);
        if(!empty($re)){
            $re->delete();
        }
        RemoteInterviewUser::model()->deleteAllByAttributes(array('interview_id'=>$id));
        $this->redirect($this->createUrl("admin/company/remoteInterview/listByCompany/id/".$comId));
    }
    public function actionListByCompany($id){
        $list = RemoteInterview::model()->findAllByAttributes(array('company_id'=>$id));
        $recordCount = count($list);
        $this->smarty->assign('recordCount',$recordCount);
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('current','company');
        $this->smarty->display('admin/company/remoteInterview/listPageNew.html');
    }
    public function actionJsonByCompany(){
        $page = $_GET['page1'];
        $companyId = $_GET['companyId'];
        $criteria = new CDbCriteria();
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$companyId));
        $list_all = RemoteInterview::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $data= RemoteInterview::model()->findAll($criteria);  //记录分页
        $list3='{"list":'.CJSON::encode($data).',"dataCount":"'.$recordCount.'"}';
        print $list3;
    }

    public function  actionDetail($id){
        $remoteInterview = RemoteInterview::model()->findByPk($id);
        $this->smarty->assign('remote',$remoteInterview);
        $this->smarty->assign('current','company');
        $this->smarty->display('admin/company/remoteInterview/detail.html');
    }
}