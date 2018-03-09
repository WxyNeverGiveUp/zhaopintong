<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-9-19
 * Time: 下午7:04
 */

class SchoolInformation extends CActiveRecord
{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{school_information}}";
    }
}