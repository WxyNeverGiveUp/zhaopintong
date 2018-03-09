<?php

/**
 * Created by PhpStorm.
 * User: shihao
 * Date: 17-7-20
 * Time: 上午10:13
 */
class InviteRecord extends CActiveRecord
{
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{invite_record}}";
    }
}