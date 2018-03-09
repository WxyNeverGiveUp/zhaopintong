<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-25
 * Time: 上午3:22
 */

class CompanyCommentUser extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{company_comment_user}}';
    }


    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
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