<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
/**
* 
*/
class LoginController extends Controller
{
     public function actionIndex(){
          $data = json_decode(file_get_contents("php://input"));
          
     	// $username = Yii::app()->request->getPost('username');
     	// $password = Yii::app()->request->getPost('password');

     	// $UserModel = User::model();

     	// $UserInfo = $UserModel->findByAttributes(array('username'=>$username));

     	// if ($UserInfo->password == $password) {
     	// 	Yii::app()->cache->set('qwer',$UserInfo->id,100);
     	// 	echo 111;
     	// }else{
     	// 	echo 000;
     	// }
     }	

      public function actionLogin(){
        $data = json_decode(file_get_contents("php://input"));
        $userType = $data->userType;
        if($userType=='1')
        {
            $wsdl = "http://202.198.130.30:9082/ids4Api/services/ids4?wsdl";
            $username="T7eeibpOEgI=";
            $password="MWVd4cs03NvosoS8uvcBlw==";
            $client = new ids4Service($wsdl,$username,$password);
            $user = $data->username;
            $pass = $data->password;
            if($client->checkPassword($user,$pass))
            {
                $username=$client->getUserNameByID($user);

                $model = User::model()->findByAttributes(array('username'=>$user));
                if($model)
                {
                    // Yii::app()->session['user_id']= $model->id;
                    $model->is_league = 2;
                    $model->save();
                    $study = $this->actionStatus($model->id);
                    if($study){
                        // Yii::app()->session['username'] = $username;
                        $Array = array('code'=>0,'username'=>$username,'user_id'=>$model->id);
                        $json = CJSON::encode($Array);
                        print $json;
                    }else{
                        $Array = array('code'=>2,'username'=>$username,'user_id'=>$model->id);
                        $json = CJSON::encode($Array);
                        print $json;
                    }
                }
                else
                {
                    $use = new User();
                    $use->username=$user;  //插入用户名
                    $use->is_league = 2;
                    if($use->save())
                    {
                        $attribute = array('username'=>$user);
                        $id = User::model()->findByAttributes($attribute)->id;   //查询用户id
                        $userDetail= new UserDetail();
                        $userDetail->user_id=$id;                   //在t_userdetail插入user_id
                        // Yii::app()->session['user_id']=$id;
                        $userDetail->realname=$username;   //在t_userdetail插入realname
                        $userDetail->head_url = "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/".'assets/resources/resources/img/common/head_pic.gif';
                        $userDetail->save();
                        $resume = new Resume();
                        $resume->user_id = $id;
                        $resume->save();
                        $study = $this->actionStatus($id);
                        // Yii::app()->session['count'];
                        //if()
                        if($study){
                            // Yii::app()->session['resume_id']=$resume->attributes['id'];
                            $Array = array('code'=>0,'username'=>$username,'user_id'=>$id);
                            $json = CJSON::encode($Array);
                            print $json;
                        }else{
                            $Array = array('code'=>2,'username'=>$username,'user_id'=>$id);
                            $json = CJSON::encode($Array);
                            print $json;
                        }


                    }
                    else
                    {
                        print '{"code":"1"}';
                    }
                }
            }
            else{

                print  '{"code":"1"}';
            }

        }
        else if($userType=='2')
        {
            $user = trim($data->username);
            $pass = md5(trim($data->password));
            $model = User::model()->findByAttributes(array('username'=>$user,'password'=>$pass));   //查询数据库
            if($model)   //如何有满足的记录，转到主页，不然就是用户名与密码错误
            {
                if($model->status=='1')   //判断用户是否激活
                {

                    // Yii::app()->session['user_id']=$model->id;
                    $username = UserDetail::model()->findByAttributes(array('user_id'=>$model->id))->realname;
                    $study = $this->actionStatus($model->id);
                    if($study){
                        // Yii::app()->session['username'] = $username;
                        $Array = array('code'=>0,'username'=>$username,'user_id'=>$model->id);
                        $json = CJSON::encode($Array);
                        print $json;
                    }else{
                        $Array = array('code'=>2,'username'=>$username,'user_id'=>$model->id);
                        $json = CJSON::encode($Array);
                        print $json;
                    }
                }
                else
                {
                    print '{"code":"8"}';

                }
            }
            else
            {
                print '{"code":"9"}';
            }
        }
    }
   
    public function actionStatus($data){
        $sql="select sign from {{study_experience}} where user_id='".$data."'";
        $result=StudyExperience::model()->findBySql($sql);
        if(!$result){
            $sqll = "select sign from {{work_experience}} where user_id='".$data."'";
            $result = WorkExperience::model()->findBySql($sqll);
            if($result){
                return true;
            }
        }else{
            return true;
        }
    }

    public function actionReg()
    {   
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $email = $data->email;
        $password = $data->password;
        $status = $data->status;

        $user =new User();

        $token = md5($email.$password.date("Y-m-d H:i:s",time()));
        $user->token = $token; //创建用于激活识别码

        if ($userModel = User::model()->findByAttributes(array('username'=>$email))) {
            $user = $userModel;
            $user->token = $token;
            $hasUser = 1;
        }else{
            $hasUser = 0;
        }
        $user->password = md5(trim($password));
        $user->username = $email;
        $user->email=$email;
        
        $user->token_exptime = time()+60*60*24;
        $user->reg_time = date("Y-m-d H:i:s",time());

        // 验证邮箱有效性
        $email = $email;
        $email_filter = filter_var($email,FILTER_VALIDATE_EMAIL);

        $email_arr = explode("@",$email);
        $len = count($email_arr);
        $domain = $email_arr[$len-1];
        $domain_filter = checkdnsrr($domain, 'MX');
        if ($email_filter && $domain_filter) {
            include("protected/extensions/phpmailer/sendl.php");
            $gao = new smtpclass();
            //创建发送邮件的对象
            $emailbody = "亲爱的".$username."：
                <br/>感谢您在我站注册了新帐号,请点击链接激活您的帐号。<br/>"."
                <a href='"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/".
                "user/user/active/verify/".$token."'
               target= '_blank'>"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/user/user/active/verify/".$token."</a><br/>
                如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";
            //发送邮件
            $gao->senduserMail("smtp.ym.163.com",25,"join@dsjyw.net","hellojoin","join@dsjyw.net",$email,"东北高师就业联盟网邮箱激活","'".$emailbody."'","HTML");
        }else{
            print '{"code":"1"}';
            return;
        }

        $tr = Yii::app()->db->beginTransaction();
        try {
            $user_rs = $user->save();
            if (!$hasUser) {
                $user_id = $user->attributes['id'];
                if ($user_id) {
                    $userDetail = new UserDetail();
                    $userDetail->realname = $username;
                    $userDetail->current_status = $status;
                    $userDetail->user_id =$user_id;
                    $userDetail->head_url = "http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/".'assets/resources/resources/img/common/head_pic.gif';
                    $detail_rs = $userDetail->save();

                    $resume = new Resume();
                    $resume->user_id = $user_id;
                    $resume_rs = $resume->save();
                    if (!$resume_rs||!$detail_rs) {
                        throw new Exception("Error Processing Request");
                    }
                }
            }
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollback();
            print '{"code":"1"}';
            return;
        }
        print '{"code":"0"}';
    }


    public function actionDeelMessage(){ 
         $data = json_decode(file_get_contents("php://input"));
         $user_id = $data->user_id;     
         $realname = $data->realname; 
         $city_id = $data->city_id;
         $gender = $data->gender;
         // $school = $data->school;
         $school_name = $data->school_name;
         $major_name = $data->major_name;
         $deparment = $data->deparment;
         $degree = $data->degree;
         $yearStart = $data->yearStart;
         $yearEnd = $data->yearEnd;
         
         $city_name = $city_id;
         $city_id = City::model()->findByAttributes(array('name'=>$city_name))->id;

         $userDetail = UserDetail::model()->findByAttributes(array('user_id'=>$user_id));
         $studyEx = new StudyExperience();
         $userDetail->realname = $realname;
         $userDetail->gender = $gender;
         $userDetail->city_id = $city_id;
         $userDetail->account_place = $city_name;
         // $studyEx->study_specialty_id = $_POST['study_specialty_id'];
         $studyEx->position_degree_id=$degree;
         $studyEx->start_time = $this->actionChangeTime($yearStart,'07');
         $studyEx->end_time = $this->actionChangeTime($yearEnd,'09');
         $studyEx->deparment=$deparment;
         $user = User::model()->findByPk($user_id);
         if(isset($school)&&$school!='请输入您的学校') {
             $studyEx->school_name = $school;
             $user->is_league = 0;
         }
         elseif($school_name=='东北师范大学') {
             $studyEx->school_name = $school_name;
             $user->is_league = 2;
             $user->is_activated =1;
         }
         elseif($school_name==1) {
             $studyEx->school_name = $this->actionChangeSchool($school_name);
             $user->is_league = 0;
         }
         else{
             $studyEx->school_name = $this->actionChangeSchool($school_name);
             $user->is_league = 1;
         }
         $user->save();
         $studyEx->major_name = $major_name;
         $studyEx->user_id = $user_id;
         $studyEx->sign = '1';
         if($userDetail->save()&&$studyEx->save()){
           $Array = array('code'=>0,'user_id'=>$user_id);
           $json = CJSON::encode($Array);
           print $json;
         }else{
           $Array = array('code'=>1);
           $json = CJSON::encode($Array);
           print $json;
         }
    }

    public function actionPassword()
    {
        $data = json_decode(file_get_contents("php://input")); 
        $curentPassword = $data->curentPassword;
        $newPassword = $data->newPassword;
        $user_id = $data->user_id;
        $model = User::model()->findByAttributes(array('id'=>$user_id));
       
        if($model->password==md5($curentPassword))
        {
           $model->password=md5($newPassword);
            if($model->save())
            {
               $Array = array('code'=>0,'user_id'=>$user_id);
               $json = CJSON::encode($Array);
               print $json;
            }
            else{

               $Array = array('code'=>1,'user_id'=>$user_id);
               $json = CJSON::encode($Array);
               print $json;
            }

        }
        else
        {                    
               $Array = array('code'=>2,'user_id'=>$user_id);
               $json = CJSON::encode($Array);
               print $json;
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
         $wsdl = "http://202.198.130.30:9082/ids4Api/services/ids4?wsdl";
            $username="T7eeibpOEgI=";
            $password="MWVd4cs03NvosoS8uvcBlw==";
            $client = new ids4Service($wsdl,$username,$password);
            $user = 2013012857;
            $pass = 'ch84150272';
            if($client->checkPassword($user,$pass)){
                 $username=$client->getUserNameByID($user);
                 var_dump($username);
            }
    }
	
}