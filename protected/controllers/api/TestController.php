<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
date_default_timezone_set('Asia/Shanghai');
/**
* 
*/
class TestController extends Controller
{
	public function actionAnnounceList(){
        $Array = array();
        $offset = $_POST['offset'];
        // $offset = $HTTP_RAW_POST_DATA['offset'];
        // $data = json_decode(file_get_contents("php://input"));
        // $offset = $data->offset;

        $cri = new CDbCriteria();
        $cri->select = 'id,type_id,title,add_time';
        $cri->order = 't.id desc';
        $cri->limit = 15;
        $cri->offset = $offset;

        $announceModel = Announcement::model();
        $announceInfo = $announceModel->with('type')->findAll($cri);
        

        // 由于涉及到多个表的联查，字段分布在AR对象的不同属性，直接将AR对象转为json无法获得所有字段
        // 将查询信息重组为一个数组
        foreach ($announceInfo as $key => $value) {
            $Array[$key]['id'] = $value->id;
            $Array[$key]['type'] = $value->type->name;
            $Array[$key]['title'] = $value->title;
            $Array[$key]['add_time'] = $value->add_time;
            $Array[$key]['time'] = substr($value->add_time, 0,10);
        }

        $Array = CJSON::encode($Array);
        print_r($Array);
    }
	public function actionIndex(){
		$url = 'http://api.myjoin.cn/api/company/add';
        $data = array('username'=>'chenh',
                      'password'=>md5('159753'),
                      'data'=>Array
                            (   
                                'name' => 'lalalchenhccc2',
                                'trade_id' => 7,
                                'full_address' => '4123123124123',
                                'phone' => '1111111111',
                                'type_id' => 1,
                                'property_id' => 3,
                                'postal_code' => '111111',
                                'email' => '11111111123',
                                'is_join_big_recruitment' => 0,
                                'is_join_recruitment_week' => 0,
                                'website' => '111111111',
                                'is_famous' => 0,
                                'video_url' => '12222222222',
                                'city_id' => 21,
                                'introduction' => '<p>12333333333333333lk</p>'
                            )
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

     public function actionPosition(){
        $url = 'http://api.myjoin.cn/api/position/update';
        $data = array('username'=>'chenh',
                      'password'=>md5('159753'),
                      'data'=>Array
                            (   
                                'id' => 10207,
                                'is_teacher' => 1,
                                'company_id' =>120,
                                'name' => '2123123123sdfff',
                                'type_id' => 2,
                                'type_id2' => 4,
                                'degree_id' => 2,
                                'specialty_id' => 2,
                                'recruitment_num' => 11,
                                'position_source' => 1,
                                'is_join_big_recruitment' => 0,
                                'is_join_recruitment_week' => 0,
                                'contactName' => '123kijjl5vb',
                                'cellphone' => 11111111,
                                'post' => 11111,
                                'telephone' => 11111111,
                                'contactEmail' => 11111111,
                                'position_duty' => '<p>111111111</p>',
                                'city_id' =>21
                            )
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


     public function actionCareerTalk(){
        $url = 'http://api.myjoin.cn/api/careerTalk/update';
        $data = array('username'=>'chenh',
                      'password'=>md5('159753'),
                      'data'=>Array
                            (   
                                'id' => 1809,
                                'company_id' => '3722',
                                'name' => 'qweqwe123',
                                'time' => '2016-04-21 19:53:26',
                                'place' => 'qweqwedddd',
                                'type' => '3',
                                'live_url' => '11111111',
                                'url' => '222222222222222',
                                'button' => '提交',
                                'description' => '<p>asdasfasdddddddddddd</p>',
                            )
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


 ?>