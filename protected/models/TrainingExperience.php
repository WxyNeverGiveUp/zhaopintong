<?php 

/**
*
 *培训经历
*/
class TrainingExperience extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{training_experience}}";
	}

	public function rules(){
		return array(
			   array('id','safe'),
               array('training_name','safe'),
               array('training_organization','safe'),
               array('start_time','safe'),
                array('end_time','safe'),
               array('training_content','safe'),
               array('resume_id','safe')
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,training_name,start_time,end_time,training_content,training_organization';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>