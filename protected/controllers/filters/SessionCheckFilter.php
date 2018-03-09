<?php
/**
* 功能：利用过滤器，实现用户的登录
*/
   class SessionCheckFilter extends CFilter 
   {

    protected function preFilter($filterChain)
    {

         if (isset(yii::app()->session['user_id'])&&isset(yii::app()->session['username']))
         {
             $filterChain->run();
         }
         else
         {
             if (!Yii::app()->request->isAjaxRequest) {
                 Yii::app()->runController('site/notLogin');
              }
              else{
             $list='{"code":1,"errMsg":"'.'尚未登陆'.'"}';
             print $list;
              } 
         }
    }


}
?>