<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/8
 * Time: 10:23
 */

class CompanyController extends Controller{
    public function actionList(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }if(isset($_GET['isEliteFirm'])){
            $isEliteFirm = $_GET['isEliteFirm'];
            $this->smarty->assign('isEliteFirm',$isEliteFirm);
        }
        else{
            $this->smarty->assign('isEliteFirm',null);
        }
        if(isset($_GET['isEliteSchool'])){
            $isEliteSchool = $_GET['isEliteSchool'];
            $this->smarty->assign('isEliteSchool',$isEliteSchool);
        }
        else{
            $this->smarty->assign('isEliteSchool',null);
        }
        $tradeList = CacheService::getInstance()->companyTrade();
        $propertyList  = CacheService::getInstance()->companyProperty();
        $cityList  = CacheService::getInstance()->allCity();
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList',$tradeList);
        $this->smarty->assign('cityList',$cityList);
        $this->smarty->display('company/company-new.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria();
        //$criteria -> condition = ('is_discarded=0');
        $criteria -> order = 'concern_num desc';
        $list_all = Company::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= Company::model()->findAll($criteria);  //¼ÇÂ¼·ÖÒ³
        $list2='{"code":0,"data":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    public function actionDetail($id){
        $company = CompanyService::getInstance()->detail($id);
        $userId = Yii::app()->session['user_id'];
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>$userId),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));

        $positionNum = Position::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $careerTalkNum = /*CareerTalk::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $dataCount =*/ count(CTService::getInstance()->searchForFront($page=0,$searchWord=4,$timeId=0,$preachTypeId=0,$industryId=0,$id,0,0));
        $mySchools = StudyExperience::model()->findAll(array(
            'condition' => 'user_id=:userId',
            'params' => array(':userId'=>$userId),
        ));

        $schoolBrothersIds = array();
        $command = Yii::app()->db->createCommand();
        foreach($mySchools as $school){
            $schoolBrothers2 = $command
                ->select('*')
                ->from('t_study_experience')
                ->where(array('like','school_name','%'.$school['school_name'].'%'))
                ->having('user_id!=:id', array(':id'=>$userId))
                ->queryAll();
            foreach($schoolBrothers2 as $schoolBrother){
                $conditions = "1=1 ";
                $params = array();
                if ($company['name'] != null && $company['name']!=""){
                    $conditions .= " and w.company_name LIKE :keyword ";
                    $params[':keyword']='%'.$company['name'].'%';
                }
                if ($schoolBrother['user_id'] != 0 && $schoolBrother['user_id']!="0"){
                    $conditions .= " and w.user_id = :userId ";
                    $params[':userId']=$schoolBrother['user_id'];
                }
                $command2 = Yii::app()->db->createCommand()
                    ->select('w.id')
                    ->from('t_work_experience w');
                $work = $command2->where($conditions,$params)->queryAll();
                if($work!=null) {
                    $schoolBrothersIds[] = $schoolBrother['user_id'];
                  }
                }
            $command->reset();
            }

        $schoolBrothersIdsNew = array_unique($schoolBrothersIds);
        $schoolBrothers = array();
        foreach($schoolBrothersIdsNew as $schoolBrothersIdNew){
            $schoolBrothers[] = CompanyService::getInstance()->getSchoolBrotherByUserId($schoolBrothersIdNew);
        }
        $this->smarty->assign('positionNum',$positionNum);
        $this->smarty->assign('careerTalkNum',$careerTalkNum);
        $this->smarty->assign('schoolBrothers',$schoolBrothers);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('company', $company);
        //$this->smarty->display('company/detail-new.html');
        $current="current";
        $this->smarty->assign('introduce',$current);
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
        $this->smarty->display('company/company-introduction.html');
    }


    public function actionSearch($searchWord = null, $propertyId = 0, $cityId = 0, $tradeId = 0, $isFamous = 0,
                                 $isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0){
        $searchWord = $_POST['searchWord'];
        $propertyId =$_POST['propertyId'];
        $cityId = $_POST['cityId'];
        $tradeId = $_POST['tradeId'];
        $isFamous = $_POST['isFamous'];
        $isJoinBigRecruitment = $_POST['isJoinBigRecruitment'];
        $isJoinRecruitmentWeek = $_POST['isJoinRecruitmentWeek'];
        $companyListOnePage = CompanyService::getInstance()->search(1, $searchWord,$propertyId, $cityId, $tradeId, $isFamous,$isJoinBigRecruitment,$isJoinRecruitmentWeek);
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList  = CompanyProperty::model()->findAll();
        $cityList  = City::model()->findAll();
        $recordCount = $companyListOnePage['recordCount'];
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList',$tradeList);
        $this->smarty->assign('cityList',$cityList);
        $this->smarty->assign('companyList', $companyListOnePage['list']);
        $this->smarty->assign('propertyId', $propertyId);
        $this->smarty->assign('cityId', $cityId);
        $this->smarty->assign('tradeId', $tradeId);
        $this->smarty->assign('isFamous', $isFamous);
        $this->smarty->assign('isJoinBigRecruitment', $isJoinBigRecruitment);
        $this->smarty->assign('isJoinRecruitmentWeek', $isJoinRecruitmentWeek);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('keyword', $searchWord);
        $this->smarty->display('company/list.html');
    }

    public function  actionSearchJson($page=0, $searchWord = null, $propertyId = 0, $locationId = 0, $industryId = 0, $isEliteFirm = 0,$isEliteSchool = 0,
                                      $isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0,$heatSort=0,$timeSort=1){
        $companyListOnePage = CompanyService::getInstance()->search2($page, $searchWord,$propertyId, $locationId, $industryId, $isEliteFirm,$isEliteSchool,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$heatSort,$timeSort);
        $SearchJson='{"code":0,"data":'.CJSON::encode($companyListOnePage['list']).',"dataCount":"'.$companyListOnePage['recordCount'].'"}';
        print  $SearchJson;
    }


    public  function  actionListPositionByCompany($id){
        $company = Company::model()->findByPk($id);
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('id', $id);
        $current="current";
        $this->smarty->assign('po',$current);
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
        $this->smarty->display('company/post/post.html');
    }

    public function  actionPositionJsonByCompany($page=0,$id){
        $positionList= CompanyService::getInstance()->getPositionByCompany($page,$id);
        $dataCount = $positionList['recordCount'];
        $json='{"code":0,"data":'.CJSON::encode($positionList['list']).',"dataCount":"'.$dataCount.'"}';
        print  $json;
    }

    public function actionConcern($companyId,$isFollow){
        $company = Company::model()->findByPk($companyId);
        if($company==null){
            $this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId' => $companyId, ':userId' => $userId),
        ));
        if($isFollow==1) {
            $companyUserOne = new CompanyUser();
            $companyUserOne->company_id = $companyId;
            $companyUserOne->user_id = $userId;
            $companyUserOne->save();
        }
        else{
        $companyUser->delete();
        }
        $fNumber = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$companyId),
        ));
        $list='{"code":0,"data":{"followNumber":"'.$fNumber.'"},"errMsg":"'.'¹Ø×¢Ê§°Ü'.'"}';
        print $list;
    }



    public function actionUnconcern($companyId){
        $company = Company::model()->findByPk($companyId);
        if($company==null){
            $this->actionList();
            reuturn;
        }
        $userId = Yii::app()->session['user_id'];
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$companyId,':userId'=>$userId),
        ));
        if($companyUser == null){
            $this->actionList();
        }
    }


    public function  actionSSS(){
        $this->smarty->assign('keyword',"¶«±±Ê¦·¶´óÑ§¾»ÔÂÐ£Çø");
        $this->smarty->display('company/sssss.html');
    }


    public function actionListCT($id){
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('id',$id);
        $current="current";
        $this->smarty->assign('preach',$current);
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
        $this->smarty->display('company/company-preach-new.html');
    }


    public function actionCTJson($id){

        $list = CTService::getInstance()->searchForFront($page=0,$searchWord=4,$timeId=0,$preachTypeId=0,$industryId=0,$id,0,0);
        foreach ($list as $key => $value) {
            if(isset($value['id']))
                $careerTalk[$key]['id'] = $value['id'];
            else
                $careerTalk[$key]['id'] = "0";
            if(isset($value['time'])){
                $time = $value['time'];
                $month = date('m',strtotime($time));
                $day = date('d',strtotime($time));
                $weekArray = array("日", "一", "二", "三", "四", "五", "六");
                $week = "周" . $weekArray[date("w", strtotime($time))];
                $careerTalk[$key]['month'] = $month;
                $careerTalk[$key]['date'] = $day;
                $careerTalk[$key]['week'] = $week;
                $careerTalk[$key]['time'] = date('H:i',strtotime($time));
            }
            else{
                $careerTalk[$key]['month'] = "无";
                $careerTalk[$key]['date'] = "无";
                $careerTalk[$key]['week'] = "无";
                $careerTalk[$key]['time'] = "无";
            }
            if(isset($value['name']))
                $careerTalk[$key]['company'] = $value['name'];
            else
                $careerTalk[$key]['company'] = "无";
            if(isset($value['preachType'])){
                if($value['preachType']==1)
                    $careerTalk[$key]['preachType'] = '实地宣讲会';
                elseif($value['preachType']==2)
                    $careerTalk[$key]['preachType'] = '视频宣讲会';
                else
                    $careerTalk[$key]['preachType'] = '外地宣讲会';
            }
            else
                $careerTalk[$key]['preachType'] = "无";
            if(isset($value['location']))
                $careerTalk[$key]['location'] = $value['location'];
            else
                $careerTalk[$key]['location'] = "无";
            if(isset($value['isOverdue']))
                $careerTalk[$key]['isOverdue'] = $value['isOverdue'];
            else
                $careerTalk[$key]['isOverdue'] = "无";
            //if(isset($value['isEnroll'])){
            $caUser = CareerTalkUser::model()->find(array(
                'condition' => 'career_talk_id=:id AND user_id=:userId',
                'params' => array(':id'=>$value['id'],':userId'=>Yii::app()->session['user_id']),
            ));
            $concerned = $caUser?1:0;
            $careerTalk[$key]['isEnroll'] = $concerned;
        }
        $json = '{"code":0,"data":'.json_encode($careerTalk).'}';
        print $json;
    }

    public function actionEnrollCT($preachId,$isEnroll){
        $ca = CareerTalk::model()->findByPk($preachId);
        if($ca==null){
            $this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $caUser = CareerTalkUser::model()->find(array(
            'condition' => 'career_talk_id=:id AND user_id=:userId',
            'params' => array(':id'=>$preachId,':userId'=>$userId),
        ));

        if($isEnroll==1) {
            $caUser2 = new CareerTalkUser();
            $caUser2->career_talk_id= $preachId;
            $caUser2->user_id = $userId;
            $caUser2->save();
        }
        else{
            $caUser->delete();
        }
        $list='{"code":0,"data":""}';
        print $list;
    }


    public  function actionInsert(){
        for($i = 1; $i <= 9999; $i++){
        $company = new Company();
        $company->name = '²âÊÔ';
        $company->trade_id = 1;
        //TODO:³ÇÊÐidÎ´Â¼Èë
        $company->city_id = 2;
        $company->full_address = 'ss';
        $company->phone = 'asdasd';
        $company->type_id = 3;
        $company->property_id = 2;
        $company->postal_code = '130117';
        $company->email = '123123@qq.com';
        $company->website = 'www.asdasd.com';
        $company->is_famous = 2;
        $company->is_join_big_recruitment = 1;
        $company->is_join_recruitment_week = 0;
        $company->introduction = '2adasdasd';
        $company->concern_num = 0;
        $company->entering_time = date("Y-m-d H:i:s",time());
        $company->save();
        }
    }

    public  function  actionLocation(){
        if(isset($_GET['Id'])){
           $ss = $_GET['Id'];
            if($ss == 'Lmore')
                $cityList = CacheService::getInstance()->province();
            else
                $cityList = CacheService::getInstance()->city()[$ss];
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($cityList).'}';
        print  $SearchJson;
    }

    public  function  actionIndustry(){
        if(isset($_GET['Id'])){
            $ss = $_GET['Id'];
            if($ss == 'Imore')
                $List = CacheService::getInstance()->companyTrade();
        }
        $SearchJson='{"code":0,"data":'.CJSON::encode($List).'}';
        print  $SearchJson;
    }
    public  function  actionIn(){
        $this->smarty->display('admin/company/33.html');
    }

    public  function  actionPreachVideoByCompany($id){
        $flag = 0;
        $newest = CareerTalk::model()->find(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$id),
            'order'=>'time DESC'
        ));
        $allPreach = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND id!=:newId AND url!=""',
            'params' => array(':id'=>$id,':newId'=>$newest->id),
            'limit'=>4
        ));
        if($newest!=null||$allPreach!=null)
            $flag=1;
        $this->smarty->assign('flag',$flag);
        $hotPreach = CareerTalk::model()->findAll(array(
            'condition' => 'company_id!=:id AND url!=""',
            'params' => array(':id'=>$id),
            'order'=>'time DESC',
            'limit'=>4
        ));
        $hotids = array();
        foreach ($hotPreach as $key => $value) {
            if(isset($value->id)){
                $hotids[]=$value->id;
            }
        }
        $cri = new CDbCriteria();
        $cri->with = array('count');
        $cri->select = 'id,url,name';
        $params = array();
        $conditions = 'company_id!=:id AND url!=""';
        $params[':id']=$id;
        $cri->condition=$conditions;
        $cri->params=$params;
        $lastPreach = CareerTalk::model()->findAll($cri);
        foreach ($lastPreach as $key => $value) {
            if(isset($value->id)){
                if(in_array($value->id,$hotids))
                 continue;
                else
                    $lastPreachs[$key]['id'] = $value->id;
            }
            else
                $lastPreachs[$key]['id'] = 0;
            if(isset($value->url))
                $lastPreachs[$key]['url'] = $value->url;
            else
                $lastPreachs[$key]['url'] = null;
            if(isset($value->name))
                $lastPreachs[$key]['name'] = $value->name;
            else
                $lastPreachs[$key]['name'] = null;
            if(isset($value->count))
                $lastPreachs[$key]['number'] = $value->count;
            else
                $lastPreachs[$key]['number'] = 0;
        }
        if($lastPreachs!=null){
            usort($lastPreachs, function ($a, $b) {
                return $b['number'] - $a['number'];
            });
        }

        //$lastPreach = CareerTalk::model()->findAllBySql('select * from t_career_talk where company_id!=:companyId and url !="" and id not in'.$hotids.' order by time DESC limit 4',array(':companyId'=>$id));


        $this->smarty->assign('allPreach', $allPreach);
        $this->smarty->assign('lastPreachs', $lastPreachs);
        $this->smarty->assign('hotPreach', $hotPreach);
        $this->smarty->assign('newest', $newest);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('id',$id);
        $current="current";
        $this->smarty->assign('preachOnDemand',$current);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/preach-on-demand.html');
    }

    public function actionPreachJson(){
        $page = $_GET['page1'];
        $id = $_GET['id'];
        $hotPreach = CareerTalk::model()->findAll(array(
            'condition' => 'company_id!=:id AND url!=""',
            'params' => array(':id'=>$id),
            'order'=>'time DESC',
            'limit'=>4
        ));
        $hotids = array();
        foreach ($hotPreach as $key => $value) {
            if(isset($value->id)){
                $hotids[]=$value->id;
            }
        }
        $cri = new CDbCriteria();
        $cri->select = 'id,url,name';
        $params = array();
        $conditions = 'company_id!=:id AND url!=""';
        $params[':id']=$id;
        $cri->condition=$conditions;
        $cri->params=$params;
        $cri->addNotInCondition('id', $hotids);
        $list_all = CareerTalk::model()->findAll($cri);
        $pageSize = 12;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $cri -> limit = $pageSize;
        $cri -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $lastPreach = CareerTalk::model()->findAll($cri);
        foreach ($lastPreach as $key => $value) {
            if(isset($value->id)){
                    $lastPreachs[$key]['id'] = $value->id;
            }
            else
                $lastPreachs[$key]['id'] = 0;
            if(isset($value->url))
                $lastPreachs[$key]['url'] = $value->url;
            else
                $lastPreachs[$key]['url'] = null;
            if(isset($value->name))
                $lastPreachs[$key]['name'] = $value->name;
            else
                $lastPreachs[$key]['name'] = null;
            if(isset($value->count))
                $lastPreachs[$key]['number'] = $value->count;
            else
                $lastPreachs[$key]['number'] = 0;
            $lastPreachs[$key]['startTagName'] = '<script>';
            $lastPreachs[$key]['endTagName'] = '</script>';
        }
        if($lastPreachs!=null){
            usort($lastPreachs, function ($a, $b) {
                return $b['number'] - $a['number'];
            });
        }
        $searchJson = '{"code":0,"data":'.CJSON::encode($lastPreachs).',"dataCount":'.$recordCount.'}';
        print $searchJson;
    }  
    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + concern,enrollCT')
        );
    }
}
