<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
/**
* 
*/
class CompanyController extends Controller
{
	 public function  actionSearchJson($user_id=0,$page=0, $searchWord = null, $propertyId = 0, $locationId = 0, $industryId = 0, $isEliteFirm = 0,$isEliteSchool = 0,
                                      $isJoinBigRecruitment = 0, $isJoinRecruitmentWeek = 0,$heatSort=0,$timeSort=1){
        $companyListOnePage = CompanyService::getInstance()->search2($page, $searchWord,$propertyId, $locationId, $industryId, $isEliteFirm,$isEliteSchool,$isJoinBigRecruitment,$isJoinRecruitmentWeek,$heatSort,$timeSort);

        
        $CompanyUser = CompanyUser::model()->findAllByAttributes(array('user_id'=>$user_id));
        foreach ($CompanyUser as $key => $value) {
            foreach ($companyListOnePage['list'] as $k => $v) {
                if ($v['id']==$value->company_id) {
                    $companyListOnePage['list'][$k]['isfollow'] = 0;
                }
            }
        }

        $SearchJson='{"code":0,"data":'.CJSON::encode($companyListOnePage['list']).',"dataCount":"'.$companyListOnePage['recordCount'].'"}';
        print  $SearchJson;
    }

    public function actionCompanyProperty(){
    	$Array = array();

		$PropertyInfo = CompanyProperty::model()->findAll();
		foreach ($PropertyInfo as $key => $value) {
			$property[$key]['id'] = $value->id;
			$property[$key]['name'] = $value->name;
		}

		$Array['property'] = $property;

		$Array = CJSON::encode($Array);
		print($Array);
    }

    public function actionDetail($id,$user_id=0){
        $id = (int)$id;
        $companyInfo = CompanyService::getInstance()->detail($id);
        
        $positionInfo = CompanyService::getInstance()->getPositionByCompanyId($id);

        $positionUser = PositionUser::model()->findAllByAttributes(array('user_id'=>$user_id));
        $ResumeUser = ResumeUser::model()->findAllByAttributes(array('user_id'=>$user_id));

        if ($positionInfo['list']) {
            foreach ($positionInfo['list'] as $key => $value) {
                if ($value['messageSource']==1) {
                $positionInfo['list'][$key]['messageSource'] = '东北师大';
                }else if ($value['messageSource']==2) {
                    $positionInfo['list'][$key]['messageSource'] = '六所部属';
                }else if ($value['messageSource']==3) {
                    $positionInfo['list'][$key]['messageSource'] = '其它';
                }
                foreach ($positionUser as $k => $v) {
                    if ($value['id']==$v->position_id) {
                        $positionInfo['list'][$key]['collection'] = 1;
                    }else{
                        $positionInfo['list'][$key]['collection'] = 0;
                    }
                }
            }

            foreach ($positionInfo['list'] as $key => $value) {
                foreach ($ResumeUser as $k => $v) {
                    if ($value['id']==$v->position_id) {
                        $positionInfo['list'][$key]['sended'] = 1;
                    }else{
                        $positionInfo['list'][$key]['sended'] = 0;
                    }
                }
            }
        }

        
        
        $recruitInfo = $this->CTJson($id,$user_id);

        $allComment = $this->actionCommentJson($id,2);
        $staffComment = $this->actionCommentJson($id,1);
        $notstaffComment = $this->actionCommentJson($id,0);

        $interviewExperience = $this->actionInterviewJson($id);

        $Array['company'] = $companyInfo;
        $Array['position'] = $positionInfo['list'];
        $Array['recruit'] = $recruitInfo;
        $Array['allComment'] = $allComment;
        $Array['staffComment'] = $staffComment;
        $Array['notstaffComment'] = $notstaffComment;
        $Array['interviewExperience'] = $interviewExperience;

        print(CJSON::encode($Array));


    }


    public function CTJson($id,$user_id=0){

        $list = CTService::getInstance()->searchForFront($page=0,$searchWord=4,$timeId=0,$preachTypeId=0,$industryId=0,$id,0,0);
        foreach ($list as $key => $value) {
            if(isset($value['id']))
                $careerTalk[$key]['id'] = $value['id'];
            else
                $careerTalk[$key]['id'] = "0";
            if(isset($value['time'])){
                $time = $value['time'];
                $month = date('m',strtotime($time));
                $day = date('d',strtotime($time));
                $weekArray = array("日", "一", "二", "三", "四", "五", "六");
                $week = "周" . $weekArray[date("w", strtotime($time))];
                $careerTalk[$key]['month'] = $month;
                $careerTalk[$key]['date'] = $day;
                $careerTalk[$key]['week'] = $week;
                $careerTalk[$key]['time'] = date('H:i',strtotime($time));
            }
            else{
                $careerTalk[$key]['month'] = "无";
                $careerTalk[$key]['date'] = "无";
                $careerTalk[$key]['week'] = "无";
                $careerTalk[$key]['time'] = "无";
            }
            if(isset($value['name']))
                $careerTalk[$key]['company'] = $value['name'];
            else
                $careerTalk[$key]['company'] = "无";
            if(isset($value['preachType'])){
                if($value['preachType']==1)
                    $careerTalk[$key]['preachType'] = '视频宣讲';
                elseif($value['preachType']==2)
                    $careerTalk[$key]['preachType'] = '实地宣讲';
                else
                    $careerTalk[$key]['preachType'] = '外地宣讲';
            }
            else
                $careerTalk[$key]['preachType'] = "无";
            if(isset($value['location']))
                $careerTalk[$key]['location'] = $value['location'];
            else
                $careerTalk[$key]['location'] = "无";
            if(isset($value['isOverdue']))
                $careerTalk[$key]['isOverdue'] = $value['isOverdue'];
            else
                $careerTalk[$key]['isOverdue'] = "无";
            //if(isset($value['isEnroll'])){
            $caUser = CareerTalkUser::model()->find(array(
                'condition' => 'career_talk_id=:id AND user_id=:userId',
                'params' => array(':id'=>$value['id'],':userId'=>$user_id),
            ));
            $concerned = $caUser?1:0;
            $careerTalk[$key]['isEnroll'] = $concerned;
        }
        return $careerTalk;
    }

    public function actionCommentJson($id,$commentTypeId){
        // if(isset($_GET['commentTypeId']))
        //     $commentTypeId = $_GET['commentTypeId'];
        // else
        //     $commentTypeId=1;
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
        // $list_all = CompanyComment::model()->findAll($criteria);
        // $pageSize = 100;
        // $currentPage = Yii::app()->request->getParam('currentPage',$page1);
        // $criteria -> limit = $pageSize;
        // $criteria -> offset = ($currentPage-1)*$pageSize;
        // $recordCount = count($list_all);
        $criteria->with = array('userdetail');
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
        return $comment;
    }

    public function actionInterviewJson($id){
        // $page1 = $_GET['page1'];
        $criteria = new CDbCriteria;
        $criteria->condition='company_id=:companyId AND is_ok=1';
        $criteria->params=array(':companyId'=>$id);
        $list_all = InterviewExperience::model()->findAll($criteria);
        // $pageSize = 5;
        // $currentPage = Yii::app()->request->getParam('currentPage',$page1);
        // $criteria -> limit = $pageSize;
        // $criteria -> offset = ($currentPage-1)*$pageSize;
        // $recordCount = count($list_all);
        // $criteria->with = array('userdetail','ispraise','praiseCount');
        $criteria->with = array('userdetail');
        $list= InterviewExperience::model()->findAll($criteria);
        // 重组不同表的数据
        foreach ($list as $key => $value) {
            if(isset($value->id))
                $interviewExperience[$key]['experienceId'] = $value->id;
            else
                $interviewExperience[$key]['experienceId'] = "无";
            if(isset($value->userdetail->head_url))
                $interviewExperience[$key]['imgLinks'] = $value->userdetail->head_url;
            else
                $interviewExperience[$key]['imgLinks'] = "无";
            if(isset($value->is_public)){
                if($value->is_public==0)
                    $interviewExperience[$key]['name'] = '匿名用户';
                else
                $interviewExperience[$key]['name'] = $value->userdetail->realname;
            }
            else
                $interviewExperience[$key]['name'] = "无";
            if(isset($value->city))
                $interviewExperience[$key]['location'] = $value->city;
            else
                $interviewExperience[$key]['location'] = "无";
            if(isset($value->interview_position))
                $interviewExperience[$key]['position'] = $value->interview_position;
            else
                $interviewExperience[$key]['position'] = "无";
            if(isset($value->is_getjob))
                $interviewExperience[$key]['isAccept'] = $value->is_getjob;
            else
                $interviewExperience[$key]['isAccept'] = "无";
            if(!empty($value->ispraise))
                $interviewExperience[$key]['isPraise'] = 1;
            else
                $interviewExperience[$key]['isPraise'] = 0;
            if(isset($value->interview_date))
                $interviewExperience[$key]['interviewTime'] = $value->interview_date;
            else
                $interviewExperience[$key]['interviewTime'] = "无";
            if(isset($value->interview_round))
                $interviewExperience[$key]['interviewRounds'] = $value->interview_round;
            else
                $interviewExperience[$key]['interviewRounds'] = "无";
            if(isset($value->description))
                $interviewExperience[$key]['experience'] = $value->description;
            else
                $interviewExperience[$key]['experience'] = "无";
            if(isset($value->addtime))
                $interviewExperience[$key]['shareTime'] = $value->addtime;
            else
                $interviewExperience[$key]['shareTime'] = "无";
            if(isset($value->praiseCount))
                $interviewExperience[$key]['praiseNum'] = $value->praiseCount;
            else
                $interviewExperience[$key]['praiseNum'] = "无";
        }
        return $interviewExperience;
        // if($_GET['sortId']==1&&$interviewExperience!=null) {
        //     usort($interviewExperience, function ($a, $b) {
        //         return $b['praiseNum'] - $a['praiseNum'];
        //     });
        // }
        // $listJson='{"data":'.CJSON::encode($interviewExperience).',"dataCount":"'.$recordCount.'"}';
        // print $listJson;
    }
	
}