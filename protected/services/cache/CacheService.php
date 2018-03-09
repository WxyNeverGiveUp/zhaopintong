<?php
/**
 * 作者：陈永暖
 * 功能：将数据存到memcached缓存里
 * 联系方式：18680449852
 */

    class CacheService
    {
       
       static private $instance = null; 

        public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new CacheService();
        }
        return self::$instance;
       }

       //将t_company_property的信息存到缓存里     
        public function companyProperty()
        {
              if(Yii::app()->cache->get('property')===false)
               {
                  $sql="select SUM(status) from {{company_property}}";
                  $property = CompanyProperty::model()->findAll();
                  Yii::app()->cache->set('property', $property,12222,new CDbCacheDependency($sql));
                }
                else
                {
                       $property= Yii::app()->cache->get('property');
                 }
        	   return $property;
        }

       //将t_company_trade的信息存到缓存里 
        public function companyTrade()
        {     
               if(Yii::app()->cache->get('trade')===false)
               {
                  $sql="select SUM(status) from {{company_trade}}";
                  $trade = CompanyTrade::model()->findAll();
                  Yii::app()->cache->set('trade', $trade,12222,new CDbCacheDependency($sql));                                                 
                }
                else
                {                	                  	 
                       $trade= Yii::app()->cache->get('trade');
                       
                 }        	                             
        	    return $trade;
        }


         //将t_position_type的信息存到缓存里 
        public function positionType()
        {   
                  	 
        	   if(Yii::app()->cache->get('type')===false)
                 {
                     $sql="select SUM(status) from {{position_type}}";
                     $type = PositionType::model()->findAll();
                     Yii::app()->cache->set('type', $type,12222, new CDbCacheDependency($sql));
                 }
                 else
                 {
                         $type= Yii::app()->cache->get('type');
                 }
                
        	    return $type;
        }

        //将t_position_specialty的信息存到memcached里 
         public function positionSpecialty()
         {        	 
        	   if(Yii::app()->cache->get('specialty')===false)
                 {
                     $sql="select SUM(status) from {{position_specialty}}";
                     $specialty = PositionSpecialty::model()->findAll();
                     Yii::app()->cache->set('specialty', $specialty, 12222,new CDbCacheDependency($sql));
                 }
                 else
                 {
                         $specialty= Yii::app()->cache->get('specialty');
                 }
        	    return $specialty;
         }
         

         //将t_degree的信息存到memcached里 
         public function degree()
         {        	 
        	   if(Yii::app()->cache->get('degree')===false)
                 {
                     $sql="select SUM(status) from {{degree}}";
                     $degree = Degree::model()->findAll();
                     Yii::app()->cache->set('degree', $degree,12222, new CDbCacheDependency($sql));
                 }
                 else
                 {
                         $degree= Yii::app()->cache->get('degree');
                 }
        	    return $degree;
         }

         //将t_study_specialty的信息存到memcached里 
         public function studySpecialty()
         {        	 
        	   if(Yii::app()->cache->get('study')===false)
                 {
                     $sql="select SUM(status) from {{study_specialty}}";
                     $criteria2=new CDbCriteria;
                     $criteria2->select='id,name';
                     $criteria2->distinct = true;
                     $criteria2->condition='parent_id=0';
                     $study  = StudySpecialty::model()->findAll($criteria2);
                     Yii::app()->cache->set('study', $study, 12222,new CDbCacheDependency($sql));
                 }
                 else
                 {
                         $study= Yii::app()->cache->get('study');
                 }
        	    return $study;
         }

        public function allStudySpecialty()
        {
            if(Yii::app()->cache->get('allStudy')===false)
            {
                $sql="select SUM(status) from {{study_specialty}}";
                $criteria2=new CDbCriteria;
                $criteria2->select='id,name';
                $criteria2->distinct = true;
                $criteria2->condition='parent_id!=0';
                $study  = StudySpecialty::model()->findAll($criteria2);
                Yii::app()->cache->set('allStudy', $study,12222, new CDbCacheDependency($sql));
            }
            else
            {
                $study= Yii::app()->cache->get('allStudy');
            }
            return $study;
        }

        public function childSpecialty()
        {
            if(Yii::app()->cache->get('childStudy')===false)
            {
                $criteria=new CDbCriteria;
                $criteria->select='id,name';
                $criteria->distinct = true;
                $criteria->condition='parent_id=0';
                $model  = StudySpecialty::model()->findAll($criteria);
                foreach($model as $m){
                    $mode = StudySpecialty::model()->findAllByAttributes(array('parent_id'=>$m->id));
                    $arr[$m->id]=$mode;
                }
                $sql="select SUM(status) from {{study_specialty}}";
                Yii::app()->cache->set('childStudy',$arr,12222,new CDbCacheDependency($sql));
            }
            else
            {
                $arr= Yii::app()->cache->get('childStudy');
            }
            return $arr;
        }


        //t_param_province的信息存到memcached里
         public function province()
         {
              if(Yii::app()->cache->get('province')===false)
                 {
                     $sql="select SUM(status) from {{param_province}}";
                     $model = Province::model()->findAll();
                     Yii::app()->cache->set('province',$model, 12222,new CDbCacheDependency($sql));
                 }
                 else
                 {
                          $model= Yii::app()->cache->get('province');
                 }
              return  $model;
         }


        public function allCity()
        {
            if(Yii::app()->cache->get('allCity')===false)
            {
                $sql="select SUM(status) from {{param_city}}";
                $model = City::model()->findAll();
                Yii::app()->cache->set('allCity',$model, 12222,new CDbCacheDependency($sql));
            }
            else
            {
                $model= Yii::app()->cache->get('allCity');
            }
            return  $model;
        }


         public function city()
         {
           
            if(Yii::app()->cache->get('city')===false)
            {
             $model = Province::model()->findAll();
                foreach($model as $m){
                    $mode = City::model()->findAllByAttributes(array('province_id'=>$m->id));
                    $arr[$m->id]=$mode;
                }
               $sqll="select SUM(status) from {{param_city}}";
               Yii::app()->cache->set('city',$arr, 12222,new CDbCacheDependency($sqll));
           }
           else
           {
                  $arr= Yii::app()->cache->get('city');
           }
             return $arr;
         }

        




    }
?>