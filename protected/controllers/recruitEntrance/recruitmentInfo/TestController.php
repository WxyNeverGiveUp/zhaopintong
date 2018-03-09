<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/24
 * Time: 11:05
 */
class TestController extends Controller
{
    //查看单位其它招聘信息
    public function actionOther(){
        //$companyId = yii::app()->session['company_id'];
        //$lastUpdate = Yii::app()->session['companyUserName'];
        $companyId = 4;
        $lastUpdate ="wangshuhang";
        $conditions ="'1'='1' and p.type_id=pt.id and p.degree_id = d.id and p.company_id =:companyId and p.last_update !=:lastUpdate";
        $params = array(':companyId'=>$companyId,':lastUpdate'=>$lastUpdate);
        $command = Yii::app()->db->createCommand()
            ->select('p.id,p.name,pt.name as positiontype,d.name as degree,p.recruitment_num,p.last_update,p.is_publish')
            ->from('t_position p,t_position_type pt,t_degree d')
            ->order('p.id asc');
        $offset =0;
        $model = $command->where($conditions,$params)->limit(10)->offset($offset)->queryAll();
        echo "更新者".$model[0]['last_update'];
//        var_dump($model[0]['last_update']);
//        $this->smarty->assign('recruitInforList',$model);
//        $this->smarty->display('recruitEntrance/recruitment-otherInfo.html');

    }

    public function actionJson(){

//        $id = $_POST['id'];
        $id = 2;
        $sql = 'select * from {{study_specialty}} where id = '.$id;
        $major = StudySpecialty::model()->findBySql($sql);
        $minor = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>$id));
        $minorList = array();
        if (!empty($minor)) {
            $minorList = json_decode(CJSON::encode($minor),true);
        }
       $list2='{"major":"'.$major->name.'","id":"'.$id.'","items":'.json_encode($minorList,JSON_UNESCAPED_UNICODE).'}';
        print $list2;
    }

    public function actionJson1(){
        $provinceId = 5;
        if($provinceId!=null&&""!=$provinceId){
            if($provinceId!=1&&$provinceId!=2&&$provinceId!=3&&$provinceId!=4){
                $city = City::model()->findAllByAttributes(array('province_id'=>$provinceId));
                $cityList = array();
                if(!empty($city)){
                    $cityList = json_encode(CJSON::encode($city),true);
                }
                $result1 = array(
                    "items"=>json_encode($cityList,JSON_UNESCAPED_UNICODE)
                );
                print $result1['items'];
            }else{
                $result1 = null;
                print $result1['items'];
            }
        }
    }
}