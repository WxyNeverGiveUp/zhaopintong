<?php

/**
 *
 *用户详情
 */
class UserDetail extends CActiveRecord
{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{user_detail}}";
    }

    public function rules(){
        return array(
            array('realname','safe'),
            array('gender','safe'),
            array('current_live','safe'),
            array('account_place','safe'),
            array('birthday','safe'),
            array('work_experience','safe'),
            array('height','safe'),
            array('weight','safe'),
            array('nation','safe'),
            array('ID_number','safe'),
            array('political_status','safe'),
            array('marital_status','safe'),
            array('arrival time','safe'),
            array('about_me','safe'),
            array('user_id','safe'),
            // array('ad_url','safe'),
            array('head_url','safe')//'file','types'=>'jpg,gif,png')
        );
    }


    public function relations(){
        return array(
            'city'=>array(self::BELONGS_TO,'City','city_id','select'=>'name')
        );
    }

    public function getAll($user_id){
        $list = $this->findByAttributes(array('user_id'=>$user_id));
        return $list;
    }

    public function getList($user_id){
        $cri = new CDbCriteria();
        $cri->select = 'id,realname,city_id,head_url,about_me,gender,account_place,current_live,birthday,work_experience';
        $cri->addCondition("user_id = $user_id");
        $list = $this->find($cri);
        return $list;
    }

}

?>