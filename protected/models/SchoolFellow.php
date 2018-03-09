<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 10:59
 * 校友信息
 */
class SchoolFellow extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{school_fellow}}';
    }
}