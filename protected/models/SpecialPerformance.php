<?php 

/**
*
 * 专长
*/
class SpecialPerformance extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{special_performance}}";
	}

	public function rules(){
		return array(
               array('name','safe')
			);
	}

	
}

 ?>