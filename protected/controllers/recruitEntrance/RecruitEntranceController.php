<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-8-2
 * Time: 上午12:56
 *
 * UPDate: 17-6-15
 * Time: 18点42分
 * User: 王超
 */


Yii::import('ext.muzhi.MuZhiSdk',true);
 class RecruitEntranceController extends Controller{
     public  $code = '';

     /*
      * 登录跳页
      */
     public function actionToLogin()
     {
         $this->smarty->display('recruitEntrance/user/login.html');
     }
     /*
     * 用人单位登录 手机+密码
     */
     public function actionLogin()
     {
         $user = trim($_POST['username']);
         $pass = md5(trim($_POST['password']));

         $sql = "select * from {{company_login_user}} where phone='$user' AND password='$pass'";
         $model = CompanyLoginUser::model()->findBySql($sql);
         //判断该用户是否有对应的公司存在
         $companyExist = Company::model()->findByPk($model->company_id);
         //print $model->name;
         //$model = Recruitment::model()->findByAttributes(array('username'=>$user,'password'=>$pass));   //查询数据库
         //$model = Recruitment::model()->findByAttributes(array('phone' => $user, 'password' => $pass));
         if ($model&&$companyExist->id!=null)   //如何有符合的用人单位联系人登录账号，进入宣讲会首页，否则用户名与密码错误
         {
//            if ($model->status == 1)   //判断用户是否激活 审核通过  暂时不加
//            {
             Yii::app()->session['company_id'] = $model->company_id;//公司ID
//             echo "company_id".Yii::app()->session['company_id'];
             Yii::app()->session['com_phone'] = $model->phone;//当前用户手机号
             Yii::app()->session['companyUserName'] = $model->name;//当前用户名字
             Yii::app()->session['contact_id'] = $model->id;//当前用户ID
             $msg ="信息发布联系人";
             if ($model->type_id == $msg ){
                 $model->admin_id = $model->id;
                 $model->save();
             }

             //转到用人单位主页
             $this->redirect($this->createUrl("recruitEntrance/RecruitIndex/index"));
         }
         else {
             if($model->id!=null)$model->delete();
             $msg = "用户名与密码错误";
             $this->smarty->assign("msg",$msg);
             $this->smarty->display('recruitEntrance/user/login.html');
         }
     }
     public function actionStatus($data)
     {
         $sql = "select status from {{company}} where company_id='" . $data . "'";
         $result = CompanyLoginUser::model()->findBySql($sql);
         if (!$result) {
             return false;
         } else {
             return true;
         }
     }

     public function actionLogout()                //退出登陆
     {
         unset(Yii::app()->session['company_id']);
         unset(Yii::app()->session['com_phone']);
         unset(Yii::app()->session['company_name']);
         unset(Yii::app()->session['focus_num']);
         unset(Yii::app()->session['contact_id']);
         unset(Yii::app()->session['companyUserName']);
         $this->redirect(Yii::app()->webConstants->getSite());    //转到主页index.html
     }

     public function actionToRegister(){
         $this->smarty->display('recruitEntrance/uploadInf.html');
     }

     public function actionRegister(){
         $company = new Company();
         $array = UploadPicService::getInstance()->uploadTwo();
         $company->daima = $array[0];
         $company->zhizhao = $array[1];
         $company->status = 0;
         $company->save();
         $insertId = $company->attributes['id'];
         $this->redirect($this->createUrl('recruitEntrance/recruitEntrance/createCompany/id/'.$insertId));
     }


     /*
      * 未入住企业注册页面视图
      */
     public function actionCreateCompany(){
         $propertyList = CompanyProperty::model()->findAll();
         $tradeList = CompanyTrade::model()->findAll();
         $city = City::model()->findAll();
         $provinceList = Province::model()->findAll();
         $economicList = CompanyEconomicType::model()->findAll();
         $companySizeList = CompanyUnitSize::model()->findAll();
         //$this->smarty->assign('id',$id);
         $this->smarty->assign('provinceList',$provinceList);
         $this->smarty->assign('propertyList',$propertyList);
         $this->smarty->assign('tradeList',$tradeList);
         $this->smarty->assign('cityList',$city);
         $this->smarty->assign('economicType',$economicList);
         $this->smarty->assign('companySizeList',$companySizeList);
         $this->smarty->display('recruitEntrance/user/unregistered-company.html');
     }

     /*
      * 未入住企业信息保存方法
      */
     public function actionAddCompany(){
        $company = new Company();
        $company->name = $_POST['companyName'];
        $company->trade_id = $_POST['trade_id'];//所属行业id
        $company->property_id = $_POST['property_id'];//单位性质id
        $company->phone = $_POST['companyPhone'];
        $company->email = $_POST['recruitEmail'];
        $company->postal_code = $_POST['postalCode'];
        $company->type_id = $_POST['typeId'];//单位类型id   1教育行业2非教育行业
        $company->unit_size = $_POST['scale'];//单位规模
        $company->full_address = $_POST['fullAddress'];
        $company->introduction = $_POST['introduction'];
        $company->economic_type_id =$_POST['economicType'];
        $company->entering_time = date("Y-m-d H:i:s",time());
        $company->is_school_company = isset($_POST['is_school_company'])?$_POST['is_school_company']:0;
        $company->organization_code = $_POST['organization_code'];
        $company->is_front_input = 1;
        $company->city_id = $_POST['city_id'];
        $company->is_front_input = 1;
        $company->is_ok = 0;
        $company->save();
        $companyId= $company->id;
//        echo json_encode($companyId,true);

        $companyCity = new CompanyCity();
        $companyCity->company_id = $company->attributes['id'];
        $companyCity->city_id = $_POST['city_id'];
        $companyCity->save();
        //图片上传  公司执照 保存到{{company_zhizhao}}
         $condition = "1=1 and (company_id =  :company_id)";
         $params = array(':company_id' => $companyId);
//       $params = array(':company_id' => $companyModel->id);
         CompanyZhiZhao::model()->deleteAll($condition, $params);
         if (!empty($_POST)) {
             $zhizhao = $_FILES['zhizhaoUrl'];
             $zhizhaoType = $zhizhao['type']; //文件类型。
             /* 判断文件类型，这个例子里仅支持jpg和gif类型的图片文件。*/
             $tp = array("image/gif", "image/pjpeg", "image/jpeg", "image/png");
             foreach ($_FILES["zhizhaoUrl"]["error"] as $key => $error) {
                 if (!in_array( $zhizhaoType[$key], $tp)) {
                     unset($zhizhao[$key]);
                 }
                 if ($error == UPLOAD_ERR_OK) {
                     $tmp_name = $_FILES["zhizhaoUrl"]["tmp_name"][$key];
                     $a = explode(".", $_FILES["zhizhaoUrl"]["name"][$key]);  //截取文件名跟后缀
                     $prename = $a[0];
                     $imgFileName = date('YmdHis') . mt_rand(100, 999) . "." . $a[1];  // 文件的重命名 （日期+随机数+后缀）
                     /* 将文件从临时文件夹移到上传文件夹中。*/
                     if (move_uploaded_file($tmp_name, 'assets/uploadFile/recruitEntrance/company-zhizhao/'.$imgFileName)) {
                         $companyZhizhao = new CompanyZhiZhao();
                         $companyZhizhao->url = $imgFileName;
                         $companyZhizhao->company_id = $companyId;
                         $companyZhizhao->save();
                     }
                     else {
                         $this->redirect(array('getCompanyAuthenticationData'));
                     }
                 }
             }
         }
        //联系人保存
         $contact = new CompanyLoginUser();
         $contact->phone = $_POST['phone'];
         $contact->name = $_POST['contactName'];
         $contact->password = md5($_POST['password']);
         $contact->sex_id = $_POST['sex'];
         $contact->company_id = $company->attributes['id'];
         $contact->email = $_POST['email'];
         $contact->type_id = "信息发布联系人";
         $contact->is_schoolfellow = isset($_POST['is_schoolfellow'])?$_POST['is_schoolfellow']:0;
         $contact->save();

         //校友保存
         if($contact->is_schoolfellow==1)
         {
             $schoolFellow = new SchoolFellow();
             $schoolFellow->highest_education_id = $_POST['highestEducation'];
             $schoolFellow->graduation_year  = $_POST['graduationYear'];
             $schoolFellow->college_id = $_POST['apartment'];
             $schoolFellow->mailing_address  = $_POST['mailingAddress'];
             $schoolFellow->stu_id = $_POST['studentId'];
             $schoolFellow->major_id = $_POST['major'];
             $schoolFellow->user_id = $contact->id;
             $schoolFellow->save();
         }


         //跳转到首页
        $this->redirect($this->createUrl('recruitEntrance/recruitEntrance/ToLogin'));
     }

     /*
      * 已入住企业注册视图
      */
     public function actionFillCompany()
     {
         $propertyList = CompanyProperty::model()->findAll();
         $tradeList = CompanyTrade::model()->findAll();
         $city = City::model()->findAll();
         $provinceList = Province::model()->findAll();
         $economicList = CompanyEconomicType::model()->findAll();
         //获得所有单位规模
         $companySizeList = CompanyUnitSize::model()->findAll();
         $this->smarty->assign('companySizeList',$companySizeList);
//         echo json_encode($companySizeList,true);
         $this->smarty->assign('provinceList',$provinceList);
         $this->smarty->assign('propertyList',$propertyList);
         $this->smarty->assign('tradeList',$tradeList);
         $this->smarty->assign('cityList',$city);
         $this->smarty->assign('economicType',$economicList);
         $this->smarty->display('recruitEntrance/user/register-company.html');
     }

     /*
      * 代码查询并注册
      */
     public function actionRegisteredCompany()
     {
         $companyDaima = $_GET['barCode'];
         Yii::app()->session['barCode'] = $companyDaima;
         $sql = "select * from {{company}} where bar_code ='$companyDaima'";
         $company = CompanyLoginUser::model()->findBySql($sql);
         $companyId = $company->id;
         $companyName = $company->name;
         if(isset($_GET['barCode']))$flag=true;
         else $flag=false;

         $propertyList = CompanyProperty::model()->findAll();
         $tradeList = CompanyTrade::model()->findAll();
         $city = City::model()->findAll();
         $provinceList = Province::model()->findAll();
         $economicList = CompanyEconomicType::model()->findAll();
         $companyUnitSize = CompanyUnitSize::model()->findAll();

         $this->smarty->assign('flag',$flag);
         $this->smarty->assign('companyId',$companyId);
         $this->smarty->assign('companyName',$companyName);
         $this->smarty->assign('companyUnitSize',$companyUnitSize);
         $this->smarty->assign('provinceList',$provinceList);
         $this->smarty->assign('propertyList',$propertyList);
         $this->smarty->assign('tradeList',$tradeList);
         $this->smarty->assign('cityList',$city);
         $this->smarty->assign('economicType',$economicList);
         $this->smarty->display('recruitEntrance/user/register-company.html');
     }

     /*
      * 已入住企业注册保存方法
      */
     public function actionSaveCompany()
     {
         $companyId = Yii::app()->session['company_id'] ;
//         $company = Company::model()->findByPk($companyId);
//         echo json_encode($companyId,true);
        if (empty($companyId))
        {
            //跳转到首页
            $this->redirect($this->createUrl('site/index'));
        }else{
            //联系人保存
            $contact = new CompanyLoginUser();
            $contact->phone = $_POST['phone'];
            $contact->name = $_POST['contactName'];
            $contact->password = md5($_POST['password']);
            $contact->sex_id = $_POST['sex'];
            $contact->company_id = $companyId;
            $contact->email = $_POST['email'];
            $contact->type_id = "信息发布联系人";
            $contact->is_schoolfellow = $_POST['is_schoolfellow'];
            $contact->save();

            //校友保存
            if($contact->is_schoolfellow==1)
            {
                $schoolFellow = new SchoolFellow();
                $schoolFellow->highest_education_id = $_POST['highestEducation'];
                $schoolFellow->graduation_year  = $_POST['graduationYear'];
                $schoolFellow->college_id = $_POST['apartment'];
                $schoolFellow->mailing_address  = $_POST['mailingAddress'];
                $schoolFellow->stu_id = $_POST['studentId'];
                $schoolFellow->major_id = $_POST['major'];
                $schoolFellow->user_id = $contact->id;
                $schoolFellow->save();
            }

            //图片上传
            $condition = "1=1 and (company_id =  :company_id)";
            $params = array(':company_id' => $companyId);
//        $params = array(':company_id' => $companyModel->id);
            CompanyZhiZhao::model()->deleteAll($condition, $params);
            if (!empty($_POST)) {
                $zhizhao = $_FILES['zhizhaoUrl'];
                $zhizhaoType = $zhizhao['type']; //文件类型。
                /* 判断文件类型，这个例子里仅支持jpg和gif类型的图片文件。*/
                $tp = array("image/gif", "image/pjpeg", "image/jpeg", "image/png");
                foreach ($_FILES["zhizhaoUrl"]["error"] as $key => $error) {
                    if (!in_array( $zhizhaoType[$key], $tp)) {
                        unset($zhizhao[$key]);
                    }
                    if ($error == UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES["zhizhaoUrl"]["tmp_name"][$key];
                        $a = explode(".", $_FILES["zhizhaoUrl"]["name"][$key]);  //截取文件名跟后缀
                        $prename = $a[0];
                        $imgFileName = date('YmdHis') . mt_rand(100, 999) . "." . $a[1];  // 文件的重命名 （日期+随机数+后缀）
                        /* 将文件从临时文件夹移到上传文件夹中。*/
                        if (move_uploaded_file($tmp_name, 'assets/uploadFile/recruitEntrance/'.$imgFileName)) {
                            $companyZhizhao = new CompanyZhiZhao();
                            $companyZhizhao->url = $imgFileName;
                            $companyZhizhao->company_id = $companyId;
                            $companyZhizhao->save();
                        }
                        else {
                            $this->redirect(array('getCompanyAuthenticationData'));
                        }
                    }
                }
            }
            //跳转到首页
            $this->redirect($this->createUrl('recruitEntrance/recruitEntrance/ToLogin'));
        }

     }
     public function actionSendCode(){

         $tel = $_GET['tel'];
         //生成验证码
         for($i=0;$i<4;$i++){
             $this->code .= rand(0,9);
         }
         $content = '你的验证码是 '.$this->code;
         send_sms($content,$tel);
         //传给前端，由前端进行验证码的验证
         echo "right|".$this->code;

     }


     public function actionCreateCareerTalk($id){
         $this->smarty->assign('id',$id);
         $this->smarty->display('recruitEntrance/upload.html');
     }


     public function actionAddCareerTalk(){
         $companyId = $_POST['companyId'];
         $careerTalk = new CareerTalk();
         $careerTalk->name  = $_POST['name'];
         $careerTalk->time  = $_POST['time'];
         $careerTalk->place  = $_POST['place'];
         $careerTalk->type  = $_POST['type'];
         $careerTalk->is_live  = $_POST['live'];
         $careerTalk->company_id = $companyId;
         $careerTalk->is_front_input = 1;
         $careerTalk->save();
         $this->redirect($this->createUrl('recruitEntrance/recruitEntrance/createPosition/id/'.$companyId));
     }


     public function actionCreatePosition($id){
         $specialtyList = PositionSpecialty::model()->findAll();
         $degreeList = Degree::model()->findAll();
         $typeList = PositionType::model()->findAll();
         //$this->smarty->assign('id',$id);
         $this->smarty->assign('specialtyList',$specialtyList);
         $this->smarty->assign('degreeList',$degreeList);
         $this->smarty->assign('typeList',$typeList);
         $this->smarty->display('recruitEntrance/employ.html');
     }


     public function actionAddPosition(){
//         $companyId = $_POST['companyId'];
         $position = new Position();
         $position->name  = $_POST['name'];
         $position->city_id  = $_POST['city_id'];
         $position->specialty_id  = $_POST['specialty'];
         $position->degree_id  = $_POST['degree'];
         $position->type_id = $_POST['type'];
         //$position->company_id = $companyId;
         $position->is_front_input = 1;
         $position->save();
         $positionContacts = new PositionContacts();
         $positionContacts->position_id = $position->attributes['id'];
         $positionContacts->name = $_POST['contactName'];
         $positionContacts->cellphone = $_POST['cellphone'];
         $positionContacts->post = $_POST['post'];
         $positionContacts->telephone = $_POST['telephone'];
         $positionContacts->email = $_POST['contactEmail'];
         $positionContacts->save();
         $this->redirect(array('site/index'));
     }

     /**
      * 判断用户名是否重复
      */
     public function actionPhoneJson()
     {
         $phone = $_GET['phone'];
         $companyLoginUserModel = CompanyLoginUser::model();
         $sql = "SELECT * FROM {{company_login_user}} WHERE phone = ".$phone;
         $companyLoginUserInfo = $companyLoginUserModel->findBySql($sql);
         if (empty($companyLoginUserInfo))
         {
             $UserJson='{"code":1}';
         }else{
             $UserJson='{"code":0}';
         }
         print  $UserJson;
     }

     /**
      * 判断用户名是否重复18位统一社会信用代码或组织机构代码
      */
     public function actionCompanyDaimaJson()
     {
        $barCode = $_GET['barCode'];
        $companyModel = Company::model();
        $sql = "SELECT * FROM {{company}} WHERE organization_code = ".$barCode;
        $companyInfo = $companyModel->findBySql($sql);
        if (empty($companyInfo))
        {
            $DaimaJson = '{"code":1}';
        }else{
            $DaimaJson = '{"code":0}';
        }
        print $DaimaJson;
     }

     /**
      * 根据公司统一社会信用代码查找公司名字
      */
     public function actionQueryNameByDaima()
     {
         $barCode = $_GET['barCode'];
         if (empty($barCode)){
             $companyNameJson = '{"code":0}';
         }else{
             $companyModel = Company::model();
             $sql = "SELECT id,name FROM {{company}} WHERE organization_code = '$barCode' OR name='$barCode'";
             $companyInfo = $companyModel->findBySql($sql);
             Yii::app()->session['company_id'] = $companyInfo->id;//公司ID
             if (empty($companyInfo)){
                 $companyNameJson = '{"code":0}';
             }else{
                 $companyNameJson = '{"code":1,"name":'.CJSON::encode($companyInfo->name).'}';
             }
         }
         print $companyNameJson;
     }
 }