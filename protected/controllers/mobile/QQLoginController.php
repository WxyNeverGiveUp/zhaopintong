<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
/**
* 
*/
class QQLoginController extends Controller
{
    public function actionIndex(){
        echo 'qqlogin';
    }

    public function actionLogin()
    {
        $username = $_POST['openid'];
        $figureurl = $_POST['figureurl'];

        if($username == '' || $figureurl == ''){
            $result = array(
                'code'=>'-1',
                'error'=>'参数不能为空'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
            return;
        }

        // 是否注册
        $user = User::model()->findByAttributes(array('username'=>$username));
        //var_dump($user);
        if($user){
            $user_id = $user->attributes['id'];
        }else{
            $user = new User();
            $user->username = $username;
            if($user->save()){
                $user_id = $user->attributes['id'];
                $resume = new Resume();
                $resume->user_id = $user_id;
                $resume_rs = $resume->save();
                if (!$resume_rs) {
                    throw new Exception("Error Processing Request");
                }
            }
        }


        // 是否填写过详细信息
        $user_detail = UserDetail::model()->findByAttributes(array('user_id'=>$user_id));
        if($user_detail){
            if($user_detail->attributes['realname'] == ''){
                $result = array(
                    'code'=>'1',
                    'user_id'=>$user_id
                );
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
            }else{
                $result = array(
                    'code'=>'0',
                    'user_id'=>$user_id
                );
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
            }

        }else{
            $user_detail = new UserDetail();
            $user_detail->user_id =$user_id;
            $user_detail->head_url = $figureurl;
            if($user_detail->save()){
                $result = array(
                    'code'=>'1',
                    'user_id'=>$user_id
                );
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
            }

        }

    }
}