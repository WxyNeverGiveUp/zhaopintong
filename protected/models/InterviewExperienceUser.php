<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-25
 * Time: 上午12:21
 */

class InterviewExperienceUser extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{interview_experience_user}}';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return InterviewExperience the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}