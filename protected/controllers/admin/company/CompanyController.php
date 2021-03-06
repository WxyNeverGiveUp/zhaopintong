<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/8
 * Time: 10:23
 */

class CompanyController extends Controller
{
	//只是为了跳转页面 以后删除  李琪
public function actionLiQi(){
	
	 $this->smarty->display('admin/company/lianxiren.html');
	
}

    //单位管理下的单位列表
    public function actionList()
    {
        $companyList = Company::model()->findAll();
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList = CompanyProperty::model()->findAll();
        $cityList = City::model()->findAll();
        $recordCount = count($companyList);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList', $tradeList);
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('current', 'company');
        $this->smarty->display('admin/company/company-info.html');
    }

    public function actionJson()
    {
        $page = $_GET['page'];
        $criteria = new CDbCriteria();
        //$criteria -> condition = ('is_discarded=0');
        $criteria->condition="is_front_input=0 OR is_ok=1";
        $criteria->order = 'entering_time DESC';
        $list_all = Company::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', $page);
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage - 1) * $pageSize;
        $recordCount = count($list_all);
        $list = Company::model()->findAll($criteria);  //记录分页
        $list2 = '{"list":' . CJSON::encode($list) . ',"dataCount":"' . $recordCount . '"}';
        print $list2;
//        var_dump($page);
    }


    //单位通过审核的详情查看
    public function actionDetail($id)
    {
        $company = Company::model()->findByPk($id);
        //TODO:没有连接性质表、行业表、地区表、招聘动态、师兄师姐

        //城市
        $city_id = CompanyCity::model()->findByAttributes(array('company_id' => $id))->city_id;
        $this->smarty->assign('city',City::model()->findByPk($city_id)->name);

        //单位类型  id==1 是教育行业，2是非教育行业，数据库有空字段 在前端判断
        $type_id = $company->type_id;
        $this->smarty->assign('type',$type_id);

        //经济类型
        $economic_type_id = $company->economic_type_id;
        $this->smarty->assign('economic_type',EconomicType::model()->findByPk($economic_type_id)->economic_type);

        //单位规模
        $unit_size = $company->unit_size;
        $this->smarty->assign('unit_size',CompanyUnitSize::model()->findByPk($unit_size)->size);

        //单位行业
        $trade_id = $company->trade_id;
        $this->smarty->assign('trade',CompanyTrade::model()->findByPk($trade_id)->name);

        //视频
        $videoUrl = $company->video_url;
        $this->smarty->assign('videoUrl',$videoUrl);

        //logo
        $this->smarty->assign('logoUrl',$company->logo);

        //执照
        //执照 查找认证资料多图显示
        $condition = "1=1 and company_id = ".$id;
        $params = array(':company_id' => $id);
        $zhizhaoUrl = CompanyZhiZhao::model()->findAll($condition, $params);
        $zhizhaoUrl = json_decode(CJSON::encode($zhizhaoUrl),TRUE);
        $this->smarty->assign('zhizhaoUrl',$zhizhaoUrl);

        //单位性质
        $property_id = $company->property_id;
        $property = CompanyProperty::model()->findByPk($property_id)->name;
        $this->smarty->assign('property',$property);

        //传真
        $fax = $company->fax;
        $this->smarty->assign('fax',$fax);

        //联系人
        $companyContact = CompanyLoginUser::model()->findByAttributes(array('company_id'=>$id));
        $companyContactName = $companyContact->name;
        $companyContactTel = $companyContact->phone;
        $companyContactDuty = $companyContact->duty;
        $this->smarty->assign('companyContactName',$companyContactName);
        $this->smarty->assign('companyContactDuty',$companyContactDuty);
        $this->smarty->assign('companyContactTel',$companyContactTel);

//        $userId = 1;
//        $companyUser = CompanyUser::model()->find(array(
//            'condition' => 'company_id=:companyId AND user_id=:userId',
//            'params' => array(':companyId' => $id, ':userId' => $userId),
//        ));
//        $concerned = $companyUser ? 1 : 0;
//        $this->smarty->assign('concerned', $concerned);

        $this->smarty->assign('company', $company);
        $this->smarty->assign('current', 'company');
        $this->smarty->display('admin/company/detail.html');
    }

    public function actionCreate()
    {
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList = CompanyProperty::model()->findAll();
        //$cityList  = City::model()->findAll();
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList', $tradeList);
        $this->smarty->assign('provinceList', $provinceList);
        $this->smarty->assign('current', 'company');
        $this->smarty->display('admin/company/add.html');
    }

    public function actionAdd()
    {   
       
        // 判断单位的唯一性
        $companyName = $_POST['name'];
        $companyName = trim($companyName);
        $checkResult = Company::model()->countByAttributes(array('name'=>$companyName));
        if ($checkResult) {
            echo "<script>
                     alert('该单位已存在');
                     window.history.go(-1);
                   
                  </script>";
            die();
         } 

         // 创建事务
        $flag=flase;
        $tr = Yii::app()->db->beginTransaction();
        try {
            $company = new Company();
            $company->name = trim($_POST['name']);
            if ($_POST['name'] != null && $_POST['name'] != '') {
                //TODO:单位规模 经济类型
                $company->trade_id = $_POST['tradeId'];
                $company->company_bright = $_POST['bright'];
                $company->full_address = $_POST['fullAddress'];
                $company->phone = $_POST['phone'];
                $company->type_id = $_POST['typeId'];
                $company->property_id = $_POST['propertyId'];
                $company->postal_code = $_POST['postalCode'];
                $company->email = $_POST['email'];
                $company->website = $_POST['website'];
                $company->organization_code = $_POST['organization_code'];
                $company->is_famous = $_POST['isFamous'];
                $company->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
                $company->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
                $company->introduction = $_POST['introduction'];
                $company->concern_num = 0;
                $company->entering_time = date("Y-m-d H:i:s", time());
                $company->last_update = Yii::app()->session['user_name'];
                $company_rs = $company->save();

                //logo上传
                if (isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name']))
                {
                    $logo = $_FILES['logo'];
                    $upErr = $logo['error'];
                    if ($upErr == 0)
                    {
                        $logoType = $logo['type']; //文件类型。
                        $a=explode(".",$_FILES["logo"]["name"]);  //截取文件名跟后缀
                        //$prename = substr($a[0],10);   //如果你到底的图片名称不是你所要的你可以用截取字符得到
                        $prename = $a[0];
                        $imgFileName = date('YmdHis').mt_rand(100,999).".".$a[1];  // 文件的重命名 （日期+随机数+后缀）
//                    echo json_encode($a[1],true);
                        /* 判断文件类型，这个例子里仅支持jpg和gif类型的图片文件。*/
                        if ($logoType == 'image/jpeg' || $logoType == 'image/gif'||$logoType == 'image/jpg'||$logoType == 'image/png'||$logoType == 'image/PNG'||$logoType == 'image/JPG')
                        {
//                        $imgFileName = strtotime("now");
                            $imgTmpFile = $logo['tmp_name'];
                            /* 将文件从临时文件夹移到上传文件夹中。*/
                            move_uploaded_file($imgTmpFile, 'assets/uploadFile/recruitEntrance/company-logo/'.$imgFileName);

                        }
                    }
                    $company->logo = $imgFileName;
                } else{
                    $company->logo = $company->logo;
                }
                $id = $company->id;
                $company->save();

                //公司执照认证资料上传
                if (!empty($_FILES['zhizhaoUrl'])) {
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
                                $companyZhizhao->company_id = $company->id;
                                $companyZhizhao->save();
                            }
                            else {
//                                $this->redirect($this->createUrl("admin/company/company/toEdit/id/$id"));
                            }
                        }
                    }
                }

                $companyModel = Company::model()->findByPk($company->attributes['id']);
                //$companyModel->opposite_id = 666;
                $companyModel->save();
                
                // 插入城市信息
                $companyCity_rs = 1;
                $cityList = $_POST['cityId'];
                foreach ($cityList as $key => $value) {
                    $companyCity = new CompanyCity();
                    $companyCity->city_id = $value;
                    $companyCity->company_id = $company->attributes['id'];
                    $companyCity_rs = $companyCity->save();
                }
            }
            if (!$company_rs||!$companyCity_rs) {//||$code
                throw new Exception("Error Processing Request");
                
            }

            $tr->commit();
            $flag=true;
        } catch (Exception $e) {
            $tr->rollback();
        }

        if($flag==true)//插入成功则同步信息
        {
            $logoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-logo/';
            $zhizhaoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-zhizhao/';
            $videoUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-video/';

            //同步单位信息到OA
            $url="http://localhost:8080/company/sync/add";
            //验证方式选择token
            $data = array(
                'token'=>$this->sendtoken(),
                'data'=>Array(
                    'bright'=>Array(
                        $company->company_bright
                    ),
                    'companyInfo'=>Array
                    (
                        'enteringTime'=>$company->entering_time,
                        'tradeId'=>$company->trade_id,
                        'cityId'=>$company->city_id,
                        'fullAddress'=>$company->full_address,
                        'phone'=>$company->phone,
                        'typeId'=>$company->type_id,
                        'propertyId'=>$company->property_id,
                        'postalCode'=>$company->postal_code,
                        'email'=>$company->email,
                        'isJoinBigRecruitment'=>$company->is_join_big_recruitment,
                        'isJoinRecruitmentWeek'=>$company->is_join_recruitment_week,
                        'oppositeId'=>$company->id,
                        'companyName'=>$company->name,
                        'website'=>$company->website,
                        'isEducationIndustry'=>$company->type_id,
                        'introduction'=>$company->introduction,
                        'isFamous'=>$company->is_famous,
                        'videoUrl'=>$videoUrl.$company->video_url,//暂时没实现管理员上传视频功能
                        'logo'=>$logoFileUrl.$company->logo,
                        'organizationCode'=>$company->organization_code,
                        'status'=>$company->is_ok,
                        'lastUpdate'=>$company->last_update,
                        'isIndexShow'=>$company->is_index_show,
                        'isFrontInput'=>$company->is_front_input,
                        'daima'=>$company->organization_code,//重复了
                        'zhizhao'=>$zhizhaoFileUrl.$companyZhizhao->url, //认证资料图片一张
                    ),

                )

            );
            // 注释部分可以额外设置请求头，此处默认即可需要时可自行修改
            $headerArray = array(
                'Accept:application/json, text/javascript, */*',
                'Content-Type:application/json;charset=UTF-8'
            );

            $json_data = CJSON::encode($data);

            $ch = curl_init();  // 初始化
            curl_setopt($ch, CURLOPT_URL, $url);  //同步接口地址
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
            curl_setopt($ch, CURLOPT_HEADER, 0);  //是否显示头信息
            curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//设置头信息


            $output = curl_exec($ch);//////
            curl_close($ch); // 关闭数据同步会话
            $res = CJSON::decode($output);
            if($res['code']==0)//同步成功
                $this->redirect($this->createUrl("admin/company/company/list"));
            else
                echo 'fail';//同步失败
        }
    }


    public function actionToEdit($id)
    {
        $company = Company::model()->findByPk($id);
        $companyCity = CompanyCity::model()->findByAttributes(array('company_id' => $id));
        $provinceId = City::model()->findByPk($companyCity->city_id)->province_id;
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList = CompanyProperty::model()->findAll();
        $cityList = City::model()->findAllByAttributes(array('province_id'=>$provinceId));
        $provinceList = Province::model()->findAll();

        $brightened = Brightened::model()->findAllByAttributes(array('related_id'=>$id,'type'=>1));
        $brightList = array();
        $brightList[1]=array('name'=>'绩效奖金多',check=>0);
        $brightList[2]=array('name'=>'年终多薪',check=>0);
        $brightList[3]=array('name'=>'带薪年假',check=>0);
        $brightList[4]=array('name'=>'五险一金',check=>0);
        $brightList[5]=array('name'=>'晋升空间大',check=>0);
        $brightList[6]=array('name'=>'弹性工作制',check=>0);
        $brightList[7]=array('name'=>'内部培训',check=>0);
        $brightList[8]=array('name'=>'办公环境优美气氛好',check=>0);
        $brightList[9]=array('name'=>'期权激励',check=>0);
        $brightList[10]=array('name'=>'解决户口',check=>0);
        $brightList[11]=array('name'=>'餐补',check=>0);
        $brightList[12]=array('name'=>'班车接送',check=>0);
        $brightList[13]=array('name'=>'包三餐',check=>0);
        foreach ($brightened as $key => $value) {
            $search = array_search(array('name'=>$value->name,check=>0), $brightList);
            if ($search) {
                $brightList[$search]['check'] = 1;
            }
        }
        
        $this->smarty->assign('brightList',$brightList);
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList', $tradeList);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('companyCity', $companyCity);
        $this->smarty->assign('provinceId', $provinceId);
        $this->smarty->assign('current', 'company');
        $this->smarty->assign('provinceList', $provinceList);
        $this->smarty->display('admin/company/edit.html');
    }

    public function  actionEdit($id)
    {
        $company = Company::model()->findByPk($id);

        $company->name = $_POST['name'];
        if ($_POST['name'] != null && $_POST['name'] != '') {
            $company->trade_id = $_POST['tradeId'];
            $company->full_address = $_POST['fullAddress'];
            $company->phone = $_POST['phone'];
            $company->type_id = $_POST['typeId'];
            $company->property_id = $_POST['propertyId'];
            $company->postal_code = $_POST['postalCode'];
            $company->email = $_POST['email'];
            $company->website = $_POST['website'];
            $company->organization_code = $_POST['organization_code'];
            $company->is_famous = $_POST['isFamous'];
            $company->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
            $company->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
            $company->introduction = $_POST['introduction'];
            $company->entering_time = date("Y-m-d H:i:s", time());
            $company->last_update = Yii::app()->session['user_name'];
            $company->company_bright = $_POST['bright'];
            $company->save();

            $CityModel = CompanyCity::model()->findByAttributes(array('company_id' => $id));
            if ($_POST['cityId'][0]) {
                $cityId = $_POST['cityId'][0];
                $CityModel->city_id = $_POST['cityId'][0];
                $CityModel->company_id = $company->attributes['id'];
                $CityModel->save();
            } else {
                $cityId = $CityModel->city_id;
            }


            //logo上传
            if (isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name']))
            {
                $logo = $_FILES['logo'];
                $upErr = $logo['error'];
//                    echo json_encode($upErr,true);
                if ($upErr == 0)
                {
                    $logoType = $logo['type']; //文件类型。
                    $a=explode(".",$_FILES["logo"]["name"]);  //截取文件名跟后缀
                    //$prename = substr($a[0],10);   //如果你到底的图片名称不是你所要的你可以用截取字符得到
                    $prename = $a[0];
                    $imgFileName = date('YmdHis').mt_rand(100,999).".".$a[1];  // 文件的重命名 （日期+随机数+后缀）
//                    echo json_encode($a[1],true);
                    /* 判断文件类型，这个例子里仅支持jpg和gif类型的图片文件。*/
                    if ($logoType == 'image/jpeg' || $logoType == 'image/gif'||$logoType == 'image/jpg'||$logoType == 'image/png'||$logoType == 'image/PNG'||$logoType == 'image/JPG')
                    {
//                        $imgFileName = strtotime("now");
                        $imgTmpFile = $logo['tmp_name'];
                        /* 将文件从临时文件夹移到上传文件夹中。*/
                        move_uploaded_file($imgTmpFile, 'assets/uploadFile/recruitEntrance/company-logo/'.$imgFileName);

                    }
                }
                $company->logo = $imgFileName;
            } else{
                $company->logo = $company->logo;
            }
            $company->save();


            //video上传
            if (isset($_FILES['video_url']) && is_uploaded_file($_FILES['video_url']['tmp_name']))
            {
                $video = $_FILES['video_url'];
                $upErr = $video['error'];
                if ($upErr == 0)
                {
                    $videoType = $video['type']; //文件类型。
                    $a=explode(".",$_FILES["video_url"]["name"]);  //截取文件名跟后缀
                    $prename = $a[0];
                    $imgFileName = date('YmdHis').mt_rand(100,999).".".$a[1];  // 文件的重命名 （日期+随机数+后缀）
//                    echo json_encode($a[1],true);
                    /* 判断文件类型，这个例子里仅支持jpg和gif类型的图片文件。*/
//                    if ($logoType == 'image/jpeg' || $logoType == 'image/gif'||$logoType == 'image/jpg'||$logoType == 'image/png'||$logoType == 'image/PNG'||$logoType == 'image/JPG')
//                    {
//                        $imgFileName = strtotime("now");
                    $imgTmpFile = $video['tmp_name'];
                    /* 将文件从临时文件夹移到上传文件夹中。*/
                    move_uploaded_file($imgTmpFile, 'assets/uploadFile/recruitEntrance/company-video/'.$imgFileName);

//                    }
                }    $company->video_url = $imgFileName;
            } else{
                $company->video_url = $company->video_url;
            }
            $company->save();

            //公司执照认证资料上传
            if (!empty($_FILES['zhizhaoUrl'])) {
                $condition = "1=1 and (company_id =  :company_id)";
                $params = array(':company_id' => $company->id);
                CompanyZhiZhao::model()->deleteAll($condition, $params);

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
                            $companyZhizhao->company_id = $id;
                            $companyZhizhao->save();
                        }
                        else {
                            $this->redirect($this->createUrl("admin/company/company/toEdit/id/$id"));
                        }
                    }
                }
            }

            //修改之后同步至OA
            if($company->save())//插入成功则同步信息
            {
                $logoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-logo/';
                $zhizhaoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-zhizhao/';
                $videoUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-video/';

                $companyZhizhao = CompanyZhiZhao::model()->findByAttributes(array('company_id'=>$id));
                //同步单位信息到OA update
                $url="http://localhost:8080/company/sync/update";
                //验证方式选择token
                $data = array(
                    'token'=>$this->sendtoken(),
                    'data'=>Array(
                        'bright'=>Array(
                            $company->company_bright
                        ),
                        'companyInfo'=>Array
                        (
                            'enteringTime'=>$company->entering_time,
                            'tradeId'=>$company->trade_id,
                            'cityId'=>$company->city_id,
                            'fullAddress'=>$company->full_address,
                            'phone'=>$company->phone,
                            'typeId'=>$company->type_id,
                            'propertyId'=>$company->property_id,
                            'postalCode'=>$company->postal_code,
                            'email'=>$company->email,
                            'isJoinBigRecruitment'=>$company->is_join_big_recruitment,
                            'isJoinRecruitmentWeek'=>$company->is_join_recruitment_week,
                            'oppositeId'=>$company->id,
                            'companyName'=>$company->name,
                            'website'=>$company->website,
                            'isEducationIndustry'=>$company->type_id,
                            'introduction'=>$company->introduction,
                            'isFamous'=>$company->is_famous,
                            'videoUrl'=>$videoUrl.$company->video_url,//暂时没实现管理员上传视频功能
                            'logo'=>$logoFileUrl.$company->logo,
                            'organizationCode'=>$company->organization_code,
                            'status'=>$company->is_ok,
                            'lastUpdate'=>$company->last_update,
                            'isIndexShow'=>$company->is_index_show,
                            'isFrontInput'=>$company->is_front_input,
                            'daima'=>$company->organization_code,//重复了
                            'zhizhao'=>$zhizhaoFileUrl.$companyZhizhao->url, //认证资料图片一张
                        ),

                    )

                );
                // 注释部分可以额外设置请求头，此处默认即可需要时可自行修改
                $headerArray = array(
                    'Accept:application/json, text/javascript, */*',
                    'Content-Type:application/json;charset=UTF-8'
                );

                $json_data = CJSON::encode($data);

                $ch = curl_init();  // 初始化
                curl_setopt($ch, CURLOPT_URL, $url);  //同步接口地址
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
                curl_setopt($ch, CURLOPT_HEADER, 0);  //是否显示头信息
                curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息
                curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//设置头信息


                $output = curl_exec($ch);//////
                curl_close($ch); // 关闭数据同步会话
                $res = CJSON::decode($output);
                if($res['code']==0)//同步成功
                    $this->redirect($this->createUrl("admin/company/company/list"));
                else
                    echo 'fail';//同步失败
            }
            $this->redirect($this->createUrl("admin/company/company/detail/id/$id"));
        }
    }
//    public function actionEdit($id)
//    {
//         // // 同步数据至就业办公平台
//         //        $post = $_POST;
//         //        if (!isset($post['bright'])) {
//         //            $post['bright'] = array();
//         //        }
//
//         //        $post['id'] = $company->opposite_id;
//
//         //        $post['oppositeId'] = $id;
//         //        $post['cityId'] = $cityId;
//         //        $post_data = CJSON::encode($post);
//         //        $url = 'http://www.dsjyw.net/api/company/sendUpdate';
//
//         //        $json = array('json'=>$post_data);
//
//         //        $json =  http_build_query($json);
//
//
//         //        $ch = curl_init();
//         //        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址
//         //        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
//         //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
//         //        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
//         //        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);//要提交的信息
//         //        $out = curl_exec($ch);
//         //        curl_close($ch);
//        $tr = Yii::app()->db->beginTransaction();
//        try {
//            $company = Company::model()->findByPk($id);
//
//            $company->name = $_POST['name'];
//            if ($_POST['name'] != null && $_POST['name'] != '') {
//                $company->trade_id = $_POST['tradeId'];
//                //城市id未录入
//
//                $company->full_address = $_POST['fullAddress'];
//                $company->phone = $_POST['phone'];
//                $company->type_id = $_POST['typeId'];
//                $company->property_id = $_POST['propertyId'];
//                $company->postal_code = $_POST['postalCode'];
//                $company->email = $_POST['email'];
//                $company->website = $_POST['website'];
//                $company->organization_code = $_POST['organization_code'];
//                $company->is_famous = $_POST['isFamous'];
//                $company->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
//                $company->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
//                $company->introduction = $_POST['introduction'];
//                $company->entering_time = date("Y-m-d H:i:s", time());
//                $company->last_update = Yii::app()->session['user_name'];
//                $ff = $this->uploadImg();
//                if ($ff != "assets/uploadFile/companyImg/") {
//                    $ff = $ff;
//                } else
//                    $ff = 'assets/resources/resources/img/common/company_logo.png';
//                $company->logo = $ff;
//                //$f_video = $this->uploadVideoByApi();
//                //if($f_video!=null)
//                $company->video_url = $_POST['vid'];
//                $company_rs = $company->save();
//
//                $companyCity_rs = 1;
//                $CityModel =  CompanyCity::model()->findByAttributes(array('company_id' => $id));
//                if ($_POST['cityId'][0]) {
//                    $cityId = $_POST['cityId'][0];
//                    $CityModel->city_id = $_POST['cityId'][0];
//                    $CityModel->company_id = $company->attributes['id'];
//                    $companyCity_rs = $CityModel->save();
//                }else{
//                    $cityId = $CityModel->city_id;
//                }
//
//                CompanyPic::model()->deleteAllByAttributes(array('company_id' => $id));
//                $pic_rs = 1;
//                // $pics = UploadPicService::getInstance()->uploadThree();
//                // for ($i = 0; $i < count($pics); $i++) {
//                //     if ($pics[$i] != null) {
//                //         $pic = new CompanyPic();
//                //         $pic->url = $pics[$i];
//                //         $pic->company_id = $company->attributes['id'];
//                //         $pic->save();
//                //     }
//                // }
//
//
//                $br_rs = 1;
//                if (isset($_POST['bright'])) {
//                    Brightened::model()->deleteAllByAttributes(array('related_id' => $id, 'type' => 1));
//                    $brightList = $_POST['bright'];
//                    for ($i = 0; $i < count($brightList); $i++) {
//                        $br = new Brightened();
//                        $br->type = 1;
//                        $br->name = $brightList[$i];
//                        $br->related_id = $company->attributes['id'];
//                        $br->save();
//                    }
//                }
//
//
////                // 同步数据至就业办公平台
////                $post = $_POST;
////                if (!isset($post['bright'])) {
////                    $post['bright'] = array();
////                }
////
////                $post['id'] = $company->opposite_id;
////
////                $post['oppositeId'] = $id;
////                $post['cityId'] = $cityId;
////                $post_data = CJSON::encode($post);
////                $url = 'http://www.dsjyw.net/api/company/sendUpdate';
////
////                $json = array('json'=>$post_data);
////
////                $json =  http_build_query($json);
////
////
////                $ch = curl_init();
////                curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址
////                curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
////                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
////                curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
////                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);//要提交的信息
////                $out = curl_exec($ch);
////                curl_close($ch);
////
////                $curl_rs = CJSON::decode($out);
////                if (isset($curl_rs['code'])) {
////                    $code = $curl_rs['code'];
////                }else{
////                    $code = 1;
////                }
////                if (!$code) {
////                    $company->opposite_id = $curl_rs['data']['id'];
////                    $company->save();
////                }
////
//                $company->opposite_id=666;
//                $company->save();
//
//
//
//
//            }
//            if (!$company_rs|!$companyCity_rs||!$pic_rs||!$br_rs||$code) {
//                throw new Exception("Error Processing Request");
//
//            }
//
//            $tr->commit();
//
//        } catch (Exception $e) {
//            $tr->rollback();
//        }
//
//        $this->redirect($this->createUrl("admin/company/company/list"));
//
//
//
//    }

    public function actionDel($id)//职位和宣讲会为空的时候删除
    {
        $flag = false;
        $tr = Yii::app()->db->beginTransaction();
        try {
            $company = Company::model()->findByPk($id);
            $positionModel = Position::model();
            $careerTalkModel = CareerTalk::model();
            if (!empty($company)){
                // 该单位下职位数目
                $position_num = $positionModel->count(array('condition'=>'company_id='.$id));

                // 该单位下宣讲会数目
                $careerTalk_num = $careerTalkModel->count(array('condition'=>'company_id='.$id));
                
                if ($position_num||$careerTalk_num) {
                    throw new Exception ( '删除失败，请检查该单位下是否还有招聘信息或宣讲会信息' );
                }else{
                    $company_rs = Company::model()->deleteAllByAttributes(array('id'=>$id));
                }
            }
            if (!$company_rs) {
                throw new Exception ( '单位删除失败' );
            }

//            if ($company->opposite_id) {
//                $url = 'http://www.dsjyw.net/api/company/sendDel';
//                $ch = curl_init();
//                curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址
//                curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
//                curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
//                curl_setopt($ch, CURLOPT_POSTFIELDS, 'id='.$company->opposite_id);//要提交的信息
//                $out = curl_exec($ch);
//                curl_close($ch);
//                $curl_rs = CJSON::decode($out);
//
//                if (isset($curl_rs['code'])) {
//                    $code = $curl_rs['code'];
//                }else{
//                    $code = 1;
//                }
//            }else{
//                $code = 0;
//            }
//
//            if ($code) {
//                throw new Exception("删除失败，就业办公平台数据同步故障");
//
//            }

            $tr->commit();
            $flag=true;
        } catch (Exception $e) {
            $tr->rollback();
            $errMsg = $e->getMessage();
            // print_r($errMsg);
            echo "<script>
                     alert('删除失败，请检查该单位下是否还有招聘信息或宣讲会信息')
                     window.history.go(-1)
                   
                  </script>";
            die();
        }
        if($flag==true)
        //同步删除oa
        {
            $url="http://localhost:8080/company/sync/del";
            //验证方式选择token
            $data = array(
                'token'=>$this->sendtoken(),
                'data'=>Array(
                    'id'=>$id
                )

            );
            // 注释部分可以额外设置请求头，此处默认即可需要时可自行修改
            $headerArray = array(
                'Accept:application/json, text/javascript, */*',
                'Content-Type:application/json;charset=UTF-8'
            );
            $json_data = CJSON::encode($data);
            $ch = curl_init();  // 初始化
            curl_setopt($ch, CURLOPT_URL, $url);  //同步接口地址
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
            curl_setopt($ch, CURLOPT_HEADER, 0);  //是否显示头信息
            curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//设置头信息


            $output = curl_exec($ch);//////
            curl_close($ch); // 关闭数据同步会话
            $res = CJSON::decode($output);
            if($res['code']==0)//同步成功
                $this->redirect($this->createUrl("admin/company/company/list"));
            else
                echo 'fail';//同步失败
        }
        $this->redirect($this->createUrl("admin/company/company/list"));
    }

    public function actionSearch()
    {
        $keyword = $_POST['keyword'];
        $propertyId = $_POST['propertyId'];
        // $cityId = $_POST['cityId'];
        $tradeId = $_POST['tradeId'];
        $isEliteFirm = 0;
        $isEliteSchool = 0;
        if (isset($_POST['isEliteSchool'])) {
            $isEliteSchool = $_POST['isEliteSchool'];
        }
        if (isset($_POST['isEliteFirm'])) {
            $isEliteFirm = $_POST['isEliteFirm'];
        }
        if (isset($_POST['isJoinBigRecruitment']))
            $isJoinBigRecruitment = $_POST['isJoinBigRecruitment'];
        else
            $isJoinBigRecruitment = null;
        if (isset($_POST['isJoinRecruitmentWeek']))
            $isJoinRecruitmentWeek = $_POST['isJoinRecruitmentWeek'];
        else
            $isJoinRecruitmentWeek = null;
        $companyListOnePage = CompanyService::getInstance()->search2(1, $keyword, $propertyId, 0, $tradeId, $isEliteFirm, $isEliteSchool, $isJoinBigRecruitment, $isJoinRecruitmentWeek, 0,0);
//        print_r($companyListOnePage);
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList = CompanyProperty::model()->findAll();
        $cityList = City::model()->findAll();
        $recordCount = $companyListOnePage['recordCount'];
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList', $tradeList);
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('companyList', $companyListOnePage['list']);
        $this->smarty->assign('propertyId', $propertyId);
        //$this->smarty->assign('cityId', $cityId);
        $this->smarty->assign('tradeId', $tradeId);
        $this->smarty->assign('isEliteSchool', $isEliteSchool);
        $this->smarty->assign('isEliteFirm', $isEliteFirm);
        $this->smarty->assign('isJoinBigRecruitment', $isJoinBigRecruitment);
        $this->smarty->assign('isJoinRecruitmentWeek', $isJoinRecruitmentWeek);
        $this->smarty->assign('recordCount', $recordCount);
        $this->smarty->assign('keyword', $keyword);
        $this->smarty->assign('current', 'company');
        $this->smarty->display('admin/company/search.html');
    }

    public function  actionSearchJson($page = 0, $keyword = null, $propertyId = 0, $tradeId = 0, $isEliteFirm = 0, $isEliteSchool = 0,
                                      $isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0)
    {
        $companyListOnePage = CompanyService::getInstance()->search2($page, $keyword, $propertyId, 0, $tradeId, $isEliteFirm, $isEliteSchool, $isJoinBigRecruitment, $isJoinRecruitmentWeek, 0, 0);
        $dataCount = $companyListOnePage['recordCount'];
        $SearchJson = '{"list":' . CJSON::encode($companyListOnePage['list']) . ',"dataCount":' . $dataCount . '}';
        print  $SearchJson;
    }

    public function actionConcern($companyId)
    {
        $company = Company::model()->findByPk($companyId);
        if ($company == null) {
            $this->actionList();
            return;
        }
        $userId = 1;
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId' => $companyId, ':userId' => $userId),
        ));
        if ($companyUser != null) {
            $this->actionList();
            return;
        }
        $companyUserOne = new CompanyUser();

        $companyUserOne->company_id = $companyId;
        $companyUserOne->user_id = $userId;
        $companyUserOne->save();

        $company->concern_num++;
        $company->save();
    }

    public function actionUnconcern($companyId)
    {
        $company = Company::model()->findByPk($companyId);
        if ($company == null) {
            $this->actionList();
            reuturn;
        }
        $userId = 1;
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId' => $companyId, ':userId' => $userId),
        ));
        if ($companyUser == null) {
            $this->actionList();
        }

        $companyUser->delete();
        $company->concern_num--;

        $company->save();

    }

    public function uploadImg()
    {
        //url为文件上传时input的name
        $file = CUploadedFile::getInstanceByName('logo');
        if (!$file) {
            $fileBUrl = "assets/uploadFile/companyImg/";
            return $fileBUrl;
        } else {
            //文件保存路径
            $uploadPath = "assets/uploadFile/companyImg/";

            //获取文件后缀名
            $extName = $file->getExtensionName();
            //给文件重命名
            $fileName = time() . '.' . $extName;
            //保存文件
            $fileUrl = $uploadPath . $fileName;
            $file->saveAs($fileUrl);
            return $fileUrl;
        }
    }

    /**
     * 缩略图片生成
     * @ path 图片路径
     * @ width 图片宽度
     * @ height 图片高度
     */
    public function actionGetThumb($path, $w, $h)
    {
        return CompanyService::getInstance()->GetThumb($path, $w, $h);
    }

    public function uploadVideo()
    {
        //url为文件上传时input的name
        $file = CUploadedFile::getInstanceByName('file');
        if (!$file) {
            $fileBUrl = "assets/uploadFile/companyVideo/";
            return $fileBUrl;
        } else {
            //文件保存路径
            $uploadPath = "assets/uploadFile/companyVideo/";

            //获取文件后缀名
            $extName = $file->getExtensionName();
            //给文件重命名
            $fileName = time() . '.' . $extName;
            //保存文件
            $fileUrl = $uploadPath . $fileName;
            $file->saveAs($fileUrl);
            return $fileUrl;
        }
    }

    public function uploadVideoByApi()
    {
        //url为文件上传时input的name
        $file = CUploadedFile::getInstanceByName('file');
        //$file =$GLOBALS['HTTP_RAW_POST_DATA'];
        /*     if(!$file){
                 $fileBUrl="assets/uploadFile/companyVideo/";
                 return $fileBUrl;
             }else {
                 //文件保存路径
                 $uploadPath ="assets/uploadFile/companyVideo/";

                 //获取文件后缀名
                 $extName = $file->getExtensionName();
                 //给文件重命名
                 $fileName = time().'.'.$extName;
                 //保存文件
                 $fileUrl = $uploadPath.$fileName;*/
        $polyvSDK = new PolyvSDK();
        $result = $polyvSDK->uploadfile('title1', 'desc1', 'tag1', '', $file);
        // $result = $polyvSDK->uploadfile('title1','desc1','tag1','','E:\wamp\www\new_dsjyw\assets\uploadFile\companyVideo\1433418850.flv');
        return $result['swf_link'];
        // }
    }

    public function actionListFamous()
    {
        $famousList = Company::model()->findAll(array(
            'condition' => 'is_famous!=0',
        ));
        $this->smarty->assign('recordCount', count($famousList));
        $this->smarty->assign('current', 'famous');
        $this->smarty->display('admin/company/listFamous.html');
    }

    public function actionFamousJson()
    {
        $page = $_GET['page'];
        $criteria = new CDbCriteria;
        $criteria->condition = 'is_famous!=0';
        $list_all = Company::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', $page);
        $criteria->order = 'is_index_show DESC,entering_time DESC';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage - 1) * $pageSize;
        $recordCount = count($list_all);
        $list = Company::model()->findAll($criteria);  //记录分页
        $list2 = '{"list":' . CJSON::encode($list) . ',"dataCount":"' . $recordCount . '"}';
        print $list2;
    }

    public function actionIndexShow($id)
    {
        if (Company::model()->count(array('condition' => 'is_index_show=1')) < 5) {
            $famous = Company::model()->findByPk($id);
            $famous->is_index_show = 1;
            $famous->save();
        }
        $this->actionListFamous();
    }

    public function actionCancelIndexShow($id)
    {
            $famous = Company::model()->findByPk($id);
            $famous->is_index_show = 0;
            $famous->save();
        $this->actionListFamous();
    }

    public function actionDelFamous($id)
    {
        $company = Company::model()->findByPk($id);
        if (!empty($company))
            $company->delete();
        $this->redirect($this->createUrl("admin/company/company/listFamous"));
    }

//    public function actionListIndexInput()
//    {
//        $indexInputList = Companyweek2017::model()->findAll(array(
//            'condition' => 'is_front_input=1',
//        ));
//        $this->smarty->assign('recordCount', count($indexInputList));
//        $this->smarty->assign('current', 'companyCheck');
//        $this->smarty->display('admin/company/listIndexInput.html');
//    }

    //公司列表，未审核的或未通过的公司，从前端公司自己注册的，列表显示供管理员审核
    public function actionListIndexInput()
    {
        $indexInputList = Company::model()->findAll(array(
            'condition' => '(is_ok=0 OR is_ok=2 OR is_ok=3)AND is_front_input=1',
        ));
        $this->smarty->assign('recordCount', count($indexInputList));
        $this->smarty->assign('current', 'companyCheck');
        $this->smarty->display('admin/company/listIndexInput.html');
    }
    public function actionIndexInputJson()
    {
        $page = $_GET['page'];
        $criteria = new CDbCriteria;
        $criteria->condition = '(is_ok=0 OR is_ok=2 OR is_ok=3) AND is_front_input=1';//is_front_input=1
        $list_all = Company::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage', $page);
        $criteria->order = 'is_index_show DESC,entering_time DESC';
        $criteria->limit = $pageSize;
        $criteria->offset = ($currentPage - 1) * $pageSize;
        $recordCount = count($list_all);
        $list = Company::model()->findAll($criteria);  //记录分页
        $list2 = '{"list":' . CJSON::encode($list) . ',"dataCount":"' . $recordCount . '"}';
        print $list2;
    }

    public function actionCheck($id){
        $company = Company::model()->findByPk($id);
        $company->is_ok=1;
        //$company->save();
        //同步数据到OA
        if($company->save())//插入成功则同步信息
        {
            $logoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-logo/';
            $zhizhaoFileUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-zhizhao/';
            $videoUrl = 'http://www.dsjyw.net/assets/uploadFile/recruitEntrance/company-video/';

            $companyZhizhao = CompanyZhiZhao::model()->findByAttributes(array('company_id'=>$id));
            //同步单位信息到OA
            $url="http://localhost:8080/company/sync/add";
            //验证方式选择token
            $data = array(
                'token'=>$this->sendtoken(),
                'data'=>Array(
                    'bright'=>Array(
                        $company->company_bright
                    ),
                    'companyInfo'=>Array
                    (
                        'enteringTime'=>$company->entering_time,
                        'tradeId'=>$company->trade_id,
                        'cityId'=>$company->city_id,
                        'fullAddress'=>$company->full_address,
                        'phone'=>$company->phone,
                        'typeId'=>$company->type_id,
                        'propertyId'=>$company->property_id,
                        'postalCode'=>$company->postal_code,
                        'email'=>$company->email,
                        'isJoinBigRecruitment'=>$company->is_join_big_recruitment,
                        'isJoinRecruitmentWeek'=>$company->is_join_recruitment_week,
                        'oppositeId'=>$company->id,
                        'companyName'=>$company->name,
                        'website'=>$company->website,
                        'isEducationIndustry'=>$company->type_id,
                        'introduction'=>$company->introduction,
                        'isFamous'=>$company->is_famous,
                        'videoUrl'=>$videoUrl.$company->video_url,//暂时没实现管理员上传视频功能
                        'logo'=>$logoFileUrl.$company->logo,
                        'organizationCode'=>$company->organization_code,
                        'status'=>$company->is_ok,
                        'lastUpdate'=>$company->last_update,
                        'isIndexShow'=>$company->is_index_show,
                        'isFrontInput'=>$company->is_front_input,
                        'daima'=>$company->organization_code,//重复了
                        'zhizhao'=>$zhizhaoFileUrl.$companyZhizhao->url, //认证资料图片一张
                    ),

                )

            );
            // 注释部分可以额外设置请求头，此处默认即可需要时可自行修改
            $headerArray = array(
                'Accept:application/json, text/javascript, */*',
                'Content-Type:application/json;charset=UTF-8'
            );

            $json_data = CJSON::encode($data);

            $ch = curl_init();  // 初始化
            curl_setopt($ch, CURLOPT_URL, $url);  //同步接口地址
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
            curl_setopt($ch, CURLOPT_HEADER, 0);  //是否显示头信息
            curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//设置头信息


            $output = curl_exec($ch);//////
            curl_close($ch); // 关闭数据同步会话
            $res = CJSON::decode($output);
            if($res['code']==0)//同步成功
                $this->redirect($this->createUrl("admin/company/company/list"));
            else
                echo 'fail';//同步失败
        }
        //$po = Position::model()->findByAttributes(array('company_id'=>$id));
        //$po->is_ok=1;
        //$po->save();
        //$ca = CareerTalk::model()->findByAttributes(array('company_id'=>$id));
        //$ca->is_ok=1;
        //$ca->save();
        //$this->actionListIndexInput();
       // $this->redirect($this->createUrl("admin/company/company/listIndexInput"));
    }

    public function actionCheckFail($id){
        $company = Company::model()->findByPk($id);
        $company->is_ok = 2;
        $company->save();
        $this->redirect($this->createUrl("admin/company/company/listIndexInput"));
    }
    public function actionDelIndexInput($id){
        $company = Company::model()->findByPk($id);
        if (!empty($company))
            $company->delete();
        $po = Position::model()->findByAttributes(array('company_id'=>$id));
        $ca = CareerTalk::model()->findByAttributes(array('company_id'=>$id));
        if (!empty($po))
            $po->delete();
        if (!empty($ca))
            $ca->delete();
        $this->redirect($this->createUrl("admin/company/company/listIndexInput"));
    }

    //查看公司详情
    public function actionDetailIndexInput($id)
    {
        $company = Company::model()->findByPk($id);
        //这里需要添加更多公司信息 前端需要修改

        //城市
        $city_id = CompanyCity::model()->findByAttributes(array('company_id' => $id))->city_id;
        $this->smarty->assign('city',City::model()->findByPk($city_id)->name);

        //单位类型  id==1 是教育行业，2是非教育行业，数据库有空字段
        $type_id = $company->type_id;
        $this->smarty->assign('type_id',$type_id);

        //经济类型
        $economic_type_id = $company->economic_type_id;
        $this->smarty->assign('economic_type',EconomicType::model()->findByPk($economic_type_id)->economic_type);

        //单位规模
        $unit_size = $company->unit_size;
        $this->smarty->assign('unit_size',CompanyUnitSize::model()->findByPk($unit_size)->size);

        //18位代码
        $organization_code =$company->organization_code;
        $this->smarty->assign('organization_code',$organization_code);

        //视频
        $videoUrl = $company->video_url;
        $this->smarty->assign('videoUrl',$videoUrl);
        //单位logo
        $logoUrl = $company->logo;
        $this->smarty->assign('logoUrl',$logoUrl);

        //执照 查找认证资料多图显示
        $condition = "1=1 and company_id = ".$id;
        $params = array(':company_id' => $id);
        $zhizhaoUrl = CompanyZhiZhao::model()->findAll($condition, $params);
        $zhizhaoUrl = json_decode(CJSON::encode($zhizhaoUrl),TRUE);
        $this->smarty->assign('zhizhaoUrl',$zhizhaoUrl);

        //联系人
        $companyContact = CompanyLoginUser::model()->findByAttributes(array('company_id'=>$id));
        $companyContactName = $companyContact->name;
        $companyContactTel = $companyContact->phone;
        $companyContactDuty = $companyContact->duty;
        $this->smarty->assign('companyContact',$companyContact);
        $this->smarty->assign('companyContactName',$companyContactName);
        $this->smarty->assign('companyContactDuty',$companyContactDuty);
        $this->smarty->assign('companyContactTel',$companyContactTel);


        $this->smarty->assign('trade',CompanyTrade::model()->findByPk($company->trade_id)->name);
        $this->smarty->assign('property',CompanyProperty::model()->findByPk($company->property_id)->name);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('current', 'companyCheck');
        $kind = substr($company->daima, -3);
        $kind2 = substr($company->zhizhao,-3);
        $this->smarty->assign('kind',$kind);
        $this->smarty->assign('kind2',$kind2);
        $this->smarty->display('admin/company/detailIndexInput.html');
    }
    public function actionToEditFront($id)
    {
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('current', 'companyCheck');
        $kind = substr($company->daima, -3);
        $kind2 = substr($company->zhizhao,-3);
        $this->smarty->assign('kind',$kind);
        $this->smarty->assign('kind2',$kind2);
        $companyCity = CompanyCity::model()->findByAttributes(array('company_id' => $id));
        $tradeList = CompanyTrade::model()->findAll();
        $propertyList = CompanyProperty::model()->findAll();
        $cityList = City::model()->findAll();
        $this->smarty->assign('cityList', $cityList);
        $this->smarty->assign('propertyList', $propertyList);
        $this->smarty->assign('tradeList', $tradeList);
        $this->smarty->assign('companyCity', $companyCity);
        $this->smarty->assign('city',$cityList);
        $this->smarty->assign('current','position');
        $this->smarty->assign('provinceList',Province::model()->findAll());
        $this->smarty->assign('provinceId',City::model()->findByPk(CompanyCity::model()->findByAttributes(array('company_id'=>$id))->city_id)->province_id);
        $this->smarty->assign('cityId',CompanyCity::model()->findByAttributes(array('company_id'=>$id))->city_id);
        $this->smarty->assign('current', 'companyCheck');
        $this->smarty->display('admin/company/editIndexInput.html');
    }

    public function actionEditFront($id)
    {
        $company = Company::model()->findByPk($id);
        $company->name = $_POST['name'];
        if ($_POST['name'] != null && $_POST['name'] != '') {
            $company->trade_id = $_POST['tradeId'];
            $company->full_address = $_POST['fullAddress'];
            $company->phone = $_POST['phone'];
            $company->type_id = $_POST['typeId'];
            $company->property_id = $_POST['propertyId'];
            $company->postal_code = $_POST['postalCode'];
            $company->email = $_POST['email'];
            $company->website = $_POST['website'];
            $company->fax = $_POST['fax'];
            $company->is_join_big_recruitment = $_POST['isJoinBigRecruitment'];
            $company->is_join_recruitment_week = $_POST['isJoinRecruitmentWeek'];
            $company->introduction = $_POST['introduction'];
            $company->entering_time = date("Y-m-d H:i:s", time());
            $company->last_update = Yii::app()->session['user_name'];
            $array = UploadPicService::getInstance()->uploadTwo();
            $company->daima = $array[0];
            $company->zhizhao = $array[1];
            $company->status = 0;
            $company->save();
            CompanyCity::model()->deleteAllByAttributes(array('company_id' => $id));
            $city = $_POST['cityId'];
                $companyCity = new CompanyCity();
                $companyCity->city_id = $city;
                $companyCity->company_id = $company->attributes['id'];
                $companyCity->save();
        }
        $this->redirect($this->createUrl("admin/company/company/detailIndexInput/id/".$id));
    }

    public function Curl($post_data){

        $url = 'http://www.dsjyw.net/api/company/send';
        $data = array(
                      'data'=>$post_data,
            );
        $json_data = CJSON::encode($data);

        $json = array('json'=>$json_data);

        $json =  http_build_query($json);

        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址 
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);//要提交的信息 
        $out = curl_exec($ch);
        curl_close($ch);

        echo $out;
    
    }
}
