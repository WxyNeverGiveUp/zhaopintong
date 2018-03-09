<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/7/21
 * Time: 20:48
 */
class PublishController extends Controller
{
    //过滤器
    public function filters()
    {
        return array(
            array(
                'application.filters.RecruitmentFilter',
            ),
        );
    }

    public function actionToCancel($id)
    {
    $sql = 'select id,name,company_id from {{position}} where id ='.$id;
    $position = Position::model()->findBySql($sql);
    $this->smarty->assign('position',$position);
    $this->smarty->display('recruitEntrance/recruitment-info/recruitment-info.html');
    }

    public function actionCancel()
    {
        $cancelPosition = new CancelPosition();
        $cancelPosition->position_id = $_POST['positionId'];
        $cancelPosition->cancel_reason = $_POST['cancelReason'];
//        echo "id".$cancelPosition->position_id;
//        echo "取消原因".$cancelPosition->cancel_reason;
        if($cancelPosition->position_id!=null&&$cancelPosition->position_id>0&&$cancelPosition->cancel_reason!=null&&""!=$cancelPosition->cancel_reason){
            $cancelPosition->save();
        }

        $currentPage = $_POST['currentPage'];
        $keyword  =$_POST['keyword'];
//        echo "当前页".$currentPage;
//        echo "关键字".$keyword;
        if($cancelPosition->position_id!=null&&$cancelPosition->position_id>0){
            $sql = "select * from {{position}} where id=".$cancelPosition->position_id;
            $position = Position::model()->findBySql($sql);
            $position->is_publish = 0;
            $position->save();
        }
        $this->redirect($this->createUrl("recruitEntrance/recruitmentInfo/recruitment/index?currentPage=$currentPage&keyword=$keyword"));//分页怎么办，不是第一页，我需返回的地址

    }
    //发布招聘信息
    public function actionJsonPublish(){
     $positionId = $_GET['positionId'];
     if($positionId!=null&&$positionId>0){
         $sql = "select * from {{position}} where id=".$positionId;
         $position = Position::model()->findBySql($sql);
         $position->is_publish=1;
         $position->save();
         $code = 1;
         $result ='{"code":'.$code.'}';
         echo $result;
     }else{
         $code = 0;
         $result ='{"code":'.$code.'}';
         echo $result;
     }


    }

}