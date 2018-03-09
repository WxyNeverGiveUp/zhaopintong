<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/25
 * Time: 16:13
 */
class CompanyUserType extends CActiveRecord
{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{company_user_size}}";
    }
}