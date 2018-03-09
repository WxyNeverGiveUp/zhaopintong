<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 11:01
 * 职位需求-需求专业
 */
class CareerRequireMajor extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{career_require_major}}';
    }
}