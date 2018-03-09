<?php 

/**
*求职信
*/
class ApplicationLetter extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{application_letter}}";
	}

	public function rules(){
		return array(
			   //array('id','safe'),
			  // array('title','safe'),
              // array('content','safe'),
              // array('resume_id','safe'),
               array('title,content, resume_id', 'required'),
			);
	}
}

 ?>