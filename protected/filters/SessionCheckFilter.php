<?php
   class SessionCheckFilter extends CFilter
   {

    protected function preFilter($filterChain)
    {

         if (isset($_SESSION['user_id']) && (!empty($_SESSION['user_id'])))
         {
            $filterChain->run();
         }
         else
         {
             echo "(2, 用户id获取失败,您需要重新登录)";
             $smarty = new smarty();
             $smarty->display('user/login.html');
             //$this->redirect(array('user/tologin'));
            // $this->redirect('user/tologin');
            // $this->runController('user/tologin');
             //$this->smarty->display('user/login.html');
             //Controller::redirect(array('user/tologin'));
         }
    }
}
?>