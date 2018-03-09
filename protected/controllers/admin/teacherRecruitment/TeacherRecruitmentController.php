<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/20
 * Time: 上午10:26
 */

class TeacherRecruitmentController extends Controller {
    /**
     * 教师招考的Controller
     */
    //跳转到增加教师招考
    public function actionCreate(){
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/add.html');
    }

    //增加教师招考
    public function  actionAdd()
    {
        $announcement = new Announcement();
        $announcement->type_id = null;
        $announcement->title = $_POST['title'];
        $announcement->content = $_POST['content'];
        $announcement->add_time = date('y-m-d H:i:s',time());
        $announcement->clicks = 0;
        $announcement->is_tea_recruitment = true;
        $announcement->city_id = $_POST['city_id'];
        $announcement->save();
        $this->redirect($this->createUrl("admin/teacherRecruitment/teacherRecruitment/toSearch"));
    }

    //跳转到删除
    public function actionToDelete($id){
        $announcement = Announcement::model()->findByPk($id);
        $this->smarty->assign('announcement',$announcement);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/delete.html');
    }

    //删除教师招考
    public function actionDelete($id){    //删除
        $announcement = Announcement::model()->findByPk($id);
        if(!empty($announcement))
            $announcement->delete();
        $this->redirect($this->createUrl("admin/teacherRecruitment/teacherRecruitment/toSearch"));
    }

    //显示教师招考详情
    public function  actionDetail($id){
        $announcement = Announcement::model()->findByPk($id);
        $announcement->clicks = $announcement->clicks + 1 ;
        $announcement->save();
        $this->smarty->assign('announcement',$announcement);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/detail.html');
    }

    //显示教师招考列表
    public function actionList(){
        $criteria = new CDbCriteria();
        $criteria -> order = 'add_time desc';
        $criteria -> with = array('type','city',);
        $criteria ->addCondition("is_tea_recruitment = 1");
        $list_all = Announcement::model()->findAll($criteria);
        $pageSize = 10;
        $recordCount = count($list_all);
        $this->smarty->assign('liebiao',$list_all);
        $this->smarty->assign('pageSize',$pageSize);
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/list.html');
    }

    //分页
    public function  actionJson(){
        $page = $_GET['page'];
        $criteria = new CDbCriteria();
        $criteria ->addCondition("is_tea_recruitment = 1");
        $criteria -> order = 'add_time desc';
        $criteria -> with = array('type','city',);
        $list_all = Announcement::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $criteria -> with = array('type','city',);
        $recordCount =count($list_all);
        $list= Announcement::model()->findAll($criteria);  //记录分页

        foreach( $list as $key => $value){
            $listJson[$key]['id'] = $value->id;
            $listJson[$key]['title'] = $value->title;
            $listJson[$key]['content'] = $value->content;
            $listJson[$key]['add_time'] = $value->add_time;
            $listJson[$key]['clicks'] = $value->clicks;
            $listJson[$key]['is_tea_recruitment'] = $value->is_tea_recruitment;
            if($value->type != null) $listJson[$key]['type'] = $value->type->name;
            else $listJson[$key]['type'] = '无';
            if($value->city != null ) $listJson[$key]['city'] = $value->city->name;
            else $listJson[$key]['city'] = '无';
        }

        $list2='{"list":'.CJSON::encode($listJson).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    //跳转到更改教师招考
    public function actionToEdit($id){     //改操作
        $announcement = Announcement::model()->findByPk($id);
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('id',$id);
        $this->smarty->assign('title',$announcement->title);
        $this->smarty->assign('content',$announcement->content);
        if($announcement->city != null) $this->smarty->assign('city_id',$announcement->city->id);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/edit.html');

    }

    //更改教师招考
    public function actionEdit($id){
        $announcement = Announcement::model()->findByPk($id);
        $announcement->title = $_POST['title'];
        $announcement->content = $_POST['content'];
        $announcement->is_tea_recruitment = true;
        $announcement->city_id = $_POST['city_id'];
        $announcement->save();
        $this->redirect($this->createUrl("admin/teacherRecruitment/teacherRecruitment/toSearch"));
    }



    //跳转到搜索页面
    public function actionToSearch(){
        $provinceList = Province::model()->findAll();
        $cityList = City::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign( 'cityList' , $cityList ) ;
        $this->smarty->assign('keyword','');
        $this->smarty->assign('city_id',0);
        $this->smarty->assign('recordCount',0);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/search.html');
    }

    //搜索
    public function actionSearch(){
        $keyword = $_POST['keyword'];
        $city_id = $_POST['city_id'];

        $announcementTypeList = AnnouncementType::model()->findAll();
        $provinceList = Province::model()->findAll();
        $list = TeacherRecruitmentService::getInstance()->search($keyword,$city_id);
        $dataCount = count($list['list']);

        $cityList = City::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign( 'cityList' , $cityList ) ;
        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('city_id',$city_id);
        $this->smarty->assign('recordCount',$dataCount);
        $this->smarty->assign('current','teacherRe');
        $this->smarty->display('admin/teacherRecruitment/search.html');
    }

    public function actionGetCityJson($provinceId) {
        $cityList = City::model()->findAllByAttributes(array("province_id"=>$provinceId));
        $dataCount = count($cityList);
        $cityJson = '{"data":'.CJSON::encode($cityList).',"dataCount":'.$dataCount.',"code":0}';
        print $cityJson;
    }

    //搜索分页
    public function actionSearchJson(){
        $keyword = $_GET['keyword'];
        $city_id = $_GET['city_id'];
        $page = $_GET['page'];
        $teacherRecruitmentService = TeacherRecruitmentService::getInstance();
        $list = $teacherRecruitmentService->search($keyword,$city_id);
        $SearchResult = $teacherRecruitmentService->searchWithPage($page,$keyword,$city_id);
        $dataCount = count($list['list']);
        $SearchJson = '{"list":'.CJSON::encode($SearchResult['list']).',"dataCount":'.$dataCount.'}';
        print  $SearchJson;
    }
}