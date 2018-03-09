<?php
/**
 * Created by PhpStorm.
 * User: lixiang
 * Date: 2015/6/21
 * Time: 21:11
 */

class CompanyPic extends CActiveRecord{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{company_pic}}';
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'company'=>array(self::BELONGS_TO, 'Company', 'company_id')
        );
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}