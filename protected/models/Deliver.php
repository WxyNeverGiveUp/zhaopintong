<?php

/**
 * Created by PhpStorm.
 * Date: 2017/8/5
 * Time: 10:46
 */
class Deliver  extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{deliver}}';
    }

}