<?php 

/**
*
 * 工作经历
*/
class WorkExperience extends CActiveRecord
{
	public $name;
	public $value;
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{work_experience}}";
	}

	public function rules(){
		return array(
                array('work_type','safe'),
                array('company_name','safe'),
                array('department','safe'),
                array('position_name','safe'),
                array('trade_id','safe'),
                array('position_type_id','safe'),
                array('level','safe'),
                array('experience_describe','safe'),
                array('start_time','safe'),
                array('end_time','safe'),
                array('city_id','safe'),
                array('salary','safe'),
                array('report_object','safe'),
                array('subordinates_num','safe'),
                array('is_copy','safe'),
                array('is_practice','safe'),
                array('sign','safe'),
			);
	}

	public function relations(){
		return array(
                 'trade'=>array(self::BELONGS_TO,'trade', 'trade_id'),
                 'position_type'=>array(self::BELONGS_TO,'position_type', 'position_type_id'),
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}
    


	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,company_name,position_name,start_time,end_time,work_type,
		                experience_describe,sign';
		$cri->addCondition("resume_id = $resume_id and is_copy=0");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>