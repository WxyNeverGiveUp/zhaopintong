<?php
/**
 * Created by PhpStorm.
 * User: erdan
 * Date: 2015/7/2
 * Time: 22:34
 */

class CompanyCityweek2017 extends CActiveRecord{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{company_cityweek2017}}";
    }
}