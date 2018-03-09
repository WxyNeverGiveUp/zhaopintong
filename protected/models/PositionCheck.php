<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/27
 * Time: 上午9:46
 */

class PositionCheck extends CActiveRecord{
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{position_check}}';
    }
}