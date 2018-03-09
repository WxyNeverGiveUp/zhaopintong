<?php
/**
 * 后台-宣讲会管理
 * User: yuzhengfei
 * Date: 2017/11/1
 * Time: 9:00
 */
class TalkController extends Controller{

    /**
     * @param $id
     * 根据公司id显示该公司所有的宣讲会信息
     */
    public function actionListByCompany($id){
        $criteria = new CDbCriteria();
        //根据公司id查询
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$id));
        //获取总数量
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        //获取所有的行业的信息
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("trade",$trade);
        $this->smarty->assign("companyId",$id);
        //$this->smarty->assign('list',$list_all);
        $this->smarty->assign('current','talk');
        $this->smarty->display('admin/talk/listByCompany.html');
    }

    /**
     * 分页获取指定公司得宣讲会信息
     *
     */
    public function actionJsonByCompany(){
        $page = $_GET['page1'];
        $companyId = $_GET['companyId'];

        //获取指定companyId得所有宣讲会
        $listAll = CareerTalk::model()->findAllByAttributes(array('company_id'=>$companyId));
        $recordCount = count($listAll);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);

        //分页查询
        $criteria = new CDbCriteria();
        $criteria -> condition = ('company_id=:id');
        $criteria -> params = (array(':id'=>$companyId));
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage-1)*$pageSize;
        $list = CareerTalk::model()->findAll($criteria);
        $aList = '{"list":'.CJSON::encode($list).',"dataCount":'.$recordCount.'}';
        print $aList;
    }

    /**
     * @param $id
     * 去创建宣讲会页
     */
    public function actionCreate($id){

        $company_id = $id;
        //$user_id = Yii::app()->session['user_name'];

        $companyUserModel = CompanyLoginUser::model();
        $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE company_id = '$company_id'";
        $companyUserList = $companyUserModel->findAllBySql($sql);

        //echo $companyUserList;
        $this->smarty->assign('companyUserList',$companyUserList);

        $this->smarty->assign('companyId', $id);
        $this->smarty->assign('current', 'talk');
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->display('admin/talk/create.html');
    }

    /**
     * 添加宣讲会
     */
    public function actionAdd(){
//        $Talk = new CareerTalk();
//        $Talk->name = $_POST['name'];
//        $Talk->time = $_POST['time'];
//        $Talk->place = $_POST['place'];
//        $Talk->type = $_POST['type'];
//        $Talk->company_id = $_POST['companyId'];
//        $Talk->description = $_POST['description'];
//        //待确定前端传入的字段
//        $Talk->url = $_POST['url'];
//        $Talk->live_url = $_POST['liveUrl'];
//        $Talk->last_update = Yii::app()->session['user_name'];
//        $Talk->save();
//        $this->redirect($this->createUrl("admin/talk/talk/listByCompany/id/".$_POST[companyId]));

        //$_SESSION['company_id']=210;
        //echo 23333;
        //判空
        if(!(// 需求职位和专业
            isset($_POST['job_name']) && isset($_POST['job_num'])

            // 基本信息
            && isset($_POST['name']) && isset($_POST['time']) && isset($_POST['date']) && isset($_POST['place'])

            // 是否需要笔试面试场地
            && isset($_POST['written_place']) && isset($_POST['audition_place']) && isset($_POST['comment'])

            // 添加联系人和领队人
            && isset($_POST['contact_name']) && isset($_POST['contact_job']) && isset($_POST['contact_phone'])
            && isset($_POST['leader_name']) && isset($_POST['leader_job']) && isset($_POST['leader_phone'])

            // 添加来访人
            && isset($_POST['visitor_num']) && isset($_POST['visitor_names']) && isset($_POST['visitor_jobs']) && isset($_POST['visitor_phones'])

            // 是否需要宾馆
            && isset($_POST['reserve_hotel'])
        )){
            $result = array(
                'code' => '-1',
                'ext' => '参数不足111'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
            return;
        }

        //添加需求职位
        $job_name = $_POST['job_name'];
        $job_num = $_POST['job_num'];
        $job_comment = $_POST['job_comment'];

        // 添加需求专业，需求专业数组转字符串
        if(isset($_POST['major_names']) && isset($_POST['major_degrees']) && isset($_POST['major_nums'])){
            $major_names = implode(',',$_POST['major_names']);
            $major_degrees = implode(',',$_POST['major_degrees']);
            $major_nums = implode(',',$_POST['major_nums']);
            $job_id = $this->add_job($job_name, $job_num, $job_comment, $major_names, $major_degrees, $major_nums);
        }else{
            $job_id = $this->add_job($job_name, $job_num, $job_comment, '', '', '');
        }

        // 基本信息
        $talk = new CareerTalk();
        $talk->name = $_POST['name'];
        $arr = explode('-',$_POST['time']);
        $talk->time = $_POST['date'].' '.$arr[0].':00';
        $talk->date = $_POST['date'];
        $talk->period = $_POST['time'];
        $talk->place = $_POST['place'];
        $talk->type = 2;   //宣讲会类型(1.视频宣讲、2.实地宣讲、3.外地宣讲)
        $talk->company_id = $_POST['company_id'];
        $talk->description = $_POST['description'];
        $talk->last_update = Yii::app()->session['user_name'];;
        $talk->is_front_input = 0;   //是否首页插入
        $talk->job_id = $job_id;//此处需求可能改为使用job_ids

        // 笔试、面试场地
        $talk->is_need_write_place = $_POST['written_place'];
        $talk->is_need_face_place = $_POST['audition_place'];
        $talk->place_comment = $_POST['comment'];
        if($talk->is_need_write_place == 'yes' && isset($_POST['written_room_num'])
            && isset($_POST['written_time_start']) && isset($_POST['written_time_end'])
            && isset($_POST['written_room_size'])
        ){
            $talk->write_place_id = $this->add_place('write', $_POST['written_room_num'], $_POST['written_time_start'], $_POST['written_time_end'], $_POST['written_room_size']);
        }
        if($talk->is_need_face_place == 'yes' && isset($_POST['audition_room_num'])
            && isset($_POST['audition_time_start']) && isset($_POST['audition_time_end'])
            && isset($_POST['audition_room_size'])
        ){
            $talk->face_place_id = $this->add_place('face', $_POST['audition_room_num'], $_POST['audition_time_start'], $_POST['audition_time_end'], $_POST['audition_room_size']);
        }

        // 添加联系人、领队人
        $talk->contact_user_id = $this->add_visitor($_POST['contact_name'], $_POST['contact_job'], $_POST['contact_phone']);
        $talk->leader_user_id = $this->add_visitor($_POST['leader_name'], $_POST['leader_job'], $_POST['leader_phone']);

        // 添加来访人
        $talk->visitor_sum = $_POST['visitor_num'];
        $talk->visitor_info_ids = $this->add_visitors($_POST['visitor_names'], $_POST['visitor_jobs'], $_POST['visitor_phones']);

        // 是否需要宾馆
        $talk->is_need_hotel = $_POST['reserve_hotel'];

        // 附件上传
        $attachment_ids = $this->add_attachments();
        $talk->attachment_ids = $attachment_ids;

        if($talk->save()){
            //return $this->redirect(array('list'));
            $this->redirect($this->createUrl("admin/talk/talk/listByCompany/id/".$_POST[company_id]));
        }else{
            $result = array(
                'code' => '1',
                'ext' => '添加失败'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * @param $name
     * @param $num
     * @param $comment
     * @param $major_names
     * @param $major_degrees
     * @param $major_nums
     * @return int|mixed|null
     *
     * 添加需求专业
     *
     */
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

    /**
     * @param $need_type
     * @param $num_require
     * @param $start_time
     * @param $end_time
     * @param $place_size
     * @return int|mixed|null
     *
     * 添加笔试、面试场地
     *
     */
    function add_place($need_type,$num_require,$start_time,$end_time,$place_size){
        $place = new CareerNeedPlace();
        $place->need_type = $need_type;
        $place->num_require = $num_require;
        $place->start_time = $start_time;
        $place->end_time = $end_time;
        $place->place_size = $place_size;
        $place->created_time = date('Y-m-d H:i:s');
        $place->last_modified_time = date('Y-m-d H:i:s');
        if($place->save()){
            return $place->id;
        }else{
            return -1;
        }
    }

    /**
     * @param $visitor_name
     * @param $visitor_job
     * @param $visitor_phone
     * @return int|mixed|null
     *
     * 添加联系人、领队人
     *
     */
    function add_visitor($visitor_name, $visitor_job, $visitor_phone){
        $visitor = new CareerVisitor();
        $visitor->name = $visitor_name;
        $visitor->duty = $visitor_job;
        $visitor->phone = $visitor_phone;
        if ($visitor->save()){
            return $visitor->id;
        }else{
            return -1;
        }
    }

    /**
     * @param $visitor_names
     * @param $visitor_jobs
     * @param $visitor_phones
     * @return int|string
     *
     * 添加来访人
     *
     */
    function add_visitors($visitor_names, $visitor_jobs, $visitor_phones){

        $sum = count($visitor_names);
        $ids = array();
        for($i=0; $i<$sum; $i++){
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

    /**
     * @return int|string
     *
     * 添加附件
     */
    function add_attachments(){
        $sum = count($_FILES['talk_file']['name']);
        if($sum==0){
            echo '请选择文件后重试';
            return -1;
        }
        //存所有添加的附件id
        $ids=array();
        for($i=0;$i<$sum;$i++){
            if (isset($_FILES['talk_file']) && is_uploaded_file($_FILES['talk_file']['tmp_name'][$i])){
                $talk_file = $_FILES['talk_file'];
                $upErr = $talk_file['error'][$i];
                if ($upErr == 0) {
                    $logoType = $talk_file['type'][$i]; //文件类型。
                    $arr = explode(".", $_FILES['talk_file']['name'][$i]);  //截取文件名跟后缀
                    $prename = $arr[0];
                    $file_name = date('YmdHis') . mt_rand(100, 999) . "." . $arr[1];  // 文件的重命名 （日期+随机数+后缀）
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

    /**
     * @param $id
     *
     * 根据id删除宣讲会信息
     */
    public function actionDel($id){
        $talk = CareerTalk::model()->findByPk($id);
        if(!empty($talk)){
            $talk->delete();
        }
        //删除该宣讲会对应得所有人员
        CareerTalkUser::model()->deleteAllByAttributes(array('career_talk_id'=>$id));
        $this->redirect($this->createUrl("admin/talk/talk/list"));
    }

    /**
     * 去宣讲会列表页
     */
    public function actionList(){
        $criteria = new CDbCriteria();
        //查询所有宣讲会得数量
        $recordCount = count(CareerTalk::model()->findAll($criteria));
        //获取所有行业的信息，从行业表中
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign("trade",$trade);
        //$this->smarty->assign('list',$list_all);
        $this->smarty->assign('current','talk');
        $this->smarty->display('admin/talk/listPageNew.html');
    }

    /**
     * 分页刷列表页
     *
     */
    public function actionListJson(){
        $page = $_GET['page1'];

        //查询所有数据
        $listAll = CareerTalk::model()->findAll();
        $recordCount = count($listAll);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);

        $criteria = new CDbCriteria();
        //根据id的降序排序
        $criteria->order = 'id desc';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage-1)*$pageSize;
        $list = CareerTalk::model()->findAll($criteria);
        $aList = '{"list":'.CJSON::encode($list).',"dataCount":'.$recordCount.'}';
        print $aList;
    }

    /**
     * 去宣讲会查询页
     *
     */
    public function actionSearch(){
        $keyword = $_POST['keyword'];
        $time = $_POST['time'];
        $type = $_POST['type'];
        $tradeId = $_POST['tradeId'];

        //echo $tradeId;

        //获取根据搜索条件搜索出来的结果的数量
        $dataCount = count(CTService::getInstance()->search($keyword,$time,$tradeId,$type,0));

        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('time',$time);
        $this->smarty->assign('type',$type);
        $this->smarty->assign('tradeId',$tradeId);
        $this->smarty->assign('recordCount',$dataCount);
        //获取行业信息
        $trade = CompanyTrade::model()->findAll();
        $this->smarty->assign("trade",$trade);
        $this->smarty->assign('current','talk');
        $this->smarty->display('admin/talk/search.html');
    }

    /**
     * 分页刷查询结果页
     *
     */
    public function actionSearchJson(){
        $page = $_GET['page1'];
        $keyword = $_GET['keyword'];
        $time = $_GET['time'];
        $type = $_GET['type'];
        $tradeId = $_GET['tradeId'];

        //获取数量
        $dataCount = count(CTService::getInstance()->search($keyword,$time,$tradeId,$type,0));

        //获取列表
        $list = CTService::getInstance()->search($keyword,$time,$tradeId,$type,$page);
        $recordCount = count($list);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);

        $criteria = new CDbCriteria();
        //根据id的降序排序
        $criteria->order = 'id desc';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage-1)*$pageSize;
        $alist = CareerTalk::model()->findAll($criteria);

        $searchJson = '{"list":'.CJSON::encode($alist).',"dataCount":'.$recordCount.'}';
        print $searchJson;
    }

    /**
     * 去修改页
     *
     */
    public function actionToEdit(){

        //获取宣讲会id
        $id = Yii::app()->request->getParam('id');
        //echo $id;

        //根据宣讲会id查询数据
        $talk = CareerTalk::model()->findByPk($id);

        // 可以选择的联系人和领队人
        $company_id = $_POST['company_id'];
        $companyUserModel = CompanyLoginUser::model();
        $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE company_id = '$company_id'";
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

        // 联系人、领队人、来访人信息
        $leader = CareerVisitor::model()->findByPk($talk->leader_user_id);
        $contact = CareerVisitor::model()->findByPk($talk->contact_user_id);
        $visitors = array();
        $visitor_ids = explode(',',$talk->visitor_info_ids);
        foreach($visitor_ids as $v_id){
            $visitor = CareerVisitor::model()->findByPk($v_id);
            $visitor = json_decode(CJSON::encode($visitor),TRUE);
            $visitors[] = $visitor;
        }

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
        $this->smarty->display('admin/talk/edit.html');
    }

    /**
     * 修改
     *
     */
    public function actionEdit(){
        //判空
        if(!(// 宣讲会id
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

        //根据id查询
        $talk = CareerTalk::model()->findByPk($_POST['talk_id']);

        // 基本信息
        $talk->name=$_POST['name'];
        $arr = explode('-',$_POST['time']);
        $talk->time=$_POST['date'].' '.$arr[0].':00';
        $talk->date = $_POST['date'];
        $talk->period = $_POST['time'];
        $talk->place=$_POST['place'];
        $talk->type=2;
        $talk->company_id=$_POST['company_id'];
        $talk->description=$_POST['description'];
        $talk->last_update=$_SESSION['user_name'];
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

        // 笔试、面试场地
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

        // 添加联系人、领队人
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
            //$this->redirect($this->createUrl("admin/talk/talk/listByNew/list);
            return $this->redirect(array('list'));
        }else{
            $result = array(
                'code' => '1',
                'ext' => '修改失败'
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $num
     * @param $comment
     * @param $major_names
     * @param $major_degrees
     * @param $major_nums
     * @return int|mixed|null
     *
     * 修改专业信息
     *
     */
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

    /**
     * @param $id
     * @param $need_type
     * @param $num_require
     * @param $start_time
     * @param $end_time
     * @param $place_size
     * @return int|mixed|null
     *
     * 修改面试地点和笔试地点
     *
     */
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

    /**
     * 详情页
     *
     */
    public function actionDetail(){
        $id=Yii::app()->request->getParam('id');
        //echo $id;
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

        // 领队人、来访人信息
        $leader = CareerVisitor::model()->findByPk($talk->leader_user_id);
        $contact = CareerVisitor::model()->findByPk($talk->contact_user_id);
        $visitors = array();
        $visitor_ids = explode(',',$talk->visitor_info_ids);
        foreach($visitor_ids as $v_id){
            $visitor = CareerVisitor::model()->findByPk($v_id);
            $visitor = json_decode(CJSON::encode($visitor),TRUE);
            $visitors[] = $visitor;
        }

        //echo $visitors;
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
        $this->smarty->display('admin/talk/detail.html');

    }

}

?>