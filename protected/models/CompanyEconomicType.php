<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/25
 * Time: 10:00
 */
class CompanyEconomicType extends CActiveRecord
{
    public function tableName(){
        return '{{economic_type}}';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}