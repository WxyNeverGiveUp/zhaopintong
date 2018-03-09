<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/20
 * Time: 11:04
 * 宣讲会-附件
 */
class CareerAttachment extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{career_attachment}}';
    }
}