<?php 

/**
*
 *
 * 校内职务
*/
class SchoolDuty extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{school_duty}}";
	}

	public function rules(){
		return array(
               array('duty_name','safe'),
               array('start_time','safe'),
               array('end_time','safe'),
               array('school','safe'),
               array('duty_performance','safe'),
               array('resume_id','safe')
			);
	}


	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,duty_name,school,start_time,end_time';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>