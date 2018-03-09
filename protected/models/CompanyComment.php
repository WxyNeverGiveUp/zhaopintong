<?php

class CompanyComment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{company_comment}}';
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        if(!isset(Yii::app()->session['user_id']))
            $arr = array(self::MANY_MANY, 'User',
                't_company_comment_user(comment_id, user_id)','having'=>'ispraise.id=0');
        else
            $arr = array(self::MANY_MANY, 'User',
                't_company_comment_user(comment_id, user_id)','having'=>'ispraise.id='.Yii::app()->session['user_id']);
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'ispraise'=>$arr,
            'praiseCount'=>array(self::STAT, 'CompanyCommentUser', 'comment_id'),
            'userdetail'=>array(SELF::HAS_ONE,'UserDetail','','on'=>'t.user_id=userdetail.user_id','select'=>'realname,head_url')
        );
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
