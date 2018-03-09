<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/28
 * Time: 14:01
 */
class CompanyZhiZhao extends CActiveRecord
{
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{company_zhizhao}}';
    }
}