<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-10
 * Time: 上午1:39
 */



class RecruitmentService {

    public function indexList(){
        $conditions ="'1'='1' and c.is_teacher = 0 ";
        $params = array();

        $command = Yii::app()->db->createCommand()
            ->select('c.id,c.name,p.name as city_name,c.entering_time,co.name as companyName')
            ->from('t_position c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->join('t_company co', 'co.id=c.company_id')
            ->order('c.entering_time desc');
        $command2 = Yii::app()->db->createCommand()
            ->select('c.id,c.name,p.name as city_name,c.entering_time,co.name as companyName')
            ->from('t_position c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->join('t_company co', 'co.id=c.company_id')
            ->order('c.entering_time desc');
        $offset = 0;
        $offset2 = 8;
        return array($command->where($conditions,$params)->limit(8)->offset($offset)->queryAll(),$command2->where($conditions,$params)->limit(8)->offset($offset2)->queryAll());
    }


    public function indexTeacherList(){
        $conditions ="'1'='1' and c.is_teacher = 1 ";
        $params = array();

        $command = Yii::app()->db->createCommand()
            ->select('c.id,c.name,p.name as city_name,c.entering_time,co.name as companyName')
            ->from('t_position c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->join('t_company co', 'co.id=c.company_id')
            ->order('c.entering_time desc');
        $command2 = Yii::app()->db->createCommand()
            ->select('c.id,c.name,p.name as city_name,c.entering_time,co.name as companyName')
            ->from('t_position c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->join('t_company co', 'co.id=c.company_id')
            ->order('c.entering_time desc');
        $offset = 0;
        $offset2 = 8;
        return array($command->where($conditions,$params)->limit(8)->offset($offset)->queryAll(),$command2->where($conditions,$params)->limit(8)->offset($offset2)->queryAll());
    }


    public function search2($page, $kind, $searchWord, $propertyId, $locationId, $positionTypeId,$degreeId,$messageSourceId,$majorId,
                            $isJoinBigRecruitment , $isJoinRecruitmentWeek ,$heatSort,$timeSort){
        $offset = ($page-1)*10;
        $cri = new CDbCriteria();
        //$cri->addCondition('t.collection.user_id',$user_id);
        $cri->with = array('company','degree','positionspecialty','city','brightened');
        $cri->select = 'id,name,position_source,company_id,entering_time';
        $conditions = "1=1 AND (t.is_front_input=0 OR t.is_ok=1)";
        $params = array();
        if ($searchWord != null && $searchWord!=""){
            $conditions .= " and (t.name LIKE :keyword OR company.name LIKE :keyword)";
            $params[':keyword']='%'.$searchWord.'%';
        }
        if ($kind != null && $kind!=""&& $kind!=2&& $kind!='2'){
            $conditions .= " and t.is_teacher = :kind ";
            $params[':kind']=$kind;
        }
        if ($propertyId != 0 && $propertyId!="0"){
            $conditions .= " and company.property_id = :propertyId ";
            $params[':propertyId']=$propertyId;
        }
        if ($locationId != 0 && $locationId!="0"){
            $conditions .= " and t.city_id = :locationId ";
            $params[':locationId']=$locationId;
        }
        if ($positionTypeId != 0 && $positionTypeId!="0"){
            $conditions .= " and t.type_id =:typeId ";
            $params[':typeId']=$positionTypeId;
        }
        if ($degreeId != 0 && $degreeId!="0"){
            $conditions .= " and t.degree_id =:degreeId ";
            $params[':degreeId']=$degreeId;
        }
        if ($messageSourceId != 0 && $messageSourceId!="0"){
            $conditions .= " and t.position_source =:messageSourceId ";
            $params[':messageSourceId']=$messageSourceId;
        }
        if ($majorId != 0 && $majorId!="0"){
            $conditions .= " and t.specialty_id =:majorId ";
            $params[':majorId']=$majorId;
        }
        if ($isJoinBigRecruitment != 0){
            $conditions .= " and t.is_join_big_recruitment =:isJoinBigRecruitment ";
            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
        }
        if ($isJoinRecruitmentWeek != 0){
            $conditions .= " and t.is_join_recruitment_week =:isJoinRecruitmentWeek ";
            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
        }
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = Position::model()->count($cri);
        $cri->limit =10;
        $cri->offset = $offset;
        if($timeSort==1)
            $cri->order = 't.entering_time DESC';
        //$cri->having = 'collection.id='.$user_id;
        // $cri->join = "LEFT JOIN t_position_user on t.id=t_position_user.position_id AND t_position_user.user_id=$user_id";
        //$cri->condition  = 't_position_user.user_id=1';
        $data = Position::model()->with('collection')->findAll($cri);
        foreach ($data as $key => $value) {
            if(isset($value->id))
                $position[$key]['id'] = $value->id;
            else
                $position[$key]['id'] = "无";
            if(isset($value->company_id))
                $position[$key]['companyId'] = $value->company_id;
            else
                $position[$key]['companyId'] = "无";
            if(isset($value->name))
                $position[$key]['posName'] = $value->name;
            else
                $position[$key]['posName'] = "无";
            if(isset($value->company->name))
                $position[$key]['comName'] = $value->company->name;
            else
                $position[$key]['comName'] = "无";
            if(isset($value->position_source)){
                if($value->position_source=='1')
            $position[$key]['publish'] = '东北师大';
            elseif($value->position_source=='2')
                $position[$key]['publish'] = '6所部属';
             else
                 $position[$key]['publish'] = '互联网';
            }
            else
                $position[$key]['publish'] = "无";
            if(isset($value->city->name))
                $position[$key]['city'] = $value->city->name;
            else
                $position[$key]['city'] = "无";

            if(isset($value->degree->name))
                $position[$key]['degree'] = $value->degree->name;
            else
                $position[$key]['degree'] = "无";
            if(isset($value->positionspecialty->name))
                $position[$key]['major'] = $value->positionspecialty->name;
            else
                $position[$key]['major'] = "无";
            if(isset($value->brightened[0]->name))
                $position[$key]['specialA'] = $value->brightened[0]->name;
            else
                $position[$key]['specialA'] = "无";
            if(isset($value->brightened[1]->name))
                $position[$key]['specialB'] = $value->brightened[1]->name;
            else
                $position[$key]['specialB'] = "无";
            if(!empty($value->collection))
                $position[$key]['collection'] = 1;
            else
                $position[$key]['collection'] = 0;
            if(isset($value->collectionCount))
                $position[$key]['followNumber'] = $value->collectionCount;
            else
                $position[$key]['followNumber'] = 0;
            if(isset($value->entering_time))
                $position[$key]['enteringTime'] = date('Y-m-d',strtotime($value->entering_time));
            else
                $position[$key]['enteringTime'] = "无";
        }
        

        $searchListOnePage = array();
        if($heatSort==1&&$position!=null) {
            usort($position, function ($a, $b) {
                return $b['followNumber'] - $a['followNumber'];
            });
        }
        $searchListOnePage['list']=$position;
        $searchListOnePage['recordCount']=$recordCount;
        return $searchListOnePage;
    }

//    public function search3($user_id=0,$page, $kind, $searchWord, $propertyId, $locationId, $positionTypeId,$degreeId,$messageSourceId,$majorId,
//                            $isJoinBigRecruitment , $isJoinRecruitmentWeek ,$heatSort,$timeSort){
//        $offset = ($page-1)*10;
//        $cri = new CDbCriteria();
//        //$cri->addCondition('t.collection.user_id',$user_id);
//        $cri->with = array('company','degree','positionspecialty','city','brightened');
//        $cri->select = 'id,name,position_source,company_id,entering_time';
//        $conditions = "1=1 AND (t.is_front_input=0 OR t.is_ok=1)";
//        $params = array();
//        if ($searchWord != null && $searchWord!=""){
//            $conditions .= " and (t.name LIKE :keyword OR company.name LIKE :keyword)";
//            $params[':keyword']='%'.$searchWord.'%';
//        }
//        if ($kind != null && $kind!=""&& $kind!=2&& $kind!='2'){
//            $conditions .= " and t.is_teacher = :kind ";
//            $params[':kind']=$kind;
//        }
//        if ($propertyId != 0 && $propertyId!="0"){
//            $conditions .= " and company.property_id = :propertyId ";
//            $params[':propertyId']=$propertyId;
//        }
//        if ($locationId != 0 && $locationId!="0"){
//            $conditions .= " and t.city_id = :locationId ";
//            $params[':locationId']=$locationId;
//        }
//        if ($positionTypeId != 0 && $positionTypeId!="0"){
//            $conditions .= " and t.type_id =:typeId ";
//            $params[':typeId']=$positionTypeId;
//        }
//        if ($degreeId != 0 && $degreeId!="0"){
//            $conditions .= " and t.degree_id =:degreeId ";
//            $params[':degreeId']=$degreeId;
//        }
//        if ($messageSourceId != 0 && $messageSourceId!="0"){
//            $conditions .= " and t.position_source =:messageSourceId ";
//            $params[':messageSourceId']=$messageSourceId;
//        }
//        if ($majorId != 0 && $majorId!="0"){
//            $conditions .= " and t.specialty_id =:majorId ";
//            $params[':majorId']=$majorId;
//        }
//        if ($isJoinBigRecruitment != 0){
//            $conditions .= " and t.is_join_big_recruitment =:isJoinBigRecruitment ";
//            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
//        }
//        if ($isJoinRecruitmentWeek != 0){
//            $conditions .= " and t.is_join_recruitment_week =:isJoinRecruitmentWeek ";
//            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
//        }
//        $cri->condition=$conditions;
//        $cri->params=$params;
//        $recordCount = Position::model()->count($cri);
//        $cri->limit =10;
//        $cri->offset = $offset;
//        if($timeSort==1)
//            $cri->order = 't.entering_time DESC';
//        //$cri->having = 'collection.id='.$user_id;
//        // $cri->join = "LEFT JOIN t_position_user on t.id=t_position_user.position_id AND t_position_user.user_id=$user_id";
//        //$cri->condition  = 't_position_user.user_id=1';
//
//        $data = $positionModel->with('collection')->findAll($cri);
//        foreach ($data as $key => $value) {
//            if(isset($value->id))
//                $position[$key]['id'] = $value->id;
//            else
//                $position[$key]['id'] = "无";
//            if(isset($value->company_id))
//                $position[$key]['companyId'] = $value->company_id;
//            else
//                $position[$key]['companyId'] = "无";
//            if(isset($value->name))
//                $position[$key]['posName'] = $value->name;
//            else
//                $position[$key]['posName'] = "无";
//            if(isset($value->company->name))
//                $position[$key]['comName'] = $value->company->name;
//            else
//                $position[$key]['comName'] = "无";
//            if(isset($value->position_source)){
//                if($value->position_source=='1')
//            $position[$key]['publish'] = '东北师大';
//            elseif($value->position_source=='2')
//                $position[$key]['publish'] = '6所部属';
//             else
//                 $position[$key]['publish'] = '互联网';
//            }
//            else
//                $position[$key]['publish'] = "无";
//            if(isset($value->city->name))
//                $position[$key]['city'] = $value->city->name;
//            else
//                $position[$key]['city'] = "无";
//
//            if(isset($value->degree->name))
//                $position[$key]['degree'] = $value->degree->name;
//            else
//                $position[$key]['degree'] = "无";
//            if(isset($value->positionspecialty->name))
//                $position[$key]['major'] = $value->positionspecialty->name;
//            else
//                $position[$key]['major'] = "无";
//            if(isset($value->brightened[0]->name))
//                $position[$key]['specialA'] = $value->brightened[0]->name;
//            else
//                $position[$key]['specialA'] = "无";
//            if(isset($value->brightened[1]->name))
//                $position[$key]['specialB'] = $value->brightened[1]->name;
//            else
//                $position[$key]['specialB'] = "无";
//            if(!empty($value->collection))
//                $position[$key]['collection'] = 1;
//            else
//                $position[$key]['collection'] = 0;
//            if(isset($value->collectionCount))
//                $position[$key]['followNumber'] = $value->collectionCount;
//            else
//                $position[$key]['followNumber'] = 0;
//            if(isset($value->entering_time))
//                $position[$key]['enteringTime'] = date('Y-m-d',strtotime($value->entering_time));
//            else
//                $position[$key]['enteringTime'] = "无";
//        }
//
//
//        $searchListOnePage = array();
//        if($heatSort==1&&$position!=null) {
//            usort($position, function ($a, $b) {
//                return $b['followNumber'] - $a['followNumber'];
//            });
//        }
//        $searchListOnePage['list']=$position;
//        $searchListOnePage['recordCount']=$recordCount;
//        return $searchListOnePage;
//    }

//查看招聘信息详情
    public function detail($id){
        $detail = array();
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
        if ($position->city_id == 0 || $position->city_id == 1 || $position->city_id == 2 || $position->city_id == 3 || $position->city_id == 4) {
            $province = "";
        } else {
            $sql5 = 'select name from {{param_province}} where id =' . $city->province_id;
            $province = Province::model()->findBySql($sql5);
        }
        //拼凑出工作地点
        if($province==""){
            $place = $city->name;
        }else{
            $place =$province->name."&nbsp;".$city->name;
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

        //公司用户（招聘信息发布联系人）
//        $companyUserName = "wangshuhang";
        $companyUserName = Yii::app()->session['companyUserName'];

        //招聘信息来源 1代表东北师大2代表6所部属3代表互联网
        if(null!=$position->position_source){
            if($position->position_source==1){
                $positionSource = "东北师大";
            }else if($position->position_source==2){
                $positionSource ="6所部署";
            }else if($position->position_source==3){
                $positionSource ="互联网";
            }else{
                $positionSource ="未知";
            }
        }

      //发布该招聘信息的公司的名称
        $sql8 = "select name from t_company where id = ".$position->company_id;
        $company = Company::model()->findBySql($sql8);
        $companyName = $company->name;

       //审核原因
        $sql9 = "select * from t_position_check where position_id = ".$position->check_reason_id;
        $positionCheck = PositionCheck::model()->findBySql($sql9);
        $checkReason = $positionCheck->check_reason;

        $detail['position'] = $position;
        $detail['brightsList'] = $brightsList;
        $detail['degree'] = $degree->name;
        $detail['positionType'] = $positionType->name;
        $detail['place']=$place;
        $detail['specialtyList']=$specialtyList;
        $detail['dead_time']=$dead_time;
        $detail['companyUserName'] =$companyUserName;
        $detail['isOk']=$isOk;
        $detail['positionSource']=$positionSource;
        $detail['companyName']=$companyName;
        $detail['checkReason']=$checkReason;

        return $detail;
    }

//根据条件查询招聘信息列表
    public function searchAdmin($page,$companyName,$positionName,$cityId,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$isEliteSchool,$isEliteFirm,$isOk){

        $conditions = '1=1 and p.company_id = c.id';
        $params = array();
        if(null!=$companyName && ""!=trim($companyName)){
            $conditions .=" and p.company_id LIKE :companyName";
            $params[':companyName'] = '%'.trim($companyName).'%';
        }
        if (null!=$positionName && ""!=trim($positionName)){
            $conditions .= " and p.name LIKE :positionName ";
            $params[':positionName']='%'.trim($positionName).'%';
        }
        if ($cityId > 0 && "0"!=$cityId){
            $conditions .= " and p.city_id = :cityId";
            $params[':cityId']=$cityId;
        }
        if (null!=$isJoinBigRecruitment && ""!=$isJoinBigRecruitment){
            $conditions .= " and p.is_join_big_recruitment =:isJoinBigRecruitment ";
            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
        }
        if (null!=$isJoinRecruitmentWeek && "0"!=$isJoinRecruitmentWeek){
            $conditions .= " and p.is_join_recruitment_week =:isJoinRecruitmentWeek ";
            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
        }
        if (0!=$isEliteSchool&& "0"!=$isEliteSchool&& null!=$isEliteSchool){
            $conditions .= " and c.is_famous = 1";
        }
        if (0!=$isEliteFirm&& "0"!=$isEliteFirm&&null!=$isEliteFirm){
            $conditions .= " and c.is_famous = 2";
        }
        if(null!=$isOk){
         if(1==$isOk){
             $conditions .=" and p.is_ok =".$isOk;
         }else{
             $conditions .=" and p.is_ok != 1";
         }
        }
        $command = Yii::app()->db->createCommand()
            ->select('p.id,c.name,p.name,p.release_time,p.dead_time,p.company_user_name,p.is_ok,p.last_update')
            ->from('t_position p,t_company c')
            ->order('p.id desc');
        $offset = ($page-1)*10;
        $pageSize = 10;
        $model = $command->where($conditions, $params)->limit($pageSize)->offset($offset)->queryAll();

        $recordCount = count($model);
        $searchListOnePage = array();
        $searchListOnePage['list']=$model;
        $searchListOnePage['recordCount']=$recordCount;
        return $searchListOnePage;
    }

    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new RecruitmentService();

        }
        return self::$instance;
    }

    private function __construct(){

    }

    private function __clone(){

    }
} 