<?php 

/**
*
 *学习经历
*/
class StudyExperience extends CActiveRecord
{
	public $school;
	public $name;
	public $value;
	public $num;
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{study_experience}}";
	}

	public function rules(){
		return array(
                array('school_name','safe'),
                array('deparment','safe'),
                array('major_name','safe'),
                array('study_specialty_id','safe'),
                array('position_degree_id','safe'),
                array('end_time','safe'),
                array('start_time','safe'),
                array('study_describe','safe'),
                array('user_id','safe'),
                array('as_present_status','safe'),
                array('sign','safe'),
                array('gpa','safe'),
                array('rank','safe'),
			);
	}

	public function relations(){
		return array(
                 'position_degree'=>array(self::BELONGS_TO,'Degree', 'position_degree_id')

			);
	}

    public function getAll($user_id){
    	$list = $this->findAllByAttributes(array('user_id'=>$user_id));
    	return $list;
    }

    public function getList($user_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,school_name,deparment,major_name,
		                position_degree_id,start_time,end_time,sign,
		                study_describe';
		$cri->order = 't.id desc';
		$cri->addCondition("user_id = $user_id");
		$list = $this->with('position_degree')->findAll($cri);
		return $list;
	}
}

 ?>