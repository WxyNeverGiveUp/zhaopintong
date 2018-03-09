<?php
/**
 * Created by PhpStorm.
 * User: Dongyang
 * Date: 2015/4/28
 * Time: 20:11
 * 宣讲会
 */
class CareerTalk extends CActiveRecord{

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{career_talk}}';
    }

    public function relations(){
        return array(
            'company'=>array(SELF::BELONGS_TO,'Company','company_id','select'=>'name'),
            'distantcompany'=>array(SELF::BELONGS_TO,'Company','company_id','select'=>'name'),
            'count'=>array(self::STAT, 'CareerTalkUser', 'career_talk_id'),
        );
    }

/*
     public function relations()
    {
        return array(

               'CareerTalkUser'=>array(self::BELONGS_TO,'CareerTalkUser','','on'=>'career_talk.user_id=career_talk_user.user_id','select'=>'user_id'),
                'Company'=>array(self::BELONGS_TO,'Company','','on'=>'career_talk.company_id=company.id','select'=>'name')
            );
    }*/
}
?>