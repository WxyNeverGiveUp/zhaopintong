<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27
 * Time: 9:24
 */
class CompanyLoginUserType extends CActiveRecord
{
    public function tableName(){
        return '{{company_user_type}}';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}