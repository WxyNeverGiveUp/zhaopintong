<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/28
 * Time: 下午6:46
 */

class IndexImage extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return  parent::model($className);
    }

    public function tableName(){
        return '{{index_image}}';
    }
}