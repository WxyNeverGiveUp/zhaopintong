<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/11
 * Time: 14:59
 */

class CompanyService{
    public function search($page,$keyword,$propertyId, $tradeId, $isFamous,
                           $isJoinBigRecruitment, $isJoinRecruitmentWeek){

        $conditions = "1=1 ";
        $params = array();
        if ($keyword != null && $keyword!=""){
            $conditions .= " and c.name LIKE :keyword ";
            $params[':keyword']='%'.$keyword.'%';
        }
        if ($propertyId != 0 && $propertyId!="0"){
            $conditions .= " and c.property_id = :propertyId ";
            $params[':propertyId']=$propertyId;
        }
        /*if ($cityId != 0 && $cityId!="0"){
            $conditions .= " and c.city_id = :cityId ";
            $params[':cityId']=$cityId;
        }*/
        if ($tradeId != 0 && $tradeId!="0"){
            $conditions .= " and c.trade_id =:tradeId ";
            $params[':tradeId']=$tradeId;
        }
        if ($isFamous != 0){
            $conditions .= " and c.is_famous =:isFamous ";
            $params[':isFamous']=$isFamous;
        }
        if ($isJoinBigRecruitment != 0){
            $conditions .= " and c.is_join_big_recruitment =:isJoinBigRecruitment ";
            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
        }
        if ($isJoinRecruitmentWeek != 0){
            $conditions .= " and c.is_join_recruitment_week =:isJoinRecruitmentWeek ";
            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.id, c.name, p.name property_name, t.name trade_name,c.is_join_big_recruitment,c.is_join_recruitment_week,c.logo,b.name brightened_name')
            ->from('t_company c')
            ->join('t_company_property p', 'c.property_id=p.id')
            //->join('t_param_city city', 'c.city_id=city.id')
            ->join('t_company_trade t', 'c.trade_id=t.id')
            ->leftJoin('t_brightened b','c.id=b.related_id')
            ->order('concern_num desc');
        $command2 = clone $command;
        $recordCount = count($command->where($conditions,$params)->queryAll());
        $offset = ($page-1)*10;

        $searchListOnePage = array();
        $searchListOnePage['list']=$command2->where($conditions,$params)->limit(10)->offset($offset)->queryAll();
        $searchListOnePage['recordCount']=$recordCount;

        return $searchListOnePage;
    }

    public function search2($page,$searchWord,$propertyId, $locationId, $industryId, $isEliteFirm,$isEliteSchool,
                           $isJoinBigRecruitment, $isJoinRecruitmentWeek,$heatSort,$timeSort){
        $offset = ($page-1)*10;
        $cri = new CDbCriteria();
        $cri->with = array('companytrade','companyproperty','city','companycity','concernCount','concern','brightened');
        $cri->select = 'id,name,is_famous,last_update';
        $conditions = "1=1 AND (t.is_front_input=0 OR t.is_ok=1)";
        $params = array();
        if ($searchWord != null && $searchWord!=""){
            $conditions .= " and t.name LIKE :keyword ";
            $params[':keyword']='%'.$searchWord.'%';
        }
        if ($propertyId != 0 && $propertyId!="0"){
            $conditions .= " and t.property_id = :propertyId ";
            $params[':propertyId']=$propertyId;
        }
        if ($locationId != 0 && $locationId!="0"){
            $conditions .= " and companycity.city_id = :locationId ";
            $params[':locationId']=$locationId;
        }
        if ($industryId != 0 && $industryId!="0"){
            $conditions .= " and t.trade_id =:tradeId ";
            $params[':tradeId']=$industryId;
        }
        if ($isEliteFirm == 1&&$isEliteSchool==0){
            $conditions .= " and t.is_famous =2 ";
        }
        if ($isEliteFirm == 0&&$isEliteSchool==1){
            $conditions .= " and t.is_famous =1";
        }
        if ($isEliteFirm ==1&&$isEliteSchool==1){
            $conditions .= " and (t.is_famous =1 or t.is_famous =2)";
        }
        if ($isJoinBigRecruitment != null){
            $conditions .= " and t.is_join_big_recruitment =:isJoinBigRecruitment ";
            $params[':isJoinBigRecruitment']=$isJoinBigRecruitment;
        }
        if ($isJoinRecruitmentWeek != null){
            $conditions .= " and t.is_join_recruitment_week =:isJoinRecruitmentWeek ";
            $params[':isJoinRecruitmentWeek']=$isJoinRecruitmentWeek;
        }
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = Company::model()->count($cri);
        $cri->group = 't.id';
        $cri->limit =10;
        $cri->offset = $offset;
        if($timeSort==1)
            $cri->order = 'entering_time DESC';

        $data = Company::model()->findAll($cri);
        foreach ($data as $key => $value) {
            if(isset($value->id))
                $company[$key]['id'] = $value->id;
            else
                $company[$key]['id'] = "无";
            if(isset($value->name))
                $company[$key]['name'] = $value->name;
            else
                $company[$key]['name'] = "无";
            if(isset($value->companytrade->name))
                $company[$key]['trade_name'] = $value->companytrade->name;
            else
                $company[$key]['trade_name'] = "无";
            if(isset($value->companyproperty->name))
                $company[$key]['property_name'] = $value->companyproperty->name;
            else
                $company[$key]['property_name'] = "无";
            if(isset($value->brightened[0]->name))
                $company[$key]['brightspot1'] = $value->brightened[0]->name;
            else
                $company[$key]['brightspot1'] = "无";
            if(isset($value->brightened[1]->name))
                $company[$key]['brightspot2'] = $value->brightened[1]->name;
            else
                $company[$key]['brightspot2'] = "无";
            if(isset($value->concernCount))
                $company[$key]['followNumber'] = $value->concernCount;
            else
                $company[$key]['followNumber'] = 0;
            if(!empty($value->concern))
                $company[$key]['isfollow'] = 0;
            else
                $company[$key]['isfollow'] = 1;
            if(isset($value->last_update))
                $company[$key]['lastUpdate'] = $value->last_update;
            else
                $company[$key]['lastUpdate'] = "无";
            if(!empty($value->city)) {
                $cityList = '';
                $i = 0;
                $count = count($value->city);
                foreach ($value->city as $city) {
                    $i++;
                    if ( $i === $count){
                        $cityList .= $city->name;
                    }
                    else {
                        $cityList .= $city->name.',';
                    }
                }
                $company[$key]['city'] = $cityList;
            }
            else
                $company[$key]['city'] = "无";

        }
        $searchListOnePage = array();
        if($heatSort==1) {
            usort($company, function ($a, $b) {
                return $b['followNumber'] - $a['followNumber'];
            });
        }
        $searchListOnePage['list']=$company;
        $searchListOnePage['recordCount']=$recordCount;
        return $searchListOnePage;
    }

    public function getSchoolBrotherByUserId($userId){

        $conditions = "1=1 ";
        $params = array();
        if ($userId != null && $userId!=""){
            $conditions .= " and c.user_id =:userId ";
            $conditions .= " and p.user_id =:userId ";
            $params[':userId']=$userId;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.user_id,c.realname, c.head_url, p.school_name schoolName, p.major_name majorName')
            ->from('t_user_detail c,t_study_experience p');
        $record = $command->where($conditions,$params)->queryRow();
        return $record;
    }

    public function detail($companyId){
        $value = Company::model()->with('companytrade','companyproperty','city','companypic')->findByPk($companyId);
        if(isset($value->name))
            $companyDetail['id'] = $value->id;
        else
            $companyDetail['id'] = "无";
        if(isset($value->name))
        $companyDetail['name'] = $value->name;
        else
            $companyDetail['name'] = "无";
        if(isset($value->full_address))
            $companyDetail['full_address'] = $value->full_address;
        else
            $companyDetail['full_address'] = "无";
        if(isset($value->website)&&$value->website!=null)
            $companyDetail['website'] = $value->website;
        else
            $companyDetail['website'] = "无";
        if(isset($value->is_famous))
            $companyDetail['is_famous'] = $value->is_famous;
        else
            $companyDetail['is_famous'] = "无";
        if(isset($value->logo))
            $companyDetail['logo'] = $value->logo;
        else
            $companyDetail['logo'] = "无";
        if(isset($value->video_url))
            $companyDetail['video_url'] = $value->video_url;
        else
            $companyDetail['video_url'] = "无";
        if(isset($value->introduction))
            $companyDetail['introduction'] = $value->introduction;
        else
            $companyDetail['introduction'] = "无";

            if(isset($value->companyproperty->name))
            $companyDetail['companyproperty'] = $value->companyproperty->name;
        else
            $companyDetail['companyproperty'] = "无";
            if(isset($value->companytrade->name))
            $companyDetail['companytrade'] = $value->companytrade->name;
        else
            $companyDetail['companytrade'] = "无";

        if(!empty($value->city)) {
            $cityList = '';
            $i = 0;
            $count = count($value->city);
            foreach ($value->city as $city) {
                $i++;
                if ( $i === $count){
                    $cityList .= $city->name;
                }
                else {
                    $cityList .= $city->name.',';
                }
            }
            $companyDetail['city'] = $cityList;
        }
        else
            $companyDetail['city'] = "无";

            if(!empty($value->companypic))
                foreach($value->companypic as $cpic) {
                    $companyDetail['companypic'][] = $cpic->url;
                }
            else
            $companyDetail['companypic'] = "";
        return $companyDetail;
    }


    public function  getPositionByCompany($page,$id){
        $data = Position::model()->with('degree','positiontype','positionspecialty')->findAll(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId' => $id),
            'order'=>'t.id',
            'offset'=>($page-1)*10,
            'limit'=>10
        ));
        $recordCount = Position::model()->countByAttributes(array('company_id'=>$id));
        foreach ($data as $key => $value) {
            if(isset($value->id))
                $position[$key]['id'] = $value->id;
            else
                $position[$key]['id'] = "无";
            if(isset($value->name))
                $position[$key]['jobName'] = $value->name;
            else
                $position[$key]['jobName'] = "无";
            if(isset($value->entering_time))
                $position[$key]['time'] = date("Y-m-d",strtotime($value->entering_time));
            else
                $position[$key]['time'] = "无";
            if($value->is_teacher==1){
                if(isset($value->type_id)&&$value->type_id==1)
                    $position[$key]['jobType'] = "学前教师";
                elseif(isset($value->type_id)&&$value->type_id==2)
                    $position[$key]['jobType'] = "小学教师";
                elseif(isset($value->type_id)&&$value->type_id==3)
                    $position[$key]['jobType'] = "初中教师";
                elseif(isset($value->type_id)&&$value->type_id==4)
                    $position[$key]['jobType'] = "高中教师";
                elseif(isset($value->type_id)&&$value->type_id==5)
                    $position[$key]['jobType'] = "大学教师";
                elseif(isset($value->type_id)&&$value->type_id==6)
                    $position[$key]['jobType'] = "培训辅导";
                elseif(isset($value->type_id)&&$value->type_id==7)
                    $position[$key]['jobType'] = "其他";
                else
                    $position[$key]['jobType'] = "无";
            }
            else{
                if(isset($value->positiontype->name))
                    $position[$key]['jobType'] = $value->positiontype->name;
                else
                    $position[$key]['jobType'] = "无";
            }
            if(isset($value->position_source))
                $position[$key]['messageSource'] = $value->position_source;
            else
                $position[$key]['messageSource'] = "无";
            if(isset($value->degree->name))
                $position[$key]['degreeRequire'] = $value->degree->name;
            else
                $position[$key]['degreeRequire'] = "无";
            if(isset($value->positionspecialty->name))
                $position[$key]['major'] = $value->positionspecialty->name;
            else
                $position[$key]['major'] = "无";
        }

        $ListOnePage = array();
        $ListOnePage['list']=$position;
        $ListOnePage['recordCount']=$recordCount;
        return $ListOnePage;
    }

    public function  getPositionByCompanyId($id){
        $data = Position::model()->with('degree','positiontype','positionspecialty')->findAll(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId' => $id)
        ));
        $recordCount = Position::model()->countByAttributes(array('company_id'=>$id));
        foreach ($data as $key => $value) {
            if(isset($value->id))
                $position[$key]['id'] = $value->id;
            else
                $position[$key]['id'] = "无";
            if(isset($value->name))
                $position[$key]['jobName'] = $value->name;
            else
                $position[$key]['jobName'] = "无";
            if(isset($value->entering_time))
                $position[$key]['time'] = date("Y-m-d",strtotime($value->entering_time));
            else
                $position[$key]['time'] = "无";
            if($value->is_teacher==1){
                if(isset($value->type_id)&&$value->type_id==1)
                    $position[$key]['jobType'] = "学前教师";
                elseif(isset($value->type_id)&&$value->type_id==2)
                    $position[$key]['jobType'] = "小学教师";
                elseif(isset($value->type_id)&&$value->type_id==3)
                    $position[$key]['jobType'] = "初中教师";
                elseif(isset($value->type_id)&&$value->type_id==4)
                    $position[$key]['jobType'] = "高中教师";
                elseif(isset($value->type_id)&&$value->type_id==5)
                    $position[$key]['jobType'] = "大学教师";
                elseif(isset($value->type_id)&&$value->type_id==6)
                    $position[$key]['jobType'] = "培训辅导";
                elseif(isset($value->type_id)&&$value->type_id==7)
                    $position[$key]['jobType'] = "其他";
                else
                    $position[$key]['jobType'] = "无";
            }
            else{
                if(isset($value->positiontype->name))
                    $position[$key]['jobType'] = $value->positiontype->name;
                else
                    $position[$key]['jobType'] = "无";
            }
            if(isset($value->position_source))
                $position[$key]['messageSource'] = $value->position_source;
            else
                $position[$key]['messageSource'] = "无";
            if(isset($value->degree->name))
                $position[$key]['degreeRequire'] = $value->degree->name;
            else
                $position[$key]['degreeRequire'] = "无";
            if(isset($value->positionspecialty->name))
                $position[$key]['major'] = $value->positionspecialty->name;
            else
                $position[$key]['major'] = "无";
        }

        $ListOnePage = array();
        $ListOnePage['list']=$position;
        $ListOnePage['recordCount']=$recordCount;
        return $ListOnePage;
    }


public function GetThumb($path, $w, $h){
    $file_name = md5($path . $w . $h);
    Yii::import("ext.EPhpThumb.EPhpThumb");
    $thumb = new EPhpThumb();
    $thumb->init();
    $thumb->create('./' . $path)
        ->adaptiveResize($w, $h)
        ->save('assets/uploadFile/companyImg/' . $file_name . '.jpg');
    return 'assets/uploadFile/companyImg/' . $file_name . '.jpg';
}


    public function indexCompany(){
        $companyList = Company::model()->findAll(array(
            'condition' => 'is_famous!=0 AND is_index_show=1',
            'order' => 'entering_time DESC',
            'limit'=>'5'
        ));
        foreach ($companyList as $key => $value) {
            if(isset($value->logo)&&$value->logo!='assets/uploadFile/companyImg/') {
                //$img = $this->GetThumb($value->logo,'224','148');
                $img = $value->logo;
                $companyList[$key]['logo'] = $img;
            }
            else
                $companyList[$key]['logo'] = "无";
            if(isset($value->introduction)) {
                $word = mb_substr($value->introduction,0,112,'utf-8');
                $companyList[$key]['introduction'] = $word;
            }
            else
                $companyList[$key]['introduction'] = "无";
        }
        return $companyList;
    }


    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new CompanyService();

        }
        return self::$instance;
    }
    private function __construct(){

    }

    private function __clone(){

    }
}