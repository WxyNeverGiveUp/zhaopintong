<?php
     /**************************实现填写基本信息的功能********************************/
    class MessageController extends Controller{
    	public function actionSeekMessage(){
    		 $atSchoolYear=array(2019,2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,
             2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990,
             1989,1988,1987,1986,1985,1984,1983,1982,1981,1980);
             $atSchoolMonth=array(1,2,3,4,5,6,7,8,9,10,11,12);
             $specialty = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>0));             
             $model = Province::model()->findAll();              
             $realname = UserDetail::model()->findByAttributes(array('user_id'=>Yii::app()->session['user_id']))->realname;              
             $this->smarty->assign('realname',$realname);
             $this->smarty->assign('specialty',$specialty);
             $this->smarty->assign('model',$model);
             $this->smarty->assign('atSchoolYear',$atSchoolYear);
             $this->smarty->assign('atSchoolMonth',$atSchoolMonth);
             $this->smarty->assign('isLeague',User::model()->findByPk(Yii::app()->session['user_id'])->is_league);
             $this->smarty->assign('schoolInformation',SchoolInformation::model()->
                 findByAttributes(array('id'=>intval(User::model()->findByPk(Yii::app()->session['user_id'])->username))));
             $this->smarty->display('user/perfect-info.html');
    	} 
    	public function actionDeelMessage(){                          
             $userDetail = UserDetail::model()->findByAttributes(array('user_id'=>Yii::app()->session['user_id']));
             Yii::app()->session['username']= $_POST['realname'];
             $studyEx = new StudyExperience();
             $userDetail->realname = $_POST['realname'];
             $userDetail->gender = $this->actionChangeSex($_POST['gender']);
             $userDetail->city_id = $_POST['city_id'];
             $userDetail->account_place = $this->actionChangeCity($_POST['city_id']);
             $studyEx->study_specialty_id = $_POST['study_specialty_id'];
             $studyEx->position_degree_id=$this->actionChangeDegree($_POST['degree']);
             $studyEx->start_time = $this->actionChangeTime($_POST['yearStart'],'07');
             $studyEx->end_time = $this->actionChangeTime($_POST['yearEnd'],'09');
             $studyEx->deparment=$_POST['deparment'];
             $user = User::model()->findByPk(Yii::app()->session['user_id']);
             if(isset($_POST['school'])&&$_POST['school']!='请输入您的学校') {
                 $studyEx->school_name = $_POST['school'];
                 $user->is_league = 0;
             }
             elseif($_POST['school_name']=='东北师范大学') {
                 $studyEx->school_name = $_POST['school_name'];
                 $user->is_league = 2;
                 $user->is_activated =1;
             }
             elseif($_POST['school_name']==1) {
                 $studyEx->school_name = $this->actionChangeSchool($_POST['school_name']);
                 $user->is_league = 0;
             }
             else{
                 $studyEx->school_name = $this->actionChangeSchool($_POST['school_name']);
                 $user->is_league = 1;
             }
             $user->save();
             $studyEx->major_name = $_POST['major_name'];
             $studyEx->user_id = Yii::app()->session['user_id'];
             $studyEx->sign = '1';
             if($userDetail->save()&&$studyEx->save()){
               $this->redirect(array('job/job/index'));
             }
    	}
        public function actionChangeCity($id){
           $name = City::model()->findByAttributes(array('id'=>$id))->name;
           return $name;
        }
    	public function actionChangeTime($year,$month){
    		  return $year.'-'.$month.'-01';             
    	}

        public function actionChangeSchool($data){
            switch($data){
                case 1: return '东北师范大学';
                    break;
                case 2: return '吉林师范大学';
                    break;
                case 3: return '辽宁师范大学';
                    break;
                case 4: return '长春师范大学';
                    break;
                case 5: return '哈尔滨师范大学';
                    break;
                case 6: return '内蒙古民族大学';
                    break;
                case 7: return '东北林业大学';
                    break;
                case 8: return '佳木斯大学';
                    break;
                case 9: return '齐齐哈尔大学';
                    break;
                case 10: return '哈尔滨学院';
                    break;
                case 11: return '鹤岗师范高等专科学校';
                    break;
                case 12: return '牡丹江师范学院';
                    break;
                case 13: return '大庆师范学院';
                    break;
                case 14: return '哈尔滨体育学院';
                    break;
                case 15: return '北华大学';
                    break;
                case 16: return '吉林工程技术师范学院';
                    break;
                case 17: return '通化师范学院';
                    break;
                case 18: return '长春大学';
                    break;
                case 19: return '白城师范学院';
                    break;
                case 20: return '延边大学';
                    break;
                case 21: return '吉林体育学院';
                    break;
                case 22: return '沈阳师范大学';
                    break;
                case 23: return '鞍山师范学院';
                    break;
                case 24: return '渤海大学';
                    break;
                case 25: return '沈阳大学';
                    break;
                case 26: return '赤峰学院';
                    break;
                case 27: return '绥化学院';
                    break;
                case 28: return '呼伦贝尔学院';
                    break;
            }
        }
    	public function actionChangeDegree($data){
    		switch($data){
    			case "本科": return 1;
    			break;
    			case "硕士": return 2;
    			break;
    			case "博士": return 3;
    			break;
                case "专科": return 4;
                    break;
                case "其它": return 5;
                    break;
            }
    	}
    	public function actionChangeSex($data){
    		switch($data){
    			case "女性": return 0;
    			break;
    			case "男性": return 1;
    			break;  			        
    		}
    	}
    	public function actionTest(){
    		$test = $this->actionChangeDegree("博士");
    		echo $test;
    	}
    	public function actionSpecialType(){
	         if(isset($_GET['majorId']))
	         {
	              $model = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>$_GET['majorId']));
	              $json='{"code":0,"data":'.CJSON::encode($model).'}';
	              print $json;            
	         }
    	}
    	public function actionCity(){   

           if(isset($_GET['id']))
           {
                 $model = City::model()->findAllByAttributes(array('province_id'=>$_GET['id']));
                 $json='{"code":0,"data":'.CJSON::encode($model).'}';
                 print $json;               
           }
    	}  	 
        
    }
?>