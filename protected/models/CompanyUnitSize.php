<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/22
 * Time: 10:16
 */
class CompanyUnitSize extends CActiveRecord
{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{company_unit_size}}";
    }
}