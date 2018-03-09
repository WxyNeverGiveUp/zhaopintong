<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 2015/5/12
 * Time: 15:05
 */

class CompanyCommentController extends Controller{
    public function actionList($id){
        //TODO:获取评论列表或评论综合评价
        $avgComment = Yii::app()->db->createCommand()
            ->select('AVG(whole_comment)*20 avg_whole_comment,AVG(is_agree_leader)*20 avg_is_agree_leader,AVG(work_grow)*20 avg_work_grow,AVG(skill_grow)*20 avg_skill_grow,AVG(work_condition)*20 avg_work_condition,AVG(work_pressure)*20 avg_work_pressure,AVG(company_future)*20 avg_company_future,whole_comment,is_public,identity,content')
            ->from('t_company_comment')
            ->where('company_id=:companyId', array(':companyId'=>$id))
            ->queryRow();
        $commentList  = CompanyComment::model()->findAll(array(
            'condition' => 'company_id=:companyId AND is_ok=1',
            'params' => array(':companyId'=>$id),
        ));
        $company = Company::model()->findByPk($id);
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $allNum = CompanyComment::model()->count(array(
            'condition' => 'company_id=:companyId AND is_ok=1',
            'params' => array(':companyId'=>$id),
        ));
        $employeeNum = CompanyComment::model()->count(array(
            'condition' => 'company_id=:companyId and is_employee=1 AND is_ok=1',
            'params' => array(':companyId'=>$id),
        ));
        $notEmployeeNum = CompanyComment::model()->count(array(
            'condition' => 'company_id=:companyId and is_employee=0 AND is_ok=1',
            'params' => array(':companyId'=>$id),
        ));
        $concerned = $companyUser?1:0;
        $this->smarty->assign('recordCount',count($commentList));
        $this->smarty->assign('avgComment',$avgComment);
        $this->smarty->assign('companyId',$id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('allNum',$allNum);
        $this->smarty->assign('employeeNum',$employeeNum);
        $this->smarty->assign('notEmployeeNum',$notEmployeeNum);
        $current="current";
        $this->smarty->assign('comment',$current);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/companyComment/comment-new.html');
    }

    public function actionJson($id,$page){
        if(isset($_GET['commentTypeId']))
            $commentTypeId = $_GET['commentTypeId'];
        else
            $commentTypeId=1;
        if(isset($_GET['sortId']))
            $sortId = $_GET['sortId'];
        else
            $sortId=0;
        $criteria = new CDbCriteria;
        $isAllCondition = ' AND is_employee=:is_employee';
        $isAllParams = array(':is_employee'=>$commentTypeId);
        $query = 'company_id=:companyId AND is_ok=1';
        $queryParam = array(':companyId'=>$id);
        if($commentTypeId!=2) {
            $query .= $isAllCondition;
            $queryParam = array_merge($queryParam,$isAllParams);
        }
        $criteria->condition=$query;
        $criteria->params= $queryParam;
        if($sortId==0){
            $criteria->order = 'whole_comment DESC';
        }
        $list_all = CompanyComment::model()->findAll($criteria);
        $pageSize = 5;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        $recordCount = count($list_all);
        $criteria->with = array('userdetail','ispraise','praiseCount');
        $list= CompanyComment::model()->findAll($criteria);  //记录分页
        // 重组不同表的数据
        foreach ($list as $key => $value) {
            if(isset($value->id))
                $comment[$key]['id'] = $value->id;
            else
                $comment[$key]['id'] = "无";
            if(isset($value->userdetail->head_url))
                $comment[$key]['imgLinks'] = $value->userdetail->head_url;
            else
                $comment[$key]['imgLinks'] = "无";
            if(isset($value->is_public)&&isset($value->userdetail)){
                if($value->is_public==0)
                    $comment[$key]['userName'] = '匿名用户';
                else
                    $comment[$key]['userName'] = $value->userdetail->realname;
            }
            else
                $comment[$key]['userName'] = "无";
            if(isset($value->is_employee)){
                if($value->is_employee==1)
                $comment[$key]['userType'] = '在职员工';
                else
                    $comment[$key]['userType'] = '已退休';
            }
            else
                $comment[$key]['userType'] = "无";
            if(isset($value->whole_comment))
                $comment[$key]['width'] = ($value->whole_comment*20).'%';
            else
                $comment[$key]['width'] = "无";
            if(isset($value->content))
                $comment[$key]['comment'] = $value->content;
            else
                $comment[$key]['comment'] = "无";
            if(!empty($value->ispraise))
                $comment[$key]['isPraise'] = 1;
            else
                $comment[$key]['isPraise'] = 0;
            if(isset($value->praiseCount))
                $comment[$key]['praiseNum'] = $value->praiseCount;
            else
                $comment[$key]['praiseNum'] = "无";
            if(isset($value->addtime))
                $comment[$key]['time'] = $value->addtime;
            else
                $comment[$key]['time'] = "无";
        }
        if($sortId==1&&$comment!=null) {
            usort($comment, function ($a, $b) {
                return $b['praiseNum'] - $a['praiseNum'];
            });
        }
        $list2='{"data":'.CJSON::encode($comment).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    public function actionCreate($id){
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$id,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$id),
        ));
        $company = Company::model()->findByPk($id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('concerned',$concerned);
        $this->smarty->assign('concernedNum',$concernedNum);
        $current="current";
        $this->smarty->assign('comment',$current);
        $hasDemand = CareerTalk::model()->findAll(array(
            'condition' => 'company_id=:id AND url!=""',
            'params' => array(':id'=>$id)
        ));
        $flag = $hasDemand?1:0;
        $this->smarty->assign('flag',$flag);
        $hasRemote = RemoteInterview::model()->findAll(array(
            'condition' => 'company_id=:id',
            'params' => array(':id'=>$id)
        ));
        $flagRe = $hasRemote?1:0;
        $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/companyComment/i-comment-new.html');
    }

    public function actionAdd(){
        $comment = new CompanyComment();
        $comment->is_employee = $_POST['is_employee'];
        $comment->whole_comment = $_POST['whole_comment'];
        $comment->identity = $_POST['identity'];
        $comment->is_agree_leader = $_POST['is_agree_leader'];
        $comment->is_public = 1;
        $comment->company_id = $_POST['companyId'];
        //TODO:从session获取、验证；
        $comment->user_id = Yii::app()->session['user_id'];
        $comment->save();
        $this->redirect($this->createUrl("company/companyComment/createMore/companyId/".$_POST['companyId']."/commentId/".$comment->getPrimaryKey()));
    }

     public function actionCreateMore($companyId,$commentId){
        $companyUser = CompanyUser::model()->find(array(
            'condition' => 'company_id=:companyId AND user_id=:userId',
            'params' => array(':companyId'=>$companyId,':userId'=>Yii::app()->session['user_id']),
        ));
        $concerned = $companyUser?1:0;
        $company = Company::model()->findByPk($companyId);
        $concernedNum = CompanyUser::model()->count(array(
            'condition' => 'company_id=:companyId',
            'params' => array(':companyId'=>$companyId),
        ));
        $this->smarty->assign('concernedNum',$concernedNum);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('commentId', $commentId);
        $this->smarty->assign('concerned',$concerned);
        $current="current";
        $this->smarty->assign('comment',$current);
        $hasDemand = CareerTalk::model()->findAll(array(
             'condition' => 'company_id=:id AND url!=""',
             'params' => array(':id'=>$companyId)
         ));
         $flag = $hasDemand?1:0;
         $this->smarty->assign('flag',$flag);
         $hasRemote = RemoteInterview::model()->findAll(array(
             'condition' => 'company_id=:id',
             'params' => array(':id'=>$companyId)
         ));
         $flagRe = $hasRemote?1:0;
         $this->smarty->assign('flagRe',$flagRe);
        $this->smarty->display('company/companyComment/detail-comment.html');
    }

    public function actionAddMore(){
        $comment = CompanyComment::model()->findByPk($_POST['commentId']);
        $comment->skill_grow = $_POST['skill_grow'];
        $comment->work_condition = $_POST['work_condition'];
        $comment->work_pressure = $_POST['work_pressure'];
        $comment->company_future = $_POST['company_future'];
        $comment->content = $_POST['content'];
        //TODO:从session获取、验证；
        $comment->user_id = Yii::app()->session['user_id'];
        $comment->addtime = date('Y-m-d H:i:s',time());
        $comment->save();
        $this->redirect($this->createUrl("company/companyComment/list/id/".$comment->company_id));
    }

    public function actionToEdit($id){
        $comment = CompanyComment::model()->findByPk($id);
        $company = Company::model()->findByPk($comment->company_id);
        $this->smarty->assign('company', $company);
        $this->smarty->assign('contacts', $comment);
        $this->smarty->display('company/companyComment/edit.html');
    }

    public function actionEdit($id){
        $comment = CompanyComment::model()->findByPk($id);
        $comment->whole_comment = $_POST['wholeComment'];
        $comment->is_agree_leader = $_POST['isAgreeLeader'];
        $comment->is_public = $_POST['isPublic'];
        $comment->work_grow = $_POST['workGrow'];
        $comment->skill_grow = $_POST['skillGrow'];
        $comment->work_condition = $_POST['workCondition'];
        $comment->work_pressure = $_POST['workPressure'];
        $comment->company_future = $_POST['companyFuture'];
        $comment->save();
        $this->actionList($comment->company_id);
    }

    public function actionDel($id){
        $comment = CompanyContacts::model()->findByPk($id);
        $companyId = $comment->company_id;
        if(!empty($comment))
            $comment->delete();
        $this->actionList($companyId);
    }

    public function actionPraise($commentId,$isPraise){
        $re = CompanyComment::model()->findByPk($commentId);
        if($re==null){
            //$this->actionList($experienceId);
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $coUser = CompanyCommentUser::model()->find(array(
            'condition' => 'comment_id=:commentId AND user_id=:userId',
            'params' => array(':commentId'=>$commentId,':userId'=>$userId),
        ));

        if($isPraise==1) {
            $commentUser = new CompanyCommentUser();
            $commentUser->comment_id= $commentId;
            $commentUser->user_id = $userId;
            $commentUser->save();
        }
        else{
            $coUser->delete();
        }
        $number = CompanyCommentUser::model()->count(array(
            'condition' => 'comment_id=:coId',
            'params' => array(':coId'=>$commentId),
        ));
        $listJson='{"code":"0","data":{"praiseNum":"'.$number.'"}}';
        print $listJson;
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter + create,add,createMore,addMore,praise')
        );
    }
}