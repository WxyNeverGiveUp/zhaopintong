<?php 

/**
*
 *项目经验
*/
class ProjectExperience extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{project_experience}}";
	}

	public function rules(){
		return array(
               array('project_name','safe'),
               array('team_size','safe'),
               array('project_profile','safe'),
               array('project_role','safe'),
               array('start_time','safe'),
               array('end_time','safe'),
               array('project_results','safe'),
               array('resume_id','safe')
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,project_name,project_role,start_time,end_time';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>