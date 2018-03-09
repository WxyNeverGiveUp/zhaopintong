<?php 

/**
*语言能力
*/
class LanguagesAbility extends CActiveRecord
{
	public $name;
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{languages_ability}}";
	}

	public function rules(){
		return array(
			   array('id','safe'),
               array('type_id','safe'),
               array('point','safe'),
               array('les_say','safe'),
               array('rea_wri','safe'),
                array('grade','safe'),
                array('grade_id','safe'),
               array('resume_id','safe'),                                 
			);
	}

	public function relations(){
		return array(
                 'language_type'=>array(self::BELONGS_TO,'LanguagesType', 'type_id')
			);
	}

	public function getAll($resume_id){
		$list = $this->findAllByAttributes(array('resume_id'=>$resume_id));
		return $list;
	}

	public function getList($resume_id){
		$cri = new CDbCriteria();
		$cri->select = 'type_id,point,les_say,rea_wri';
		$cri->addCondition("resume_id = $resume_id");
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>