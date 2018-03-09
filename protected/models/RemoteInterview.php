<?php
/**
 * Created by PhpStorm.
 * User: erdan
 * Date: 2015/7/22
 * Time: 17:00
 */

class RemoteInterview extends CActiveRecord
{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{remote_interview}}";
    }
    public function relations(){
        if(!isset(Yii::app()->session['user_id']))
            $arr = array(self::MANY_MANY, 'User',
                't_remote_interview_user(interview_id, user_id)','having'=>'enroll.id=0');
        else
            $arr = array(self::MANY_MANY, 'User',
                't_remote_interview_user(interview_id, user_id)','having'=>'enroll.id='.Yii::app()->session['user_id']);
        return array(
            'enroll'=>$arr
        );
    }
}

?>