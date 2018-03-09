<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/27
 * Time: 上午9:46
 */

class Admin extends CActiveRecord{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{admin}}';
    }
}