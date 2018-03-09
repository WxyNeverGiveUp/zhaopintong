<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/12
 * Time: 15:05
 */

class CompanyCommentController extends Controller{
    public function actionList(){
        $commentList  = CompanyComment::model()->findAll();
        $this->smarty->assign('recordCount',count($commentList));
        $this->smarty->assign('current','companyComment');
        $this->smarty->display('admin/company/companyComment/list.html');
    }

    public function actionJson(){
        $page = $_GET['page1'];
        $criteria = new CDbCriteria;
        $list_all = CompanyComment::model()->findAll($criteria);
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria->order = 'is_ok,addtime DESC';
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $list= CompanyComment::model()->findAll($criteria);  //记录分页
        $list2='{"list":'.CJSON::encode($list).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }
    public function actionCheck($id){
        $comment = CompanyComment::model()->findByPk($id);
        $comment->is_ok=1;
        $comment->save();
        $this->actionList();
    }
    public function actionDel($id){
        $comment = CompanyComment::model()->findByPk($id);
        if(!empty($comment)) {
            $comment->delete();
            $this->actionList();
        }
    }
}