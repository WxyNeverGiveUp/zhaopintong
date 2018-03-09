<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 14:00
 */
class TCompanyController extends Controller
{
    /**
     * @param $name
     * 根据用户ID查取单位ID，然后根据单位ID查找公司基本信息
     */
    public function actionGetCompanyBaseManager()
    {
        $id =  Yii::app()->session['contact_id'];
//        echo json_encode($id,true);
        $companyUserModel =CompanyLoginUser::model();
        $companyUserInfo = $companyUserModel->findByPk($id);
        //企业亮点
        $propertyList = CompanyProperty::model()->findAll();
        $this->smarty->assign('propertyList', $propertyList);
//        echo json_encode($companyUserInfo->company_id,true);
        $companyModel = Company::model();
        $sql = "SELECT name,full_address,website,logo,video_url,company_bright FROM {{company}} WHERE id =".$companyUserInfo->company_id;
        $companyInfo = $companyModel->findBySql($sql);
        $name = $companyInfo->name;
        $fullAddress = $companyInfo ->full_address;
        $website = $companyInfo ->website;
        $logo = $companyInfo->logo;
        $videoUrl = $companyInfo->video_url;
        $companyBright = $companyInfo->company_bright;
        $this->smarty->assign('name',$name);
        $this->smarty->assign('fullAddress',$fullAddress);
        $this->smarty->assign('website1',$website);
        $this->smarty->assign('logo',$logo);
        $this->smarty->assign('videoUrl',$videoUrl);
        $this->smarty->assign('companyBright',$companyBright);
//        echo json_encode($videoUrl,true);
        $this->smarty->display('recruitEntrance/company-info/basic-info.html');
    }

    /**
     * 根据用户ID查取单位ID，然后根据单位ID更新公司基本信息
     */
    public function actionUpdateCompanyBaseManager()
    {
        $id =  Yii::app()->session['contact_id'];
        $companyUserModel =CompanyLoginUser::model();
        $companyUserInfo = $companyUserModel->findByPk($id);
        $companyModel = Company::model();
        $companyInfo = $companyModel->findByPk($companyUserInfo->company_id);
        if (!empty($_POST))
        {
//            $companyInfo->name= $_POST['name'];
//            $companyInfo->full_address = $_POST['full_address'];
            $companyInfo->last_update = Yii::app()->session['companyUserName'];//最新更新的用户
            $companyInfo->website = $_POST['website'];
//            $companyInfo->logo = $_POST['logo'];
//            echo json_encode(empty($_FILES['logo']),true);
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
                $companyInfo->logo = $imgFileName;
            } else{
                $companyInfo->logo = $companyInfo->logo;
            }
//            echo json_encode($_POST['logo'],true);
//            echo json_encode($companyInfo->logo,true);
            if (isset($_FILES['video_url']) && is_uploaded_file($_FILES['video_url']['tmp_name']))
            {
                $video = $_FILES['video_url'];
                $upErr = $video['error'];
//                    echo json_encode($upErr,true);
                if ($upErr == 0)
                {
                    $videoType = $video['type']; //文件类型。
                    $a=explode(".",$_FILES["video_url"]["name"]);  //截取文件名跟后缀
                    //$prename = substr($a[0],10);   //如果你到底的视频名称不是你所要的你可以用截取字符得到
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
                }    $companyInfo->video_url = $imgFileName;
            } else{
                $companyInfo->video_url = $companyInfo->video_url;
            }

            $companyInfo->company_bright = $_POST['company_bright'];
            if ($companyInfo->save()){
                $this->redirect(array('getCompanyBaseManager'));//对应的是当前Controller的actionIndex方法
            }

        }
    }


    /**
     * 根据用户ID查取公司ID，然后根据公司ID查找公司认证信息
     */
    public function actionGetCompanyAuthenticationData()
    {
        $id =  Yii::app()->session['contact_id'];
        $companyUserModel =CompanyLoginUser::model();
        $companyUserInfo = $companyUserModel->findByPk($id);
        $companyModel = Company::model();
        $sql = "SELECT * FROM {{company}} WHERE id = ".$companyUserInfo->company_id;
        $companyInfo = $companyModel->findBySql($sql);
        $companyZhiZhao = CompanyZhiZhao::model();
        $zhizhaoSql = "SELECT * FROM {{company_zhizhao}} WHERE company_id =".$companyUserInfo->company_id;
        $companyZhiZhaoList = $companyZhiZhao->findAllBySql($zhizhaoSql);
        $this->smarty->assign('companyZhiZhaoList',$companyZhiZhaoList);
        $status = $companyInfo->is_ok;//认证状态 0未审核 1-已通过 2-审核失败 3-等待认证
        $name = $companyInfo->name;//公司名字
        $city =$companyInfo->full_address;//单位所在城市
        $address = $companyInfo->full_address;//单位地址
        $phone = $companyInfo->phone;//单位固定电话
        $email = $companyInfo->email;//招聘邮箱
        $postalCode = $companyInfo->postal_code;//邮政编码
        $typeId = $companyInfo->type_id;//单位类型
        $propertyId = $companyInfo->property_id;//单位性质
        $economicType = $companyInfo->economic_type_id;//经济类型ID
        $tradeId = $companyInfo->trade_id;//所属行业ID
        $unitSize = $companyInfo->unit_size;//单位规模
        $barCode = $companyInfo->organization_code;//组织结构代码
        $zhizhao = $companyInfo->zhizhao;//企业认证资料
        $introduction = $companyInfo->introduction;//介绍
        $provinceId =$companyInfo->province_id;
        $cityId = $companyInfo->city_id;

        //获得所有的省份
        $provinceList = Province::model()->findAll();
        $this->smarty->assign('provinceList',$provinceList);
        //获得所有的城市
        $cityList = City::model()->findAll();
        $this->smarty->assign('cityList',$cityList);
        //获得所有单位规模
        $companySizeList = CompanyUnitSize::model()->findAll();
        $this->smarty->assign('companySizeList',$companySizeList);
        //获取公司所属行业
        $companyTradeList = CompanyTrade::model()->findAll();
        $this->smarty->assign('companyTradeList',$companyTradeList);
        //获得所有的单位经济类型
        $companyEconomicTypeList = CompanyEconomicType::model()->findALL();
        $this->smarty->assign('companyEconomicTypeList',$companyEconomicTypeList);
        //获得所有的单位性质
        $companyPropertyList = CompanyProperty::model()->findAll();
        $this->smarty->assign('companyPropertyList',$companyPropertyList);
        $this->smarty->assign('status',$status);
        $this->smarty->assign('name',$name);
        $this->smarty->assign('city',$city);
        $this->smarty->assign('address',$address);
        $this->smarty->assign('phone',$phone);
        $this->smarty->assign('email',$email);
        $this->smarty->assign('postalCode',$postalCode);
        $this->smarty->assign('typeId',$typeId);
        $this->smarty->assign('propertyId',$propertyId);
        $this->smarty->assign('economicType',$economicType);
        $this->smarty->assign('tradeId',$tradeId);
        $this->smarty->assign('unitSize',$unitSize);
        $this->smarty->assign('barCode',$barCode);
        $this->smarty->assign('zhizhao',$zhizhao);
        $this->smarty->assign('introduction',$introduction);
        $this->smarty->assign('provinceId',$provinceId);
        $this->smarty->assign('cityId',$cityId);
//        echo json_encode($provinceId,true);
//        echo json_encode($cityId,true);
//        echo json_encode($introduction,true);
        $this->smarty->display('recruitEntrance/company-info/certificated-info.html');
    }

    /**
     * @param $id
     * 根据用户ID查取公司ID，然后根据公司ID更新公司认证信息
     */
    public function actionUpdateCompanyAuthenticationData()
    {
        $id =  Yii::app()->session['contact_id'];//当前用户ID
        $companyUserModel = CompanyLoginUser::model();
        $companyUserInfo = $companyUserModel->findByPk($id);
        $companyModel = Company::model();
        $sql = "SELECT * FROM {{company}} WHERE id = ".$companyUserInfo->company_id;
        $companyInfo = $companyModel->findBySql($sql);
//        echo json_encode($companyInfo->id,true);
//        echo json_encode($companyInfo->website,true);
//        echo json_encode($companyUserInfo->company_id,true);
        $condition = "1=1 and (company_id =  :company_id)";
        $params = array(':company_id' => $companyInfo->id);
        CompanyZhiZhao::model()->deleteAll($condition, $params);
//        echo  json_encode($companyId,true);
        if (isset($_FILES['zhizhaoUrl'])) {
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
                        $companyZhizhao->company_id = $companyUserInfo->company_id;
                        $companyZhizhao->save();
                    }
                    else {
                        $this->redirect(array('getCompanyAuthenticationData'));
                    }
                }
            }
        }
//            echo json_encode($zhizhao,true);
//        $companyInfo->status = $_POST['status'];//认证状态

        $companyInfo->is_ok = 3;//is_ok==3表示重新认证
        $companyInfo->name = $_POST['name'];//公司名字
        $companyInfo->full_address = $_POST['full_address'];//单位地址
        $companyInfo->phone = $_POST['phone'];//单位固定电话
        $companyInfo->email = $_POST['email'];//招聘邮箱
        $companyInfo->postal_code = $_POST['postal_code'];//邮政编码
        $companyInfo->type_id = $_POST['unit-type'];//单位类型
        $companyInfo->property_id = $_POST['property_id'];//单位性质
        $companyInfo->economic_type_id = $_POST['economic_type_id'];//经济类型ID
        $companyInfo->trade_id = $_POST['trade_id'];//所属行业ID
        $companyInfo->unit_size = $_POST['unit_size'];//单位规模
        $companyInfo->organization_code = $_POST['barCode'];//组织结构代码
        $companyInfo->zhizhao = $_POST['zhizhao'];//企业认证资料
        $companyInfo->introduction = $_POST['introduction'];//介绍
        $companyInfo->province_id = $_POST['provinceId'];//省份名称
        $companyInfo->city_id = $_POST['cityId'];//单位所在城市
        $companyInfo->last_update = Yii::app()->session['companyUserName'];//最新更新的用户
//        echo json_encode( $companyInfo->province_id,true);
//        echo json_encode( $companyInfo->city_id,true);

        if ($companyInfo->save()) {
            $this->redirect(array('getCompanyAuthenticationData'));
        }
    }
    public function filters()
    {
        return array(
            array(
                'application.filters.RecruitmentFilter',
            ),
        );
    }

}