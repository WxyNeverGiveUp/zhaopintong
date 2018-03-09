<?php

/**
 *
 * 城市常量
 */
class City extends CActiveRecord
{
    public $cityId;
    public $cityName;
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{param_city}}";
    }
    public function relations(){
        return array(
            'province'=>array(SELF::BELONGS_TO,'Province','province_id','select'=>'name')
        );
    }
    
}

