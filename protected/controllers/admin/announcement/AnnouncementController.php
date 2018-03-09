<?php
/**
 * Created by RuiN Zhang
 */

class AnnouncementController extends  Controller{
    /**
     *公告类型的Controller
     */
    //跳转到增加公告类型
    public function actionCreateType(){
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/addAnnouncementType.html');
    }

    //增加公告类型
    public function actionAddType(){
        $announcementType = new AnnouncementType();
        $announcementType->name = $_POST['name'];
        $announcementType->save();
        $this->redirect($this->createUrl('admin/announcement/announcement/listType'));
    }

    //显示公告类型列表
    public function actionListType(){
        $criteria = new CDbCriteria();
        $list = AnnouncementType::model()->findAll($criteria);
        $pageSize = 10;
        $recordCount = count($list);
        $this->smarty->assign('typeList',$list);
        $this->smarty->assign('pageSize',$pageSize);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/listAnnouncementType.html');
    }

    //分页传JSON
    public function actionTypeJson(){
        $page = $_GET['page'];
        $criteria = new CDbCriteria();
        $list_all = AnnouncementType::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount =count($list_all);
        $list= AnnouncementType::model()->findAll($criteria);  //记录分页
        $list2='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    //跳转到更改公告类型
    public function actionToEditType($id){
        $announcementType = AnnouncementType::model()->findByPk($id);
        $this->smarty->assign('announcementType' , $announcementType);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/editAnnouncementType.html');
    }

    //更改公告类型
    public function actionEditType($id){
        $announcementType = AnnouncementType::model()->findByPk($id);
        $announcementType->name = $_POST['name'];
        $announcementType->save();
        $this->redirect($this->createUrl('admin/announcement/announcement/listType'));
    }

    //跳转到删除公告类型
    public function actionToDeleteType($id){
        $announcementType = AnnouncementType::model()->findByPk($id);
        $this->smarty->assign('announcementType' , $announcementType);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/deleteAnnouncementType.html');
    }

    //删除公告类型
    public function actionDeleteType($id){
        $announcementType = AnnouncementType::model()->findByPk($id);
        if( !empty($announcementType))
            $announcementType->delete();
        $this->redirect($this->createUrl('admin/announcement/announcement/listType'));
    }


    /**
     * 公告的Controller
     */
    //跳转到增加公告
    public function actionCreate(){
        $announcementTypeList = AnnouncementType::model()->findAll();
        $cityList = City::model()->findAll();
        $this->smarty->assign('cityList',$cityList);
        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/add.html');
    }

    //增加公告
    public function  actionAdd()
    {
        $announcement = new Announcement();
        $announcement->type_id = $_POST['type_id'];
        $announcement->title = $_POST['title'];
        $announcement->content = $_POST['content'];
        $announcement->add_time = date('y-m-d H:i:s',time());
        $announcement->clicks = 0;
        $announcement->is_tea_recruitment = false;
        $announcement->city_id = null;
        $announcement->save();
        $this->redirect($this->createUrl('admin/announcement/announcement/toSearch'));
    }

    //跳转到删除
    public function actionToDelete($id){
        $announcement = Announcement::model()->findByPk($id);
        $this->smarty->assign('announcement',$announcement);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/delete.html');
    }

    //删除公告
    public function actionDelete($id){    //删除
        $announcement = Announcement::model()->findByPk($id);
        if(!empty($announcement))
            $announcement->delete();
        $this->redirect($this->createUrl('admin/announcement/announcement/toSearch'));
    }

    //显示公告详情
    public function  actionDetail($id){
        $announcement = Announcement::model()->findByPk($id);
        $announcement->clicks = $announcement->clicks + 1 ;
        $announcement->save();
        $this->smarty->assign('announcement',$announcement);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/detail.html');
    }

    //显示公告列表
    public function actionList(){
        $criteria = new CDbCriteria();
        $criteria -> order = 'add_time desc';
        $criteria -> with = array('type','city',);
        $criteria ->addCondition("is_tea_recruitment = 0");
        $list_all = Announcement::model()->findAll($criteria);
        $pageSize = 10;
        $recordCount = count($list_all);
        $this->smarty->assign('liebiao',$list_all);
        $this->smarty->assign('pageSize',$pageSize);
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/list.html');
    }

    //公告列表分页
    public function  actionJson(){
        $page = $_GET['page'];
        $criteria = new CDbCriteria();
        $criteria ->addCondition("is_tea_recruitment = 0");
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
            if($value->city != null && $value->is_tea_recruitment == true) $listJson[$key]['city'] = $value->city->name;
            else $listJson[$key]['city'] = '无';
        }

        $list2='{"list":'.CJSON::encode($listJson).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    //跳转到更改公告
    public function actionToEdit($id){     //改操作
        $announcement = Announcement::model()->findByPk($id);

        $cityList = City::model()->findAll();
        $this->smarty->assign('cityList',$cityList);
        $announcementTypeList = AnnouncementType::model()->findAll();
        $this->smarty->assign('announcementTypeList',$announcementTypeList);

        $this->smarty->assign('id',$id);
        $this->smarty->assign('type_id',$announcement->type_id);
        $this->smarty->assign('title',$announcement->title);
        $this->smarty->assign('content',$announcement->content);
        if($announcement->city != null) $this->smarty->assign('city_id',$announcement->city->id);
        $this->smarty->assign('is_tea_recruitment',$announcement->is_tea_recruitment);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/edit.html');

    }

    //更改公告
    public function actionEdit($id){
        $announcement = Announcement::model()->findByPk($id);
        $announcement->type_id = $_POST['type_id'];
        $announcement->title = $_POST['title'];
        $announcement->content = $_POST['content'];
        $announcement->is_tea_recruitment = false;
        $announcement->city_id = null;
        $announcement->save();
        $this->redirect($this->createUrl('admin/announcement/announcement/toSearch'));
    }

    //跳转到搜索页面
    public function actionToSearch(){
        $announcementTypeList = AnnouncementType::model()->findAll();
        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('keyword','');
        $this->smarty->assign('type_id',0);
        $this->smarty->assign('recordCount',0);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/search.html');
    }

    //搜索
    public function actionSearch(){
        $keyword = $_POST['keyword'];
        $type_id = $_POST['type_id'];

        $announcementTypeList = AnnouncementType::model()->findAll();

        $list = AnnouncementService::getInstance()->search($keyword,$type_id);
        $dataCount = count($list['list']);

        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('type_id',$type_id);
        $this->smarty->assign('recordCount',$dataCount);
        $this->smarty->assign('current','announcement');
        $this->smarty->display('admin/announcement/search.html');
    }

    //搜索分页
    public function actionSearchJson(){
        $keyword = $_GET['keyword'];
        $type_id = $_GET['type_id'];
        $page = $_GET['page'];
        $announcementService = AnnouncementService::getInstance();
        $list = $announcementService->search($keyword,$type_id);
        $SearchResult = $announcementService->searchWithPage($page,$keyword,$type_id);
        $dataCount = count($list['list']);
        $SearchJson = '{"list":'.CJSON::encode($SearchResult['list']).',"dataCount":'.$dataCount.'}';
        print  $SearchJson;
    }
}