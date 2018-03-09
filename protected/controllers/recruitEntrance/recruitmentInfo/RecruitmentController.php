<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 16:11
 * 进入招聘信息列表展示页
 */
class RecruitmentController extends Controller
{
    //过滤器
    public function filters()
    {
        return array(
            array(
                'application.filters.RecruitmentFilter',
            ),
        );
    }

    //进入列表页
    public function actionIndex()
    {
        $companyId = yii::app()->session['company_id'];
        $companyUserName = yii::app()->session['companyUserName'];
//        $companyId = 3;
//        $companyUserName = "wangshuhang";
        $conditions = "'1'='1' and p.type_id=pt.id and p.degree_id = d.id and p.company_id =:companyId and p.company_user_name = :companyUserName";
        $params = array(':companyId' => $companyId, ':companyUserName' => $companyUserName);
        $command = Yii::app()->db->createCommand()
            ->select('p.id,p.name,pt.name as positionType,d.name as degree,p.recruitment_num,p.is_publish,p.is_ok')
            ->from('t_position p,t_position_type pt,t_degree d')
            ->order('p.id desc');
        $offset = 0;
        $model = $command->where($conditions, $params)->limit(10)->offset($offset)->queryAll();

        $keyword = $_GET['keyword'];//方便前台分页ajax拿到keyword参数
        //得到总记录数
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position');
        $conditions1 = "'1'='1' and company_id =:companyId and company_user_name =:companyUserName";
        if ($keyword != null && ""!=trim($keyword)) {
            $conditions1 .= " and name LIKE :keyword ";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $record = $command1->where($conditions1, $params)->queryAll();
        if ($record[0]['recordCount'] != 0) {
            $recordCount = $record[0]['recordCount'];
        } else {
            $recordCount = 0;
        }

        //传companyUserName到前台去，通过它使得招聘信息管理和单位其他招聘信息两模块的分页json可以共用
        $this->smarty->assign('companyUserName', $companyUserName); //前台页面要隐藏用户名companyUserName
        $this->smarty->assign('recruitInforList', $model);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('keyword', $keyword);
//        $zero2='2015-11-29 21:07:00';
//        echo "时间是：".strtotime($zero2);
        $this->smarty->display('recruitEntrance/recruitment-info/recruitment-info.html');
    }

    //分页获取数据
    public function actionJson()
    {
        $companyId = Yii::app()->session['company_id'];
        $companyUserName = Yii::app()->session['companyUserName'];
//        $companyId = 3;
//        $companyUserName = "wangshuhang";
        $keyword = $_GET['keyword'];
//        $page = $_GET['page1'];
        $company_user_name = $_GET['companyUserName'];//前台获得的公司用户

        $conditions = "'1'='1' and p.type_id=pt.id and p.degree_id = d.id and p.company_id =:companyId ";
        $conditions1 = "'1'='1' and p.company_id =:companyId ";
        $params = array(':companyId' => $companyId, ':companyUserName' => $companyUserName);
        $command = Yii::app()->db->createCommand()
            ->select('p.id,p.name,pt.name as positionType,d.name as degree,p.recruitment_num,p.company_user_name,p.is_publish,p.is_ok')
            ->from('t_position p,t_position_type pt,t_degree d')
            ->order('p.id desc');
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', 1);
        $offset = ($currentPage - 1) * $pageSize;
        if (null!=$company_user_name && "" != $company_user_name) {
            if ($companyUserName == $company_user_name) {
                $conditions .= ('and company_user_name =:companyUserName');
                $conditions1 .= ('and company_user_name =:companyUserName');
                if ($keyword != null && ""!=trim($keyword)) {
                    $conditions .= " and p.name LIKE :keyword ";
                    $conditions1 .= " and p.name LIKE :keyword ";
                    $params[':keyword'] = '%' . $keyword . '%';
                }
            } else {
                $conditions .= ('and company_user_name !=:companyUserName');
                $conditions1 .= ('and company_user_name !=:companyUserName');
                if ($keyword != null && ""!=trim($keyword)) {
                    $conditions .= " and p.name LIKE :keyword ";
                    $conditions1 .= " and p.name LIKE :keyword ";
                    $params[':keyword'] = '%' . $keyword . '%';
                }
            }
        }
        $position = $command->where($conditions, $params)->limit($pageSize)->offset($offset)->queryAll();//记录分页
        //得到总记录数
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position p');
        $record = $command1->where($conditions1, $params)->queryAll();
        $recordCount = $record[0]['recordCount'];

        if (!empty($position)) {
            $positionList = json_decode(CJSON::encode($position), true);
        }else{
            $position = array(  //没查询到数据，为保证json格式
                "id"=>-1
            );
            $positionList = json_decode(CJSON::encode($position), true);
            $position[0]['company_user_name'] = 0;
        }
        $result = array(
            "items" => $positionList,
            "dataCount" => $recordCount,
            "keyword" =>$keyword,
            "companyUserName"=>$position[0]['company_user_name']

        );
        $result1 = array(
            'obj' => [$result]
        );

        echo json_encode($result1['obj'], JSON_UNESCAPED_UNICODE);
    }

    //查看详情页
    public function actionDetail($id)
    {

        //根据id查询职位
        $sql = 'select id,is_teacher,name,type_id,company_id,city_id,degree_id,specialty_ids,
        recruitment_num,position_duty,position_need,dead_time,bright,descrption,company_user_name,is_ok,
        is_join_big_recruitment,is_join_recruitment_week,check_reason_id from {{position}} where id = ' . $id;
        $position = Position::model()->findBySql($sql);
// var_dump($position);
        //查询亮点
        $brightsString = $position->bright;
        $brightsList = explode("，", $brightsString);//中文逗号
//        var_dump($brightsList);
        //查询学历要求
        $sql2 = 'select name from {{degree}} where id = ' . $position->degree_id;
        $degree = Degree::model()->findBySql($sql2);
//        var_dump($degree['name']);
        //查询职位类别
        $sql3 = 'select name from {{position_type}} where id = ' . $position->type_id;
        $positionType = PositionType::model()->findBySql($sql3);
//        var_dump($positionType['name']);

        //查询工作城市
        if($position->city_id!=null&&$position->city_id>0){
            $sql4 = 'select province_id,name from {{param_city}} where id = ' . $position->city_id;
            $city = City::model()->findBySql($sql4);
        }
//        var_dump($city['name']);

        //查询工作省份，如果城市为四个直辖市，两个特别行政区则没有省
        if($position->city_id!=null&&$position->city_id>0) {
            if ($position->city_id <= 0 || $position->city_id == 1 || $position->city_id == 2 || $position->city_id == 3 || $position->city_id == 4 || $position->city_id == 390 || $position->city_id == 391) {
                $province = "";
            } else {
                $sql5 = 'select name from {{param_province}} where id =' . $city->province_id;
                $province = Province::model()->findBySql($sql5);
            }
            //拼凑出工作地点
            if ($province == "") {
                $place = $city->name;
            } else {
                $place = $province->name . "&nbsp;" . $city->name;
            }
        }else{//数据库根本没存city_id的字段
            $place = "";
        }
//        echo "省".$province;

        //查询需求专业
        $sql6 = 'select specialty_ids from {{position}} where id =' . $id;
        $specialtyIds = Position::model()->findBySql($sql6);
        $specialtyIds1 = explode(",", $specialtyIds->specialty_ids);
        if($specialtyIds1[0]!="0"){
            for ($i = 0; $i < (count($specialtyIds1) - 1); $i++) {
                $sql7 = 'select name from {{study_specialty}} where id =' . $specialtyIds1[$i];
                $specialty = StudySpecialty::model()->findBySql($sql7);
                $specialtyList[$i] = $specialty->name;
            }
        }
        if($specialtyIds1[0]=="0"){
            $specialtyList[0]= "0";
        }
//        var_dump($specialtyList);
        //获得截止日期
        if($position->dead_time!=null){
            //获得年
            $year = date('Y', strtotime($position->dead_time));

            //获得月
            $month = date('m', strtotime($position->dead_time));
            if (substr($month, 0, 1) == '0') {
                $month = substr($month, 1, 2);
            }

            //获得日
            $day = date('d', strtotime($position->dead_time));
            if (substr($day, 0, 1) == '0') {
                $day = substr($day, 1, 2);
            }
            if($year!=1969){
                $dead_time = ''.$year.'年'.$month.'月'.$day.'日';
            }else{
                $dead_time = "无";
            }

        }else{
            $dead_time = "无";
        }
//        echo "日期".$dead_time;

        //设置审核状态
        if(null!=$position->is_ok){
            if($position->is_ok==-1){
                $isOk = "待审核";
            }
            if($position->is_ok==0){
                $isOk = "审核未通过";
            }
            if($position->is_ok==1){
                $isOk = "审核通过";
            }
        }
        //审核理由
        if($position->check_reason_id!=null&&$position->check_reason_id>0){
            $positionCheck = PositionCheck::model()->findByPk($position->check_reason_id);
//            if($positionCheck->check_reason==1){
//                $checkReason ="信息不完整";
//            }else if($positionCheck->check_reason==1){
//                $checkReason ="信息有误";
//            }
        }
        //公司端是否发布
        if($position->is_publish==0){
            $isPublish = "未发布";
        }else if($position->is_publish==1){
            $isPublish = "已发布";
        }else{
            $isPublish = "";
        }
        //公司用户
//        $companyUserName = "wangshuhang";
        $companyUserName = Yii::app()->session['companyUserName'];

        //返回正确页面所需参数
        $currentPage = $_GET['currentPage'];
        $keyword = $_GET['keyword'];
//        echo "当前页".$currentPage;
//        echo "关键字".$keyword;


        $this->smarty->assign('position', $position);
        $this->smarty->assign('brightsList', $brightsList);
        $this->smarty->assign('degree', $degree->name);
        $this->smarty->assign('positionType', $positionType->name);
        $this->smarty->assign('place',$place);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('dead_time',$dead_time);
        $this->smarty->assign('companyUserName',$companyUserName);
        $this->smarty->assign('currentPage',$currentPage);
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('isOk',$isOk);
        $this->smarty->assign('positionCheck',$positionCheck);
        $this->smarty->assign('isPublish',$isPublish);

//        $this->redirect($this->createUrl("recruitEntrance/recruitmentInfo/recruitment/json"));
        $this->smarty->display('recruitEntrance/recruitment-info/recruitment-detail.html');
    }

    //删除招聘信息
    public function actionDel($id)
    {
        //返回正确页面所需参数
        $currentPage = $_GET['currentPage'];
        $keyword = $_GET['keyword'];

        //在未删除前重新计算总记录数
        $companyId = Yii::app()->session['company_id'];
        $companyUserName = Yii::app()->session['companyUserName'];
//        $companyId = 3;
//        $companyUserName ="wangshuhang";
        $conditions = "'1'='1' and p.company_id =:companyId and p.company_user_name = :companyUserName";
        $params = array(':companyId' => $companyId, ':companyUserName' => $companyUserName);
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position p');
        if ($keyword != null && ""!=trim($keyword)) {
            $conditions .= " and p.name LIKE :keyword ";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $record = $command1->where($conditions, $params)->queryAll();
        $recordCount = $record[0]['recordCount'];
        if($recordCount%10==1){
            $currentPage =$currentPage-1;
        }
        //删除招聘信息
        $sql ='select * from {{position}} where id='.$id;
        $position = Position::model()->findBySql($sql);
        if (!empty($position)) {
            $position->delete();
        } else {
            $this->smarty->assign('delFail', "删除失败！");
        }

//        echo "当前页".$currentPage;
        $this->redirect($this->createUrl("recruitEntrance/recruitmentInfo/recruitment/index?currentPage=$currentPage&keyword=$keyword"));
    }

    //根据省id获取市
    public function actionCityJson()
    {
        $provinceId = $_GET['provinceId'];
        $cityList = array();
        if ($provinceId != null && "" != $provinceId) {
            if ($provinceId != 1 && $provinceId != 2 && $provinceId != 3 && $provinceId != 4&&$provinceId!= 33&&$provinceId!=34) {
                $city = City::model()->findAllByAttributes(array('province_id' => $provinceId));

                if (!empty($city)) {
                    $cityList = json_decode(CJSON::encode($city), true);
                }
                $result = array(
                    "items" => $cityList,
                    "code"=>1           //为1则不是直辖市或特别行政区
                );
                $result1 =array(
                    "obj" =>[$result]
                );
                echo json_encode($result1['obj'], JSON_UNESCAPED_UNICODE);
            } else {
                $cityList = array(
                    "code"=>0         //为0则是直辖市或特别行政区
                );
                $result1 = array(
                    'obj' => [$cityList]
                );

                print json_encode($result1['obj'],JSON_UNESCAPED_UNICODE);
            }
        }
    }

    //根据所需专业大类查询所需专业小类
    public function actionMinorSpecialtyJson()
    {
        $majorId = $_GET['majorId'];
//        $majorId = 2;
        if ($majorId != null && "" != $majorId && $majorId != 0) {
            $minor = StudySpecialty::model()->findAllByAttributes(array('parent_id' => $majorId));
            $minorList = array();
            if (!empty($minor)) {
                $minorList = json_decode(CJSON::encode($minor), true);
            }
            $result = array(
                "items" => json_encode($minorList, JSON_UNESCAPED_UNICODE)
            );
            print $result["items"];
        } else {
            $result = null;
            print $result["items"];
        }
    }

    //去增添页
    public function actionToAdd()
    {
        //查询所有学历
        $sql = 'select id,name from {{degree}}';
        $degreeList = Degree::model()->findAllBySql($sql);

        //查询所有省份，包括直辖市
        $sql1 = 'select id,name from {{param_province}}';
        $provinceList = Province::model()->findAllBySql($sql1);

        //查询所有所需专业大类
        $sql2 = 'select id,name from {{study_specialty}}  where parent_id = 0';
        $majorSpecialtyList = StudySpecialty::model()->findAllBySql($sql2);

        //查出教育类的职位类别
        $sql3 = 'select id,name from {{position_type}} where status = 1';
        $positionType1 = PositionType::model()->findAllBySql($sql3);

        //查出非教育类的职位类别
        $sql4 = 'select id,name from {{position_type}} where status =0';
        $positionType2 = PositionType::model()->findAllBySql($sql4);

        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('provinceList', $provinceList);
        $this->smarty->assign('majorSpecialtyList', $majorSpecialtyList);
        $this->smarty->assign('positionType1', $positionType1);
        $this->smarty->assign('positionType2', $positionType2);
        $this->smarty->display('recruitEntrance/recruitment-info/apply-position.html');
    }

    //增加招聘信息
    public function actionAdd()
    {
        $position = new Position();

        $companyId = Yii::app()->session['company_id'];
//        $companyId = 3;
        if ($companyId != null && $companyId > 0) {
            $position->company_id = $companyId;
        }
        //是否是教师
        if ($_POST['isTeacher'] == 0 || $_POST['isTeacher'] == 1) {
            $position->is_teacher = $_POST['isTeacher'];
        }
        //是否参加大招会
        if ($_POST['isJoinBigRecruitment'] == 1 || $_POST['isJoinBigRecruitment'] == 2) {
            $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        }
        //是否参加招聘周
        if ($_POST['isJoinRecruitmentWeek'] == 1 || $_POST['isJoinRecruitmentWeek'] == 2) {
            $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        }
        //职位名称
        if ($_POST['positionName'] != null && "" != trim($_POST['positionName'])) {
            $position->name = trim($_POST['positionName']);
        }
        //直辖市、特别行政区存储
        if($_POST['provinceId']!=null&&""!=$_POST['provinceId']){
            //直辖市存储
            if($_POST['provinceId']==1||$_POST['provinceId']==2||$_POST['provinceId']==3||$_POST['provinceId']==4){
                $position->city_id = $_POST['provinceId'];
            }else if($_POST['provinceId']==33||$_POST['provinceId']==34){//香港，澳门特别行政区存储
                $position->city_id = $_POST['provinceId']+357;//加357可得两特别行政区在t_param_city中的id值
            }
        }
        //其他市存储
        if ($_POST['cityId'] != null && $_POST['cityId'] > 0) {
            $position->city_id = $_POST['cityId'];
        }
        //职位类别
        if($_POST['positionType']!=null&&$_POST['positionType']>0){
            $position->type_id = $_POST['positionType'];
        }
        //学历要求
        if ($_POST['degreeId'] != null && $_POST['degreeId'] > 0) {
            $position->degree_id = $_POST['degreeId'];
        }
        //招聘人数
        if ($_POST['recruitmentNum'] != null && ""!=trim($_POST['recruitmentNum'])) {
            $position->recruitment_num = trim($_POST['recruitmentNum']);
        }

        //所需专业（多个）
        if($_POST['unlimited-professional']==null||""==$_POST['unlimited-professional']){
            if ($_POST['specialtyIds'] != null && "" != $_POST['specialtyIds']) {
                $position->specialty_ids = $_POST['specialtyIds'];
            }
        }else{//选择了不限专业
            $position->specialty_ids ="0";
        }

//        $position->company_user_name = "xiaozhi";
        //信息发布联系人
        $position->company_user_name = Yii::app()->session['companyUserName'];
       //公司端录入职位信息的时间也即公司端发布职位时间
        $position->entering_time=date('Y-m-d H:i:s',time());
        //公司端最近更新人即公司端发布该职位信息的信息发布联系人
        $position->last_update = Yii::app()->session['companyUserName'];
        //职位截止日期
        $position->dead_time = date("Y-m-d", strtotime($_POST['deadTime']));
        //岗位职责
        $position->position_duty = trim($_POST['positionDuty']);
        //岗位需求
        $position->position_need = trim($_POST['positionNeed']);
        //职位亮点
        $position->bright = trim($_POST['bright']);
        //职位描述
        $position->descrption = trim($_POST['descrption']);
        //职位来源
        $position->position_source=3;//1代表东北师大2代表6所部属3代表互联网

        $position->save();
        $this->redirect($this->createUrl("recruitEntrance/recruitmentInfo/recruitment/index"));
    }

    //前往修改页
    public function actionToEdit($id)
    {
        //得到招聘信息
        $sql = 'select * from {{position}} where id = ' . $id;
        $position = Position::model()->findBySql($sql);
//        var_dump($position);
        //查询所有学历
        $degreeList = Degree::model()->findAll();
//          var_dump($degreeList);
        //查询所有省份，包括直辖市和特别行政区
        $sql1 = 'select id,name from {{param_province}}';
        $provinceList = Province::model()->findAllBySql($sql1);

//        var_dump($provinceList);
        //查询招聘信息中的省份
        $selectedProvince = null ;
        if($position->city_id!=null&&$position->city_id>0){
            $sql2 = 'select province_id from {{param_city}} where id = ' . $position->city_id;
            $selectedProvince = City::model()->findBySql($sql2);
        }
//        var_dump($selectedProvince['province_id']);
        //查询被选中省的所有市
        if($selectedProvince->province_id!=null){
                $sql3 = 'select id,name from {{param_city}} where province_id = '.$selectedProvince->province_id;
                $cityList =City::model()->findAllBySql($sql3);
        }

//        var_dump($cityList);
        //查询所有所需专业大类
        $sql4 = 'select id,name from {{study_specialty}}  where parent_id = 0';
        $majorSpecialtyList = StudySpecialty::model()->findALLBySql($sql4);
//       var_dump($majorSpecialtyList);
        //查出教育类的职位类别
        $sql5 = 'select id,name from {{position_type}} where status = 1';
        $positionType1 = PositionType::model()->findAllBySql($sql5);
//        var_dump($positionType1);

        //查出非教育类的职位类别
        $sql6 = 'select id,name from {{position_type}} where status =0';
        $positionType2 = PositionType::model()->findAllBySql($sql6);
//        var_dump($positionType2);

        //查询所有被选中的专业
        $sql7 = 'select specialty_ids from {{position}} where id =' . $id;
        $specialtyIds = Position::model()->findBySql($sql7);
        $specialtyIds1 = explode(",", $specialtyIds->specialty_ids);
        if($specialtyIds1[0]!="0"){
            for ($i = 0; $i < (count($specialtyIds1) - 1); $i++) {
                $sql8 = 'select id,name,parent_id from {{study_specialty}} where id =' . $specialtyIds1[$i];
                $specialty = StudySpecialty::model()->findBySql($sql8);
                $specialtyList[$i]['id'] = $specialty->id;
                $specialtyList[$i]['name'] = $specialty->name;
                $specialtyList[$i]['parent_id'] = $specialty->parent_id;
            }
        }
        if($specialtyIds1[0]=="0"){
            $specialtyList[0]= "0";
        }
//       var_dump($specialtyList);

        //返回正确页面所需参数
        $currentPage = $_GET['currentPage'];
        $keyword = $_GET['keyword'];

        $this->smarty->assign('position', $position);
        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('provinceList', $provinceList);
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('majorSpecialtyList', $majorSpecialtyList);
        $this->smarty->assign('selectedProvince', $selectedProvince->province_id);
        $this->smarty->assign('positionType1', $positionType1);
        $this->smarty->assign('positionType2', $positionType2);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('currentPage', $currentPage);
        $this->smarty->assign('keyword', $keyword);

        $this->smarty->display('recruitEntrance/recruitment-info/update-position.html');

    }

    //修改招聘信息
    public function actionEdit($id)
    {
        //职位信息
        $sql = 'select * from {{position}} where id = ' . $id;
        $position = Position::model()->findBySql($sql);
        //是否是教师
        if ($_POST['isTeacher'] == 0 || $_POST['isTeacher'] == 1) {
            $position->is_teacher = $_POST['isTeacher'];
        }
        //是否参加大招会
        if ($_POST['isJoinBigRecruitment'] == 1 || $_POST['isJoinBigRecruitment'] == 2) {
            $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        }
        //是否参加招聘周
        if ($_POST['isJoinRecruitmentWeek'] == 1 || $_POST['isJoinRecruitmentWeek'] == 2) {
            $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        }
        //职位名称
        if ($_POST['positionName'] != null && "" != trim($_POST['positionName'])) {
            $position->name = $_POST['positionName'];
        }
        //职位类型
        if ($_POST['positionType'] != null && $_POST['positionType'] > 0) {
            $position->type_id = $_POST['positionType'];
        }
        //直辖市、特别行政区存储
        if($_POST['provinceId']!=null&&""!=$_POST['provinceId']){
            //直辖市存储
            if($_POST['provinceId']==1||$_POST['provinceId']==2||$_POST['provinceId']==3||$_POST['provinceId']==4){
                $position->city_id = $_POST['provinceId'];
            }else if($_POST['provinceId']==33||$_POST['provinceId']==34){//香港，澳门特别行政区存储
                $position->city_id = $_POST['provinceId']+357;//加357可得两特别行政区在t_param_city中的id值
            }
        }
        //其他市存储
        if ($_POST['cityId'] != null && $_POST['cityId'] > 0) {
            $position->city_id = $_POST['cityId'];
        }
        //学历
        if ($_POST['degreeId'] != null && $_POST['degreeId'] > 0) {
            $position->degree_id = $_POST['degreeId'];
        }
        //招聘人数
        if ($_POST['recruitmentNum'] != null && $_POST['recruitmentNum'] > 0) {
            $position->recruitment_num = $_POST['recruitmentNum'];
        }
        //所需专业（多个）
        if($_POST['unlimited-professional']==null||""==$_POST['unlimited-professional']){
            if ($_POST['specialtyIds'] != null && "" != $_POST['specialtyIds']) {
                $position->specialty_ids = $_POST['specialtyIds'];
            }
        }else{//选择了不限专业
            $position->specialty_ids ="0";
        }

        $position->company_user_name = Yii::app()->session['companyUserName'];
//        $position->company_user_name = "wangshuhang";

        //$position->dead_time = date("Y-m-d",strtotime($_POST['deadTime']));
        $position->dead_time = $_POST['deadTime'];
        $position->position_duty = $_POST['positionDuty'];
        $position->position_need = $_POST['positionNeed'];
        $position->bright = $_POST['bright'];
        $position->descrption = $_POST['descrption'];//拼写有错，但数据库就这么写的
        //公司端修改职位信息的时间也即公司端最新发布职位时间（在未发布的前提下）
        if($position->is_publish==0){
            $position->entering_time = date('Y-m-d H:i:s', time());
        }
        //职位来源
        $position->position_source=3;//1代表东北师大2代表6所部属3代表互联网
        $position->save();

        //返回正确页面所需参数
        $currentPage = $_POST['currentPage'];
        $keyword = $_POST['keyword'];
        $this->redirect($this->createUrl("recruitEntrance/recruitmentInfo/recruitment/index?currentPage=$currentPage&keyword=$keyword"));
    }

    //查看单位其它招聘信息
    public function actionOther()
    {
        $companyId = yii::app()->session['company_id'];
        $companyUserName = Yii::app()->session['companyUserName'];
//        $companyId = 3;
//        $companyUserName = "wangshuhang";
        $conditions = "'1'='1' and p.type_id=pt.id and p.degree_id = d.id and p.company_id =:companyId and p.company_user_name !=:companyUserName";
        $params = array(':companyId' => $companyId, ':companyUserName' => $companyUserName);
        $command = Yii::app()->db->createCommand()
            ->select('p.id,p.name,pt.name as positionType,d.name as degree,p.recruitment_num,p.company_user_name,p.is_publish,p.is_ok')
            ->from('t_position p,t_position_type pt,t_degree d')
            ->order('p.id desc');
        $offset = 0;
        $model = $command->where($conditions, $params)->limit(10)->offset($offset)->queryAll();
//var_dump($model[2]['company_user_name']);
        //得到总记录数
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position');
        $conditions1 = "'1'='1' and company_id =:companyId and company_user_name !=:companyUserName";
        $record = $command1->where($conditions1, $params)->queryAll();
        if ($record[0]['recordCount'] != 0) {
            $recordCount = $record[0]['recordCount'];
        } else {
            $recordCount = 0;
            $model[0]['company_user_name'] = 0;
        }
        $keyword = $_GET['keyword'];//方便前台分页ajax拿到keyword参数
        $this->smarty->assign('companyUserName', $model[0]['company_user_name']);
        $this->smarty->assign('recruitInforList', $model);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('keyword', $keyword);
        $this->smarty->display('recruitEntrance/recruitment-info/recruitment-otherInfo.html');
        //前台隐藏一个companyUserName
    }

    //搜索职位名称
    public function actionSearch()
    {
        $companyId = yii::app()->session['company_id'];
        $companyUserName = yii::app()->session['companyUserName'];
        $company_user_name = $_POST['companyUserName'];
        $keyword = $_POST['keyword'];
        $keyword = trim($keyword);//去掉首尾空格
//        $companyId = 3;
//        $companyUserName = "wangshuhang";
        $conditions = "'1'='1' and p.type_id=pt.id and p.degree_id = d.id and p.company_id =:companyId ";
        $conditions1 ="'1'='1' and t_position.company_id =:companyId ";//统计记录数的
        //初始化params
        $params = array(
            ":companyId" => $companyId,
            ":companyUserName" => $companyUserName,
        );
        //判断是招聘信息管理还是单位其他招聘信息
        if ($companyUserName == $company_user_name) {
            $conditions .= 'and p.company_user_name = :companyUserName';
            $conditions1 .= 'and t_position.company_user_name = :companyUserName';
        } else {
            $conditions .= 'and p.company_user_name != :companyUserName';
            $conditions1 .= 'and t_position.company_user_name != :companyUserName';
        }
        //根据前台传来的关键字增加查询条件
        if ($keyword != null && $keyword != "") {
            $conditions .= " and p.name like :keyword";
            $conditions1 .= " and t_position.name like :keyword";
            $params[':keyword'] = "%$keyword%";
        }
        //得到查询数据
        $command = Yii::app()->db->createCommand()
            ->select('p.id,p.name,pt.name as positionType,d.name as degree,p.recruitment_num,p.company_user_name,p.is_publish,p.is_ok')
            ->from('t_position p,t_position_type pt,t_degree d')
            ->order('p.id asc');
        $offset = 0;
        $model = $command->where($conditions, $params)->limit(10)->offset($offset)->queryAll();

        //得到记录数
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position');
        $record = $command1->where($conditions1,$params)->queryAll();
        if ($record[0]['recordCount'] != 0) {
            $recordCount = $record[0]['recordCount'];
        } else {
            $recordCount = 0;
        }

        $this->smarty->assign('recruitInforList', $model);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('keyword',$keyword);
        //传companyUserName到前台去，通过它使得招聘信息管理和单位其他招聘信息两模块的分页json可以共用
        if ($companyUserName == $company_user_name) {
            $this->smarty->assign('companyUserName', $company_user_name); //前台页面要隐藏用户名companyUserName
            $this->smarty->display('recruitEntrance/recruitment-info/recruitment-info.html');
        } else if ($companyUserName != $company_user_name) {
            if($model[0]['company_user_name']!=null){
                $companyUserName= $model[0]['company_user_name'];
            }else{
                $companyUserName =0;
            }
            $this->smarty->assign('companyUserName', $companyUserName); //前台页面要隐藏用户名companyUserName
            $this->smarty->display('recruitEntrance/recruitment-info/recruitment-otherInfo.html');
        }

    }

    //最近投递模块，查询所有的简历（郑文华负责）
    public function actionQueryAll()
    {
        $cri = new CDbCriteria();
        $cri->with = array('userdetail', 'studyexperience','deliver');
        $cri->select = 'user_id';
        $conditions = "1=1 ";
        $params = array();

        $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->group = 't.id';
        $cri->order = 't.id DESC';
        $allData = Resume::model()->findAll($cri);
        //记录出所有查询的数量
        $recordCount = count( $allData);
//        var_dump($recordCount);

        $pageSize = 8;
        $cri -> limit = $pageSize;
        $data = Resume::model()->findAll($cri);

        // 重组不同表的数据
        foreach ($data as $key => $value) {
            if (isset($value->userdetail->realname))
                //姓名
                $model[$key]['realname'] = $value->userdetail->realname;
            else
                $model[$key]['realname'] = "无";
            if (isset($value->userdetail->city_id)) {
                //生源地
                $model[$key]['account_place'] = City::model()->findByPk($value->userdetail->city_id)->name;
            } else
                $model[$key]['account_place'] = "无";
            if (isset($value->studyexperience->position_degree_id)) {
                //学历
                $degree = Degree::model()->findByPk($value->studyexperience->position_degree_id);
                $model[$key]['degree'] = $degree->name;
            } else
                $model[$key]['degree'] = "无";
            if (isset($value->userdetail->head_url))
                //头像
                $model[$key]['head_url'] = $value->userdetail->head_url;
            else
                $model[$key]['head_url'] = "无";
            if (isset($value->user_id))
                $model[$key]['user_id'] = $value->user_id;
            else
                $model[$key]['user_id'] = "无";
            if (isset($value->studyexperience->major_name))
                //专业
                $model[$key]['major_name'] = $value->studyexperience->major_name;
            else
                $model[$key]['major_name'] = "无";
            //学校
            if (isset($value->studyexperience->school_name))
                $model[$key]['school_name'] = $value->studyexperience->school_name;
            else
                $model[$key]['school_name'] = "无";
            //毕业时间
            if(isset($value->studyexperience->end_time))
                $model[$key]['year'] = substr($value->studyexperience->end_time, 0, 4);
            else
                $model[$key]['year'] = "无";
            //投递职位
            if(isset($value->deliver->deliver_position)){
                $model[$key]['deliver_position'] = $value->deliver->deliver_position;
            }else{
                $model[$key]['deliver_position'] = "无";
            }
            //投递时间
            if(isset($value->deliver->create_time)){
                $time = strtotime($value->deliver->create_time);
                $model[$key]['create_time'] = date('Y-m-d', $time);
            }else{
                $model[$key]['create_time'] = "无";
            }
        }
//        var_dump($model);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('modelList', $model);
        $this->smarty->display('recruitEntrance/recruitment-info/recruitmentInformation-latestDeliver.html');

    }

//根据名字或者职位查询简历（郑文华负责）
    public function actionQueryByName()
    {
        // 数据查询
        $name=$_GET['name'];
        $page =$_GET['currentPage'];
        //如果接收到前台传过来的页数，如果没有，则显示第一页
        $pageNow = $page ? $page : 1;
        $cri = new CDbCriteria();
        $cri->with = array('userdetail', 'studyexperience','deliver');
        $cri->select = 'user_id';
        $conditions = "1=1  and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''  ";
        $params = array();
        if ($name != ''&&$name != -1) {
        $conditions .= " and (userdetail.realname like :name or deliver.deliver_position like :position) ";
        $params = array(':name'=>"%$name%",':position'=>"%$name%");
     }


        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->group = 't.id';
        $cri->order = 't.id DESC';
        $allData = Resume::model()->findAll($cri);
        //记录出所有查询的数量
        $recordCount = count( $allData);
//        var_dump($recordCount);
        //分页信息
        $pageSize = 8;
        $currentPage = Yii::app()->request->getParam('pageNow',$pageNow);
        $cri -> limit = $pageSize;
        $cri -> offset = ($currentPage-1)*$pageSize;
        $data = Resume::model()->findAll($cri);

        // 重组不同表的数据
        foreach ($data as $key => $value) {
            if (isset($value->userdetail->realname))
                //姓名
                $model[$key]['realname'] = $value->userdetail->realname;
            else
                $model[$key]['realname'] = "无";
            if (isset($value->userdetail->city_id)) {
                //生源地
                $model[$key]['account_place'] = City::model()->findByPk($value->userdetail->city_id)->name;
            } else
                $model[$key]['account_place'] = "无";
            if (isset($value->studyexperience->position_degree_id)) {
                //学历
                $degree = Degree::model()->findByPk($value->studyexperience->position_degree_id);
                $model[$key]['degree'] = $degree->name;
            } else
                $model[$key]['degree'] = "无";
            if (isset($value->userdetail->head_url))
                //头像
                $model[$key]['head_url'] = $value->userdetail->head_url;
            else
                $model[$key]['head_url'] = "无";
            if (isset($value->user_id))
                $model[$key]['user_id'] = $value->user_id;
            else
                $model[$key]['user_id'] = "无";
            if (isset($value->studyexperience->major_name))
                //专业
                $model[$key]['major_name'] = $value->studyexperience->major_name;
            else
                $model[$key]['major_name'] = "无";
            //学校
            if (isset($value->studyexperience->school_name))
                $model[$key]['school_name'] = $value->studyexperience->school_name;
            else
                $model[$key]['school_name'] = "无";
            //毕业时间
            if(isset($value->studyexperience->end_time))
                $model[$key]['year'] = substr($value->studyexperience->end_time, 0, 4);
            else
                $model[$key]['year'] = "无";
            //投递职位
            if(isset($value->deliver->deliver_position)){
                $model[$key]['deliver_position'] = $value->deliver->deliver_position;
            }else{
                $model[$key]['deliver_position'] = "无";
            }
            //投递时间
            if(isset($value->deliver->create_time)){
                $time = strtotime($value->deliver->create_time);
                $model[$key]['create_time'] = date('Y-m-d', $time);
            }else{
                $model[$key]['create_time'] = "无";
            }

        }

//在浏览器中打印出信息
//        $json = CJSON::encode($model);
//        $modelJson='[{"code":0,"data":'.$json.',"dataCount":"'.$recordCount.'"}]';
//
//        print  $modelJson;
//    var_dump($model);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('modelList', $model);
        $this->smarty->assign('name', $name);
        $this->smarty->display('recruitEntrance/recruitment-info/recruitmentInformation-latestDeliver.html');


    }

    //列出按条件查询的结果（郑文华负责）
    public function actionListAllJson(){
        // 数据查询
        $name=$_GET['name'];
        $page = $_GET['currentPage'];
        //如果接收到前台传过来的页数，如果没有，则显示第一页
        $pageNow = $page ? $page : 1;
        $cri = new CDbCriteria();
        $cri->with = array('userdetail', 'studyexperience','deliver');
        $cri->select = 'user_id';
        $conditions = "1=1 ";
        $params = array();
        if ($name != ''&&$name != -1) {
            $conditions .= " and (userdetail.realname like :name or deliver.deliver_position like :position) ";
            $params = array(':name'=>"%$name%",':position'=>"%$name%");
        }

        $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->group = 't.id';
        $cri->order = 't.id DESC';

        $allData = Resume::model()->findAll($cri);
        //记录出所有查询的数量
        $recordCount = count( $allData);
//        var_dump($recordCount);
       //分页信息
        $pageSize = 8;
        $currentPage = Yii::app()->request->getParam('pageNow',$pageNow);
        $cri -> limit = $pageSize;
        $cri -> offset = ($currentPage-1)*$pageSize;
        $data = Resume::model()->findAll($cri);

        // 重组不同表的数据
        foreach ($data as $key => $value) {
            if (isset($value->userdetail->realname))
                //姓名
                $model[$key]['realname'] = $value->userdetail->realname;
            else
                $model[$key]['realname'] = "无";
            if (isset($value->userdetail->city_id)) {
                //生源地
                $model[$key]['account_place'] = City::model()->findByPk($value->userdetail->city_id)->name;
            } else
                $model[$key]['account_place'] = "无";
            if (isset($value->studyexperience->position_degree_id)) {
                //学历
                $degree = Degree::model()->findByPk($value->studyexperience->position_degree_id);
                $model[$key]['degree'] = $degree->name;
            } else
                $model[$key]['degree'] = "无";
            if (isset($value->userdetail->head_url))
                //头像
                $model[$key]['head_url'] = $value->userdetail->head_url;
            else
                $model[$key]['head_url'] = "无";
            if (isset($value->user_id))
                $model[$key]['user_id'] = $value->user_id;
            else
                $model[$key]['user_id'] = "无";
            if (isset($value->studyexperience->major_name))
                //专业
                $model[$key]['major_name'] = $value->studyexperience->major_name;
            else
                $model[$key]['major_name'] = "无";
            //学校
            if (isset($value->studyexperience->school_name))
                $model[$key]['school_name'] = $value->studyexperience->school_name;
            else
                $model[$key]['school_name'] = "无";
            //毕业时间
            if(isset($value->studyexperience->end_time))
                $model[$key]['year'] = substr($value->studyexperience->end_time, 0, 4);
            else
                $model[$key]['year'] = "无";
            //投递职位
            if(isset($value->deliver->deliver_position)){
                $model[$key]['deliver_position'] = $value->deliver->deliver_position;
            }else{
                $model[$key]['deliver_position'] = "无";
            }
            //投递时间
            if(isset($value->deliver->create_time)){
                $time = strtotime($value->deliver->create_time);
                $model[$key]['create_time'] = date('Y-m-d', $time);
            }else{
                $model[$key]['create_time'] = "无";
            }

        }

        $json = CJSON::encode($model);
        $modelJson='[{"code":0,"data":'.$json.',"dataCount":"'.$recordCount.'"}]';
        print  $modelJson;
    }

}