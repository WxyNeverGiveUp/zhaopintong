<?php
/**
 * Created by PhpStorm.
 * User: Dongyang
 * Date: 2015/4/28
 * Time: 20:51
 *
 * 宣讲会用户关联
 */
/*
 * @property integer id
 * @property integer careerTalkId
 * @property integer userId
 * */
class CareerTalkMajor extends CActiveRecord{

    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function tableName(){
        return '{{career_talk_major}}';
    }
}
?>