<?php

/**
 *
 */
class Resume extends CActiveRecord
{


    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{resume}}";
    }

    public function rules(){
        return array(
            array('user_id','safe'),
            array('department','safe'),
            array('major_name','safe'),
            array('position_specialty_id','safe'),
            array('position_degree_id','safe'),
            array('start_time','safe'),
            array('end_time','safe'),
            array('study_describe','safe')
        );
    }

    public function relations(){
        return array(
            'userdetail'=>array(SELF::HAS_ONE,'UserDetail','','on'=>'t.user_id=userdetail.user_id','select'=>'realname,account_place,head_url,city_id,intent_city_id,intent_salary,intent_require'),
            'degree'=>array(SELF::BELONGS_TO,'Degree','position_degree_id','select'=>'name'),
            'studyexperience'=>array(SELF::BELONGS_TO,'StudyExperience','','on'=>'t.user_id=studyexperience.user_id','select'=>'school_name,major_name,end_time,position_degree_id,study_specialty_id'),
            //'city'=>array(self::BELONGS_TO,'City','','on'=>'userdetail.city_id=city.id','select'=>'name'),
            //'studyspecialty'=>array(SELF::BELONGS_TO,'StudySpecialty','','on'=>'studyexperience.study_specialty_id=studyspecialty.id','select'=>'name')
            'inviterecord'=>array(SELF::BELONGS_TO,'InviteRecord','','on'=>'t.user_id=inviterecord.user_id','select'=>'created_time,status'),
            'deliver'=>array(SELF::BELONGS_TO,'Deliver','','on'=>'t.user_id=deliver.user_id','select'=>'create_time,company_id,deliver_position'),
        );
    }

    public function getAll($user_id){
        $list = $this->findByAttributes(array('user_id'=>$user_id));
        return $list;
    }

    public function getList($user_id){
        $cri = new CDbCriteria();
        $cri->select = 'id,department,major_name,position_specialty_id,position_degree_id,
                        start_time,end_time,study_describe';
        $cri->addCondition("user_id = $user_id");
        $list = $this->find($cri);
        return $list;
    }
}

?>