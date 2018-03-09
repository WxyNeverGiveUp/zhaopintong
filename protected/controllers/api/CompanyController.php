<?php 
// date_default_timezone_set('Asia/Shanghai');
/**
* 
*/
class CompanyController extends Controller
{
    public function actionTest(){
        echo 1111;
    }
    
    public function actionAdd(){
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }


        $data = CJSON::decode($GLOBALS['HTTP_RAW_POST_DATA']);

        
        // 权限认证
        if (!isset($data['token'])) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }
        $token = $data['token'];
              
        $status = $this->token($token);

        if ($status) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }


        if (!isset($data['data'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }
        
        $data['name'] = trim($data['name']);
        $data = $data['data'];
        $data['opposite_id'] = $data['id'];
        $city_id = $data['city_id'];
        if (isset($data['bright'])) {
            $bright = $data['bright'];
            unset($data['bright']);
        }
        unset($data['city_id']);
        unset($data['id']);


        // 判断该单位信息是否已经存在
        $companyName = $data['name'];
        $companyName = trim($companyName);
        $companyCheck = Company::model()->find(array(
            'select'=>'id',
            'condition'=>'name=:name',
            'params'=>array(':name'=>$companyName)
            ));
        if ($companyCheck) {
            $cityCheck = CompanyCity::model()->find(array(
              'select'=>'id',
              'condition'=>'company_id=:company_id',
              'params'=>array(':company_id'=>$companyCheck->id)
            ));
        }else{
            $cityCheck = null;
        }
        

        // 创建model
        if ($companyCheck) {
            $company = company::model()->findByPk($companyCheck->id);
        }elseif ($companyCheck==null) {
            $company = new company();
        }

        if ($cityCheck) {
            $companyCity = CompanyCity::model()->findByPk($cityCheck->id);
        }elseif ($cityCheck==null) {
            $companyCity = new CompanyCity();
        }

        // 插入数据
      
        $tr = Yii::app()->db->beginTransaction();  //创建事务
        try{

            foreach ($data as $key => $value) {
                $company->$key = $value;
            }
            
            $company->concern_num = 0;
            $company->entering_time = date("Y-m-d H:i:s", time());
            // $company->last_update = 'jyoa';
            $company->logo = 'assets/resources/resources/img/common/company_logo.png';
            $company_rs = $company->save();

            
            $companyCity->city_id = $city_id;
            $companyCity->company_id = $company->attributes['id'];
            $city_rs = $companyCity->save();
            
            if (isset($bright)) {
                for ($i = 0; $i < count($bright); $i++) {
                    $br = new Brightened();
                    $br->type = 1;
                    $br->name = $bright[$i];
                    $br->related_id = $company->attributes['id'];
                    $br_rs = $br->save();
                }
            }else{
                $br_rs = 1;
            }
            


            if (!$company_rs || !$city_rs||!$br_rs) {
                throw new Exception ( 'exception message' );
            }

            $rs = $tr->commit();
        } catch(Exception $e){
            $tr->rollback();
            print_r($this->errMsg('添加失败!'));
            return;

        }

        $message = array(
                    'code'=>0,
                    'data'=>array(
                          'id'=>$company->attributes['id']
                        )      
                    );
        $json = CJSON::encode($message);
        print_r($json);



    }

    public function actionUpdate(){
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }


        $data = CJSON::decode($GLOBALS['HTTP_RAW_POST_DATA']);

        
        // 权限认证
        if (!isset($data['token'])) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }
        $token = $data['token'];
              
        $status = $this->token($token);

        if ($status) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }

        if (!isset($data['data'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }
        

        $data = $data['data'];
        $city_id = $data['city_id'];
        $id = $data['id'];
        $opposite_id = $data['opposite_id'];
        if (isset($data['bright'])) {
            $bright = $data['bright'];
            unset($data['bright']);
        }
        unset($data['city_id']);
        unset($data['id']);
        unset($data['opposite_id']);

        // 判断数据是否为旧数据
        if ($opposite_id==0) {
            // 如果是旧数据，在联盟网数据库中查找该单位是否存在
            $companyName = $data['name'];
            $company = Company::model()->findByAttributes(array('name'=>$companyName));

            // 如果存在，写入opposite_id
            if ($company==null) {
                $company = new Company();
                $CityModel = new CompanyCity();
            }else{
                $CityModel = CompanyCity::model()->findByAttributes(array('company_id'=>$company->id));
                
            }
        }else{
            $company = Company::model()->findByPk($opposite_id);
            $CityModel = CompanyCity::model()->findByAttributes(array('company_id'=>$opposite_id));
        }

        // 修改数据
        
        $tr = Yii::app()->db->beginTransaction();  //创建事务
        try{
            
            $company->opposite_id = $id;
            $company->logo = 'assets/resources/resources/img/common/company_logo.png';

            foreach ($data as $key => $value) {
                $company->$key = $value;
            }
            $company->entering_time = date("Y-m-d H:i:s", time());
            // $company->last_update = 'jyoa';
            
            $company_rs = $company->save();

            if ($CityModel!=null) {
                $CityModel->city_id = $city_id;
                $CityModel->company_id = $company->attributes['id'];
                $city_rs = $CityModel->save();
            }elseif ($CityModel==null) {
                $city_rs = 1;
            }
            
            if (isset($bright)) {
                Brightened::model()->deleteAllByAttributes(array('related_id' => $id, 'type' => 1));
                for ($i = 0; $i < count($bright); $i++) {
                    $br = new Brightened();
                    $br->type = 1;
                    $br->name = $bright[$i];
                    $br->related_id = $company->attributes['id'];
                    $br_rs = $br->save();
                }
            }else{
                $br_rs = 1;
            }
            

            if (!$company_rs || !$city_rs||!$br_rs) {
                throw new Exception ( 'exception message' );
            }

            $rs = $tr->commit();
        } catch(Exception $e){
            $tr->rollback();
            print_r($this->errMsg('单位信息修改失败'));
            return;

        }

        $message = array(
                    'code'=>0,
                    'data'=>array(
                          'id'=>$company->attributes['id']
                        )      
                    );
        $json = CJSON::encode($message);
        print_r($json);

    }

    public function actionDel(){
        
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }


        $data = CJSON::decode($GLOBALS['HTTP_RAW_POST_DATA']);

        
        // 权限认证
        if (!isset($data['token'])) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }
        $token = $data['token'];
              
        $status = $this->token($token);

        if ($status) {
            print_r($this->errMsg('权限认证失败'));
            return;
        }

        if (!isset($data['data'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }
    
        $data = $data['data'];

        $companyModel = Company::model();
        $positionModel = Position::model();
        $careerTalkModel = CareerTalk::model();

        $tr = Yii::app()->db->beginTransaction();  //创建事务
        try {

            foreach ($data['id'] as $key => $value) {
                // 该单位下职位数目
                $position_num = $positionModel->count(array('condition'=>'company_id='.$value));

                // 该单位下宣讲会数目
                $careerTalk_num = $careerTalkModel->count(array('condition'=>'company_id='.$value));

                
                // 删除单位信息
                if ($position_num||$careerTalk_num) {
                    throw new Exception ( 'exception message' );
                }else{

                    $companyInfo = $companyModel->findByPk($value);
                    if ($companyInfo!=null) {
                        $rs = $companyInfo->delete();
                    }else{
                        $rs = 1;
                    }
                
                    
                }

                if (!$rs) {
                    throw new Exception ( 'exception message' );
                }      
            }
            $tr->commit(); 
            
        } catch (Exception $e) {
            $tr->rollback();
            print_r($this->errMsg('删除单位信息失败,请检查单位下是否有未删除的招聘信息和宣讲会信息'));
            return;
        }

        

        $message = array(
                    'code'=>0,
                    'data'=>$data['id']    
                    );
        $json = CJSON::encode($message);
        print_r($json);

        
    }

    
    // 返回错误信息
    public function errMsg($Msg){
        $message = array(
                    'code'=>1,
                    'data'=>array(
                          'errMsg'=>$Msg
                        )      
                    );
        $json = CJSON::encode($message);
        return $json;

    }



    public function actionSend(){
        $send_data = $_POST['json'];
        $post_data = CJSON::decode($send_data);

        if ($post_data['typeId']==1) {
            $data['companyInfo']['isEducationIndustry'] = 1;
        }elseif ($post_data['typeId']==2) {
            $data['companyInfo']['isEducationIndustry'] = 0;
        }

        $tradeId = CompanyTrade::model()->findByPk($post_data['tradeId'])->jyoa_id;


        $data['companyInfo']['oppositeId'] = $post_data['oppositeId'];
        $data['companyInfo']['companyName'] =$post_data['name'];
        $data['companyInfo']['fullAddress'] = $post_data['fullAddress'];
        $data['companyInfo']['tradeId'] = $tradeId;
        $data['companyInfo']['phone'] = $post_data['phone'];
        $data['companyInfo']['typeId'] = 7;
        $data['companyInfo']['propertyId'] = $post_data['propertyId'];
        $data['companyInfo']['postalCode'] = $post_data['postalCode'];
        $data['companyInfo']['email'] = $post_data['email'];
        $data['companyInfo']['website'] = $post_data['website'];
        $data['companyInfo']['organizationCode'] = $post_data['organization_code'];
        $data['companyInfo']['organizationUrl'] = '';
        $data['companyInfo']['zhizhaoUrl'] = '';
        $data['companyInfo']['isFamous'] = $post_data['isFamous'];
        $data['companyInfo']['isJoinBigRecruitment'] = $post_data['isJoinBigRecruitment'];
        $data['companyInfo']['isJoinRecruitmentWeek'] = $post_data['isJoinRecruitmentWeek'];
        $data['companyInfo']['introduction'] = $post_data['introduction'];
        $data['companyInfo']['videoUrl'] = $post_data['vid'];
        $data['companyInfo']['cityId'] =$post_data['cityId'];
        $data['bright'] = $post_data['bright'];
        
        $data = array('token'=>$this->sendtoken(),
                      'data'=>$data,
            );
   

        $url = 'http://jyoa.dsjyw.net/company/sync/add';

        $json_data = CJSON::encode($data);
        // print_r($json_data);die();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址 
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
                                                   "Content-length: ".strlen($json_data)
                    )); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息 
        $out = curl_exec($ch);
        curl_close($ch);

        echo $out;
    }

    public function actionSendUpdate(){
        $send_data = $_POST['json'];
        $post_data = CJSON::decode($send_data);

        if ($post_data['typeId']==1) {
            $data['companyInfo']['isEducationIndustry'] = 1;
        }elseif ($post_data['typeId']==2) {
            $data['companyInfo']['isEducationIndustry'] = 0;
        }

        $tradeId = CompanyTrade::model()->findByPk($post_data['tradeId'])->jyoa_id;

        $data['companyInfo']['id'] = $post_data['id'];
        $data['companyInfo']['oppositeId'] = $post_data['oppositeId'];
        $data['companyInfo']['companyName'] =$post_data['name'];
        $data['companyInfo']['fullAddress'] = $post_data['fullAddress'];
        $data['companyInfo']['tradeId'] = $tradeId;
        $data['companyInfo']['phone'] = $post_data['phone'];
        $data['companyInfo']['propertyId'] = $post_data['propertyId'];
        $data['companyInfo']['postalCode'] = $post_data['postalCode'];
        $data['companyInfo']['email'] = $post_data['email'];
        $data['companyInfo']['website'] = $post_data['website'];
        $data['companyInfo']['organizationCode'] = $post_data['organization_code'];
        $data['companyInfo']['daima'] = '';
        $data['companyInfo']['zhizhao'] = '';
        $data['companyInfo']['isFamous'] = $post_data['isFamous'];
        $data['companyInfo']['isJoinBigRecruitment'] = $post_data['isJoinBigRecruitment'];
        $data['companyInfo']['isJoinRecruitmentWeek'] = $post_data['isJoinRecruitmentWeek'];
        $data['companyInfo']['introduction'] = $post_data['introduction'];
        $data['companyInfo']['videoUrl'] = $post_data['vid'];
        $data['companyInfo']['cityId'] =$post_data['cityId'];
        $data['bright'] = $post_data['bright'];

        $data = array('token'=>$this->sendtoken(),
                      'data'=>$data,
            );
        
    
        $url = 'http://jyoa.dsjyw.net/company/sync/update';
        $json_data = CJSON::encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址 
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   "Content-Type: application/json",
                                                   "Content-length: ".strlen($json_data)
                    )); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息 

        $out = curl_exec($ch);
        curl_close($ch);

        echo $out;
    }

    public function actionSendDel(){
        $id = array('id'=>$_POST['id']);
        $data = array('token'=>$this->sendtoken(),
                      'data'=>$id
            );
        $json_data = CJSON::encode($data);
        
        $url = 'http://jyoa.dsjyw.net/company/sync/del';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址 
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   "Content-Type: application/json",
                                                   "Content-length: ".strlen($json_data)
                    )); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);//要提交的信息 
        
        $out = curl_exec($ch);
        curl_close($ch);

        echo $out;


    }

    public function actionTest1(){
        //设置接口URL
        $url="http://localhost:8080/company/sync/add";
        //$token = $this->sendtoken();
        //验证方式选择token或用户名密码
        $id = 7866;
        $company =Company::model()->findByAttributes(array('id'=>$id));

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
                    'oppositeId'=>$id,
                    'companyName'=>$company->name,
                    'website'=>$company->website,
                    'isEducationIndustry'=>$company->type_id,
                    'introduction'=>$company->introduction,
                    'isFamous'=>$company->is_famous,
                    'videoUrl'=>$company->video_url,
                    'logo'=>$company->logo,
                    'organizationCode'=>$company->organization_code,
                    'status'=>$company->is_ok,
                    'lastUpdate'=>$company->last_update,
                    'isIndexShow'=>$company->is_index_show,
                    'isFrontInput'=>$company->is_front_input,
                    'daima'=>$company->organization_code,
                    'zhizhao'=>$company->organization_code,
                ),

            )

        );
        // 注释部分可以额外设置请求头，此处默认即可需要时可自行修改
        $headerArray = array(
            'Accept:application/json, text/javascript, */*',
            'Content-Type:application/json;charset=UTF-8'
        );

        $json_data = CJSON::encode($data);
//        $json = array('json'=>$json_data);
//        $json =  http_build_query($json);
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


//        echo $output;
//        echo $res['data'];
//        if($res['code'] == 0){
//            print_r($this->errMsg('权限认证失败'));
//            return;
//        }else if ($res['code'] == 2){
//            print_r($this->errMsg('调用接口失败'));
//            return;
//        }
    }
}


 ?>