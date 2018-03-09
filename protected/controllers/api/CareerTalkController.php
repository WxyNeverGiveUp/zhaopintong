<?php 
// date_default_timezone_set('Asia/Shanghai');
/**
* 
*/
class CareerTalkController extends Controller
{
	
	public function actionAdd(){
		if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            print_r($this->errMsg('未接收到数据'));
            return;
        }
        // print_r(($GLOBALS['HTTP_RAW_POST_DATA']));die();

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
         

        // 插入数据

        $data = $data['data'];
        if (isset($data['company_info'])) {
            $companyName = $data['company_info']['name'];
            $companyName = trim($companyName);
            $company = Company::model()->findByAttributes(array('name'=>$companyName));
            if ($company==null) {
                $company = new Company();
                $companyCity = new CompanyCity();
                $tr = Yii::app()->db->beginTransaction();  //创建事务
                try{
                    $city_id = $data['company_info']['city_id'];
                    $id = $data['company_info']['id'];
                    if (isset($data['company_info']['bright'])) {
                        $bright = $data['company_info']['bright'];
                        unset($data['company_info']['bright']);
                    }
                    unset($data['company_info']['id']);
                    unset($data['company_info']['city_id']);
                    foreach ($data['company_info'] as $key => $value) {
                        $company->$key = $value;
                    }
                    
                    $company->concern_num = 0;
                    $company->entering_time = date("Y-m-d H:i:s", time());
                    $company->opposite_id = $id;
                    $company->last_update = 'jyoa';
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
            }else{
                $company->opposite_id = $data['company_info']['id'];
                $company_rs = $company->save();
            }
        }

        $CareerTalkModel = new CareerTalk();                       //新建模型
        $CareerTalkModel->time = $data['time'];
        $CareerTalkModel->name = $data['name'];
        $CareerTalkModel->place = $data['place'];
        $CareerTalkModel->type = $data['type'];
        if (isset($company)) {
            $CareerTalkModel->company_id = $company->attributes['id'];
        }else{
            $CareerTalkModel->company_id = $data['company_id'];
        }

        $CareerTalkModel->description = $data['description'];
        $CareerTalkModel->live_url = $data['live_url'];
        $CareerTalkModel->url = $data['url'];
        $CareerTalkModel->last_update = 'jyoa';

        if ($CareerTalkModel->save()) {
        	if (isset($company)) {
                $message = array(
                        'code'=>0,
                        'data'=>array(
                              'recruitId'=>$CareerTalkModel->attributes['id'],
                              'companyId'=>$company->attributes['id']
                            )      
                        );
            }else{
                $message = array(
                        'code'=>0,
                        'data'=>array(
                              'recruitId'=>$CareerTalkModel->attributes['id']
                            )      
                        );
            }
        	$json = CJSON::encode($message);
        	print_r($json);
        }else{
        	print_r($this->errMsg('宣讲会添加失败'));
        }

        

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
         

        // 修改数据

        $data = $data['data'];

        $CareerTalkModel = CareerTalk::model()->findByPk($data['id']);                       //新建模型
        $CareerTalkModel->time = $data['time'];
        $CareerTalkModel->name = $data['name'];
        $CareerTalkModel->place = $data['place'];
        $CareerTalkModel->type = $data['type'];
        $CareerTalkModel->company_id = $data['company_id'];
        $CareerTalkModel->description = $data['description'];
        $CareerTalkModel->live_url = $data['live_url'];
        $CareerTalkModel->url = $data['url'];
        $CareerTalkModel->last_update = 'jyoa';

        if ($CareerTalkModel->save()) {
        	$message = array(
	        		'code'=>0,
	        		'data'=>array(
	                      'id'=>$CareerTalkModel->attributes['id']
	        			)      
	        		);
        	$json = CJSON::encode($message);
        	print_r($json);
        }else{
        	print_r($this->errMsg('宣讲会添加失败'));
        }

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

        // 新建模型
        $CareerTalkModel = CareerTalk::model();
 
        $tr = Yii::app()->db->beginTransaction();
        try {
            foreach ($data['id'] as $key => $value) {
                $CareerTalkModel->deleteAll(array('condition'=>'id='.$value));
            }
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollback();
            print_r($this->errMsg('删除宣讲会失败'));
            return;
        }

        $message = array(
                'code'=>0,
                'data'=>array(
                      'id'=>$data['id']
                    )      
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



}

 ?>