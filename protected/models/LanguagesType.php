<?php 

/**
*
 * 语言种类
*/
class LanguagesType extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{languages_type}}";
	}

	public function rules(){
		return array(
               array('type_id','safe'),
               array('parent_id','safe')			);
	}
}

 ?>