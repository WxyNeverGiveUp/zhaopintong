<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 12:55
 * 公司来访人信息
 */
class CareerVisitor extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{career_visitor}}';
    }
}