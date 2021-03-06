<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-18
 * Time: 下午5:09
 */



class PositionController extends Controller{

    public function actionList(){
        $cityList = City::model()->findAll();
        $this->smarty->assign( 'cityList' , $cityList ) ;
        $positionList = Position::model()->findAll();
        $recordCount = count($positionList);
        $this->smarty->assign('recordCount',$recordCount);
        $this->smarty->assign('current','position');
        $this->smarty->display('admin/position/position.html');
    }

    public function actionListByCompany($id){
        $cityList = City::model()->findAll();
        $this->smarty->assign( 'cityList' , $cityList ) ;
        $positionList = Position::model()->findAllByAttributes(array('company_id'=>$id));
        $recordCount = count($positionList);
        $this->smarty->assign('recordCount',$recordCount);
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('current','position');
        $this->smarty->display('admin/position/positionByCompany.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria();
        //$criteria -> condition = ('is_discarded=0');
        $list_all = \Position::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria->order= 'id desc';
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= Position::model()->findAll($criteria);  //记录分页
        $list2='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    public function actionJsonByCompany(){
        $page = $_GET['page1'];
        $companyId = $_GET['companyId'];
        $criteria = new CDbCriteria();
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$companyId));
        $list_all = Position::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= Position::model()->findAll($criteria);  //记录分页
        $list3='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list3;
    }

    public function actionCreate($id){
        $degreeList = Degree::model()->findAll();
        $typeList = PositionType::model()->findAll();
        $specialtyList = PositionSpecialty::model()->findAll();
        $company = Company::model()->findByPk($id);
        //$cityList  = City::model()->findAll();
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('typeList', $typeList);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('current','position');
        $this->smarty->display('admin/position/add.html');
    }

    public function actionAdd($companyId){
        $position = new Position();
        $position->is_teacher = $_POST['isTeacher'];
        $position->name = $_POST['name'];
        $position->company_id = $companyId;
        $position->city_id = $_POST['cityId'];
        if($_POST['isTeacher']==1)
        $position->type_id = $_POST['typeId'];
        else
            $position->type_id = $_POST['typeId2'];
        $position->degree_id = $_POST['degreeId'];
        $position->specialty_id = $_POST['specialtyId'];
        $position->recruitment_num = $_POST['recruitmentNum'];
        $position->position_duty = $_POST['positionDuty'];
        $position->position_source = $_POST['positionSource'];
        $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        $position->entering_time = date("Y-m-d H:i:s",time());
        $position->views = 0;
        //$position->dead_time = date("Y-m-d H:i:s",strtotime($_POST['deadTime']));;
        $position->last_update = Yii::app()->session['user_name'];
        $position->save();
        if(isset($_POST['bright'])){
        $brightList = $_POST['bright'];
        for($i = 0; $i < count($brightList); $i++){
            $br = new Brightened();
            $br->type=2;
            $br->name=$brightList[$i];
            $br->related_id=$position->attributes['id'];
            $br->save();
          }
        }
        if(isset($_POST['contactName'])&&$_POST['contactName']!=null&&$_POST['contactName']!=''){
            $positionContacts = new PositionContacts();
            $positionContacts->position_id = $position->attributes['id'];
            $positionContacts->name = $_POST['contactName'];
            $positionContacts->cellphone = $_POST['cellphone'];
            $positionContacts->post = $_POST['post'];
            $positionContacts->telephone = $_POST['telephone'];
            $positionContacts->email = $_POST['contactEmail'];
            $positionContacts->save();
        }
        $this->redirect($this->createUrl("admin/position/position/listByCompany/id/".$companyId));
    }

    public function actionToEdit($id){
//        echo "id为：".$id;
        if(null!=$id&&$id>0){
            $sql = "select * from t_position where id=".$id;
            $position = Position::model()->findBySql($sql);
        }
//        var_dump($position);
        $degreeList = Degree::model()->findAll();
        $typeList = PositionType::model()->findAll();
        $specialtyList = PositionSpecialty::model()->findAll();
        if(null!=$position->company_id&&$position->company_id){
            $company = Company::model()->findByPk($position->company_id);
            //        var_dump($company);
        }

        $city = City::model()->findAll();
        $provinceList = Province::model()->findAll();
        $positionContact = PositionContacts::model()->findByAttributes(array('position_id'=>$id));

        $brightened = Brightened::model()->findAllByAttributes(array('related_id'=>$id,'type'=>2));
        $brightList = array();
        $brightList[1]=array('name'=>'绩效奖金多',check=>0);
        $brightList[2]=array('name'=>'年终多薪',check=>0);
        $brightList[3]=array('name'=>'带薪年假',check=>0);
        $brightList[4]=array('name'=>'五险一金',check=>0);
        $brightList[5]=array('name'=>'晋升空间大',check=>0);
        $brightList[6]=array('name'=>'弹性工作制',check=>0);
        $brightList[7]=array('name'=>'内部培训',check=>0);
        $brightList[8]=array('name'=>'办公环境优美气氛好',check=>0);
        $brightList[9]=array('name'=>'期权激励',check=>0);
        $brightList[10]=array('name'=>'解决户口',check=>0);
        $brightList[11]=array('name'=>'餐补',check=>0);
        $brightList[12]=array('name'=>'班车接送',check=>0);
        $brightList[13]=array('name'=>'包三餐',check=>0);
        foreach ($brightened as $key => $value) {
            $search = array_search(array('name'=>$value->name,check=>0), $brightList);
            if ($search) {
                $brightList[$search]['check'] = 1;
            }
        }

        $this->smarty->assign('brightList',$brightList);
        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('typeList', $typeList);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('positionContact', $positionContact);
        $this->smarty->assign('position',$position);
        $this->smarty->assign('city',$city);
        $this->smarty->assign('current','position');
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('provinceId',City::model()->findByPk($position->city_id)->province_id);
        $this->smarty->display('admin/position/edit.html');
    }

    public function actionEdit($id){
        $sql = "select * from t_position where id =".$id;
        $position = Position::model()->findBySql($sql);
        $position->is_teacher = $_POST['isTeacher'];
        $position->name = $_POST['name'];
        $position->city_id = $_POST['cityId'];
        if($_POST['isTeacher']==1)
            $position->type_id = $_POST['typeId'];
        else
            $position->type_id = $_POST['typeId2'];
        $position->degree_id = $_POST['degreeId'];
        $position->specialty_id = $_POST['specialtyId'];
        $position->recruitment_num = $_POST['recruitmentNum'];
        $position->position_duty = $_POST['positionDuty'];
        $position->position_source = $_POST['positionSource'];
        $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        //$position->dead_time = $_POST['deadTime'];
        $position->last_update = Yii::app()->session['user_name'];
        $position->save();
        Brightened::model()->deleteAllByAttributes(array('related_id'=>$id,'type'=>2));
        if(isset($_POST['bright'])){
            $brightList = $_POST['bright'];
            for($i = 0; $i < count($brightList); $i++){
                $br = new Brightened();
                $br->type=2;
                $br->name=$brightList[$i];
                $br->related_id=$position->attributes['id'];
                $br->save();
            }
        }
        PositionContacts::model()->deleteAllByAttributes(array('position_id'=>$id));
        if(isset($_POST['contactName'])&&$_POST['contactName']!=null&&$_POST['contactName']!=''){
            $positionContacts = new PositionContacts();
            $positionContacts->position_id = $position->attributes['id'];
            $positionContacts->name = $_POST['contactName'];
            $positionContacts->cellphone = $_POST['cellphone'];
            $positionContacts->post = $_POST['post'];
            $positionContacts->telephone = $_POST['telephone'];
            $positionContacts->email = $_POST['contactEmail'];
            $positionContacts->save();
        }
        $this->redirect($this->createUrl("admin/position/position/listByCompany/id/".$position->company_id));
    }

    public function actionDel($id){
        $sql = "select * from t_position where id=".$id;
        $position = Position::model()->findBySql($sql);
        if(!empty($position))
            $position->delete();
        PositionUser::model()->deleteAllByAttributes(array('position_id'=>$id));
        $this->redirect($this->createUrl("admin/position/position/list"));
    }

    public function actionDetail($id){
        $sql = "select 'degree','positiontype','positiontype','positionspecialty','city','brightenedlist'from t_position where id =".$id;
        $position = Position::model()->findBySql($sql);
//        $position = Position::model()->with('degree','positiontype','positionspecialty','city','brightenedlist')->findByPk($id);
        $this->smarty->assign('position', $position);
        $this->smarty->assign('current','position');
        $this->smarty->display('admin/position/detail.html');
    }

    public function  actionSearch(){
        $keyword = $_POST['keyword'];
        $cityId = $_POST['cityId'];
        $isJoinBigRecruitment = $_POST['isJoinBigRecruitment'];
        $isJoinRecruitmentWeek = $_POST['isJoinRecruitmentWeek'];
        $isEliteFirm = null;
        $isEliteSchool = null;
        if(isset($_POST['isEliteSchool'])){
            $isEliteSchool = $_POST['isEliteSchool'];
        }
        if(isset($_POST['isEliteFirm'])){
            $isEliteFirm = $_POST['isEliteFirm'];
        }
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('cityId',$cityId);
        $this->smarty->assign('isJoinBigRecruitment',$isJoinBigRecruitment);
        $this->smarty->assign('isJoinRecruitmentWeek',$isJoinRecruitmentWeek);
        $this->smarty->assign('isEliteSchool',$isEliteSchool);
        $this->smarty->assign('isEliteFirm',$isEliteFirm);
        $list = PositionService::getInstance()->searchAdmin(0,$keyword,$cityId,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$isEliteSchool,$isEliteFirm);
        $this->smarty->assign('recordCount',$list['recordCount']);
        $cityList = City::model()->findAll();
        $this->smarty->assign( 'cityList' , $cityList );
        $this->smarty->assign('current','position');
        $this->smarty->display('admin/position/search.html');
    }

    //搜索分页
    public function actionSearchJson(){
        $keyword = $_GET['keyword'];
        $cityId = $_GET['cityId'];
        $isJoinBigRecruitment = $_GET['isJoinBigRecruitment'];
        $isJoinRecruitmentWeek = $_GET['isJoinRecruitmentWeek'];
        $isEliteSchool = $_GET['isEliteSchool'];
        $isEliteFirm = $_GET['isEliteFirm'];
        $page = $_GET['page1'];
        $list = PositionService::getInstance()->searchAdmin($page,$keyword,$cityId,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$isEliteSchool,$isEliteFirm);
        $dataCount = $list['recordCount'];
        $SearchJson = '{"list":'.CJSON::encode($list['list']).',"dataCount":'.$dataCount.'}';
        print  $SearchJson;
    }

   public function actionListIndexInput($id)
    {
        $indexInputList = Positionweek2017::model()->findAll(array(
            'condition' => 'is_front_input=1 and company_id = :cid',
            'params'=>array(':cid'=>$id)
        ));
        $this->smarty->assign('recordCount', count($indexInputList));
        $this->smarty->assign('current', 'companyCheck');
        $this->smarty->assign('cid',$id);
        $this->smarty->display('admin/position/listIndexInput.html');
    }

    public function actionIndexInputJson($cid)
    {
        $page = $_GET['page1'];
        $criteria = new CDbCriteria;
        $criteria->condition = 'is_front_input=1 and company_id = :cid';
        $criteria->params = array(':cid'=>$cid);
        $list_all = Positionweek2017::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', $page);
        $criteria->order = 'entering_time DESC';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage - 1) * $pageSize;
        $recordCount = count($list_all);
        $list = Positionweek2017::model()->findAll($criteria);  //记录分页
        $list2 = '{"list":' . CJSON::encode($list) . ',"dataCount":"' . $recordCount . '"}';
        print $list2;
    }

    public function actionCheck($id){
        $position = Positionweek2017::model()->findByPk($id);
        $position->is_ok=1;
        $position->save();
        $this->actionListIndexInput($position->company_id);
    }

    public function actionDelIndexInput($id){
        $position = Positionweek2017::model()->findByPk($id);
        $cid = $position->company_id;
        if (!empty($position))
            $position->delete();
        $this->redirect($this->createUrl("admin/position/position/listIndexInput/id/".$cid));
    }

    public function actionDetailIndexInput($id)
    {
        $position = Positionweek2017::model()->findByPk($id);
        $this->smarty->assign('city',City::model()->findByPk($position->city_id)->name);
        $this->smarty->assign('degree',Degree::model()->findByPk($position->degree_id)->name);
        $this->smarty->assign('positionSpecialty',PositionSpecialty::model()->findByPk($position->specialty_id)->name);
        $this->smarty->assign('type',PositionType::model()->findByPk($position->type_id)->name);
        $this->smarty->assign('contact',PositionContactsweek2017::model()->findByAttributes(array('position_id' => $id)));
        $this->smarty->assign('position', $position);
        $this->smarty->assign('current', 'companyCheck');
        $this->smarty->display('admin/position/detailIndexInput.html');
    }

    public function actionToEditFront($id)
    {
        $this->smarty->assign('current', 'companyCheck');
        $position = Positionweek2017::model()->findByPk($id);
        $degreeList = Degree::model()->findAll();
        $typeList = PositionType::model()->findAll();
        $specialtyList = PositionSpecialty::model()->findAll();
        $company = Companyweek2017::model()->findByPk($position->company_id);
        $city = City::model()->findAll();
        $provinceList = Province::model()->findAll();
        $positionContact = PositionContactsweek2017::model()->findByAttributes(array('position_id'=>$id));
        $this->smarty->assign('degreeList', $degreeList);
        $this->smarty->assign('typeList', $typeList);
        $this->smarty->assign('specialtyList', $specialtyList);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('positionContact', $positionContact);
        $this->smarty->assign('position',$position);
        $this->smarty->assign('city',$city);
        $this->smarty->assign('current','position');
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('provinceId',City::model()->findByPk($position->city_id)->province_id);
        $this->smarty->assign('current', 'companyCheck');
        $this->smarty->display('admin/position/editIndexInput.html');
    }

    public function actionEditFront($id)
    {
        $position = Positionweek2017::model()->findByPk($id);
        $position->is_teacher = 0;
        $position->name = $_POST['name'];
        $position->city_id = $_POST['cityId'];
        $position->degree_id = $_POST['degreeId'];
        $position->specialty_id = $_POST['specialtyId'];
        $position->recruitment_num = $_POST['recruitmentNum'];
        $position->position_duty = $_POST['positionDuty'];
        $position->type_id = $_POST['typeId'];
        $position->position_source = $_POST['positionSource'];
        $position->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
        $position->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
        $position->last_update = Yii::app()->session['user_name'];
        $position->save();
        PositionContactsweek2017::model()->deleteAllByAttributes(array('position_id'=>$id));
        if(isset($_POST['contactName'])&&$_POST['contactName']!=null&&$_POST['contactName']!=''){
            $positionContacts = new PositionContactsweek2017();
            $positionContacts->position_id = $position->attributes['id'];
            $positionContacts->name = $_POST['contactName'];
            $positionContacts->cellphone = $_POST['cellphone'];
            $positionContacts->post = $_POST['post'];
            $positionContacts->telephone = $_POST['telephone'];
            $positionContacts->email = $_POST['contactEmail'];
            $positionContacts->save();
        }
        $this->redirect($this->createUrl("admin/position/position/detailIndexInput/id/".$id));
    }
} 
