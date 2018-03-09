<?php
/**
 * Created by PhpStorm.
 * User: shihao
 * Date: 17-7-20
 * Time: 上午10:55
 */

class RecordController extends Controller
{
    //邀约记录页初始化
    public function actionIndex(){
        //直接把总数渲染到前端，防止获取两遍数据
        $cri = new CDbCriteria();
        $cri->with = array('userdetail', 'studyexperience', 'inviterecord');
        $cri->select = 'user_id';
        $conditions = " 1=1 and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
        $params = array();
        $companyId=Yii::app()->session['company_id'];
        $conditions .= " and inviterecord.company_id =:companyId";
        $params[':companyId'] = $companyId;
        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->distinct = true;
        $recordCount = Resume::model()->count($cri);

        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->display('recruitEntrance/talentInvitation/talentInvitation-invitationRecord.html');
    }

    //邀约记录查询
    public function actionListJson(){
        //获取数据
        if(isset($_GET['currentPage'])){
            $page=$_GET['currentPage'];
        } else{
            $page=0;
        }
        if(isset($_GET['status'])&&$_GET['status']!=0){
            $status=$_GET['status'];
        }else{
            $status=0;
        }
        $companyId=Yii::app()->session['company_id'];
        //测试用数据
        //$companyId = 61;

        // 数据查询
        $cri = new CDbCriteria();
        $cri->limit = 8;
        $cri->offset = ($page-1)*8;
            $cri->with = array('userdetail', 'studyexperience', 'inviterecord');
            $cri->select = 'user_id';
            $conditions = "1=1 ";
            $params = array();
            if ($status != 0 && $status != "0") {
                $conditions .= " and inviterecord.status =:status ";
                $params[':status'] = $status;
            }
            $conditions .= " and inviterecord.company_id =:companyId";
            $params[':companyId'] = $companyId;
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition = $conditions;
            $cri->params = $params;
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $recordCount = Resume::model()->count($cri);
            $data = Resume::model()->findAll($cri);


        // 重组不同表的数据
        foreach ($data as $key => $value) {
            if (isset($value->userdetail->realname))
                $graduate[$key]['realname'] = $value->userdetail->realname;
            else
                $graduate[$key]['realname'] = "无";
            if (isset($value->userdetail->city_id)) {
                $graduate[$key]['account_place'] = City::model()->findByPk($value->userdetail->city_id)->name;
            } else
                $graduate[$key]['account_place'] = "无";
            if (isset($value->studyexperience->position_degree_id)) {
                $degree = Degree::model()->findByPk($value->studyexperience->position_degree_id);
                $graduate[$key]['degree'] = $degree->name;
            } else
                $graduate[$key]['degree'] = "无";
            if (isset($value->userdetail->head_url))
                $graduate[$key]['head_url'] = $value->userdetail->head_url;
            else
                $graduate[$key]['head_url'] = "无";
            if (isset($value->user_id))
                $graduate[$key]['user_id'] = $value->user_id;
            else
                $graduate[$key]['user_id'] = "无";
            if (isset($value->studyexperience->major_name))
                $graduate[$key]['major_name'] = $value->studyexperience->major_name;
            else
                $graduate[$key]['major_name'] = "无";
            if (isset($value->studyexperience->school_name))
                $graduate[$key]['school_name'] = $value->studyexperience->school_name;
            else
                $graduate[$key]['school_name'] = "无";
            if(isset($value->studyexperience->end_time))
                $graduate[$key]['year'] = substr($value->studyexperience->end_time, 0, 4);
            else
                $graduate[$key]['year'] = "无";
            if (isset($value->inviterecord->created_time))
                $graduate[$key]['created_time'] = substr($value->inviterecord->created_time,0,10);
            else
                $graduate[$key]['created_time'] = "无";
        }
        $json = CJSON::encode($graduate);
        if($recordCount==0) $json=CJSON::encode("0");
        $graduateJson='[{"code":0,"data":'.$json.',"dataCount":"'.$recordCount.'"}]';
        print  $graduateJson;
    }

    public function actionRemove(){
        $user_id=$_GET['user_id'];
        $companyId=Yii::app()->session['company_id'];
        //测试用数据
        //$companyId = 61;
        $record = "select * from {{invite_record}} where user_id='".$user_id."' and company_id='".$companyId."'";
        $inviteRecord=InviteRecord::model()->findBySql($record);
        $inviteRecord->delete();
        //设计返回一个成功代码
        $list = '[{"code":0,"data":""}]';
        print $list;
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