<?php

/**
 * Created by PhpStorm.
 * User: shihao
 * Date: 17-7-19
 * Time: 上午11:05
 */
class DiscoverController extends Controller
{
    //发现人才页面初始化
    public function actionIndex(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        //直接把总数渲染到前端，防止获取两遍数据
        $cri = new CDbCriteria();
        $cri->with = array('userdetail', 'studyexperience');
        $cri->select = 'user_id';
        $conditions = " 1=1 and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
        $cri->condition = $conditions;
        $cri->distinct = true;
        $recordCount = Resume::model()->count($cri);

        $majorList  = CacheService::getInstance()->allStudySpecialty();
        $degreeList = CacheService::getInstance()->degree();
        $cityList = CacheService::getInstance()->allCity();
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('majorList', $majorList);
        $this->smarty->assign('cityList',$cityList);
        $currentYear = intval(date('Y',time()));
        $this->smarty->assign('currentYear', $currentYear);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->display('recruitEntrance/talentInvitation/talentInvitation-main.html');
    }

    //列出按条件查询的结果
    public function actionListJson(){
        //条件获取
        if(isset($_GET['currentPage'])){
            $page=$_GET['currentPage'];
        } else{
            $page=0;
        }
        if(isset($_GET['degreeId'])&&$_GET['degreeId']!=-1){
            $degreeId=$_GET['degreeId'];
        } else{
            $degreeId=0;
        }
        if(isset($_GET['year'])&&$_GET['year']!=-1){
            $year=$_GET['year'];
        } else{
            $year=0;
        }
        if(isset($_GET['schoolName'])&&trim($_GET['schoolName'])!=""){
            $schoolName=trim($_GET['schoolName']);
        } else{
            $schoolName='0';
        }
        if(isset($_GET['majorId'])&&$_GET['majorId']!=-1){
            $majorId=$_GET['majorId'];
        } else{
            $majorId='0';
        }
        if(isset($_GET['locationId'])&&$_GET['locationId']!=-1){
            $locationId=$_GET['locationId'];
        } else{
            $locationId='0';
        }
        if(isset($_GET['keyword'])&&trim($_GET['keyword'])!=""){
            $keyword=$_GET['keyword'];
        }else{
            $keyword='0';
        }
        //数据传输测试
//        var_dump($page);
//        var_dump($degreeId);
//        var_dump($year);
//        var_dump($schoolName);
//        var_dump($majorId);
//        var_dump($locationId);

        // 数据查询
        $cri = new CDbCriteria();
        $cri->limit = 8;
        $cri->offset = ($page-1)*8;
            $cri->with = array('userdetail', 'studyexperience');
            $cri->select = 'user_id';
            $conditions = "1=1 ";
            $params = array();
            if ($degreeId != 0 && $degreeId != "0") {
                $conditions .= " and studyexperience.position_degree_id =:degreeId ";
                $params[':degreeId'] = $degreeId;
            }
            if ($year != 0 && $year != "0") {
                $conditions .= " and studyexperience.end_time LIKE :year ";
                $params[':year'] = '%' . $year . '%';
            }
            if ($schoolName != '0'&&$schoolName != "") {
                    $conditions .= " and studyexperience.school_name LIKE :schoolId ";
                    //$schoolName=trim($schoolName);
                    $params[':schoolId'] = '%' . $schoolName . '%';
            }
            if ($majorId != "0") {
                $conditions .= " and studyexperience.study_specialty_id=:majorId ";
                $params[':majorId'] = $majorId;
            }
            if ($locationId != "0") {
                $conditions .= " and userdetail.city_id=:locationId";
                $params[':locationId'] = $locationId;
                //$params[':locationName1']=$locationName;
                //$params[':locationName0']='%'.substr($locationName,0,-3).'%';
            }
            if($keyword != '0'){
                $conditions .=" and userdetail.realname LIKE :keyword";
                $params[':keyword']= '%' . $keyword . '%';
            }
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition = $conditions;
            $cri->params = $params;
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $cri->distinct = true;
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
            if (isset($value->user_id)) {
                $graduate[$key]['user_id'] = $value->user_id;
                $t_userId=$value->user_id;
            }
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
            if (isset($value->userdetail->intent_city_id))
                $graduate[$key]['intent_city'] = City::model()->findByPk($value->userdetail->intent_city_id)->name;
            else
                $graduate[$key]['intent_city'] = "无";
            if(isset($value->userdetail->intent_salary)) {
                switch ($value->userdetail->intent_salary){
                    case 1:
                        $graduate[$key]['intent_salary']="3000左右及以下";
                        break;
                    case 2:
                        $graduate[$key]['intent_salary']="4000左右";
                        break;
                    case 3:
                        $graduate[$key]['intent_salary']="5000左右";
                        break;
                    case 4:
                        $graduate[$key]['intent_salary']="6000左右";
                        break;
                    case 5:
                        $graduate[$key]['intent_salary']="7000左右及以下";
                        break;
                    default:
                        break;
                }
            }
            else
                $graduate[$key]['intent_salary']="无";
            if(isset($value->userdetail->intent_require)){
                if($value->userdetail->intent_require == 1)
                    $graduate[$key]['intent_require']="本专业相关";
                else
                    $graduate[$key]['intent_require']="均可";
            }
            else
                $graduate[$key]['intent_require']="无";

            $companyId=Yii::app()->session['company_id'];
            $record = "select * from {{invite_record}} where user_id='".$t_userId."' and company_id='".$companyId."'";
            $inviteRecord=InviteRecord::model()->findBySql($record);
            if($inviteRecord==null){
                $graduate[$key]['is_invited']=0;
            }else{
                $graduate[$key]['is_invited']=1;
            }
        }
        $json = CJSON::encode($graduate);
        if($recordCount==0) $json=CJSON::encode("0");
        $graduateJson='[{"code":0,"data":'.$json.',"dataCount":"'.$recordCount.'"}]';
        print   $graduateJson;
    }

    //邀请投递
    public function actionInvite(){
        $user_id=$_GET['user_id'];
        $companyId=Yii::app()->session['company_id'];
        //测试用数据
        //$companyId = 61;
        $record = "select * from {{invite_record}} where user_id='".$user_id."' and company_id='".$companyId."'";
        $inviteRecord=InviteRecord::model()->findBySql($record);
        if($inviteRecord==null) {
            $inviteRecordOne = new InviteRecord();
            $inviteRecordOne->user_id = $user_id;
            $inviteRecordOne->company_id = $companyId;
            $inviteRecordOne->status = 1;
            $inviteRecordOne->created_time = date("Y-m-d", time());
            $inviteRecordOne->last_modified_time = date("Y-m-d", time());
            $inviteRecordOne->save();
            //此返回一个成功代码
            $list = '[{"code":0,"data":""}]';
            print $list;
        }else{
            //返回一个失败代码，代表该学生已被邀请
            $list = '[{"code":1,"data":""}]';
            print $list;
        }

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
