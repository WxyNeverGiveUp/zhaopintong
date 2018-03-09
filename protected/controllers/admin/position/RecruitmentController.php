<?php

/**
 * Created by PhpStorm.
 * User: 小智
 * Date: 2017-10-20
 * Time: 上午10:10
 */
class RecruitmentController extends Controller
{
    /**
     * 进入未审核招聘信息列表页
     */
    public function actionList(){
        $cityList = City::model()->findAll();
        $companyReleaseTimeList =array(1,2,3,4,5,6,12,24);
        $this->smarty->assign( 'cityList' , $cityList) ;
        $this->smarty->assign( 'companyReleaseTimeList' , $companyReleaseTimeList) ;
        $this->smarty->assign('current','position');

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_GET['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_GET['sCityId']));
        $this->smarty->assign('sPositionName',trim($_GET['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_GET['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_GET['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_GET['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_GET['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_GET['isElite']));
        $this->smarty->display('admin/position/position.html');
    }

    /**
     * 进入已审核招聘信息列表页
     */
    public function actionCheckedList(){
        $cityList = City::model()->findAll();
        $companyReleaseTimeList =array(1,2,3,4,5,6,12,24);
        $this->smarty->assign( 'cityList' , $cityList) ;
        $this->smarty->assign( 'companyReleaseTimeList' , $companyReleaseTimeList) ;
        $this->smarty->assign('current','position');

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_GET['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_GET['sCityId']));
        $this->smarty->assign('sPositionName',trim($_GET['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_GET['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_GET['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_GET['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_GET['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_GET['isElite']));
        $this->smarty->display('admin/position/position.html');
    }

    /**
     *分页获取列表信息
     */
    public function actionJson()
    {
        $page = $_GET['page1'];
        if($_GET['sCompanyName']!=null){
            $sCompanyName = trim($_GET['sCompanyName']);
        }
//        echo "公司名：".$_GET['companyName'];
        if($_GET['sPositionName']!=null){
            $sPositionName = trim($_GET['sPositionName']);
        }
//        echo "职位名：".$_GET['positionName'];
        if($_GET['sCompanyUserName']!=null){//公司端发布人
            $sCompanyUserName = trim($_GET['sCompanyUserName']);
        }
//        echo "发布人：".$_GET['companyUserName'];
        $sCompanyReleaseTime = $_GET['sCompanyReleaseTime'];//传过来的都是数字，代表多少个小时前发布的
//        echo "发布时间：".$_GET['companyReleaseTime'];
        $sCityId = $_GET['sCityId'];
//        echo "城市id：".$_GET['cityId'];
        $isJoinBigRecruitment = $_GET['isJoinBigRecruitment'];//是否参加大招会
//        echo "是否参加大招会：".$_GET['isJoinBigRecruitment'];
        $isJoinRecruitmentWeek = $_GET['isJoinRecruitmentWeek'];//是否参加招聘周
//        echo "是否参加招聘周：".$_GET['isJoinRecruitmentWeek'];
        $isElite =$_GET['isElite'];//是否是名校名企，如果是1则为名校，2则为名企
//        echo "名校名企：".$_GET['cityId'];

        if($_GET['checkedList']==1){
            $conditions = "'1'='1' and p.is_ok ==:isOk and p.is_publish =:isPublish";
        }else{
            $conditions = "'1'='1' and p.is_ok!=:isOk and p.is_publish =:isPublish";
        }
        $params = array(
            ":isOk" => 1,        //is_ok不等于1，表明为未审核或审核未通过的职位信息。
            ":isPublish"=>1     //1表示公司端已发布
        );
        //查询条件
        if ($sCompanyName != null && "" != $sCompanyName) {
            $conditions .= " and c.name like :sCompanyName";
            $params[':sCompanyName'] = "%$sCompanyName%";
        }
        if($sCompanyUserName!=null&&""!=$sCompanyUserName){
            $conditions .= " and p.company_user_name like :sCompanyUserName";
            $params[':sCompanyUserName'] = "%$sCompanyUserName%";
        }
        if ($sCityId != null && "" != $sCityId && $sCityId>0) {
            $conditions .= " and p.city_id =:sCityId";
            $params[':sCityId'] = $sCityId;
        }
        if ($sPositionName != null && "" != $sPositionName) {
            $conditions .= " and p.name like :sPositionName";
            $params[':sPositionName'] = "%$sPositionName%";
        }
        if ($sCompanyReleaseTime != null && "" != $sCompanyReleaseTime &&$sCompanyReleaseTime>0) {
            $time = time() - $sCompanyReleaseTime * 3600;
            $date_time = date('Y-m-d H:i:s', $time);
            $conditions .= " and p.entering_time >=:dateTime";
            $params[':dateTime'] = $date_time;
        }
        if ($isJoinBigRecruitment != null && "" != $isJoinBigRecruitment) {
            $conditions .= " and p.is_join_big_recruitment =:isJoinBigRecruitment";
            $params[':isJoinBigRecruitment'] = $isJoinBigRecruitment;
        }
        if ($isJoinRecruitmentWeek != null && "" != $isJoinRecruitmentWeek) {
            $conditions .= " and p.is_join_recruitment_week =:isJoinRecruitmentWeek";
            $params[':isJoinRecruitmentWeek'] = $isJoinRecruitmentWeek;
        }
        //是否是名校名企，0：不是名校名企，1：名校，2：名企
        if($isElite!=null&&""!=$isElite){
            $conditions .= " and c.is_famous =:isElite";
            $params[':isElite'] = $isElite;
        }

        //得到查询数据
        $command = Yii::app()->db->createCommand()
            ->select('p.id,c.name as companyName,pc.name as city,p.company_user_name,p.name,p.entering_time,p.last_update,p.is_ok')
            ->from('t_position p')
            ->leftJoin('t_company c', 'c.id = p.company_id')
            ->leftJoin('t_param_city pc', 'pc.id = p.city_id')
            ->order('p.id desc');
//        var_dump($command);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', $page);
        $offset = ($currentPage - 1) * $pageSize;
        $model = $command->where($conditions, $params)->limit($pageSize)->offset($offset)->queryAll();
        $model = json_decode(CJSON::encode($model), true);
        //得到总记录数
        $command1 = Yii::app()->db->createCommand()
            ->select('count(*) as recordCount')
            ->from('t_position p')
            ->leftJoin('t_company c', 'c.id = p.company_id')
            ->leftJoin('t_param_city pc', 'pc.id = p.city_id');

        $record = $command1->where($conditions, $params)->queryAll();
        $recordCount = $record[0]['recordCount'];

        $list2='{"positionList":'.json_encode($model,JSON_UNESCAPED_UNICODE).',"dataCount":'.$recordCount.'}';
        echo $list2;

    }

    /**
     * 搜索
     */
    public function actionSearch(){
        $this->smarty->assign('current','position');//为显示侧边栏服务
        $cityList = City::model()->findAll();
        $this->smarty->assign( 'cityList' , $cityList ) ;

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_POST['sCompanyName']));
//        print "公司名：".$_POST['companyName'];
        if($_POST['sCityId']=='0'){
            $_POST['sCityId']='';
        }
        $this->smarty->assign('sCityId',$_POST['sCityId']);
//        print "城市：".$_POST['cityId'];
        $this->smarty->assign('sPositionName',trim($_POST['sPositionName']));
//        print "职位名：".$_POST['positionName'];
        $this->smarty->assign('sCompanyUserName',trim($_POST['sCompanyUserName']));
//        print "发布人：".$_POST['companyUserName'];
        if($_POST['sCompanyReleaseTime']=='0'){
            $_POST['sCompanyReleaseTime']='';
        }
        $this->smarty->assign('sCompanyReleaseTime',$_POST['sCompanyReleaseTime']);
//        print "发布时间：".$_POST['companyReleaseTime'];
        $this->smarty->assign('isJoinBigRecruitment',$_POST['isJoinBigRecruitment']);
//        print "是否参加大招会：".$_POST['isJoinBigRecruitment'];
        $this->smarty->assign('isJoinRecruitmentWeek',$_POST['isJoinRecruitmentWeek']);
//        print "是否参加招聘周：".$_POST['isJoinRecruitmentWeek'];
        $this->smarty->assign('isElite',$_POST['isElite']);
//        print "是否是名校名企：".$_POST['isElite'];
        $this->smarty->display('admin/position/position.html');
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
        //查询所有省份，包括直辖市
        $sql1 = 'select id,name from {{param_province}}';
        $provinceList = Province::model()->findAllBySql($sql1);
//        var_dump($provinceList);
        //查询招聘信息中的省份
        $selectedProvince = null;
        if ($position->city_id != null && $position->city_id > 0) {
            $sql2 = 'select province_id from {{param_city}} where id = ' . $position->city_id;
            $selectedProvince = City::model()->findBySql($sql2);
        }
//        var_dump($selectedProvince['province_id']);
        //查询被选中省的所有市
        if ($selectedProvince->province_id != null) {
//            if ($selectedProvince->province_id != 1 && $selectedProvince->province_id != 2
//                && $selectedProvince->province_id != 3 && $selectedProvince->province_id != 4
//                && $selectedProvince->province_id != 33 && $selectedProvince->province_id != 34
//            ) {
                $sql3 = 'select id,name from {{param_city}} where province_id = ' . $selectedProvince->province_id;
                $cityList = City::model()->findAllBySql($sql3);
//            }
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
        if ($specialtyIds1[0] != "0") {
            for ($i = 0; $i < (count($specialtyIds1) - 1); $i++) {
                $sql8 = 'select id,name,parent_id from {{study_specialty}} where id =' . $specialtyIds1[$i];
                $specialty = StudySpecialty::model()->findBySql($sql8);
                $specialtyList[$i]['id'] = $specialty->id;
                $specialtyList[$i]['name'] = $specialty->name;
                $specialtyList[$i]['parent_id'] = $specialty->parent_id;
            }
        }
        if ($specialtyIds1[0] == "0") {
            $specialtyList[0] = "0";
        }
//       var_dump($specialtyList);
        //公司名
        if(null!=$position->company_id&&$position->company_id>0){
            $sql8 ="select name from {{company}} where id =".$position->company_id;
            $companyName = Company::model()->findBySql($sql8);
            $companyName=$companyName->name;
        }
        //信息发布人
        if(null!=$position->company_id&&$position->company_id>0){
//            echo "公司id".$position->company_id;
            $sql9 ="select name from {{company_login_user}} where company_id=".$position->company_id;
            $companyUserName = CompanyLoginUser::model()->findAllBySql($sql9);
//            echo "信息发布人".$adminCompanyUserName;
//            $adminCompanyUserName =$adminCompanyUserName->name;
        }
        $this->smarty->assign('current','position');
        $this->smarty->assign('position', $position);
        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('provinceList', $provinceList);
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('majorSpecialtyList', $majorSpecialtyList);
        $this->smarty->assign('selectedProvince', $selectedProvince->province_id);
        $this->smarty->assign('positionType1', $positionType1);
        $this->smarty->assign('positionType2', $positionType2);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('companyName', $companyName);
        $this->smarty->assign('companyUserName', $companyUserName);

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',$_GET['sCompanyName']);
//        print "公司名：".$_POST['companyName'];
        $this->smarty->assign('sCityId',$_GET['sCityId']);
//        print "城市：".$_POST['cityId'];
        $this->smarty->assign('sPositionName',$_GET['sPositionName']);
//        print "职位名：".$_POST['positionName'];
        $this->smarty->assign('sCompanyUserName',$_GET['sCompanyUserName']);
//        print "发布人：".$_POST['companyUserName'];
        $this->smarty->assign('sCompanyReleaseTime',$_GET['sCompanyReleaseTime']);
//        print "发布时间：".$_POST['companyReleaseTime'];
        $this->smarty->assign('isJoinBigRecruitment',$_GET['isJoinBigRecruitment']);
//        print "是否参加大招会：".$_POST['isJoinBigRecruitment'];
        $this->smarty->assign('isJoinRecruitmentWeek',$_GET['isJoinRecruitmentWeek']);
//        print "是否参加招聘周：".$_POST['isJoinRecruitmentWeek'];
        $this->smarty->assign('isElite',$_GET['isElite']);
//        print "是否是名校名企：".$_POST['isElite'];

        $this->smarty->display('admin/position/edit.html');

    }

    //修改招聘信息
    public function actionEdit($id)
    {
        //职位信息
        $sql = 'select * from {{position}} where id = ' . $id;
        $position = Position::model()->findBySql($sql);

        //公司id
        $companyName = trim($_POST['companyName']);
        if ($companyName != null && "" != $companyName) {
            $company = Company::model()->findByAttributes(array('name'=>$companyName));
            $companyId=$company->id;
            if ($companyId != null && $companyId > 0) {
                $position->company_id = $companyId;
            }
        }
        //信息发布联系人
        if($_POST['companyUserName']!=null&&""!=$_POST['companyUserName']){
            $position->company_user_name = $_POST['companyUserName'];
        }
        //是否是教师
        if ($_POST['isTeacher'] == 0 || $_POST['isTeacher'] == 1) {
            $position->is_teacher = $_POST['isTeacher'];
        }
        //是否参加大招会
        if ($_POST['isJoinBigRecruitment'] == 2 || $_POST['isJoinBigRecruitment'] == 1) {
            $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        }
        //是否参加招聘周
        if ($_POST['isJoinRecruitmentWeek'] == 2 || $_POST['isJoinRecruitmentWeek'] == 1) {
            $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        }
        //职位名称
        if ($_POST['positionName'] != null && "" != trim($_POST['positionName'])) {
            $position->name = $_POST['positionName'];
        }
        //直辖市、特别行政区存储
        if ($_POST['provinceId'] != null && "" != $_POST['provinceId']) {
            //直辖市存储
            if ($_POST['provinceId'] == 1 || $_POST['provinceId'] == 2 || $_POST['provinceId'] == 3 || $_POST['provinceId'] == 4) {
                $position->city_id = $_POST['provinceId'];
            } else if ($_POST['provinceId'] == 33 || $_POST['provinceId'] == 34) {//香港，澳门特别行政区存储
                $position->city_id = $_POST['provinceId'] + 357;//加357可得两特别行政区在t_param_city中的id值
            }
        }
        //其他市存储
        if ($_POST['cityId'] != null && $_POST['cityId'] > 0) {
            $position->city_id = $_POST['cityId'];
        }
        //职位类型
        if ($_POST['positionType'] != null && $_POST['positionType'] > 0) {
            $position->type_id = $_POST['positionType'];
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
        if ($_POST['unlimited-professional'] == null || "" == $_POST['unlimited-professional']) {
            if ($_POST['specialtyIds'] != null && "" != $_POST['specialtyIds']) {
                $position->specialty_ids = $_POST['specialtyIds'];
            }
        } else {//选择了不限专业
            $position->specialty_ids = "0";
        }

        //$position->dead_time = date("Y-m-d",strtotime($_POST['deadTime']));
        $position->dead_time = $_POST['deadTime'];
        $position->position_duty = $_POST['positionDuty'];
        $position->position_need = $_POST['positionNeed'];
        $position->bright = $_POST['bright'];
        $position->descrption = $_POST['descrption'];
        //后台修改职位信息的时间也即后台最新发布职位时间（在未发布的前提下）
        if($position->is_publish==0){
            $position->entering_time = date('Y-m-d H:i:s', time());
        }
        //职位来源
        $position->position_source=$_POST['positionSource'];//1代表东北师大2代表6所部属3代表互联网
        //最近更新人
        $position->last_update=Yii::app()->session['user_name'];

        $position->save();


        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_POST['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_POST['sCityId']));
        $this->smarty->assign('sPositionName',trim($_POST['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_POST['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_POST['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_POST['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_POST['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_POST['isElite']));
        //返回列表页
        $this->smarty->display('admin/position/position.html');
    }

    //去增添页 页面待定
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
        $this->smarty->assign('current','position');

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',$_GET['sCompanyName']);
//        print "公司名：".$_POST['companyName'];
        $this->smarty->assign('sCityId',$_GET['sCityId']);
//        print "城市：".$_POST['cityId'];
        $this->smarty->assign('sPositionName',$_GET['sPositionName']);
//        print "职位名：".$_POST['positionName'];
        $this->smarty->assign('sCompanyUserName',$_GET['sCompanyUserName']);
//        print "发布人：".$_POST['companyUserName'];
        $this->smarty->assign('sCompanyReleaseTime',$_GET['sCompanyReleaseTime']);
//        print "发布时间：".$_POST['companyReleaseTime'];
        $this->smarty->assign('isJoinBigRecruitment',$_GET['isJoinBigRecruitment']);
//        print "是否参加大招会：".$_POST['isJoinBigRecruitment'];
        $this->smarty->assign('isJoinRecruitmentWeek',$_GET['isJoinRecruitmentWeek']);
//        print "是否参加招聘周：".$_POST['isJoinRecruitmentWeek'];
        $this->smarty->assign('isElite',$_GET['isElite']);
//        print "是否是名校名企：".$_POST['isElite'];

        $this->smarty->display('admin/position/add.html');
    }

    //管理员增加招聘信息
    public function actionAdd()
    {
        $position = new Position();
        //公司id
        $companyName = trim($_POST['companyName']);
        if ($companyName != null && "" != $companyName) {
            $company = Company::model()->findByAttributes(array('name'=>$companyName));
            $companyId=$company->id;
            if ($companyId != null && $companyId > 0) {
                $position->company_id = $companyId;
            }
        }

        //信息发布联系人
        if($_POST['companyUserName']!=null&&""!=$_POST['companyUserName']){
            $position->company_user_name = $_POST['companyUserName'];
        }

        //是否参加大招会
        if ($_POST['isJoinBigRecruitment'] == 1 || $_POST['isJoinBigRecruitment'] == 0) {
            $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        }
        //是否参加招聘周
        if ($_POST['isJoinRecruitmentWeek'] == 1 || $_POST['isJoinRecruitmentWeek'] == 0) {
            $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        }
        //是否是教师
        if ($_POST['isTeacher'] == 0 || $_POST['isTeacher'] == 1) {
            $position->is_teacher = $_POST['isTeacher'];
        }
        //职位名称
        if ($_POST['positionName'] != null && "" != trim($_POST['positionName'])) {
            $position->name = trim($_POST['positionName']);
        }
        //直辖市、特别行政区存储
        if ($_POST['provinceId'] != null && "" != $_POST['provinceId']) {
            //直辖市存储
            if ($_POST['provinceId'] == 1 || $_POST['provinceId'] == 2 || $_POST['provinceId'] == 3 || $_POST['provinceId'] == 4) {
                $position->city_id = $_POST['provinceId'];
            } else if ($_POST['provinceId'] == 33 || $_POST['provinceId'] == 34) {//香港，澳门特别行政区存储
                $position->city_id = $_POST['provinceId'] + 357;//加357可得两特别行政区在t_param_city中的id值
            }
        }
        //其他市存储
        if ($_POST['cityId'] != null && $_POST['cityId'] > 0) {
            $position->city_id = $_POST['cityId'];
        }
        //职位类别
        if ($_POST['positionType'] != null && $_POST['positionType'] > 0) {
            $position->type_id = $_POST['positionType'];
        }
        //学历要求
        if ($_POST['degreeId'] != null && $_POST['degreeId'] > 0) {
            $position->degree_id = $_POST['degreeId'];
        }
        //招聘人数
        if ($_POST['recruitmentNum'] != null && "" != trim($_POST['recruitmentNum'])) {
            $position->recruitment_num = trim($_POST['recruitmentNum']);
        }
        //所需专业（多个）
        if ($_POST['unlimited-professional'] == null || "" == $_POST['unlimited-professional']) {
            if ($_POST['specialtyIds'] != null && "" != $_POST['specialtyIds']) {
                $position->specialty_ids = $_POST['specialtyIds'];
            }
        } else {//选择了不限专业
            $position->specialty_ids = "0";
        }


        //后台录入职位信息的时间也即后台发布职位时间
        $position->entering_time = date('Y-m-d H:i:s', time());
        //后台最近更新人即后台发布该职位信息的管理员
        $position->last_update = Yii::app()->session['user_name'];//代表管理员
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
        $position->position_source=$_POST['positionSource'];//1代表东北师大2代表6所部属3代表互联网
        //是否是前台录入,0代表否，1代表是，区分前后台录入的唯一标识
        $position->is_front_input = 0;
        //是否发布，后台录入的默认为假发布，相当于公司那边发布了
        $position->is_publish = 1;
        $position->save();

        //返回正确页面所需参数
//        $currentPage = $_POST['currentPage'];
        $sCompanyName = trim($_POST['sCompanyName']);
        $sCityId = $_POST['sCityId'];
        $sPositionName = trim($_POST['sPositionName']);
        $sCompanyReleaseTime = $_POST['sCompanyReleaseTime'];//传过来的都是数字，代表多少个小时前发布的
        $sCompanyUserName = $_POST['sCompanyUserName'];
        $isJoinBigRecruitment = $_POST['isJoinBigRecruitment'];
        $isJoinRecruitmentWeek = $_POST['isJoinRecruitmentWeek'];
        $isElite = $_POST['isElite'];//是否是名校名企
        $this->redirect($this->createUrl("admin/position/recruitment/json?
        sCompanyName=$sCompanyName&sCityId=$sCityId&sPositionName=$sPositionName&sCompanyReleaseTime=$sCompanyReleaseTime
        &sCompanyUserName=$sCompanyUserName&isJoinBigRecruitment=$isJoinBigRecruitment&isJoinRecruitmentWeek=$isJoinRecruitmentWeek&isElite=$isElite"));
    }

    //删除招聘信息
    public function actionDel($id)
    {

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_GET['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_GET['sCityId']));
        $this->smarty->assign('sPositionName',trim($_GET['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_GET['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_GET['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_GET['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_GET['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_GET['isElite']));
//        $index = $_GET['index'];//序号
//        if($index%10==1){
//            $currentPage -=$currentPage;
//        }
        $sql = "select * from t_position where id=" . $id;
        $position = Position::model()->findBySql($sql);
        if (!empty($position)) {
            $position->delete();
        }
        //返回列表页
        $this->smarty->display('admin/position/position.html');
    }

    //查看详情页
    public function actionDetail($id)
    {
        //根据id查询职位
        $sql = 'select * from {{position}} where id = ' . $id;
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
        $sql4 = 'select province_id,name from {{param_city}} where id = ' . $position->city_id;
        $city = City::model()->findBySql($sql4);
//        var_dump($city['name']);

        //查询工作省份
        if ($position->city_id == 0 || $position->city_id == 1 || $position->city_id == 2 || $position->city_id == 3 || $position->city_id == 4||$position->city_id == 390||$position->city_id == 391) {
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
//        echo "省".$province;
        //查询需求专业
        $sql6 = 'select specialty_ids from {{position}} where id =' . $id;
        $specialtyIds = Position::model()->findBySql($sql6);
        $specialtyIds1 = explode(",", $specialtyIds->specialty_ids);
        if ($specialtyIds1[0] != "0") {
            for ($i = 0; $i < (count($specialtyIds1) - 1); $i++) {
                $sql7 = 'select name from {{study_specialty}} where id =' . $specialtyIds1[$i];
                $specialty = StudySpecialty::model()->findBySql($sql7);
                $specialtyList[$i] = $specialty->name;
            }
        }
        if ($specialtyIds1[0] == "0") {
            $specialtyList[0] = "0";
        }
//        var_dump($specialtyList);
        //获得截止日期
        if ($position->dead_time != null) {
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
            if ($year != 1969) {
                $dead_time = '' . $year . '年' . $month . '月' . $day . '日';
            } else {
                $dead_time = "无";
            }
        } else {
            $dead_time = "无";
        }
//        echo "日期".$dead_time;

        //设置审核状态
        if (null != $position->is_ok) {
            if ($position->is_ok == -1) {
                $isOk = "待审核";
            }
            if ($position->is_ok == 0) {
                $isOk = "审核未通过";
            }
            if ($position->is_ok == 1) {
                $isOk = "审核通过";
            }
        }
        //公司名
        if($position->company_id!=null&&$position->company_id>0){
            $company = Company::model()->findByPk($position->company_id);
            $companyName = $company->name;
        }
        //公司信息发布人信息
        if($position->company_id!=null&&$position->company_id>0&&$position->company_user_name!=null&&""!=$position->company_user_name) {
            $conditions = "'1'='1' and c.company_id!=:companyId and c.name =:companyUserName";
            $params = array(
                ":companyId" => $position->company_id,
                ":companyUserName" => $position->company_user_name
            );
            $command = Yii::app()->db->createCommand()
                ->select('phone,telephone,email')
                ->from('t_company_login_user c');
            $record = $command->where($conditions, $params)->queryAll();
            if($record->phone!=null&&""!=$record->phone){
                $phone = $record->phone;
            }else{
                $phone = '';
            }
            if($record->telephone!=null&&""!=$record->telephone){
                $telephone = $record->telephone;
            }else{
                $telephone = '';
            }
            if($record->email!=null&&""!=$record->email){
                $email = $record->email;
            }else{
                $email = '';
            }

        }
        //审核理由 要还是不要？
//        if($position->check_reason_id!=null&&$position->check_reason_id>0){
//            $positionCheck = PositionCheck::model()->findByPk($position->check_reason_id);
//            if($positionCheck->check_reason==1){
//                $checkReason ="信息不完整";
//            }else if($positionCheck->check_reason==1){
//                $checkReason ="信息有误";
//            }
//        }
        $this->smarty->assign('position', $position);
        $this->smarty->assign('brightsList', $brightsList);
        $this->smarty->assign('degree', $degree->name);
        $this->smarty->assign('positionType', $positionType->name);
        $this->smarty->assign('place', $place);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('dead_time', $dead_time);
        $this->smarty->assign('isOk', $isOk);
        $this->smarty->assign('companyName', $companyName);
//        $this->smarty->assign('checkReason', $checkReason);
        $this->smarty->assign('phone', $phone);
        $this->smarty->assign('telephone', $telephone);
        $this->smarty->assign('email', $email);

        //原样返回搜索参数，以便前台页面js获得参数
        $this->smarty->assign('sCompanyName',trim($_GET['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_GET['sCityId']));
        $this->smarty->assign('sPositionName',trim($_GET['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_GET['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_GET['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_GET['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_GET['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_GET['isElite']));

        $this->smarty->display('admin/position/detail.html');
    }

    //根据公司名字查找该公司的所有信息发布人
    public function actionPublisherJson(){
        $code = 0;//标识位，0为没这个公司，1为有公司但没发布人，2为有公司有发布人
        $companyName = trim($_GET['companyName']);
        $company = Company::model()->findByAttributes(array('name'=>$companyName));
        $companyId=$company->id;
//        echo "companyId:".$companyId;
        if($companyId!=null&&$companyId>0){
            $conditions ="'1'='1' and tclu.company_id =:companyId ";
            $params = array(':companyId' => $companyId);
            $command1 = Yii::app()->db->createCommand()
                ->select('name')
                ->from('t_company_login_user tclu');
            $companyLoginUser = $command1->where($conditions, $params)->queryAll();
            $publisher = json_decode(CJSON::encode($companyLoginUser), true);

            if($companyLoginUser!=null&&""!=$companyLoginUser){
              $code = 2;
                $list='{"code":'.$code.',"publisher":'.json_encode($publisher,JSON_UNESCAPED_UNICODE).'}';
                echo $list;
            }else{//公司存在，但没有信息发布人
                $code =1;
                $list2 ='{"code":'.$code.'}';
                echo $list2;
            }
//            echo "发布人".$CompanyLoginUser;
        }else{//不存在这样的公司
            $list3 ='{"code":'.$code.'}';
            echo $list3;
        }
    }

    //招聘信息审核
    public function actionCheck()
    {
        $positionId = $_GET['positionId'];
//        echo "positionId".$positionId;
        $isOk = $_GET['isOk'];
        $checkReason = $_GET['checkReason'];
        $verifierId = Yii::app()->session['user_name'];//审核人id即管理员id

        $positionCheck = new PositionCheck();
        if($positionId!=null&&$positionId>0){
            $positionCheck->position_id = $positionId;
        }
        if($checkReason==1||$checkReason==2){
            $positionCheck->check_reason = $_GET['checkReason'];
        }
        if($verifierId!=null&&$verifierId>0){
            $positionCheck->verifier_id=$verifierId;
        }
        $sql ="select * from {{position}} where id=".$positionId;
        $position = Position::model()->findBySql($sql);
//        var_dump($position);
        $position->is_ok = $isOk;
        if($isOk==0){//如果审核未通过，则招聘信息显示未发布
            $position->is_publish=0;
        }
        $position->save();

        $positionCheck->save();
        //返回正确页面

        $this->smarty->assign('sCompanyName',trim($_GET['sCompanyName']));
        $this->smarty->assign('sCityId',trim($_GET['sCityId']));
        $this->smarty->assign('sPositionName',trim($_GET['sPositionName']));
        $this->smarty->assign('sCompanyUserName',trim($_GET['sCompanyUserName']));
        $this->smarty->assign('sCompanyReleaseTime',trim($_GET['sCompanyReleaseTime']));
        $this->smarty->assign('isJoinBigRecruitment',trim($_GET['isJoinBigRecruitment']));
        $this->smarty->assign('isJoinRecruitmentWeek',trim($_GET['isJoinRecruitmentWeek']));
        $this->smarty->assign('isElite',trim($_GET['isElite']));
        $this->smarty->display('admin/position/position.html');
//        $this->redirect($this->createUrl("admin/position/recruitment/list?
//        sCompanyName=$sCompanyName&sCityId=$sCityId&sPositionName=$sPositionName&sCompanyReleaseTime=$sCompanyReleaseTime
//        &sCompanyUserName=$sCompanyUserName&isJoinBigRecruitment=$isJoinBigRecruitment&isJoinRecruitmentWeek=$isJoinRecruitmentWeek&isElite=$isElite"));


    }


} 
