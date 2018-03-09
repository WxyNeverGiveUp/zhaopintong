<?php
/**
 * Created by PhpStorm.
 * User: lix
 * Date: 2015/10/30
 * Time: 1:06
 */

class LedController extends Controller{
    //Led展示
    public function actionShow(){
        $type = $_GET['type'];
        $ledNotice = LedNotice::model()->findByPk(1);
        if(substr_count($ledNotice->content,"</p>")>1)
            $this->smarty->assign('isOneLine',0);
        else
            $this->smarty->assign('isOneLine',1);
        $this->smarty->assign('ledNotice',$ledNotice);
        $toDayPreachCount = count(CTService::getInstance()->searchForFront(null,1,0,2,0,0,0,0));
        $toDayPreach = CTService::getInstance()->searchForFront(null,1,0,2,0,0,0,0);
        $recentPreach = CTService::getInstance()->searchForFront(null,5,0,2,0,0,0,0);
        $recentPreachCount = count(CTService::getInstance()->searchForFront(null,5,0,2,0,0,0,0));
        $recentPreachs=array();
        for($i=0;$i<count($recentPreach);$i++){
            if(CareerTalk::model()->find(array('condition'=>'id = :id AND place like :word','params'=>array(':id'=>$recentPreach[$i]['id'],':word'=>'%就业中心%')))!=null)
                $recentPreach[$i]['pType'] = '本部';
            elseif(CareerTalk::model()->find(array('condition'=>'id = :id AND place like :word','params'=>array(':id'=>$recentPreach[$i]['id'],':word'=>'%校史馆%')))!=null)
                $recentPreach[$i]['pType'] = '本部';
            elseif(CareerTalk::model()->find(array('condition'=>'id = :id AND place like :word','params'=>array(':id'=>$recentPreach[$i]['id'],':word'=>'%净月%')))!=null)
                $recentPreach[$i]['pType'] = '净月';
            else
                $recentPreach[$i]['pType'] = '外地';
            $recentPreachs[date('m月d日',strtotime($recentPreach[$i]['time']))][]=$recentPreach[$i];
        }
        $this->smarty->assign('toDayPreachCount',$toDayPreachCount);
        $this->smarty->assign('toDayPreach',$toDayPreach);
        $this->smarty->assign('recentPreachs',$recentPreachs);
        $this->smarty->assign('recentPreachCount',$recentPreachCount);
        if($type==1)
            $this->smarty->display('index/wideLed.html');
        else
            $this->smarty->display('index/narrowLed.html');
    }
}