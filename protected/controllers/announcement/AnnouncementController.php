<?php
/**
 * Created by RuiN Zhang
 */
class AnnouncementController extends  Controller{
    /**
     *Add a new announcementController
     */

    public function actionToSearch(){
        $famousCompany = Company::model()->findAll(array(
            'condition' => 'is_famous=2',
            'limit'=>5
            )
        );
        $famousSchool = Company::model()->findAll(array(
            'condition' => 'is_famous=1',
            'limit'=>5
        ));
        $announcementTypeList = AnnouncementType::model()->findAll();
        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('searchWord','');
        $this->smarty->assign('searchTypeId',0);
        $this->smarty->assign('famousCompany',$famousCompany);
        $this->smarty->assign('famousSchool',$famousSchool);
        $this->smarty->display('announcement/notice.html');
    }

    public function actionSearch(){
        $keyword = $_POST['searchWord'];
        $type_id = $_POST['searchTypeId'];

        $announcementTypeList = AnnouncementType::model()->findAll();
        $list = AnnouncementService::getInstance()->search($keyword,$type_id);
        $dataCount = count($list['list']);
        $this->smarty->assign('announcementTypeList',$announcementTypeList);
        $this->smarty->assign('searchWord',$keyword);
        $this->smarty->assign('searchTypeId',$type_id);
        $this->smarty->display('announcement/notice.html');
    }

    public function actionSearchJson(){
        $keyword = $_GET['searchWord'];
        $type_id = $_GET['searchTypeId'];
        $page = $_GET['page1'];

        $announcementService = AnnouncementService::getInstance();
        $list = $announcementService->search($keyword,$type_id);
        $SearchResult = $announcementService->searchWithPage($page,$keyword,$type_id);
        $dataCount = count($list['list']);
        $SearchJson = '{"list":'.CJSON::encode($SearchResult['list']).',"dataCount":'.$dataCount.'}';
        print  $SearchJson;
    }

    public function  actionDetail($id){
        $announcement = Announcement::model()->findByPk($id);
        $announcement->clicks = $announcement->clicks + 1 ;
        $announcement->save();
        $this->smarty->assign('announcement',$announcement);
        $this->smarty->display('announcement/detail.html');
    }
}