<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/8/7
 * Time: 14:55
 */
class RecruitmentFilter extends CFilter
{
    protected function preFilter ($filterChain)
    {
        if (isset($_SESSION['contact_id']) && (!empty($_SESSION['contact_id'])))
        {
            $filterChain->run();
        }
        else
        {
            echo "(2, 用户id获取失败,您需要重新登录)";
            Yii::app()->request->redirect("http://www.dsjyw.net/recruitEntrance/recruitEntrance/ToLogin");
        }
    }

//    protected function postFilter ($filterChain)
//    {
//        echo "-->RecruitmentFilter-->post";
//    }
}