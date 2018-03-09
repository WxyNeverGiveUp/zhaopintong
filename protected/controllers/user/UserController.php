<?php
/**
 * 功能：实现登录与注册
 */
class UserController extends Controller {

    public function actionLogin()
    {


        if($_POST['userType']=='1')
        {

            $wsdl = "http://202.198.130.30:9082/ids4Api/services/ids4?wsdl";
            $username="T7eeibpOEgI=";
            $password="MWVd4cs03NvosoS8uvcBlw==";
            $client = new ids4Service($wsdl,$username,$password);
            $user = $_POST['username'];
            $pass = $_POST['password'];
            if($client->checkPassword($user,$pass))
            {
                $username=$client->getUserNameByID($user);

                $model = User::model()->findByAttributes(array('username'=>$user));
                if($model)
                {
                    Yii::app()->session['user_id']= $model->id;
                    $model->is_league = 2;
                    $model->save();
                    $study = $this->actionStatus($model->id);
                    if($study){
                        Yii::app()->session['username'] = $username;
                        $json='{"code":0,"data":"'.$username.'"}';
                        print $json;
                    }else{
                        print '{"code":2}';
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
                        Yii::app()->session['user_id']=$id;
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
                            Yii::app()->session['resume_id']=$resume->attributes['id'];
                            $json='{"code":0,"data":"'.$username.'"}';
                            print $json;
                        }else{
                            print '{"code":2}';
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
        else if($_POST['userType']=='2')
        {
            $user = trim($_POST['username']);
            $pass = md5(trim($_POST['password']));
            $model = User::model()->findByAttributes(array('username'=>$user,'password'=>$pass));   //查询数据库
            if($model)   //如何有满足的记录，转到主页，不然就是用户名与密码错误
            {
                if($model->status=='1')   //判断用户是否激活
                {

                    Yii::app()->session['user_id']=$model->id;
                    $username = UserDetail::model()->findByAttributes(array('user_id'=>$model->id))->realname;
                    $study = $this->actionStatus($model->id);
                    if($study){
                        Yii::app()->session['username'] = $username;
                        $json='{"code":0,"data":"'.$username.'"}';
                        print $json;
                    }else{
                        print '{"code":2}';
                    }
                }
                else
                {
                    print '{"code":"1"}';

                }
            }
            else
            {
                print '{"code":"1"}';
            }
        }
    }
    /*走访专题网站登陆接口 2016/9/3 黄灿*/
    public function actionLogintheme()
    {
            $wsdl = "http://202.198.130.30:9082/ids4Api/services/ids4?wsdl";
            $username="T7eeibpOEgI=";
            $password="MWVd4cs03NvosoS8uvcBlw==";
            $client = new ids4Service($wsdl,$username,$password);
            $user = $_POST['username'];
            $pass = $_POST['password'];
            if($client->checkPassword($user,$pass))
            {
                $this->redirect("http://jyj2017.dsjyw.net?r=ticket/index");
            }else{
                echo "<script language='javascript'>alert('请输入正确的学号和师大邮箱密码');location.href='http://jyj2017.dsjyw.net?r=index/index'</script>";
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
	public function actionLoginfly()
    {
            $wsdl = "http://202.198.130.30:9082/ids4Api/services/ids4?wsdl";
            $username="T7eeibpOEgI=";
            $password="MWVd4cs03NvosoS8uvcBlw==";
            $client = new ids4Service($wsdl,$username,$password);
            $user = $_POST['username'];
            $pass = $_POST['password'];
            if($client->checkPassword($user,$pass))
            {
                $this->redirect("http://jyj2017.dsjyw.net?r=fly/add&xuehao=$user");
            }else{
                echo "<script language='javascript'>alert('请输入正确的学号和师大邮箱密码');location.href='http://jyj2017.dsjyw.net?r=index/index'</script>";
                }
    }

    public function actionLayout()
    {                                           //退出登陆
        //Yii::app()->session->clear();            //删除session里的用户
        //Yii::app()->session->destroy();          //销毁session文件
        unset( Yii::app()->session['user_id'] );
        unset( Yii::app()->session['username'] );
        unset( Yii::app()->session['resume_id'] );
        $this->redirect(Yii::app()->webConstants->getSite());    //转到主页index.html
    }
    public function actionEmail($email)
    {
        $em = User::model()->findAllByAttributes(array('username'=>$email));
        if($em)
        {
            print '{"code":"0","data":""}';
        }
        else
        {
            print '{"code":"1","data":""}';
        }
    }

    public function actionReg()
    {   
        $user =new User();

        $token = md5($_POST['email'].$_POST['password'].date("Y-m-d H:i:s",time()));
        $user->token = $token; //创建用于激活识别码

        if ($userModel = User::model()->findByAttributes(array('username'=>$_POST['email']))) {
            $user = $userModel;
            $user->token = $token;
            $hasUser = 1;
        }else{
            $hasUser = 0;
        }
        $user->password = md5(trim($_POST['password']));
        $user->username = $_POST['email'];
        $user->email=$_POST['email'];
        
        $user->token_exptime = time()+60*60*24;
        $user->reg_time = date("Y-m-d H:i:s",time());

        // 验证邮箱有效性
        $email = $_POST['email'];
        $email_filter = filter_var($email,FILTER_VALIDATE_EMAIL);

        $email_arr = explode("@",$email);
        $len = count($email_arr);
        $domain = $email_arr[$len-1];
        $domain_filter = checkdnsrr($domain, 'MX');
        if ($email_filter && $domain_filter) {
            require_once('protected/extensions/swiftMailer/lib/classes/Swift.php');
            Yii::registerAutoloader(array('Swift','autoload'));
            require_once('protected/extensions/swiftMailer/lib/swift_init.php');
            require_once('protected/extensions/swiftMailer/lib/swift_required.php');

            $transport = Swift_SmtpTransport::newInstance('smtp.ym.163.com', 25)
              ->setUsername('join@dsjyw.net')
              ->setPassword('hellojoin');

            $mailer = Swift_Mailer::newInstance($transport);
            
            $emailbody = "亲爱的".$_POST['username']."：
                <br/>感谢您在我站注册了新帐号,请点击链接激活您的帐号。<br/>"."
                <a href='"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/".
                "user/user/active/verify/".$token."'
               target= '_blank'>"."http://".$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']."/user/user/active/verify/".$token."</a><br/>
                如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";

            $message = Swift_Message::newInstance('东北高师就业联盟网账号激活')
              ->setFrom(array('join@dsjyw.net' => '东北高师就业联盟网'))
              ->setTo(array($_POST['email'] => $_POST['username']))
              ->addPart($emailbody, 'text/html');

            $result = $mailer->send($message);

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
                    $userDetail->realname = $_POST['username'];
                    $userDetail->current_status = $_POST['currentStatus'];
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

    function actionActive()
    {
        header("content-type:text/html;charset=utf-8");
        $verify = stripslashes(trim($_GET['verify']));
        $nowtime = time();
        $attribute = array('token'=>$verify,'status'=>0);
        $query = User::model()->findByAttributes($attribute);
        if($query!=null)
            $token_exptime = $query->token_exptime;

        if($query)
        {
            if($nowtime>$token_exptime)
            { //24hour
                echo "<script>alert('您的激活有效期已过，请24小时后重新注册，激活，登录。.')
                location.href='http://www.dsjyw.net'
                </script>";
            }
            else
            {
                $query->status = 1;   //将status的状态改为1，表明已激活。
                if($query->save())
                {
                    echo "<script>alert('激活成功!请去主页登录')
                    location.href='http://www.dsjyw.net'</script>";
                }
            }
        }
        else
        {
            $attributes = array('token'=>$verify);
            $user = User::model()->findByAttributes($attributes);
            if($user->status==1)
            {
                echo "<script>alert('账户已激活!')
                location.href='http://www.dsjyw.net'
                </script>";
            }else
            {
                echo "<script>alert('激活失败!')
                location.href='http://www.dsjyw.net'
                </script>";
            }
        }

    }




    /*public function filters() {
       return array(
           array('application.controllers.filters.SessionCheckFilter-tologin,login')
       );
    }*/

}
?>




    
  