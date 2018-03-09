<?php 

/**
*证书
*/
class Certificate extends CActiveRecord
{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{certificate}}";
	}

	public function rules(){
		return array(
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
		$list = $this->findAll($cri);
		return $list;
	}
}

 ?>