<?php 

/**
*
 *专长档案关联
*/
class UserSpecialPerformance extends CActiveRecord
{
	 
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return "{{user_special_performance}}";
	}	 
	public function LoadModel($user_id){
        $SpecialPerformance_id = $this->findAllByAttributes(array('user_id'=>$user_id));
        foreach ($SpecialPerformance_id as $key => $value) {
        	$list[] = $value->special_performance_id;
        }
        return $list;
	}
}

 ?>