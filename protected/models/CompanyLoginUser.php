<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 8:39
 */
class CompanyLoginUser extends CActiveRecord
{
    public function tableName(){
        return '{{company_login_user}}';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function  relations(){
        return array(
            'companyName'=>array(SELF::BELONGS_TO,'Company','company_id','select'=>'name')
        );
    }
}