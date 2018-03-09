<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/24
 * Time: 下午3:57
 */

class AboutUs extends CActiveRecord{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{about_us}}';
    }
}