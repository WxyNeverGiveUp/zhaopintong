<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-8-9
 * Time: 上午2:32
 */

class SearchController extends Controller
{
    // 毕业生首页展示
    public function actionIndex(){
        $preach = $_POST['preach'];
        $company = $_POST['company'];
        $graduate = $_POST['graduate'];
        $recruit = $_POST['recruit'];
        $searchWord = trim($_POST['searchWord']);
        if($preach==1)
            Yii::app()->runController('careerTalk/careerTalk/list/search/'.$searchWord);
        elseif($company==1)
            Yii::app()->runController('company/company/list/search/'.$searchWord);
        elseif($graduate==1)
            Yii::app()->runController('graduate/graduate/index/search/'.$searchWord);
        elseif($recruit==1)
            Yii::app()->runController('position/position/list/search/'.$searchWord);
    }
}