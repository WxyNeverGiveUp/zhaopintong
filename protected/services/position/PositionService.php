<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-10
 * Time: 上午1:39
 */



class PositionService {

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

    public function search3($user_id=0,$page, $kind, $searchWord, $propertyId, $locationId, $positionTypeId,$degreeId,$messageSourceId,$majorId,
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
        
        $data = $positionModel->with('collection')->findAll($cri);
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


    public function detail($id){
        $value = Position::model()->with('degree','positiontype','positionspecialty','city','brightenedlist','positioncontacts')->findByPk($id);
        $value->views = $value->views+1;
        $value->save();
        if(isset($value->id))
            $positionDetail['id'] = $value->id;
        else
            $positionDetail['id'] = "无";
        if(isset($value->name))
            $positionDetail['name'] = $value->name;
        else
            $positionDetail['name'] = "无";
        if(isset($value->entering_time))
            $positionDetail['entering_time'] =date('m-d', strtotime($value->entering_time));
        else
            $positionDetail['entering_time'] = "无";
        if(isset($value->views))
            $positionDetail['views'] = $value->views;
        else
            $positionDetail['views'] = "无";
        if(isset($value->resume_num))
            $positionDetail['resume_num'] = $value->resume_num;
        else
            $positionDetail['resume_num'] = "无";
        if(isset($value->brightenedlist))
            $positionDetail['brightenedlist'] = $value->brightenedlist;
        else
            $positionDetail['brightenedlist'] = "";
        if($value->is_teacher==1){
            if(isset($value->type_id)&&$value->type_id==1)
                $positionDetail['positiontype'] = "学前教师";
            elseif(isset($value->type_id)&&$value->type_id==2)
                $positionDetail['positiontype'] = "小学教师";
            elseif(isset($value->type_id)&&$value->type_id==3)
                $positionDetail['positiontype'] = "初中教师";
            elseif(isset($value->type_id)&&$value->type_id==4)
                $positionDetail['positiontype'] = "高中教师";
            elseif(isset($value->type_id)&&$value->type_id==5)
                $positionDetail['positiontype'] = "大学教师";
            elseif(isset($value->type_id)&&$value->type_id==6)
                $positionDetail['positiontype'] = "培训辅导";
            elseif(isset($value->type_id)&&$value->type_id==7)
                $positionDetail['positiontype'] = "其他";
            else
                $positionDetail['positiontype'] = "无";
        }
        else{
        if(isset($value->positiontype->name))
            $positionDetail['positiontype'] = $value->positiontype->name;
        else
            $positionDetail['positiontype'] = "无";
        }
        if(isset($value->degree->name))
            $positionDetail['degree'] = $value->degree->name;
        else
            $positionDetail['degree'] = "无";
        if(isset($value->positionspecialty->name))
            $positionDetail['positionspecialty'] = $value->positionspecialty->name;
        else
            $positionDetail['positionspecialty'] = "无";
        if(isset($value->recruitment_num))
            $positionDetail['recruitment_num'] = $value->recruitment_num;
        else
            $positionDetail['recruitment_num'] = "无";

        if(isset($value->city->name))
            $positionDetail['city'] = $value->city->name;
        else
            $positionDetail['city'] = "无";
        if(isset($value->position_source))
            $positionDetail['position_source'] = $value->position_source;
        else
            $positionDetail['position_source'] = "0";
        if(isset($value->position_duty))
            $positionDetail['position_duty'] = $value->position_duty;
        else
            $positionDetail['position_duty'] = "无";
        if(isset($value->collection))
        $coll = $value->collection;
        if(!empty($coll))
            $positionDetail['collection'] = 1;
        else
            $positionDetail['collection'] = 0;
        if(isset($value->positioncontacts->name))
            $positionDetail['contactName'] = $value->positioncontacts->name;
        else
            $positionDetail['contactName'] = "无";
        if(isset($value->positioncontacts->telephone))
            $positionDetail['contactTelephone'] = $value->positioncontacts->telephone;
        else
            $positionDetail['contactTelephone'] = "无";
        if(isset($value->positioncontacts->cellphone))
            $positionDetail['contactCellphone'] = $value->positioncontacts->cellphone;
        else
            $positionDetail['contactCellphone'] = "无";
        if(isset($value->positioncontacts->email))
            $positionDetail['contactEmail'] = $value->positioncontacts->email;
        else
            $positionDetail['contactEmail'] = "无";
        return $positionDetail;
    }

    public function searchAdmin($page,$keyword,$cityId,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$isEliteSchool,$isEliteFirm){

        $offset = ($page-1)*10;
        $cri = new CDbCriteria();
        $cri->select = 'id,name,last_update';
        $conditions = "1=1 ";
        $params = array();
        if ($keyword != null && $keyword!=""){
            $conditions .= " and t.name LIKE :keyword ";
            $params[':keyword']='%'.$keyword.'%';
        }
        if ($cityId != 0 && $cityId!="0"){
            $conditions .= " and t.city_id = :cityId ";
            $params[':cityId']=$cityId;
        }
        if ($isJoinBigRecruitment != null&& $isJoinBigRecruitment!="0"){
            $conditions .= " and t.is_join_big_recruitment =:isJoinBigRecruitment ";
            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
        }
        if ($isJoinRecruitmentWeek != null&& $isJoinRecruitmentWeek!="0"){
            $conditions .= " and t.is_join_recruitment_week =:isJoinRecruitmentWeek ";
            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
        }
        if ($isEliteSchool != 0 && $isEliteSchool!="0"&&$isEliteSchool != null&&$isEliteSchool != null){
            $conditions .= " and company.is_famous = 1";
        }
        if ($isEliteFirm != 0 && $isEliteFirm!="0"&&$isEliteFirm != null&&$isEliteFirm != null){
            $conditions .= " and company.is_famous = 2";
        }
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = Position::model()->with('company')->count($cri);
        $cri->limit =10;
        $cri->offset = $offset;
        $data = Position::model()->with('company')->findAll($cri);
        $searchListOnePage = array();
        $searchListOnePage['list']=$data;
        $searchListOnePage['recordCount']=$recordCount;
        return $searchListOnePage;
    }

    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new PositionService();

        }
        return self::$instance;
    }
    private function __construct(){

    }

    private function __clone(){

    }
} 