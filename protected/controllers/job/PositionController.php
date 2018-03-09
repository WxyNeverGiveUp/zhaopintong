<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-28
 * 收藏职位
 * Time: 下午9:56
 */

class PositionController extends Controller{

    public function actionIndex()
    {
        $userId=yii::app()->session['user_id'];
        $this->smarty->assign('userId',$userId);
        $current="current";
        $this->smarty->assign('position',$current);
        $concernNum = PositionUser::model()->countByAttributes(array('user_id'=>$userId));
        $this->smarty->assign('concernNum',$concernNum);
        $this->smarty->display('job/collect_position/collect_position.html');
    }


    public function actionJson($page=0){
        $offset = ($page-1)*5;
        $cri = new CDbCriteria();
        $cri->with = array('company','degree','positionspecialty','city','brightened','collection2');
        $cri->select = 'id,name,position_source,company_id';
        $conditions = "1=1 ";
        $params = array();
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = Position::model()->count($cri);
        $cri->limit = 8;
        $cri->offset = $offset;
        $cri->order = 't.entering_time DESC';
        $data = Position::model()->findAll($cri);
        $cri2 = new CDbCriteria();
        $cri2->with = array('company','degree','positionspecialty','city','brightened');
        $cri2->select = 'id,name,position_source,company_id';
        $conditions2 = "1=1 ";
        $params2 = array();
        $cri2->condition=$conditions2;
        $cri2->params=$params2;
        $recordCount2 = 8;
        $cri2->limit = 8;
        $cri2->offset = $offset;
        $cri2->order = 't.entering_time DESC';
        $data2 = Position::model()->findAll($cri2);
        if($data==null)
            $data = $data2;
        foreach ($data as $key => $value) {
            if ($key == 0)
                $position[$key]['isFirst'] = 1;
            if (isset($value->id))
                $position[$key]['id'] = $value->id;
            else
                $position[$key]['id'] = "无";
            if (isset($value->company_id))
                $position[$key]['companyId'] = $value->company_id;
            else
                $position[$key]['companyId'] = "无";
            if (isset($value->name))
                $position[$key]['name'] = $value->name;
            else
                $position[$key]['name'] = "无";
            if (isset($value->company->name))
                $position[$key]['companyName'] = $value->company->name;
            else
                $position[$key]['companyName'] = "无";
            if (isset($value->position_source)) {
                if ($value->position_source == '1')
                    $position[$key]['position_source'] = '东北师大';
                elseif ($value->position_source == '2')
                    $position[$key]['position_source'] = '6所部属';
                else
                    $position[$key]['position_source'] = '互联网';
            } else
                $position[$key]['position_source'] = "无";
            if (isset($value->city->name))
                $position[$key]['city'] = $value->city->name;
            else
                $position[$key]['city'] = "无";
            if (isset($value->degree->name))
                $position[$key]['degree'] = $value->degree->name;
            else
                $position[$key]['degree'] = "无";
            if (isset($value->positionspecialty->name))
                $position[$key]['specialty'] = $value->positionspecialty->name;
            else
                $position[$key]['specialty'] = "无";
            if (isset($value->brightened[0]->name))
                $position[$key]['brightspot1'] = $value->brightened[0]->name;
            else
                $position[$key]['brightspot1'] = "无";
            if (isset($value->brightened[1]->name))
                $position[$key]['brightspot2'] = $value->brightened[1]->name;
            else
                $position[$key]['brightspot2'] = "无";
            if (isset($value->concernCount))
                $position[$key]['focusNum'] = $recordCount;
            else
                $position[$key]['focusNum'] = 0;
            if (!empty($value->collection2))
                $position[$key]['isFocus'] = 0;
            else
                $position[$key]['isFocus'] = 1;
            $user = User::model()->findByPk(yii::app()->session['user_id']);
            if ($user->is_league == 1 || $user->is_league == 2) {
                if ($user->is_activated == 1)
                    $position[$key]['isActivated'] = 1;
                else
                    $position[$key]['isActivated'] = 0;
            }
            else
                $position[$key]['isActivated'] = 0;
            }
        $searchListOnePage = array();
        $searchListOnePage['list']=$position;
        if(Position::model()->findAll($cri)==null)
            $recordCount = $recordCount2;
        $searchListOnePage['recordCount']=$recordCount;
        $SearchJson='{"code":0,"data":'.CJSON::encode($searchListOnePage['list']).',"dataCount":"'.$searchListOnePage['recordCount'].'"}';
        print  $SearchJson;
    }

    public function actionToSendResume($id){
        $companyContact = CompanyContacts::model()->findByAttributes(array('company_id'=>Position::model()->findByPk($id)->company_id));
        //yii::app()->session['user_id'] = 1;

        // 从session中获取用户id
        $user_id = yii::app()->session['user_id'];

        // 获取简历id
        $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;

        // 获取基本信息
        $BasicInfo = UserDetail::model()->getList($user_id);
        if ($BasicInfo->gender) {
            $BasicInfo->gender = '男';
        }else{
            $BasicInfo->gender = '女';
        }

        // 获取联系方式
        $Contact = User::model()->getList($user_id);
        // 获取教育经历
        $StudyExperience =StudyExperience::model()->getList($user_id);
        // 获取证书信息
        $Certificate = Certificate::model()->getList($resume_id);
        // 获取校内职务信息
        $SchoolDuty = SchoolDuty::model()->getList($resume_id);
        // 获取校内奖励信息
        $SchoolAward = SchoolAwards::model()->getList($resume_id);
        // 获取语言能力信息
        $LanguagesAbility = LanguagesAbility::model()->getList($resume_id);
        // 获取实习经历信息
        $WorkExperience = WorkExperience::model()->getList($user_id);
        // 获取IT技能信息
        $ItSkill = ItSkill::model()->getList($resume_id);
        // 获取项目经历信息
        $ProjectExperience = ProjectExperience::model()->getList($resume_id);
        // 获取培训经历信息
        $TrainingExperience = TrainingExperience::model()->getList($resume_id);
        // 获取求职经历信息
        $JobExperience = JobExperience::model()->getList($resume_id);
        foreach ($JobExperience as $key => $value) {
            if ($value->type) {
                $value->type = '拿到offer';
            }else{
                $value->type = '参加笔试';
            }
        }

        $resume_id = Resume::model()->findByAttributes(array('user_id'=>$user_id))->id;
        $Letter = ApplicationLetter::model()->findAllByAttributes(array('resume_id'=>$resume_id));
        $this->smarty->assign('app_letter',$Letter);

        // 数据渲染至前端视图
        $this->smarty->assign('BasicInfo',$BasicInfo);
        $this->smarty->assign('ContactInfo',$Contact);
        $this->smarty->assign('StudyExperience',$StudyExperience);
        $this->smarty->assign('Certificate',$Certificate);
        $this->smarty->assign('SchoolDuty',$SchoolDuty);
        $this->smarty->assign('SchoolAward',$SchoolAward);
        $this->smarty->assign('LanguagesAbility',$LanguagesAbility);
        $this->smarty->assign('WorkExperience',$WorkExperience);
        $this->smarty->assign('ItSkill',$ItSkill);
        $this->smarty->assign('ProjectExperience',$ProjectExperience);
        $this->smarty->assign('TrainingExperience',$TrainingExperience);
        $this->smarty->assign('JobExperience',$JobExperience);
        $this->smarty->assign('companyContact', $companyContact);
        $this->smarty->display('job/collect_position/resume-preview.html');
    }

    public  function  actionSendResume(){
        $email = $_POST['contactEmail'];
        $addLetter = $_POST['addLetter'];
        $letterContent='';
        if(isset($_POST['letter']))
        $letterContent =$_POST['letter'];
        $file = CUploadedFile::getInstanceByName('url');
        if($file!=null){
        //文件保存路径
        $uploadPath ="assets/uploadFile/";

        //获取文件后缀名
        $extName = $file->getExtensionName();
        //给文件重命名
        $fileName = time().'.'.$extName;
        //保存文件
        $fileUrl = $uploadPath.$fileName;
        $file->saveAs($fileUrl);
        }
        $mail = Yii::App()->mail;
        $mail->IsSMTP();
        $mail->AddAddress($email);
        $mail->Subject = "简历"; //邮件标题
        $content = '求职信:'."\r\n";
        if($addLetter==null||$addLetter=='')
            $contentNew = $content.$letterContent;
        else
            $contentNew = $content.$addLetter;
        $mail->Body = $contentNew."\r\n"."\r\n"."\r\n简历:\r\n".$_POST['content']; //邮件内容
        if($file!=null){
        $mail->AddAttachment(Yii::getPathOfAlias('webroot').'/assets/uploadFile/'.$fileName);
        }
        if ($mail->send()) {
            $this->redirect($this->createUrl("job/position"));
        }

    }

    public function actionConcern($postId,$isEnroll){
        $position = Position::model()->findByPk($postId);
        if($position==null){
            $this->actionList();
            return;
        }
        $userId = Yii::app()->session['user_id'];
        $positionUser = PositionUser::model()->find(array(
            'condition' => 'position_id=:positionId AND user_id=:userId',
            'params' => array(':positionId'=>$postId,':userId'=>$userId),
        ));

        if($isEnroll==1) {
            $positionUserOne = new PositionUser();
            $positionUserOne->position_id=$postId;
            $positionUserOne->user_id = $userId;
            $positionUserOne->save();
        }
        else{
            $positionUser->delete();
        }
        $fNumber = PositionUser::model()->count(array(
            'condition' => 'user_id=:userId',
            'params' => array(':userId'=>$userId),
        ));
        $list='{"code":0,"data":'.$fNumber.'}';
        print $list;
    }

    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter')
        );
    }


}