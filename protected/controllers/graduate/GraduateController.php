<?php
/**
 *
 */
class GraduateController extends Controller
{
    // 毕业生首页展示
    public function actionIndex(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        $majorList  = CacheService::getInstance()->allStudySpecialty();
        $degreeList = CacheService::getInstance()->degree();
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('majorList', $majorList);
        $currentYear = intval(date('Y',time()));
        $this->smarty->assign('currentYear', $currentYear);
        $this->smarty->display('graduate/index.html');
    }


    public function actionListJson($page=0,$degreeId=0,$year=0,$schoolName='0',$majorId='0',$locationId='0'){
        $cri = new CDbCriteria();
        $cri->limit = 10;
        $cri->offset = ($page-1)*10;
        // 数据查询
        if (isset($_GET['searchWord'])&&$_GET['searchWord']!='') {
            // 联合搜索
            $search1 = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('t_user_detail')
                ->where(array('like','realname',array('%'.$_GET['searchWord'].'%')))
                ->getText();

            $search2 = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('t_resume')
                ->where(array('like','major_name',array('%'.$_GET['searchWord'].'%')))
                ->getText();

            $search3 = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('t_study_experience')
                ->where(array('like','school_name',array('%'.$_GET['searchWord'].'%')))
                ->getText();

            $search4 = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('t_user_detail')
                ->where(array('like','account_place',array('%'.$_GET['searchWord'].'%')))
                ->union($search1)
                ->union($search2)
                ->union($search3)
                ->queryAll();

            if ($search4) {
                // 符合搜索条件的用户id
                foreach ($search4 as $key => $value) {
                    $user_id[] = $value['user_id'];
                }
               // $cri->addInCondition('t.user_id',$user_id);
            }

            $cri->with = array('userdetail','studyexperience');
            $cri->select = 'user_id,position_degree_id';
            $conditions = "1=1 ";
            $params = array();
            if ($degreeId != 0 && $degreeId!="0"){
                $conditions .= " and studyexperience.position_degree_id =:degreeId ";
                $params[':degreeId']=$degreeId;
            }
            if ($year != 0 && $year!="0"){
                $conditions .= " and studyexperience.end_time LIKE :year ";
                $params[':year']='%'.$year.'%';
            }
            if ($schoolName!="0"){
                if($schoolName=='其他'){
                    $conditions .=
                   " and studyexperience.school_name NOT LIKE :schoolName1
                    and studyexperience.school_name NOT LIKE :schoolName2
                    and studyexperience.school_name NOT LIKE :schoolName3
                    and studyexperience.school_name NOT LIKE :schoolName4
                    and studyexperience.school_name NOT LIKE :schoolName5
                    and studyexperience.school_name NOT LIKE :schoolName6
                    and studyexperience.school_name NOT LIKE :schoolName7
                    and studyexperience.school_name NOT LIKE :schoolName8
                    and studyexperience.school_name NOT LIKE :schoolName9
                    and studyexperience.school_name NOT LIKE :schoolName10
                    and studyexperience.school_name NOT LIKE :schoolName11
                    and studyexperience.school_name NOT LIKE :schoolName12
                    and studyexperience.school_name NOT LIKE :schoolName13
                    and studyexperience.school_name NOT LIKE :schoolName14
                    and studyexperience.school_name NOT LIKE :schoolName15
                    and studyexperience.school_name NOT LIKE :schoolName16
                    and studyexperience.school_name NOT LIKE :schoolName17
                    and studyexperience.school_name NOT LIKE :schoolName18
                    and studyexperience.school_name NOT LIKE :schoolName19
                    and studyexperience.school_name NOT LIKE :schoolName20
                    and studyexperience.school_name NOT LIKE :schoolName21
                    and studyexperience.school_name NOT LIKE :schoolName22
                    and studyexperience.school_name NOT LIKE :schoolName23
                    and studyexperience.school_name NOT LIKE :schoolName24
                    and studyexperience.school_name NOT LIKE :schoolName25
                    and studyexperience.school_name NOT LIKE :schoolName26
                    and studyexperience.school_name NOT LIKE :schoolName27
                    and studyexperience.school_name NOT LIKE :schoolName28 ";
                    $params[':schoolName1']='%'.'东北师范大学'.'%';
                    $params[':schoolName2']='%'.'吉林师范大学'.'%';
                    $params[':schoolName3']='%'.'长春师范大学'.'%';
                    $params[':schoolName4']='%'.'哈尔滨师范大学'.'%';
                    $params[':schoolName5']='%'.'内蒙古民族大学'.'%';
                    $params[':schoolName6']='%'.'东北林业大学'.'%';
                    $params[':schoolName7']='%'.'佳木斯大学'.'%';
                    $params[':schoolName8']='%'.'齐齐哈尔大学'.'%';
                    $params[':schoolName9']='%'.'哈尔滨学院'.'%';
                    $params[':schoolName10']='%'.'鹤岗师范高等专科学校'.'%';
                    $params[':schoolName11']='%'.'牡丹江师范学院'.'%';
                    $params[':schoolName12']='%'.'大庆师范学院'.'%';
                    $params[':schoolName13']='%'.'哈尔滨体育学院'.'%';
                    $params[':schoolName14']='%'.'北华大学'.'%';
                    $params[':schoolName15']='%'.'通化师范学院'.'%';
                    $params[':schoolName16']='%'.'吉林工程技术师范学院'.'%';
                    $params[':schoolName17']='%'.'白城师范学院'.'%';
                    $params[':schoolName18']='%'.'延边大学'.'%';
                    $params[':schoolName19']='%'.'吉林体育学院'.'%';
                    $params[':schoolName20']='%'.'鞍山师范学院'.'%';
                    $params[':schoolName21']='%'.'渤海大学'.'%';
                    $params[':schoolName22']='%'.'沈阳大学'.'%';
                    $params[':schoolName23']='%'.'赤峰学院'.'%';
                    $params[':schoolName24']='%'.'绥化学院'.'%';
                    $params[':schoolName25']='%'.'呼伦贝尔学院'.'%';
                    $params[':schoolName26']='%'.'辽宁师范大学'.'%';
                    $params[':schoolName27']='%'.'长春大学'.'%';
                    $params[':schoolName28']='%'.'沈阳师范大学'.'%';
                }
                else{
                $conditions .= " and studyexperience.school_name LIKE :schoolId ";
                $params[':schoolId']='%'.$schoolName.'%';
                }
            }
            if ($majorId!="0"){
                $conditions .= " and studyexperience.study_specialty_id=:majorId ";
                $params[':majorId']=$majorId;
            }
            if ($locationId!="0"){
                $conditions .= " and userdetail.city_id=:locationId";
                $params[':locationId']=$locationId;
                //$params[':locationName1']=$locationName;
                //$params[':locationName0']='%'.substr($locationName,0,-3).'%';
            }
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition=$conditions;
            $cri->params=$params;
            $cri->addInCondition('t.user_id',$user_id);
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $recordCount = Resume::model()->count($cri);
            $data = Resume::model()->findAll($cri);

        }else{

            $cri->with = array('userdetail','studyexperience');
            $cri->select = 'user_id';
            $conditions = "1=1 ";
            $params = array();
            if ($degreeId != 0 && $degreeId!="0"){
                $conditions .= " and studyexperience.position_degree_id =:degreeId ";
                $params[':degreeId']=$degreeId;
            }
            if ($year != 0 && $year!="0"){
                $conditions .= " and studyexperience.end_time LIKE :year ";
                $params[':year']='%'.$year.'%';
            }
            if ($schoolName!="0"){
                if($schoolName=='其他'){
                    $conditions .=
                   " and studyexperience.school_name NOT LIKE :schoolName1
                    and studyexperience.school_name NOT LIKE :schoolName2
                    and studyexperience.school_name NOT LIKE :schoolName3
                    and studyexperience.school_name NOT LIKE :schoolName4
                    and studyexperience.school_name NOT LIKE :schoolName5
                    and studyexperience.school_name NOT LIKE :schoolName6
                    and studyexperience.school_name NOT LIKE :schoolName7
                    and studyexperience.school_name NOT LIKE :schoolName8
                    and studyexperience.school_name NOT LIKE :schoolName9
                    and studyexperience.school_name NOT LIKE :schoolName10
                    and studyexperience.school_name NOT LIKE :schoolName11
                    and studyexperience.school_name NOT LIKE :schoolName12
                    and studyexperience.school_name NOT LIKE :schoolName13
                    and studyexperience.school_name NOT LIKE :schoolName14
                    and studyexperience.school_name NOT LIKE :schoolName15
                    and studyexperience.school_name NOT LIKE :schoolName16
                    and studyexperience.school_name NOT LIKE :schoolName17
                    and studyexperience.school_name NOT LIKE :schoolName18
                    and studyexperience.school_name NOT LIKE :schoolName19
                    and studyexperience.school_name NOT LIKE :schoolName20
                    and studyexperience.school_name NOT LIKE :schoolName21
                    and studyexperience.school_name NOT LIKE :schoolName22
                    and studyexperience.school_name NOT LIKE :schoolName23
                    and studyexperience.school_name NOT LIKE :schoolName24
                    and studyexperience.school_name NOT LIKE :schoolName25
                    and studyexperience.school_name NOT LIKE :schoolName26
                    and studyexperience.school_name NOT LIKE :schoolName27
                    and studyexperience.school_name NOT LIKE :schoolName28 ";
                    $params[':schoolName1']='%'.'东北师范大学'.'%';
                    $params[':schoolName2']='%'.'吉林师范大学'.'%';
                    $params[':schoolName3']='%'.'长春师范大学'.'%';
                    $params[':schoolName4']='%'.'哈尔滨师范大学'.'%';
                    $params[':schoolName5']='%'.'内蒙古民族大学'.'%';
                    $params[':schoolName6']='%'.'东北林业大学'.'%';
                    $params[':schoolName7']='%'.'佳木斯大学'.'%';
                    $params[':schoolName8']='%'.'齐齐哈尔大学'.'%';
                    $params[':schoolName9']='%'.'哈尔滨学院'.'%';
                    $params[':schoolName10']='%'.'鹤岗师范高等专科学校'.'%';
                    $params[':schoolName11']='%'.'牡丹江师范学院'.'%';
                    $params[':schoolName12']='%'.'大庆师范学院'.'%';
                    $params[':schoolName13']='%'.'哈尔滨体育学院'.'%';
                    $params[':schoolName14']='%'.'北华大学'.'%';
                    $params[':schoolName15']='%'.'通化师范学院'.'%';
                    $params[':schoolName16']='%'.'吉林工程技术师范学院'.'%';
                    $params[':schoolName17']='%'.'白城师范学院'.'%';
                    $params[':schoolName18']='%'.'延边大学'.'%';
                    $params[':schoolName19']='%'.'吉林体育学院'.'%';
                    $params[':schoolName20']='%'.'鞍山师范学院'.'%';
                    $params[':schoolName21']='%'.'渤海大学'.'%';
                    $params[':schoolName22']='%'.'沈阳大学'.'%';
                    $params[':schoolName23']='%'.'赤峰学院'.'%';
                    $params[':schoolName24']='%'.'绥化学院'.'%';
                    $params[':schoolName25']='%'.'呼伦贝尔学院'.'%';
                    $params[':schoolName26']='%'.'辽宁师范大学'.'%';
                    $params[':schoolName27']='%'.'长春大学'.'%';
                    $params[':schoolName28']='%'.'沈阳师范大学'.'%';
                }
                else{
                $conditions .= " and studyexperience.school_name LIKE :schoolId ";
                $params[':schoolId']='%'.$schoolName.'%';
                }
            }
            if ($majorId!="0"){
                $conditions .= " and studyexperience.study_specialty_id=:majorId ";
                $params[':majorId']=$majorId;
            }
            if ($locationId!="0"){
                $conditions .= " and userdetail.city_id=:locationId";
                $params[':locationId']=$locationId;
                //$params[':locationName1']=$locationName;
                //$params[':locationName0']='%'.substr($locationName,0,-3).'%';
            }
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition=$conditions;
            $cri->params=$params;
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $recordCount = Resume::model()->count($cri);
            $data = Resume::model()->findAll($cri);
        }


    // 重组不同表的数据
    foreach ($data as $key => $value) {
        //if(StudyExperience::model()->findAllByAttributes(array('user_id'=>$value->user_id))!=null){
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
        }
        $json = CJSON::encode($graduate);
        $graduateJson='{"code":0,"data":'.$json.',"dataCount":"'.$recordCount.'"}';
        print  $graduateJson;
    }



    // Ajax分类
    public function actionAjax(){

        foreach ($_POST as $key => $value) {
            if ($value) {

            }else{
                $_POST[$key] = '';
            }
        }

        // 分类搜索
        $cri = new CDbCriteria();
        $cri->with = array('userdetail','degree');
        $cri->select = 'user_id,major_name,position_degree_id';
        $cri->addSearchCondition('position_degree_id',$_POST['degree']);
        $cri->addSearchCondition('major_name',$_POST['major']);
        $cri->addSearchCondition('account_place',$_POST['account_place']);
        $cri->addSearchCondition('position_type_id',$_POST['position_type']);

        $data = Resume::model()->findAll($cri);


        if ($data) {
            foreach ($data as $key => $value) {
                $graduate[$key]['realname'] = $value->userdetail->realname;
                $graduate[$key]['account_place'] = $value->userdetail->account_place;
                $graduate[$key]['degree'] = $value->degree->name;
                $graduate[$key]['head_url'] = $value->userdetail->head_url;
                $graduate[$key]['user_id'] = $value->user_id;
                $graduate[$key]['major_name'] = $value->major_name;
            }
            $graduate = CJSON::encode($graduate);
            print_r($graduate);
        }else{

        }


    }


    // 毕业生详细信息
    public function actionDetail(){
        // 获取用户所对应的档案id
        $user_id = 1;

        $archives_id = Archives::model()->findByAttributes(array('user_id'=>$user_id))->id;

        // 获取简历id
        $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;


        // 获取基本信息
        $BasicInfo = UserDetail::model()->getAll($user_id);

        // 获取联系方式
        $ContactInfo = User::model()->getAll($user_id);

        // 获取实习经历信息
        $WorkExperienceInfo = WorkExperience::model()->getAll($archives_id);

        // 获取教育经历
        $StudyExperienceInfo =StudyExperience::model()->getAll($archives_id);

        // 渲染数据到视图
        $this->smarty->assign('UserDetail',$BasicInfo);
        $this->smarty->assign('ContactInfo',$ContactInfo);
        $this->smarty->assign('WorkExperienceInfo',$WorkExperienceInfo);
        $this->smarty->assign('StudyExperienceInfo',$StudyExperienceInfo);
        $this->smarty->display('graduate/detail.html');
    }

    public  function  actionSchool(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Smore'){
                /*$study="select  distinct school_name as id,school_name as name from {{study_experience}}";
                $schoolList =StudyExperience::model()->findAllBySql($study);*/
                $data = '[{"school":null,"name":"东北师范大学","value":null,"num":null
                },{"school":null,"name":"辽宁师范大学","value":null,"num":null},{
                "school":null,"name":"吉林师范大学","value":null,"num":null}
                ,{"school":null,"name":"哈尔滨师范大学","value":null,"num":null},{
                "school":null,"name":"内蒙古民族大学","value":null,"num":null}
                ,{"school":null,"name":"东北林业大学","value":null,"num":null},{
                "school":null,"name":"佳木斯大学","value":null,"num":null}
                ,{"school":null,"name":"齐齐哈尔大学","value":null,"num":null},{
                "school":null,"name":"哈尔滨学院","value":null,"num":null}
                ,{"school":null,"name":"鹤岗师范高等专科学校","value":null,"num":null},{"school":null,
                "name":"牡丹江师范学院","value":null,"num":null}
                ,{"school":null,"name":"大庆师范学院","value":null,"num":null},{
                "school":null,"name":"哈尔滨体育学院","value":null,"num":null}
                ,{"school":null,"name":"绥化学院","value":null,"num":null},{
                "school":null,"name":"北华大学","value":null,"num":null}
                ,{"school":null,"name":"吉林工程技术师范学院","value":null,"num":null},{
                "school":null,"name":"长春师范大学","value":null,"num":null}
                ,{"school":null,"name":"通化师范学院","value":null,"num":null},{
                "school":null,"name":"长春大学","value":null,"num":null}
                ,{"school":null,"name":"白城师范学院","value":null,"num":null}
                ,{"school":null,"name":"延边大学","value":null,"num":null}
                ,{"school":null,"name":"吉林体育学院","value":null,"num":null},{
                "school":null,"name":"沈阳师范大学","value":null,"num":null}
                ,{"school":null,"name":"鞍山师范学院","value":null,"num":null},{
                "school":null,"name":"渤海大学","value":null,"num":null}
                ,{"school":null,"name":"沈阳大学","value":null,"num":null},{
                "school":null,"name":"赤峰学院","value":null,"num":null},{
                "school":null,"name":"呼伦贝尔学院","value":null,"num":null},{
                "school":null,"name":"其他","value":null,"num":null}]';
            }
        }
        $SearchJson='{"code":0,"data":'.$data.'}';
        print  $SearchJson;
    }
    public  function  actionMajor(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Mmore'){
                $major="select  distinct major_name as id,major_name as name from {{study_experience}}";
                $majorList =StudyExperience::model()->findAllBySql($major);
            }
        }
        $SearchJson='{"code":0,"data":'.json_encode($majorList).'}';
        print  $SearchJson;
    }
    public  function  actionLocation(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Lmore'){
                $location = CacheService::getInstance()->province();
            }
            else
            {
                $location = CacheService::getInstance()->city()[$ss];
            }
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($location).'}';
        print  $SearchJson;
    }
    public  function  actionSpecialty(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Mmore'){
                //$specialty="select  distinct id as id,name as name from {{study_specialty}} WHERE parent_id=0";
                $specialtyList = CacheService::getInstance()->studySpecialty();
            }
            else
            {
                $specialtyList = CacheService::getInstance()->childSpecialty()[$ss];
            }
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($specialtyList).'}';
        print  $SearchJson;
    }
}

?>