<?php
/**
 * Created by PhpStorm.
 */

/**
 * Class Announcement
 * 公告
 */
class Announcement extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{announcement}}';
    }

    public function relations(){
        return array(
            'type'=>array(SELF::BELONGS_TO,'AnnouncementType','type_id'),
            'city'=>array(SELF::BELONGS_TO,'City','city_id'),
        );
    }
}