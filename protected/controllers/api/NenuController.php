<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
date_default_timezone_set('Asia/Shanghai');

/**
 * Created by PhpStorm.
 * User: wangjf
 * Date: 2017/8/10
 * Time: 13:52
 *
 */
class NenuController    extends Controller
{
    //查询出东北师大的学子，和就业联盟网进行数据同步
    public function actionIndex(){
        $currentYear = intval(date('Y',time()));
        $this->smarty->assign('currentYear', $currentYear);
        $this->smarty->display('recruitEntrance/talentInvitation/talentInvitation-nenu.html');

    }
    public function actionGetNenuInfo()

    {
        //签约数据 线上版的接口地址
        $url =  "http://qy.dsjyw.net/talent/recruitment/toList";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);  // 就业联盟网数据同步接口地址

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息

        curl_setopt($ch, CURLOPT_HEADER, 0);  //是否显示头信息

        $output = curl_exec($ch);

        curl_close($ch); // 关闭数据同步会话

        print $output;

    }



    /**
     *  ajax按条件请求就业联盟网的数据
     * $currPage   当前页
     * $marjorId  专业中类ID
     * $sexId  性别ID
     * $locationId  生源地ID
     * $time  毕业年份
     *  $normalId 师范生类别ID
     */
    public function actionListJson(){
//        $data['currPage'] = $_GET['currentPage'];
//        $data['marjorId'] = $_GET['majorId'];
//        $data['sexId'] = $_GET['sexId'];
//        $data['locationId'] = $_GET['locationId'];
//        $data['time'] = $_GET['time'];
//        $data['normalId'] = $_GET['normalId'];
//
        $data = array(
            'token'=>$this->sendtoken(),
            'data'=>Array
            (
                'qualification' =>$_GET['major1'],
                'majorClass' => $_GET['major3'],
                'provinceCode' =>$_GET['province'],
                'normalStu' =>$_GET['sort'],
//                'qualification' =>'本科专业',
//                'majorClass' => '1',
//                'provinceCode' =>'-1',
//                'normalStu' =>'1',

            )
        );

        //就业联盟网的接口地址
        $url = 'http://qy.dsjyw.net/talent/recruitment/ajax/list';
        $json_data = CJSON::encode($data);
        $headerArray = array(
            'Accept:application/json, text/javascript, */*',
            'Content-Type:application/json;charset=UTF-8',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//设置头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息
        $out = curl_exec($ch);
        curl_close($ch);

        print $out;

    }

    //邀请投递
    public function actionInvite(){
        $companyId=Yii::app()->session['company_id'];
        $userNum = $_GET['stuNum'];
//        $userNum = '11111';

        $sql = "select * from {{user}} where username=".$userNum;
        $userInfo = User::model()->findBySql($sql);

//        //在联盟网中查到改学生，则可以邀请投递
        if($userInfo != null) {
            $record = "select * from {{invite_record}} where user_id='" . $userInfo->id . "' and company_id='" . $companyId . "'";
            $inviteRecord = InviteRecord::model()->findBySql($record);
            //该学生还未邀约
            if ($inviteRecord == null) {
                $inviteRecordOne = new InviteRecord();
                $inviteRecordOne->user_id = $userInfo->id;
                $inviteRecordOne->company_id = $companyId;
                $inviteRecordOne->status = 1;
                $inviteRecordOne->created_time = date("Y-m-d", time());
                $inviteRecordOne->last_modified_time = date("Y-m-d", time());
                $inviteRecordOne->save();
                //此返回一个成功代码
                $list = '[{"code":0,"data":""}]';
                print $list;

            }
            else {
                //返回一个失败代码，代表该学生已被邀请
                $list = '[{"code":1,"data":""}]';
                print $list;
            }
        }else{
            //返回一个失败代码，代表该学生未在联盟网中进行过注册，不能进行邀约
            $list = '[{"code":2,"data":""}]';
            print $list;

        }

    }


}