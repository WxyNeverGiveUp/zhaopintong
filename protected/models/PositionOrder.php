<?php 

/**
* 
*/
class PositionOrder extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{position_order}}";
	}

    public function relations(){
        return array(
            'city'=>array(SELF::BELONGS_TO,'City','city_id')
        );
    }
	public function rules(){
		return array(
              array('re_email','safe'),
              array('re_time','safe'),
              array('position_name','safe'),
              array('city_id','safe'),
              array('has_send','safe'),
              array(' user_id','safe'),
			);
	}
}

 ?>