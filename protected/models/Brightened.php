<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/20
 * Time: 15:13
 *
 * 亮点
 */
class Brightened extends CActiveRecord{

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
        return "{{brightened}}";
    }
}