<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/6/18
 * Time: 下午6:19
 */

/**
 * Class AnnouncementType
 * 公告类型
 */
class AnnouncementType extends CActiveRecord{

    /**
     * @param string $className
     * @return CActiveRecord
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * @return string
     * 取得表名
     */
    public function tableName(){
        return '{{announcement_type}}';
    }

    public function relations(){
        return array(
            'announcement'=>array(SELF::HAS_MANY,'Announcement','type_id'),
        );
    }
}