<?php
/**
 * Created by PhpStorm.
 * User: lixiang
 * Date: 2015/8/2
 * Time: 18:41
 */

class CompanyController extends Controller{
    public function actionIndex()
    {
        $userId=yii::app()->session['user_id'];
        $this->smarty->assign('userId',$userId);
        $current="current";
        $this->smarty->assign('company',$current);
        $this->smarty->display('job/collect_company/collect_company.html');
    }


    public function actionJson($page=0){
        $offset = ($page-1)*5;
        $cri = new CDbCriteria();
        $cri->with = array('companytrade','companyproperty','city','companycity','focusNum','concern2','brightened');
        $cri->select = 'id,name,is_famous';
        $conditions = "1=1 ";
        $params = array();
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = Company::model()->count($cri);
        $cri->limit = 8;
        $cri->offset = $offset;
        $cri->order = 't.entering_time DESC';
        $data = Company::model()->findAll($cri);
        $cri2 = new CDbCriteria();
        $cri2->with = array('companytrade','companyproperty','city','focusNum','brightened');
        $cri2->select = 'id,name,is_famous';
        $conditions2 = "1=1 ";
        $params2 = array();
        $cri2->condition=$conditions2;
        $cri2->params=$params2;
        $recordCount2 = 8;
        $cri2->limit = 8;
        $cri2->offset = $offset;
        $cri2->order = 't.entering_time DESC';
        $data2 = Company::model()->findAll($cri2);
        if($data==null)
            $data = $data2;
        foreach ($data as $key => $value) {
            if($key==0)
                $company[$key]['isFirst'] = 1;
            if(isset($value->id))
                $company[$key]['companyId'] = $value->id;
            else
                $company[$key]['companyId'] = "无";
            if(isset($value->name))
                $company[$key]['companyName'] = $value->name;
            else
                $graduate[$key]['companyName'] = "无";
            if(isset($value->companytrade->name))
                $company[$key]['industry'] = $value->companytrade->name;
            else
                $company[$key]['industry'] = "无";
            if(isset($value->companyproperty->name))
                $company[$key]['property'] = $value->companyproperty->name;
            else
                $company[$key]['property'] = "无";
            if(isset($value->brightened[0]->name))
                $company[$key]['brightspot'] = $value->brightened[0]->name;
            else
                $company[$key]['brightspot'] = "无";
            if(isset($value->concernCount))
                $company[$key]['focusNum'] = $recordCount;
            else
                $company[$key]['focusNum'] = 0;
            if(!empty($value->concern2))
                $company[$key]['isFocus'] = 0;
            else
                $company[$key]['isFocus'] = 1;
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
        $searchListOnePage['list']=$company;
        if(Company::model()->findAll($cri)==null)
            $recordCount = $recordCount2;
        $searchListOnePage['recordCount']=$recordCount;
        $SearchJson='{"code":0,"data":'.CJSON::encode($searchListOnePage['list']).',"dataCount":"'.$searchListOnePage['recordCount'].'"}';
        print  $SearchJson;
    }

    public function actionConcern($companyId,$isFocus){
        $company = Company::model()->findByPk($companyId);
        if($company==null){
            $this->actionIndex();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId' => $companyId, ':userId' => $userId),
        ));
        if($isFocus==1) {
            $companyUserOne = new CompanyUser();
            $companyUserOne->company_id = $companyId;
            $companyUserOne->user_id = $userId;
            $companyUserOne->save();
        }
        else{
            $companyUser->delete();
        }
        $fNumber = CompanyUser::model()->count(array(
            'condition' => 'user_id=:userId',
            'params' => array(':userId'=>$userId),
        ));
        $list='{"code":0,"data":'.$fNumber.'}';
        print $list;
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter')
        );
    }

}