<?php

/**
 * This is the model class for table "{{interview_experience}}".
 *
 * The followings are the available columns in table '{{interview_experience}}':
 * 面试经验
 */
class InterviewExperience extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{interview_experience}}';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        if(!isset(Yii::app()->session['user_id']))
            $arr = array(self::MANY_MANY, 'User',
                't_interview_experience_user(interview_id, user_id)','having'=>'ispraise.id=0');
        else
            $arr = array(self::MANY_MANY, 'User',
                't_interview_experience_user(interview_id, user_id)','having'=>'ispraise.id='.Yii::app()->session['user_id']);
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
        return array(
            'ispraise'=>$arr,
            'praiseCount'=>array(self::STAT, 'InterviewExperienceUser', 'interview_id'),
            'userdetail'=>array(SELF::HAS_ONE,'UserDetail','','on'=>'t.user_id=userdetail.user_id','select'=>'realname,head_url')
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
