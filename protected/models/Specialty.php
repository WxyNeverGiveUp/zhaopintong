<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/20
 * Time: 15:22
 *
 * 专业
 */

class Specialty extends CActiveRecord{
    public $id;
    public $name;
    public $parent_id;


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
        return 't_position_specialty';
    }
}