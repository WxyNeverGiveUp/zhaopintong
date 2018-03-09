<?php
/**
 *
 *用户
 */
class User extends CActiveRecord
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return '{{user}}';
    }

    public function relations(){
        return array(
            'userdetail'=>array(SELF::HAS_ONE,'UserDetail','user_id','select'=>'realname,gender'),
            'studyexperience'=>array(SELF::BELONGS_TO,'StudyExperience','','on'=>'t.id=studyexperience.user_id','select'=>'school_name,major_name,end_time,position_degree_id,study_specialty_id'),
            'degree'=>array(SELF::BELONGS_TO,'Degree','position_degree_id','select'=>'name'),
        );
    }

    public function rules(){
        return array(
            array('username','safe'),
            array('password','safe'),
            array('email','safe'),
            array('token','safe'),
            array('token_exptime','safe'),
            array('status','safe'),
            array('reg_time','safe'),
            array('cellphone','safe'),
            array('phone','safe'),
            array('qq','safe'),
            array('wechat','safe'),
            array('is_visible','safe'),
            array('is_activated','safe')
        );
    }

    public function getAll($id){
        $list = $this->findByPk($id);
        return $list;
    }

    public function getList($id){
        $cri = new CDbCriteria();
        $cri->select = 'id,email,cellphone,qq,phone,wechat';
        $cri->addCondition("id = $id");
        $list = $this->find($cri);
        return $list;
    }


}
?>