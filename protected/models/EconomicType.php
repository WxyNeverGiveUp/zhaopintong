<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 10:57
 * 经济类型（国有经济，私有经济等）
 */
class EconomicType extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{economic_type}}';
    }
}