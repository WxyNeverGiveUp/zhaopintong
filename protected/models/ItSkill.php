<?php 

/**
*IT技巧
*/
class ItSkill extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{it_skill}}";
	}

	public function rules(){
		return array(
               array('id','safe'),
               array('name','safe'),
               array('resume_id','safe'),
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'id,name';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->find($cri);
		return $list;
	}
}

 ?>