<?php

/**
 * This is the model class for table "{{company_contacts}}".
 *职位联系人
 * The followings are the available columns in table '{{company_contacts}}':
 * @property integer $id
 * @property string $name
 * @property integer $company_id
 * @property string $post
 * @property string $cellphone
 * @property string $email
 */
class PositionContacts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
        return '{{position_contacts}}';
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
	 * @return CompanyContacts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
