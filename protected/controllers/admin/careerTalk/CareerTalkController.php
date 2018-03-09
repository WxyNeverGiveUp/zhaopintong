<?php
/**
 * Created by PhpStorm.
 * User: 冬阳
 * Date: 2015/4/28
 * Time: 20:33
 */
class CareerTalkController extends Controller{
    public function actionCreate($id){
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('current','careertalk');
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->display('admin/careerTalk/create.html');
    }
    public function actionAdd(){
        $CTalk = new CareerTalk();
        $CTalk->time = $_POST['time'];                                              //接受Post过来的值
        $CTalk->name = $_POST['name'];
        $CTalk->place = $_POST['place'];
        $CTalk->type = $_POST['type'];
        $CTalk->company_id = $_POST['companyId'];
        $CTalk->description = $_POST['description'];
        $CTalk->live_url = $_POST['live_url'];
        $CTalk->url = $_POST['vid'];
        $CTalk->last_update = Yii::app()->session['user_name'];
        $CTalk->save();
        $this->redirect($this->createUrl("admin/careerTalk/careerTalk/listByCompany/id/".$_POST['companyId']));
    }
    public function actionDel($id){
        $careerTalk = CareerTalk::model()->findByPk($id);
        if(!empty($careerTalk)){
            $careerTalk->delete();
        }
        CareerTalkUser::model()->deleteAllByAttributes(array('career_talk_id'=>$id));
        $this->redirect($this->createUrl("admin/careerTalk/careerTalk/list"));
    }
    public function actionEdit($id){                                               //编辑功能
        $careerTalk = CareerTalk::model()->findByPk($id);
        $careerTalk->name=$_POST['name'];
        $careerTalk->time=$_POST['time'];
        $careerTalk->place=$_POST['place'];
        $careerTalk->type=$_POST['type'];
        $careerTalk->description = $_POST['description'];
        $careerTalk->live_url = $_POST['live_url'];
        $careerTalk->url = $_POST['vid'];
        $careerTalk->last_update = Yii::app()->session['user_name'];
        $careerTalk->save();
        $this->redirect($this->createUrl("admin/careerTalk/careerTalk/listByCompany/id/".$careerTalk->company_id));
    }
    public function actionToEdit($id){                                             //跳转到Edit页面
        $careerTalk = CareerTalk::model()->findByPk($id);
        $this->smarty->assign('id',$id);                                           //assign函数是负责传送数据至页面
        $this->smarty->assign('name',$careerTalk->name);
        $this->smarty->assign('time',$careerTalk->time);
        $this->smarty->assign('place',$careerTalk->place);
        $this->smarty->assign('type',$careerTalk->type);
        $this->smarty->assign('description',$careerTalk->description);
        $this->smarty->assign('live_url',$careerTalk->live_url);
        $this->smarty->assign('url',$careerTalk->url);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/edit.html');
    }
    public function actionDetail($id){                                          //宣讲会详情
        $careerTalk = CareerTalk::model()->findByPk($id);
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

        $this->smarty->assign('careerTalk',$careerTalk);
        $this->smarty->assign('professionalTypeList',$professionalTypeList);
        $this->smarty->assign('professionalTypeNumber',$professionalTypeNumber);
        $this->smarty->assign('degreeList',$degreeList);
        $this->smarty->assign('degreeNumber',$degreeNumber);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/detail.html');
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
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("trade",$trade);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/search.html');
    }
    public function actionSearchJson(){
        $page = $_GET['page1'];
        $keyword = $_GET['keyword'];
        $time = $_GET['time'];
        $type = $_GET['type'];
        $industryId = $_GET['industryId'];

        $dataCount = count(CTService::getInstance()->search($keyword,$time,$industryId,$type,0));
        $list = CTService::getInstance()->search($keyword,$time,$industryId,$type,$page);

        $searchJson = '{"list":'.CJSON::encode($list).',"dataCount":'.$dataCount.'}';
        print $searchJson;
    }
    public function actionList(){
        $criteria = new CDbCriteria();
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("trade",$trade);
        //$this->smarty->assign('list',$list_all);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/listPageNew.html');
    }

    public function actionListByCompany($id){
        $criteria = new CDbCriteria();
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$id));
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("trade",$trade);
        $this->smarty->assign("companyId",$id);
        //$this->smarty->assign('list',$list_all);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/listByCompany.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $listAll = CareerTalk::model()->findAll();
        $recordCount = count($listAll);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage-1)*$pageSize;
        $list = CareerTalk::model()->findAll($criteria);
        $aList = '{"list":'.CJSON::encode($list).',"dataCount":'.$recordCount.'}';
        print $aList;
    }


    public function actionJsonByCompany(){
        $page = $_GET['page1'];
        $companyId = $_GET['companyId'];
        $listAll = CareerTalk::model()->findAllByAttributes(array('company_id'=>$companyId));
        $recordCount = count($listAll);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria = new CDbCriteria();
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$companyId));
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage-1)*$pageSize;
        $list = CareerTalk::model()->findAll($criteria);
        $aList = '{"list":'.CJSON::encode($list).',"dataCount":'.$recordCount.'}';
        print $aList;
    }


    public function actionEnroll($id){                                                  //宣讲会报名
        $CTUser = new CareerTalkUser();
        $CTUser->career_talk_id=$id;
        //$CTUser->user_id = $_POST['userId'];
        $CTUser->user_id=5;//这里先预定一个值，等到以后再从页面获取真正的用户ID
        $CTUser->save();
        $this->redirect($this->createUrl("admin/careerTalk/careerTalk/list"));
    }
    public function actionCancelEnroll($id){
        $CTUser = CareerTalkUser::model()->findByPk($id);
        if(!empty($CTUser)){//只有存在了才能删对吧
            $CTUser->delete();
        }
        $this->redirect($this->createUrl("admin/careerTalk/careerTalk/list"));
    }
    public function actionCalendricList(){//为日历版列表而生的controller，此处应该有传过来的$date值
        $riqi = '2015-01-01';
        $criteria = new CDbCriteria();
        $criteria->select = 'name,time,type,place';//查询返回字段
        $criteria->addCondition('time = :ri');//查询条件
        $criteria->params[':ri'] = $riqi;//变量赋值，这样才能用
        $criteria->order = 'time desc';//排序条件
        $calList = CareerTalk::model()->findAll($criteria);//得到查询结果并自动返回为数组型
        $this->smarty->assign('calList',$calList);
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/calendricList.html');
    }

    public function actionUploadVideo(){
        $this->smarty->assign('current','careertalk');
        $this->smarty->display('admin/careerTalk/uploadVideo.html');
    }
}
?>
