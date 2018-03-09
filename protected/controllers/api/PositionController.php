<?php 
// date_default_timezone_set('Asia/Shanghai');
/**
* 
*/
class PositionController extends Controller
{
    
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


        $data = $data['data'];

        // 插入所属单位信息
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


        // 插入职位数据
        $positionModel = new Position();
        $contactModel = new PositionContacts();

        $tr = Yii::app()->db->beginTransaction();
        try{
            $positionModel->is_teacher = $data['is_teacher'];
            $positionModel->name = $data['name'];
            if (isset($company)) {
                $positionModel->company_id = $company->attributes['id'];
            }else{
                $positionModel->company_id = $data['company_id'];
            }
            
            $positionModel->city_id = $data['city_id'];
            if($data['is_teacher']==1)
            $positionModel->type_id = $data['type_id'];
            else
                $positionModel->type_id = $data['type_id'];
            $positionModel->degree_id = $data['degree_id'];
            $positionModel->specialty_id = $data['specialty_id'];
            $positionModel->recruitment_num = $data['recruitment_num'];
            $positionModel->position_duty = $data['position_duty'];
            $positionModel->position_source = $data['position_source'];
            $positionModel->is_join_big_recruitment = $data['is_join_big_recruitment'];
            $positionModel->is_join_recruitment_week = $data['is_join_recruitment_week'];
            $positionModel->entering_time = date("Y-m-d H:i:s",time());
            $positionModel->views = 0;
            //$positionModel->dead_time = date("Y-m-d H:i:s",strtotime($data['deadTime']));;
            $positionModel->last_update = 'jyoa';
            $positon_rs = $positionModel->save();
            
            $contact_rs = 1;
            if(isset($data['contactName'])&&$data['contactName']!=null&&$data['contactName']!=''){
                $contactModel->position_id = $positionModel->attributes['id'];
                $contactModel->name = $data['contactName'];
                $contactModel->cellphone = $data['cellphone'];
                $contactModel->post = $data['post'];
                $contactModel->telephone = $data['telephone'];
                $contactModel->email = $data['contactEmail'];
                $contact_rs = $contactModel->save();
            }

            if (isset($data['bright'])) {
                $bright = $data['bright'];
                for ($i = 0; $i < count($bright); $i++) {
                    $br = new Brightened();
                    $br->type = 2;
                    $br->name = $bright[$i];
                    $br->related_id = $positionModel->attributes['id'];
                    $br_rs = $br->save();
                }
            }else{
                $br_rs = 1;
            }

            if (!$positon_rs || !$contact_rs||!$br_rs) {
                throw new Exception ( 'exception message' );
            }

            $tr->commit();

        }catch ( Exception $e ) {
            $tr->rollback ();
            print_r($this->errMsg('职位添加失败'));
            return;
        }
        
        if (isset($company)) {
            $message = array(
                    'code'=>0,
                    'data'=>array(
                          'positionId'=>$positionModel->attributes['id'],
                          'companyId'=>$company->attributes['id']
                        )      
                    );
        }else{
            $message = array(
                    'code'=>0,
                    'data'=>array(
                          'positionId'=>$positionModel->attributes['id']
                        )      
                    );
        }
 
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


        // 插入数据
        $positionModel = Position::model()->findByPk($data['id']);
        $contactModel = PositionContacts::model()->findByAttributes(array('position_id'=>$data['id']));
        if ($contactModel == null) {
            $contactModel = new PositionContacts();
        }

        $tr = Yii::app()->db->beginTransaction();
        try{
            $positionModel->is_teacher = $data['is_teacher'];
            $positionModel->name = $data['name'];
            $positionModel->company_id = $data['company_id'];
            $positionModel->city_id = $data['city_id'];
            $positionModel->type_id = $data['type_id'];
            $positionModel->degree_id = $data['degree_id'];
            $positionModel->specialty_id = $data['specialty_id'];
            $positionModel->recruitment_num = $data['recruitment_num'];
            $positionModel->position_duty = $data['position_duty'];
            $positionModel->position_source = $data['position_source'];
            $positionModel->is_join_big_recruitment = $data['is_join_big_recruitment'];
            $positionModel->is_join_recruitment_week = $data['is_join_recruitment_week'];
            $positionModel->entering_time = date("Y-m-d H:i:s",time());
            $positionModel->views = 0;
            //$positionModel->dead_time = date("Y-m-d H:i:s",strtotime($data['deadTime']));;
            $positionModel->last_update = 'jyoa';
            $positon_rs = $positionModel->save();

            $contact_rs = 1;
            if(isset($data['contactName'])&&$data['contactName']!=null&&$data['contactName']!=''){
                $contactModel->position_id = $positionModel->attributes['id'];
                $contactModel->name = $data['contactName'];
                $contactModel->cellphone = $data['cellphone'];
                $contactModel->post = $data['post'];
                $contactModel->telephone = $data['telephone'];
                $contactModel->email = $data['contactEmail'];
                $contact_rs = $contactModel->save();
            }

            if (isset($data['bright'])) {
                Brightened::model()->deleteAllByAttributes(array('related_id'=>$data['id'],'type'=>2));
                $bright = $data['bright'];
                for ($i = 0; $i < count($bright); $i++) {
                    $br = new Brightened();
                    $br->type = 2;
                    $br->name = $bright[$i];
                    $br->related_id = $positionModel->attributes['id'];
                    $br_rs = $br->save();
                }
            }else{
                $br_rs = 1;
            }

            if (!$positon_rs || !$contact_rs||!$br_rs) {
                throw new Exception("Error Processing Request");
            }
            $tr->commit();
        }catch ( Exception $e ) {
            $tr->rollback();
            print_r($this->errMsg('职位信息修改失败'));
            return;
        }

        $message = array(
                    'code'=>0,
                    'data'=>array(
                          'id'=>$positionModel->attributes['id']
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
        
        // 新建模型
        $positionModel = Position::model();
        $contactModel = PositionContacts::model();
        $brightenedModel = Brightened::model();


        $tr = Yii::app()->db->beginTransaction();
        try{
            foreach ($data['id'] as $key => $value) {
                // 删除职位联系人信息
                // $contact_rs = true;
                $contactInfo = $contactModel->findByAttributes(array('position_id'=>$value));
                if ($contactInfo==null) {
                    $contact_rs = 1;
                }else{
                    // $contact_rs = $contactModel->deleteAll(array('condition'=>'position_id='.$value));
                    $contact_rs = $contactInfo->delete();
                }


                // 删除职位信息
                $positionInfo = $positionModel->findByPk($value);
                if ($positionInfo==null) {
                    $positon_rs = 1;
                }else{
                    $positon_rs = $positionInfo->delete();
                }
                // $positon_rs = $positionModel->delete(array('condition'=>'id='.$value));
                
                // 删除职位亮点
                $br_num = $brightenedModel->countByAttributes(array('related_id'=>$value));
                if ($br_num==0) {
                    $br_rs = 1;
                }else{
                    $br_rs = $brightenedModel->deleteAllByAttributes(array('related_id'=>$value));
                }
                

                if (!$contact_rs || !$positon_rs||!$br_rs) {
                    throw new Exception("Error Processing Request");
                }
            }
            
            $tr->commit();

        }catch ( Exception $e ) {
            $tr->rollback();
            print_r($this->errMsg('职位信息删除失败'));
            return;
        }

        $message = array(
                    'code'=>0,
                    'data'=>array('id'=>$data['id'])     
                    );
        $json = CJSON::encode($message);
        print_r($json);

    }

    /*
     ***返回错误信息
    */

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