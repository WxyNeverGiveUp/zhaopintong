<?php
/**
 * Created by PhpStorm.
 * User: Lixiang
 * Date: 2015/9/12
 * Time: 15:13
 *
 * 激活码
 */
class ActivationCode extends CActiveRecord{

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
        return "{{activation_code}}";
    }
}