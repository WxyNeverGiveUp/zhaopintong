<?php
class ItSkillsDetail extends CActiveRecord{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{it_skills_detail}}';
    }
}
?>