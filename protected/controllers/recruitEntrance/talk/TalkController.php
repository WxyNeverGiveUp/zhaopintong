<?php
/**
 * Created by PhpStorm.
 * User: HaoJin
 * Date: 2017/7/25
 * Time: 09:05
 */
class TalkController extends Controller
{
    public function actionIndex(){
        echo 'Talk Index';
    }

    public function actionTest(){

        $this->smarty->display('recruitEntrance/talk/test.html');
    }

    public function actionList(){
        if(!(isset($_SESSION['contact_id'])&&
            isset($_SESSION['company_id'])
        )){
            return $this->redirect(array('/recruitEntrance/recruitEntrance/ToLogin'));
        }
        $user_id = Yii::app()->session['contact_id'];//当前用户ID
        $company_id =  Yii::app()->session['company_id'];//当前公司ID

        if(isset($_GET['preachName']) && $_GET['preachName'] != ''){
            $talk_name = $_GET['preachName'];
            $sql = "
            select tct.id,tct.name,tct.time,tct.place
            from
                t_career_talk as tct
            where
                tct.company_id=".$company_id."
                and tct.name like '%".$talk_name."%'
            ";

            $talkList = Yii::app()->db->createCommand($sql)->queryAll();
            //echo json_encode($talkList,JSON_UNESCAPED_UNICODE);
            $this->smarty->assign('baseUrl',Yii::app()->request->getHostInfo());
            $this->smarty->assign('talkList',$talkList);
            $this->smarty->display('recruitEntrance/talk/list.html');
        }else{
            $sql = '
            select tct.id,tct.name,tct.time,tct.place,tct.is_ok
            from
                t_career_talk as tct
            where
                tct.company_id='.$company_id.' 
            ';

            $talkList = Yii::app()->db->createCommand($sql)->queryAll();
            //echo json_encode($talkList,JSON_UNESCAPED_UNICODE);
            $this->smarty->assign('baseUrl',Yii::app()->request->getHostInfo());
            $this->smarty->assign('talkList',$talkList);
            $this->smarty->display('recruitEntrance/talk/list.html');
        }


    }

    public function actionToAdd(){
        if(!(isset($_SESSION['contact_id'])&&
            isset($_SESSION['company_id'])
        )){
            return $this->redirect(array('/recruitEntrance/recruitEntrance/ToLogin'));
        }
        $company_id = $_SESSION['company_id'];
        $admin_id = $_SESSION['contact_id'];
        $companyUserModel = CompanyLoginUser::model();
        $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE admin_id = '$admin_id' AND company_id = '$company_id'";
        $companyUserList = $companyUserModel->findAllBySql($sql);
        $this->smarty->assign('baseUrl',Yii::app()->request->getHostInfo());
        $this->smarty->assign('companyUserList',$companyUserList);
        $this->smarty->display('recruitEntrance/talk/add.html');
    }

    public function actionAdd(){
        $_SESSION['company_id']=Yii::app()->session['company_id'];
        if(!(// 需求职位和专业
            isset($_POST['job_name'])
            && isset($_POST['job_num'])

            // 基本信息
            && isset($_POST['name'])
            && isset($_POST['time'])
            && isset($_POST['date'])
            && isset($_POST['place'])

            // 是否需要笔试面试场地
            && isset($_POST['written_place'])
            && isset($_POST['audition_place'])
            && isset($_POST['comment'])

            // 添加联系人和领队人
            && isset($_POST['contact_name'])
            && isset($_POST['contact_job'])
            && isset($_POST['contact_phone'])
            && isset($_POST['leader_name'])
            && isset($_POST['leader_job'])
            && isset($_POST['leader_phone'])

            // 添加来访人
            && isset($_POST['visitor_num'])
            && isset($_POST['visitor_names'])
            && isset($_POST['visitor_jobs'])
            && isset($_POST['visitor_phones'])
            // 是否需要宾馆
            && isset($_POST['reserve_hotel'])
        )){
            $result = array(
                'code' => '-1',
                'ext' => '参数不足'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
            return;
        }

        //添加需求职位
        $job_name = $_POST['job_name'];
        $job_num = $_POST['job_num'];
        $job_comment = $_POST['job_comment'];

        // 添加需求专业，需求专业数组转字符串
        if(isset($_POST['major_names'])
            && isset($_POST['major_degrees'])
            && isset($_POST['major_nums'])){
            $major_names = implode(',',$_POST['major_names']);
            $major_degrees = implode(',',$_POST['major_degrees']);
            $major_nums = implode(',',$_POST['major_nums']);
            $job_id = $this->add_job($job_name,
                $job_num,
                $job_comment,
                $major_names,
                $major_degrees,
                $major_nums);
        }else{
            $job_id = $this->add_job($job_name,
                $job_num,
                $job_comment,
                '',
                '',
                '');
        }
        //echo $job_id;

        // 基本信息
        $talk = new CareerTalk();

        $talk->name=$_POST['name'];
        $arr = explode('-',$_POST['time']);
        $talk->time=$_POST['date'].' '.$arr[0].':00';
        $talk->date = $_POST['date'];
        $talk->period = $_POST['time'];
        $talk->place=$_POST['place'];
        $talk->type=2;
        $talk->company_id=$_SESSION['company_id'];
        $talk->description=$_POST['description'];
        $talk->last_update='jyoa';
        $talk->is_front_input=0;
        $talk->job_id=$job_id;//此处需求可能改为使用job_ids

        // 笔试面试场地
        $talk->is_need_write_place=$_POST['written_place'];
        $talk->is_need_face_place=$_POST['audition_place'];
        $talk->place_comment = $_POST['comment'];

        if($talk->is_need_write_place == 'yes'
            && isset($_POST['written_room_num'])
            && isset($_POST['written_time_start'])
            && isset($_POST['written_time_end'])
            && isset($_POST['written_room_size'])
        ){
            $talk->write_place_id = $this->add_place('write',
                $_POST['written_room_num'],
                $_POST['written_time_start'],
                $_POST['written_time_end'],
                $_POST['written_room_size']);
        }

        if($talk->is_need_face_place == 'yes'
            && isset($_POST['audition_room_num'])
            && isset($_POST['audition_time_start'])
            && isset($_POST['audition_time_end'])
            && isset($_POST['audition_room_size'])
        ){
            $talk->face_place_id = $this->add_place('face',
                $_POST['audition_room_num'],
                $_POST['audition_time_start'],
                $_POST['audition_time_end'],
                $_POST['audition_room_size']);
        }

        // 添加联系人和领队人
        $talk->contact_user_id = $this->add_visitor($_POST['contact_name'],
            $_POST['contact_job'],
            $_POST['contact_phone']);
        $talk->leader_user_id = $this->add_visitor($_POST['leader_name'],
            $_POST['leader_job'],
            $_POST['leader_phone']);

        // 添加来访人
        $talk->visitor_sum = $_POST['visitor_num'];
        $talk->visitor_info_ids = $this->add_visitors($_POST['visitor_names'],
            $_POST['visitor_jobs'],
            $_POST['visitor_phones']);

        // 是否需要宾馆
        $talk->is_need_hotel = $_POST['reserve_hotel'];

        // 附件上传
        $attachment_ids = $this->add_attachments();
        $talk->attachment_ids=$attachment_ids;

        if($talk->save()){
//            $result = array(
//                'code' => '0',
//                'ext' => '添加成功',
//                'id' => $talk->id
//            );
//            echo json_encode($result,JSON_UNESCAPED_UNICODE);
            return $this->redirect(array('list'));
        }else{
            $result = array(
                'code' => '1',
                'ext' => '添加失败'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }

    function add_place($need_type,$num_require,$start_time,$end_time,$place_size){
        $place = new CareerNeedPlace();

        $place->need_type=$need_type;
        $place->num_require=$num_require;
        $place->start_time=$start_time;
        $place->end_time=$end_time;
        $place->place_size=$place_size;
        $place->created_time=date('Y-m-d H:i:s');
        $place->last_modified_time=date('Y-m-d H:i:s');
        if($place->save()){
            return $place->id;
        }else{
            return -1;
        }
    }

    function add_places($need_type,$room_num,$time_start,$time_end,$room_size){
        $place_ids=array();
        $sum = count($room_num);
        for($i=0;$i<$sum;$i++){
            $place = new CareerNeedPlace();
            $place->need_type=$need_type;
            $place->num_require=$room_num[$i];
            $place->start_time=$time_start[$i];
            $place->end_time=$time_end[$i];
            $place->place_size=$room_size[$i];
            $place->created_time=date('Y-m-d H:i:s');
            $place->last_modified_time=date('Y-m-d H:i:s');
            if($place->save()){
                $place_ids[] = $place->id;
            }else{
                return -1;
            }
        }
        return implode(',',$place_ids);
    }

    function add_job($name,$num,$comment,$major_names,$major_degrees,$major_nums){
        $job = new CareerRequireJob();

        $job->name=$name;
        $job->num=$num;
        $job->comment=$comment;
        $job->major_names=$major_names;
        $job->major_degrees=$major_degrees;
        $job->major_nums=$major_nums;
        $job->created_time=date('Y-m-d H:i:s');
        $job->last_modified_time=date('Y-m-d H:i:s');
        if($job->save()){
            return $job->id;
        }else{
            return -1;
        }
    }

    function add_attachment(){
        if (isset($_FILES['talk_file']) && is_uploaded_file($_FILES['talk_file']['tmp_name'])){
            $talk_file = $_FILES['talk_file'];
            $upErr = $talk_file['error'];
            if ($upErr == 0) {
                $logoType = $talk_file['type']; //文件类型。
                $arr = explode(".", $_FILES["talk_file"]["name"]);  //截取文件名跟后缀
                $prename = $arr[0];
                $file_name = date('YmdHis') . mt_rand(100, 999) . "." . end($arr);  // 文件的重命名 （日期+随机数+后缀）
                $temp_file = $talk_file['tmp_name'];
                /* 将文件从临时文件夹移到上传文件夹中。*/
                move_uploaded_file($temp_file, 'assets/uploadFile/recruitEntrance/talk/' . $file_name);
                $attachment = new CareerAttachment();
                $attachment->name=$_FILES["talk_file"]["name"];
                $attachment->url='assets/uploadFile/recruitEntrance/talk/' . $file_name;

                if($attachment->save()){
                    return $attachment->id;
                }else{
                    return -1;
                }
            }
        }else{
            echo '请选择文件后重试';
        }
    }

    function add_attachments(){
        $sum = count($_FILES['talk_file']['name']);
        if($sum==0){
            echo '请选择文件后重试';
            return -1;
        }

        $ids=array();
        for($i=0;$i<$sum;$i++){
            if (isset($_FILES['talk_file']) && is_uploaded_file($_FILES['talk_file']['tmp_name'][$i])){
                $talk_file = $_FILES['talk_file'];
                $upErr = $talk_file['error'][$i];
                if ($upErr == 0) {
                    $logoType = $talk_file['type'][$i]; //文件类型。
                    $arr = explode(".", $_FILES['talk_file']['name'][$i]);  //截取文件名跟后缀
                    $prename = $arr[0];
                    $file_name = date('YmdHis') . mt_rand(100, 999) . "." . end($arr);  // 文件的重命名 （日期+随机数+后缀）
                    $temp_file = $talk_file['tmp_name'][$i];
                    /* 将文件从临时文件夹移到上传文件夹中。*/
                    move_uploaded_file($temp_file, 'assets/uploadFile/recruitEntrance/talk/' . $file_name);
                    $attachment = new CareerAttachment();
                    $attachment->name=$_FILES['talk_file']['name'][$i];
                    $attachment->url='assets/uploadFile/recruitEntrance/talk/' . $file_name;

                    if($attachment->save()){
                        $ids[] = $attachment->id;
                    }else{
                        return -1;
                    }
                }
            }
        }
        return implode(',',$ids);

    }

    function add_visitor($visitor_name,$visitor_job,$visitor_phone){
        $visitor = new CareerVisitor();

        $visitor->name = $visitor_name;
        $visitor->duty = $visitor_job;
        $visitor->phone = $visitor_phone;
        if($visitor->save()){
            return $visitor->id;
        }else{
            return -1;
        }
    }

    function add_visitors($visitor_names,$visitor_jobs,$visitor_phones){

        $sum = count($visitor_names);
        $ids = array();
        for($i=0;$i<$sum;$i++){
            $visitor = new CareerVisitor();
            $visitor->name = $visitor_names[$i];
            $visitor->duty = $visitor_jobs[$i];
            $visitor->phone = $visitor_phones[$i];
            if($visitor->save()){
                $ids[] = $visitor->id;
            }else{
                return -1;
            }
        }
        return implode(',',$ids);

    }

    public function actionToEdit(){
        if(!(isset($_SESSION['contact_id'])&&
            isset($_SESSION['company_id'])
        )){
            return $this->redirect(array('/recruitEntrance/recruitEntrance/ToLogin'));
        }
        $id=Yii::app()->request->getParam('id');
        $talk = CareerTalk::model()->findByPk($id);

        // 可以选择的联系人和领队人
        $company_id = $_SESSION['company_id'];
        $admin_id = $_SESSION['contact_id'];
        $companyUserModel = CompanyLoginUser::model();
        $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE admin_id = '$admin_id' AND company_id = '$company_id'";
        $companyUserList = $companyUserModel->findAllBySql($sql);

        // 宣讲会时间段
        $period = new CareerTalkPeriod();
        $period_list = $period->findAll();

        // 职位和专业
        $job = CareerRequireJob::model()->findByPk($talk->job_id);
        $majors = array();
        $major_names = explode(',',$job->major_names);
        $major_degrees = explode(',',$job->major_degrees);
        $major_nums = explode(',',$job->major_nums);
        for($i=0;$i<count($major_names);$i++){
            $major = array(
                'name'=>$major_names[$i],
                'degree'=>$major_degrees[$i],
                'num'=>$major_nums[$i]
            );
            $majors[] = $major;
        }

        // 附件
        $a_ids = explode(',',$talk->attachment_ids);
        $attachments = array();
        foreach ($a_ids as $a_id){
            $attachment = CareerAttachment::model()->findByPk($a_id);
            $attachments[] = $attachment;
        }

        // 笔试面试场地
        $write_place = CareerNeedPlace::model()->findByPk($talk->write_place_id);
        $face_place = CareerNeedPlace::model()->findByPk($talk->face_place_id);

        // 领队人来访人信息
        $leader = CareerVisitor::model()->findByPk($talk->leader_user_id);
        $contact = CareerVisitor::model()->findByPk($talk->contact_user_id);
        $visitors = array();
        $visitor_ids = explode(',',$talk->visitor_info_ids);
        foreach($visitor_ids as $v_id){
            $visitor = CareerVisitor::model()->findByPk($v_id);
            $visitor = json_decode(CJSON::encode($visitor),TRUE);
            $visitors[] = $visitor;
        }

        //var_dump($visitors);
        //var_dump($majors);
        //var_dump($talk);
        $this->smarty->assign('companyUserList',$companyUserList);
        $this->smarty->assign('talk',$talk);
        $this->smarty->assign('period_list',$period_list);
        $this->smarty->assign('job',$job);
        $this->smarty->assign('majors',$majors);
        $this->smarty->assign('attachments',$attachments);
        $this->smarty->assign('write_place',$write_place);
        $this->smarty->assign('face_place',$face_place);
        $this->smarty->assign('leader',$leader);
        $this->smarty->assign('contact',$contact);
        $this->smarty->assign('visitors',$visitors);
        $this->smarty->display('recruitEntrance/talk/edit.html');
    }

    public function actionEdit(){
        $_SESSION['company_id']=Yii::app()->session['company_id'];
        if(!(// talk_id
            isset($_POST['talk_id'])
            // 需求职位和专业
            && isset($_POST['job_name'])
            && isset($_POST['job_num'])

            // 基本信息
            && isset($_POST['name'])
            && isset($_POST['time'])
            && isset($_POST['date'])
            && isset($_POST['place'])

            // 是否需要笔试面试场地
            && isset($_POST['written_place'])
            && isset($_POST['audition_place'])
            && isset($_POST['comment'])

            // 添加联系人和领队人
            && isset($_POST['contact_name'])
            && isset($_POST['contact_job'])
            && isset($_POST['contact_phone'])
            && isset($_POST['leader_name'])
            && isset($_POST['leader_job'])
            && isset($_POST['leader_phone'])

            // 添加来访人
            && isset($_POST['visitor_num'])
            && isset($_POST['visitor_names'])
            && isset($_POST['visitor_jobs'])
            && isset($_POST['visitor_phones'])
            // 是否需要宾馆
            && isset($_POST['reserve_hotel'])
        )){
            $result = array(
                'code' => '-1',
                'ext' => '参数不足'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
            return;
        }
        $talk = CareerTalk::model()->findByPk($_POST['talk_id']);

        // 基本信息
        $talk->name=$_POST['name'];
        $arr = explode('-',$_POST['time']);
        $talk->time=$_POST['date'].' '.$arr[0].':00';
        $talk->date = $_POST['date'];
        $talk->period = $_POST['time'];
        $talk->place=$_POST['place'];
        $talk->type=2;
        $talk->company_id=$_SESSION['company_id'];
        $talk->description=$_POST['description'];
        $talk->last_update='jyoa';
        $talk->is_front_input=0;

        // 需求职位
        $job_id = $_POST['job_id'];
        $job_name = $_POST['job_name'];
        $job_num = $_POST['job_num'];
        $job_comment = $_POST['job_comment'];

        // 修改需求专业，需求专业数组转字符串
        if(isset($_POST['major_names'])
            && isset($_POST['major_degrees'])
            && isset($_POST['major_nums'])){
            $major_names = implode(',',$_POST['major_names']);
            $major_degrees = implode(',',$_POST['major_degrees']);
            $major_nums = implode(',',$_POST['major_nums']);
            $job_id = $this->edit_job($job_id,
                $job_name,
                $job_num,
                $job_comment,
                $major_names,
                $major_degrees,
                $major_nums);
        }else{
            $job_id = $this->edit_job($job_id,
                $job_name,
                $job_num,
                $job_comment,
                '',
                '',
                '');
        }
        $talk->job_id=$job_id;//此处需求可能改为使用job_ids
        //echo $job_id;

        // 笔试面试场地
        $talk->is_need_write_place=$_POST['written_place'];
        $talk->is_need_face_place=$_POST['audition_place'];
        $talk->place_comment = $_POST['comment'];

        if($talk->is_need_write_place == 'yes'
            && isset($_POST['write_place_id'])
            && isset($_POST['written_room_num'])
            && isset($_POST['written_time_start'])
            && isset($_POST['written_time_end'])
            && isset($_POST['written_room_size'])
        ){
            $talk->write_place_id = $this->edit_place($_POST['write_place_id'],
                'write',
                $_POST['written_room_num'],
                $_POST['written_time_start'],
                $_POST['written_time_end'],
                $_POST['written_room_size']);
        }

        if($talk->is_need_face_place == 'yes'
            && isset($_POST['face_place_id'])
            && isset($_POST['audition_room_num'])
            && isset($_POST['audition_time_start'])
            && isset($_POST['audition_time_end'])
            && isset($_POST['audition_room_size'])
        ){
            $talk->face_place_id = $this->edit_place($_POST['face_place_id'],
                'face',
                $_POST['audition_room_num'],
                $_POST['audition_time_start'],
                $_POST['audition_time_end'],
                $_POST['audition_room_size']);
        }

        // 添加联系人和领队人
        $talk->contact_user_id = $this->add_visitor($_POST['contact_name'],
            $_POST['contact_job'],
            $_POST['contact_phone']);
        $talk->leader_user_id = $this->add_visitor($_POST['leader_name'],
            $_POST['leader_job'],
            $_POST['leader_phone']);

        // 添加来访人
        $talk->visitor_sum = $_POST['visitor_num'];
        $talk->visitor_info_ids = $this->add_visitors($_POST['visitor_names'],
            $_POST['visitor_jobs'],
            $_POST['visitor_phones']);

        // 是否需要宾馆
        $talk->is_need_hotel = $_POST['reserve_hotel'];

        // 附件上传
        $attachment_ids = $this->add_attachments();
        if(isset($_POST['attachment_ids']) && $_POST['attachment_ids'] != ''){
            $talk->attachment_ids=$_POST['attachment_ids'].','.$attachment_ids;
        }else{
            $talk->attachment_ids=$attachment_ids;
        }


        if($talk->save()){
            return $this->redirect(array('list'));
        }else{
            $result = array(
                'code' => '1',
                'ext' => '修改失败'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }

    function edit_job($id,$name,$num,$comment,$major_names,$major_degrees,$major_nums){
        $job = CareerRequireJob::model()->findByPk($id);

        $job->name=$name;
        $job->num=$num;
        $job->comment=$comment;
        $job->major_names=$major_names;
        $job->major_degrees=$major_degrees;
        $job->major_nums=$major_nums;
        $job->created_time=date('Y-m-d H:i:s');
        $job->last_modified_time=date('Y-m-d H:i:s');
        if($job->save()){
            return $job->id;
        }else{
            return -1;
        }
    }

    function edit_place($id,$need_type,$num_require,$start_time,$end_time,$place_size){
        $place = CareerNeedPlace::model()->findByPk($id);

        $place->need_type=$need_type;
        $place->num_require=$num_require;
        $place->start_time=$start_time;
        $place->end_time=$end_time;
        $place->place_size=$place_size;
        $place->created_time=date('Y-m-d H:i:s');
        $place->last_modified_time=date('Y-m-d H:i:s');
        if($place->save()){
            return $place->id;
        }else{
            return -1;
        }
    }

    public function actionToDetail(){
        if(!(isset($_SESSION['contact_id'])&&
            isset($_SESSION['company_id'])
        )){
            return $this->redirect(array('/recruitEntrance/recruitEntrance/ToLogin'));
        }
        $id=Yii::app()->request->getParam('id');
        $talk = CareerTalk::model()->findByPk($id);

        // 职位和专业
        $job = CareerRequireJob::model()->findByPk($talk->job_id);
        $majors = array();
        $major_names = explode(',',$job->major_names);
        $major_degrees = explode(',',$job->major_degrees);
        $major_nums = explode(',',$job->major_nums);
        for($i=0;$i<count($major_names);$i++){
            $major = array(
                'name'=>$major_names[$i],
                'degree'=>$major_degrees[$i],
                'num'=>$major_nums[$i]
            );
            $majors[] = $major;
        }

        // 附件
        $a_ids = explode(',',$talk->attachment_ids);
        $attachments = array();
        foreach ($a_ids as $a_id){
            $attachment = CareerAttachment::model()->findByPk($a_id);
            $attachments[] = $attachment;
        }

        // 笔试面试场地
        $write_place = CareerNeedPlace::model()->findByPk($talk->write_place_id);
        $face_place = CareerNeedPlace::model()->findByPk($talk->face_place_id);

        // 领队人来访人信息
        $leader = CareerVisitor::model()->findByPk($talk->leader_user_id);
        $contact = CareerVisitor::model()->findByPk($talk->contact_user_id);
        $visitors = array();
        $visitor_ids = explode(',',$talk->visitor_info_ids);
        foreach($visitor_ids as $v_id){
            $visitor = CareerVisitor::model()->findByPk($v_id);
            $visitor = json_decode(CJSON::encode($visitor),TRUE);
            $visitors[] = $visitor;
        }

        //var_dump($visitors);
        //var_dump($majors);
        //var_dump($talk);
        $this->smarty->assign('talk',$talk);
        $this->smarty->assign('job',$job);
        $this->smarty->assign('majors',$majors);
        $this->smarty->assign('attachments',$attachments);
        $this->smarty->assign('write_place',$write_place);
        $this->smarty->assign('face_place',$face_place);
        $this->smarty->assign('leader',$leader);
        $this->smarty->assign('contact',$contact);
        $this->smarty->assign('visitors',$visitors);
        $this->smarty->display('recruitEntrance/talk/detail.html');
    }

    public function actionDel(){
        $id=Yii::app()->request->getParam('id');
        $talk = CareerTalk::model()->findByPk($id);
        $success = $talk->delete();
        if($success){
            $result = array(
                'code'=> '0',
                'ext'=> 'success'
            );
            echo json_encode($result);
        }else {
            $result = array(
                'code'=> '1',
                'ext'=> 'fail'
            );
            echo json_encode($result);
        }
    }

    public function actionToFlow(){
        $this->smarty->display('recruitEntrance/talk/flow.html');
    }

    public function actionGetMajorClass(){
        $degree = Yii::app()->request->getParam('degree');
        $sql = "
            select distinct major.major_class
            from
                t_career_talk_major as major
            where
                major.qualification like '%".$degree."%'
            ";

        $major_class_list = Yii::app()->db->createCommand($sql)->queryAll();
        $result = array(
            'code' => '0',
            'ext' => 'success',
            'major_class_list' => $major_class_list
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    public function actionGetMajor(){
        $degree = Yii::app()->request->getParam('degree');
        $major_class = Yii::app()->request->getParam('major_class');
        $sql = "
            select major.major_id,major.major_name
            
            from
                t_career_talk_major as major
            where
                major.qualification like '%".$degree."%'
                and major.major_class = '".$major_class."'
            ";
        $major_list = Yii::app()->db->createCommand($sql)->queryAll();
        $result = array(
            'code' => '0',
            'ext' => 'success',
            'major_list' => $major_list
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);

    }

}

?>
