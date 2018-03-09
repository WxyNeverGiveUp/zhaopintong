<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/22
 * Time: 9:42
 *
 * 职位用户收藏关联
 */


class ResumeUser extends CActiveRecord{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return "{{resume_user}}";
    }
}