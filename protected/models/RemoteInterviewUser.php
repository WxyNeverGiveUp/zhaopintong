<?php
/**
 * Created by PhpStorm.
 * User: erdan
 * Date: 2015/7/22
 * Time: 17:03
 */

class RemoteInterviewUser extends CActiveRecord
{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{remote_interview_user}}";
    }
    public function relations(){
        return array(
            'userdetail'=>array(SELF::HAS_ONE,'UserDetail','','on'=>'t.user_id=userdetail.user_id','select'=>'head_url')
        );
    }
}

?>