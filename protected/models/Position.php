<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-13
 * Time: 下午5:41
 *
 * 职位
 */

class Position extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    static $mob_id;
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
		return "{{position}}";
    }

    public function relations(){
        $arrm = array(self::MANY_MANY, 'User',
                't_position_user(position_id, user_id)','having'=>'collection.id='.self::$mob_id);
        if(!isset(Yii::app()->session['user_id']))
        $arr = array(self::MANY_MANY, 'User',
            't_position_user(position_id, user_id)','having'=>'collection.id=0');
        else
            $arr = array(self::MANY_MANY, 'User',
                't_position_user(position_id, user_id)','having'=>'collection.id='.Yii::app()->session['user_id']);
        if(!isset(Yii::app()->session['user_id']))
            $arr2 = array(self::MANY_MANY, 'User',
                't_position_user(position_id, user_id)','condition'=>'collection2.id=0','together'=>true);
        else
            $arr2 = array(self::MANY_MANY, 'User',
                't_position_user(position_id, user_id)','condition'=>'collection2.id='.Yii::app()->session['user_id'],'together'=>true);
        return array(
            'degree'=>array(SELF::BELONGS_TO,'Degree','degree_id','select'=>'name'),
            'positionspecialty'=>array(SELF::BELONGS_TO,'PositionSpecialty','specialty_id','select'=>'name'),
            'positiontype'=>array(SELF::BELONGS_TO,'PositionType','type_id','select'=>'name'),
            'company'=>array(SELF::BELONGS_TO,'Company','company_id','select'=>'name,property_id'),
            'city'=>array(self::BELONGS_TO,'City','city_id','select'=>'name'),
            'collection'=>$arr,
            'collection2'=>$arr2,
            'collection3'=>$arrm,
            'positionuser'=>array(SELF::HAS_MANY,'PositionUser','position_id'),
            'collectionCount'=>array(self::STAT, 'PositionUser', 'position_id'),
            'positioncontacts'=>array(SELF::HAS_ONE,'PositionContacts','position_id','select'=>'name,post,telephone,cellphone,email'),
            'brightened'=>array(SELF::HAS_MANY,'Brightened','related_id','select'=>'name,type','order'=>'brightened.id DESC',
                'limit'=>'2','having'=>'brightened.type=2'),
            'brightenedlist'=>array(SELF::HAS_MANY,'Brightened','related_id','select'=>'name','condition'=>'brightenedlist.type=2','together'=>false)
        );
    }
}