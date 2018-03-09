<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/20
 * Time: 上午10:48
 */

class TeacherRecruitmentController extends Controller {


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
        $provinceList = Province::model()->findAll();
        $cityList = City::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('cityList',$cityList);
        $this->smarty->assign('searchWord','');
        $this->smarty->assign('searchTypeId',0);
        $this->smarty->assign('famousCompany',$famousCompany);
        $this->smarty->assign('famousSchool',$famousSchool);
        $this->smarty->display('teacherRecruitment/notice.html');
    }

    public function actionSearch(){
        $keyword = $_POST['searchWord'];
        $city_id = $_POST['searchTypeId'];

        $list = TeacherRecruitmentService::getInstance()->search($keyword,$city_id);
        $dataCount = count($list['list']);
        $cityList = City::model()->findAll();
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        $this->smarty->assign('cityList',$cityList);
        $this->smarty->assign('searchWord',$keyword);
        $this->smarty->assign('searchTypeId',$city_id);
        $this->smarty->display('teacherRecruitment/notice.html');
    }

    public function actionSearchJson(){
        $keyword = $_GET['searchWord'];
        $city_id = $_GET['searchTypeId'];
        $page = $_GET['page1'];

        $teacherRecruitmentService = TeacherRecruitmentService::getInstance();
        $list = $teacherRecruitmentService->search($keyword,$city_id);
        $SearchResult = $teacherRecruitmentService->searchWithPage($page,$keyword,$city_id);
        $dataCount = count($list['list']);
        $SearchJson = '{"list":'.CJSON::encode($SearchResult['list']).',"dataCount":'.$dataCount.'}';
        print  $SearchJson;
    }

    public function actionGetCityJson($provinceId) {
        $cityList = City::model()->findAllByAttributes(array("province_id"=>$provinceId));
        $dataCount = count($cityList);
        $cityJson = '{"data":'.CJSON::encode($cityList).',"dataCount":'.$dataCount.',"code":0}';
        print $cityJson;
    }

    public function  actionDetail($id){
        $teacherRecruitment = Announcement::model()->findByPk($id);
        $teacherRecruitment->clicks = $teacherRecruitment->clicks + 1 ;
        $teacherRecruitment->save();
        $this->smarty->assign('teacherRecruitment',$teacherRecruitment);
        $this->smarty->display('teacherRecruitment/detail.html');
    }
}