<?php 

/**
*
 * 省份常量
*/

class Province extends CActiveRecord
{
	public $proviceId;
	public $proviceName;
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{param_province}}";
	}

 public function relations(){
        return array(
            'city'=>array(SELF::HAS_MANY,'City','province_id','select'=>'name')
        );
    }
    
 
}

