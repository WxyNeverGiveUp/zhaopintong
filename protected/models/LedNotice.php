<?php
/**
 * Created by PhpStorm.
 * User: lix
 * Date: 2015/10/30
 * Time: 1:26
 */

class LedNotice extends CActiveRecord{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }
    public function tableName(){
        return "{{led_notice}}";
    }
}

