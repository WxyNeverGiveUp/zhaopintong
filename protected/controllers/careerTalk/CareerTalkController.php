<?php
/**
 * Created by PhpStorm.
 * User: 冬阳
 * Date: 2015/4/28
 * Time: 20:33
 */
class CareerTalkController extends Controller{
    public function actionCreate(){
        $careerTalkList = CareerTalk::model()->findAll();
        $this->smarty->assign('careerTalkList',$careerTalkList);
        $this->smarty->display('careerTalk/create.html');
    }
    public function actionAdd(){
        $CTalk = new CareerTalk();
        $CTalk->time = $_POST['time'];                                              //接受Post过来的值
        $CTalk->name = $_POST['name'];
        $CTalk->place = $_POST['place'];
        $CTalk->type = $_POST['type'];
        $CTalk->company_id = $_POST['companyId'];
        $CTalk->description = $_POST['description'];
        $CTalk->url = $_POST['url'];
        $CTalk->save();
        $this->actionList();
    }
    public function actionDel($id){
        $careerTalk = CareerTalk::model()->findByPk($id);
        if(!empty($careerTalk)){
            $careerTalk->delete();
        }
        $this->actionList();
    }
    public function actionEdit($id){                                               //编辑功能
        $careerTalk = CareerTalk::model()->findByPk($id);
        $careerTalk->name=$_POST['name'];
        $careerTalk->time=$_POST['time'];
        $careerTalk->place=$_POST['place'];
        $careerTalk->type=$_POST['type'];
        //$careerTalk->companyId=$_POST['companyId'];
        $careerTalk->description=$_POST['description'];
        $careerTalk->url=$_POST['url'];
        $careerTalk->save();
        $this->actionList();
    }
    public function actionToEdit($id){                                             //跳转到Edit页面
        $careerTalk = CareerTalk::model()->findByPk($id);
        $listAll = CareerTalk::model()->findAll();
        $this->smarty->assign('id',$id);                                           //assign函数是负责传送数据至页面
        $this->smarty->assign('name',$careerTalk->name);
        $this->smarty->assign('time',$careerTalk->time);
        $this->smarty->assign('place',$careerTalk->place);
        $this->smarty->assign('type',$careerTalk->type);
        $this->smarty->assign('companyId',$careerTalk->company_id);
        $this->smarty->assign('description',$careerTalk->description);
        $this->smarty->assign('url',$careerTalk->url);
        $this->smarty->assign('listAll',$listAll);
        $this->smarty->display('careerTalk/edit.html');
    }
    public function actionDetail($id){                                          //宣讲会详情
        $company = Company::model()->findByPk(CareerTalk::model()->findByPk($id)->company_id);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$company->id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$company->id),
        ));
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $careerTalk = CareerTalk::model()->findByPk($id);
        if(strtotime(date("Y-m-d H:i:s",time()))>strtotime($careerTalk->time)){
            $this->smarty->assign('isOverdue',1);
        }
        else{
            $this->smarty->assign('isOverdue',0);
        }
        $careerTalk['time'] =  date("Y-m-d H:i",strtotime($careerTalk->time));
        $professionalTypeList = array();
        $professionalTypeNumber = array();
        $degreeList = array();
        $degreeNumber = array();
        $IA = CTService::getInstance()->intelligentAnalytic($id);
        if($IA == 'dong'){
            for($i = 0;$i<5;$i++){
                $professionalTypeList[$i] = '抱歉，暂时没人报名该宣讲会';
                $degreeList[$i] = '抱歉，暂时没人报名该宣讲会';
                $professionalTypeNumber[$i] = 0;
                $degreeNumber[$i] = 0;
            }
        }
        else{
            $professionalTypeList = $IA[0][0];
            $professionalTypeNumber = $IA[0][1];
            $degreeList = $IA[1][0];
            $degreeNumber = $IA[1][1];
        }

        $sql3 = 'select DISTINCT user_id from t_career_talk_user
              where career_talk_id ='.$id;                                  //一列已经报名ID为j的宣讲会的用户ID
        $userIdList2 = Yii::app()->db->createCommand($sql3)->queryAll();
        $cc = count($userIdList2);
        $this->smarty->assign('careerTalk',$careerTalk);
        $this->smarty->assign('professionalTypeList',$professionalTypeList);
        $this->smarty->assign('professionalTypeNumber',$professionalTypeNumber);
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('degreeNumber',$degreeNumber);
        $this->smarty->assign('number',$cc);
        $current="current";
        $this->smarty->assign('preach',$current);
        $recommendPreachs = CTService::getInstance()->recommendPreach();
        $this->smarty->assign('recommendPreachs',$recommendPreachs);
        $careerTalkUser = CareerTalkUser::model()->find(array(
            'condition' => 'career_talk_id=:careerId AND user_id=:userId',
            'params' => array(':careerId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concernedCT = $careerTalkUser?1:0;
        $this->smarty->assign('concernedCT',$concernedCT);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$company->id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$company->id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('careerTalk/preach-detail.html');
    }

    public function actionSearch(){
        $keyword = $_POST['keyword'];
        $time = $_POST['time'];
        $type = $_POST['type'];
        $industryId = $_POST['industryId'];

        $dataCount = count(CTService::getInstance()->search($keyword,$time,$industryId,$type,0));
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('time',$time);
        $this->smarty->assign('type',$type);
        $this->smarty->assign('industryId',$industryId);
        $this->smarty->assign('recordCount',$dataCount);
        $this->smarty->display('careerTalk/searchPageNew.html');
    }
    public function actionSearchJson($page=0,$searchWord=null,$timeId=0,$preachTypeId=0,$industryId=0,$id=0,$userId=0,$isFollow=0){
        $dataCount = count(CTService::getInstance()->searchForFront($searchWord,$timeId,$industryId,$preachTypeId,0,$id,$userId,$isFollow));
        $list = CTService::getInstance()->searchForFront($searchWord,$timeId,$industryId,$preachTypeId,$page,$id,$userId,$isFollow);
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
                $careerTalk[$key]['preachType'] = '视频宣讲';
                elseif($value['preachType']==2)
                    $careerTalk[$key]['preachType'] = '实地宣讲';
                elseif($value['preachType']==3)
                    $careerTalk[$key]['preachType'] = '外地宣讲';
                else
                    $careerTalk[$key]['preachType'] = '外地宣讲';
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
                /*门票 黄灿 2016/8/20*/
                $name=UserDetail::model()->find('user_id=:userId',array(':userId'=>Yii::app()->session['user_id']));
                $realname=$name->realname;//获取学生姓名
                $school=StudyExperience::model()->find('user_id=:userId',array(':userId'=>Yii::app()->session['user_id']));
                $schoolname=$school->school_name;//获取学校名称
                $careerTalk[$key]['realname'] = $realname;
                $careerTalk[$key]['schoolname'] = $schoolname;
                $careerTalk[$key]['ticket'] = $caUser->id;
                /*门票 黄灿 2016/8/20*/
        }
        

        $searchJson = '{"code":0,"data":'.json_encode($careerTalk).',"dataCount":'.$dataCount.'}';
        print $searchJson;
    }
    public function actionList(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preach.html');
    }
    public function actionJson(){
        /*$page1 = $_GET['page1'];
        $list = CTService::getInstance()->searchForFront(null,0,0,0,$page1);
        $recordCount = count(CTService::getInstance()->searchForFront(null,0,0,0,0));
        $aList = '{"code":0,"data":'.CJSON::encode($list).',"dataCount":'.$recordCount.'}';
        print $aList;*/
    }
    public function actionEnroll($id){                                                  //宣讲会报名
        $CTUser = new CareerTalkUser();
        //$CTUser->career_talk_id = $_COOKIE['careerTalkId'];
        $CTUser->career_talk_id=$id;
        //$CTUser->user_id = $_POST['userId'];       
        $CTUser->user_id=5;//这里先预定一个值，等到以后再从页面获取真正的用户ID
        $CTUser->save();
        $this->actionList();
    }

    public function actionEnroll2(){
        $id = $_GET['id'];
        $isEnroll = $_GET['isEnroll'];
        $isEnter = $_GET['isEnter'];
        $ca = CareerTalk::model()->findByPk($id);
        if($ca==null){
            $this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $caUser = CareerTalkUser::model()->find(array(
            'condition' => 'career_talk_id=:id AND user_id=:userId',
            'params' => array(':id'=>$id,':userId'=>$userId),
        ));

        if($isEnroll==1) {
            $caUser2 = new CareerTalkUser();
            $caUser2->career_talk_id= $id;
            $caUser2->user_id = $userId;
            $caUser2->save();
        }
        elseif($isEnter==1) {
            $caUser2 = new CareerTalkUser();
            $caUser2->career_talk_id= $id;
            $caUser2->user_id = $userId;
            $caUser2->save();
        }
        else{
            $caUser->delete();
        }
        $list='{"code":0,"data":""}';
        print $list;
    }

    public function actionCancelEnroll($id){
        $CTUser = CareerTalkUser::model()->findByPk($id);
        if(!empty($CTUser)){//只有存在了才能删对吧
            $CTUser->delete();
        }
        $this->actionList();
    }
    public function actionCalList(){//为日历版列表而生的controller，此处应该有传过来的$date值
          //$date = $_GET['date'];
        if(!isset($_GET['date']))
            $date = date('Y-m-d',time());
        else
            $date = $_GET['date'];
        $this->smarty->assign('date',$date);
        //$list = explode("-",$date);
        $year = ((int)substr($date,0,4));
        $month = ((int)substr($date,5,2));
        $day = ((int)substr($date,8,2));
        //if($month<10);
        //$month = substr($month.'',1,1);
        //if($day<10);
        //$day = substr($day.'',1,1);
        $this->smarty->assign('year',$year);
        $this->smarty->assign('month',$month);
        $this->smarty->assign('day',$day);
        $this->smarty->display('careerTalk/preach-calendar.html');
    }
    public function actionCalSearchJson(){
        if(isset($_GET['day'])&&isset($_GET['month'])&&isset($_GET['year'])){
            $day = $_GET['day'];
            $month = $_GET['month']+1;
            if($day<10)
                $day = '0'.$day;
            if($month<10)
                $month = '0'.$month;
            $riqi = $_GET['year'].'-'.$month.'-'.$day;
        }
        elseif(isset($_GET['date'])&&$_GET['date']!=0){
            $riqi = $_GET['date'];
        }
        else{
            $riqi = date('Y-m-d',time());
        }
        $criteria = new CDbCriteria();
        $criteria->select = 'id,name,time,type,place';//查询返回字段
        //$criteria->addCondition('time = :ri');//查询条件
        //$criteria->params[':ri'] = $riqi;//变量赋值，这样才能用
        $criteria->addSearchCondition('time',$riqi);
        $criteria->addCondition('is_front_input=0 OR is_ok=1');
        $criteria->order = 'time desc';//排序条件
        $calList = CareerTalk::model()->findAll($criteria);//得到查询结果并自动返回为数组型
        foreach ($calList as $key => $value) {
            if(isset($value['id']))
                $careerTalk[$key]['id'] = $value['id'];
            else
                $careerTalk[$key]['id'] = "0";
            if(isset($value['time'])){
                $time = $value['time'];
                $month = date('m',strtotime($time));
                $day = date('d',strtotime($time));
                $careerTalk[$key]['month'] = $month;
                $careerTalk[$key]['day'] = $day;
                $careerTalk[$key]['time'] = date('H:i',strtotime($time));
            }
            else{
                $careerTalk[$key]['month'] = "无";
                $careerTalk[$key]['day'] = "无";
                $careerTalk[$key]['time'] = "无";
            }
            if(isset($value['name']))
                $careerTalk[$key]['preachInfo'] = $value['name'];
            else
                $careerTalk[$key]['preachInfo'] = "无";
            if(isset($value['type'])){
                $careerTalk[$key]['preachTypeId'] = $value['type'];
                if($value['type']==1)
                    $careerTalk[$key]['preachType'] = '视频';
                elseif($value['type']==2)
                    $careerTalk[$key]['preachType'] = '实地';
                elseif($value['type']==3)
                    $careerTalk[$key]['preachType'] = '外地';
                else
                    $careerTalk[$key]['preachType'] = '外地';
            }
            else
                $careerTalk[$key]['preachType'] = "无";
            if(isset($value['place']))
                $careerTalk[$key]['location'] = $value['place'];
            else
                $careerTalk[$key]['location'] = "无";
        }
        $searchJson = '{"code":0,"data":'.json_encode($careerTalk).'}';
        print $searchJson;
    }
    public function actionIndexCalJson(){
        if(isset($_GET['month'])&&isset($_GET['year'])){
            $month = $_GET['month'];
            if($month<10)
                $month = '0'.$month;
            $ri = $_GET['year'].'-'.$month;
        }
        else{
            $ri = date('Y-m',time());
        }
        $criteria = new CDbCriteria();
        $criteria->select = 'name,time,type,place';//查询返回字段
        $criteria->addSearchCondition('time',$ri);
        $criteria->addCondition('is_front_input=0 OR is_ok=1');
        $criteria->order = 'time desc';//排序条件
        $calList = CareerTalk::model()->findAll($criteria);//得到查询结果并自动返回为数组型
        foreach ($calList as $key => $value) {
            if(isset($value['time'])){
                $time = $value['time'];
                $day = date('d',strtotime($time));
                $careerTalk[$key]['day'] = $day;
            }
            else{
                $careerTalk[$key]['day'] = "无";
            }
            if(isset($value))
                $careerTalk[$key]['href'] = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/'.'careerTalk/careerTalk/calList/date/'.$ri.'-'.$day;
            else
                $careerTalk[$key]['href'] = "无";
        }
        $searchJson = '{"code":0,"data":'.json_encode($careerTalk).'}';
        print $searchJson;
    }

    public function actionCalJson(){
        if(isset($_GET['month'])&&isset($_GET['year'])){
            $month = $_GET['month'];
            if($month<10)
                $month = '0'.$month;
            $ri = $_GET['year'].'-'.$month;
        }
        else{
            $ri = date('Y-m',time());
        }
        $criteria = new CDbCriteria();
        $criteria->select = 'name,time,type,place';//查询返回字段
        $criteria->addSearchCondition('time',$ri);
        $criteria->addCondition('is_front_input=0 OR is_ok=1');
        $criteria->order = 'time desc';//排序条件
        $calList = CareerTalk::model()->findAll($criteria);//得到查询结果并自动返回为数组型
        foreach ($calList as $key => $value) {
            if(isset($value['time'])){
                $time = $value['time'];
                $day = date('d',strtotime($time));
                $careerTalk[$key]['day'] = $day;
            }
            else{
                $careerTalk[$key]['day'] = "无";
            }
        }
        $searchJson = '{"code":0,"data":'.json_encode($careerTalk).'}';
        print $searchJson;
    }

    public function actionLivePreach(){
        $this->smarty->display('careerTalk/rtmp.html');
    }
    public function actionIsRightTime($id){
        if(!isset(yii::app()->session['user_id']))
            $listJson='{"code":1}';
        else{
            $ca = CareerTalk::model()->findByPk($id);
            $time = $ca->time;
            if(date('Y-m-d H:i',strtotime($time)-1800)>date('Y-m-d H:i',time()))
                    $listJson='{"code":2,"data":'.'"'.date('Y-m-d H:i',strtotime($time)-1800).'"}';
            else
                $listJson = '{"code":0,"data":'.'"'.$ca->live_url.'"}';
        }
        print $listJson;
    }
public function actionListds(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachds.html');
    }


public function actionListbs(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachbs.html');
    }


public function actionListhd(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachhd.html');
    }



public function actionListhz(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachhz.html');
    }


public function actionListxn(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachxn.html');
    }


public function actionListsx(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            $this->smarty->assign('searchWord',$search);
        }
        else{
            $this->smarty->assign('searchWord',null);
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
            $this->smarty->assign('type',$type);
        }
        else{
            $this->smarty->assign('type',0);
        }
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $remoteInterviews = RemoteInterview::model()->findAll(array(
            'order'=>'day DESC',
            'limit'=>8
        ));
        $famous = Company::model()->findAll(array(
            'condition' => 'is_famous=1 or is_famous=2',
            'limit'=>8
        ));
        $peachs = CTService::getInstance()->indexPreach();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("remoteInterviews",$remoteInterviews);
        $this->smarty->assign("famous",$famous);
        $this->smarty->assign("peachs",$peachs);
        $this->smarty->display('careerTalk/preachsx.html');
    }
    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + enroll2,livePreach')
        );
    }
}

?>
