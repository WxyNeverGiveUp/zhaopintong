<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-15
 * Time: 上午11:20
 */

class PositionSpecialty extends CActiveRecord{
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
        return "{{position_specialty}}";
    }
} 