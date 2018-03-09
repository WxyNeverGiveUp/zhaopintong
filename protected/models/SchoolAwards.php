<?php 

/**
*
 * 校内奖励
*/
class SchoolAwards extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{school_awards}}";
	}

	public function rules(){
		return array(
               array('award_name','safe'),
               array('award_time','safe'),
               array('school','safe'),
               array('explain','safe'),               
               array('resume_id','safe')
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,award_name,award_time,school';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>
