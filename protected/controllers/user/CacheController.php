<?php
      
     class CacheController extends Controller 
     {
     	 public function actionProperty()
     	 {     	 	 
     	 	 $property = CacheService::getInstance()->CompanyProperty();
             $SearchJson='{"code":0,"data":'.CJSON::encode($property).'}';
             print  $SearchJson;
     	 }

        public function actionDegree()
     	 {
     	 	 $degree = CacheService::getInstance()->degree();
             $degreeJson='{"code":0,"data":'.CJSON::encode($degree).'}';
             print  $degreeJson;
     	 }


     	 public function actionTrade()
     	 {
             if(isset($_GET['Id'])){
                 $ss = $_GET['Id'];
                 if($ss == 'other')
                     $cityList = CacheService::getInstance()->CompanyTrade();
             }
     	 	 $trade = CacheService::getInstance()->CompanyTrade();
             $SearchJson='{"code":0,"data":'.CJSON::encode($trade).'}';
             print  $SearchJson;
     	 }



     	 

        
         public function actionPositionType()
     	 {     	 	 
     	 	 $type = CacheService::getInstance()->PositionType();
     	 	 
     	 	//$this->smarty->assign('type',  $type);
     	  //  $this->smarty->display('cache/cc.html');
               //$list1='{"list":'. json_encode($type).'"}'.'<br />';
               //$list2='{"list":'. CJSON::encode($type).'"}'.'<br />';
               //$list3='{"list":'. CJavaScript::jsonEncode($type).'"}';
             // print $list1;
              //print $list2;
              //print $list3;
             $SearchJson='{"code":0,"data":'.CJSON::encode($type).'}';
             print  $SearchJson;
     	 }

         public function actionPositionSpecialty()
         {
             $position = CacheService::getInstance()->PositionSpecialty();
             $positionJson='{"code":0,"data":'.CJSON::encode($position).'}';
             print  $positionJson;
         }
       public function actionProvince()
       {
           //if(isset($_GET['Id'])){
               //$ss = $_GET['Id'];
               //if($ss == 'Lmore')
                   $provList = CacheService::getInstance()->Province();

               //else{

              // }

           //}
           $SearchJson='{"code":0,"data":'.CJSON::encode($provList).'}';
           print  $SearchJson;
       }

         public function actionLinkProvince()
         {
             //$provList = CacheService::getInstance()->Province();
             $provList = Province::model()->with('city')->findAll();
             foreach ($provList as $key => $value) {
                 if(isset($value->id))
                     $prov[$key]['proviceId'] = $value->id;
                 else
                     $prov[$key]['proviceId'] = "无";
                 if(isset($value->name))
                     $prov[$key]['proviceName'] = $value->name;
                 else
                     $prov[$key]['proviceName'] = "无";
             }
             $SearchJson='{"code":0,"data":'.CJSON::encode($prov).'}';
             print  $SearchJson;
         }

         public function actionLinkCity($provinceId)
         {
             //$cityList = CacheService::getInstance()->City();
             $cityList = City::model()->with('province')->findAllByAttributes(array('province_id'=>$provinceId));
             foreach ($cityList as $key => $value) {
                 if(isset($value->id))
                     $prov[$key]['cityId'] = $value->id;
                 else
                     $prov[$key]['cityId'] = "无";
                 if(isset($value->name))
                     $prov[$key]['cityName'] = $value->name;
                 else
                     $prov[$key]['cityName'] = "无";
             }
             $SearchJson='{"code":0,"data":'.CJSON::encode($prov).'}';
             print  $SearchJson;
         }

       public function actionCity()
       { 
           
              $list = CacheService::getInstance()->City();
              var_dump($list);
              
       }


           

     }
?>