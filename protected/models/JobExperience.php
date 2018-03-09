<?php 

/**
*求职经历
*/
class JobExperience extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{job_experience}}";
	}

	public function rules(){
		return array(
               array('time','safe'),
               array('type','safe'),
               array('company_name','safe'),
               array('position_name','safe'),
               array('user_id','safe'),
                
			);
	}

	public function getAll($user_id){
		$list = $this->findAllByAttributes(array('user_id'=>$user_id));
		return $list;
	}

	public function getList($user_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,time,type,company_name,position_name';
		$cri->addCondition("user_id = $user_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>