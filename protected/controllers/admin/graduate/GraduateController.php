<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-31
 * Time: 上午3:20
 */



class GraduateController extends  Controller{
    public function actionDel($id){
        $user = User::model()->findByPk($id);
        if(!empty($user))
            $user->delete();
        $userDetail = UserDetail::model()->findByAttributes(array('user_id'=>$id));
        if(!empty($userDetail))
            $userDetail->delete();
        $resume = Resume::model()->findByAttributes(array('user_id'=>$id));
        if(!empty($resume))
            $resume->delete();
        $studyexperience = StudyExperience::model()->findByAttributes(array('user_id'=>$id));
        if(!empty($studyexperience))
            $studyexperience->delete();
        $this->redirect($this->createUrl('admin/graduate/graduate/list'));
    }

    public function  actionDetail($id){
        $user = User::model()->with('userdetail')->findByPk($id);
        $sql = "select max(position_degree_id) as num from {{study_experience}} where user_id='" . $id . "'";
        $result = StudyExperience::model()->findBySql($sql);
        $name = Degree::model()->findByAttributes(array('id' => $result->num))->name;
        // $ResumeInfo = Resume::model()->with('degree')->getList($user_id);
        $study = "select school_name as name,major_name as value from {{study_experience}}
        where sign=1 and user_id='" . $id . "'";
        $identify = StudyExperience::model()->findAllBySql($study);
        if (empty($identify)) {
            $work = "select company_name as name,position_name as value from {{work_experience}}
            where sign=1 and user_id='" . $id . "'";
            $identify = WorkExperience::model()->findAllBySql($work);
            if (empty($identify)) {
                $identify = "";
            }
        }
        $StudyExperienceInfo = StudyExperience::model()->with('position_degree')->getList($id);
        $this->smarty->assign('user',$user);
        $this->smarty->assign('name',$name);
        $this->smarty->assign('WorkExperienceInfo', $WorkExperienceInfo);
        $this->smarty->assign('StudyExperienceInfo', $StudyExperienceInfo);
        $this->smarty->assign('current','graduate');
        $this->smarty->display('admin/graduate/detail.html');
    }

    public function actionList(){
        $criteria = new CDbCriteria();
        //$list_all = User::model()->findAll($criteria);
        $pageSize = 10;
        //$recordCount = count($list_all);

        $currentYear = intval(date('Y',time()));
        $this->smarty->assign('currentYear', $currentYear);
        // $this->smarty->assign('end_time', $end_time);
        //$this->smarty->assign('liebiao',$list_all);
        $this->smarty->assign('pageSize',$pageSize);
        //$this->smarty->assign("recordCount",$recordCount);
        $this->smarty->assign('current','graduate');
        $this->smarty->display('admin/graduate/list.html');
    }

    public function  actionJson(){
        $page = $_GET['page'];
        $criteria = new CDbCriteria();
        //$list_all = User::model()->with('userdetail','studyexperience')->findAll($criteria);
        $recordCount = User::model()->with('userdetail','studyexperience')->count();
        $pageSize = 10;
        $currentPage = Yii::app()->request->getParam('currentPage',$page);
        $criteria -> limit = $pageSize;
        $criteria -> offset = ($currentPage-1)*$pageSize;
        //$recordCount =count($list_all);
        $list= User::model()->with('userdetail','studyexperience')->findAll($criteria);  //记录分页
        foreach( $list as $key => $value){
            $listJson[$key]['id'] = $value->id;
            if(empty($value->password))
                $listJson[$key]['username'] = $value->username;
            else
                $listJson[$key]['username'] = $value->email;
            $listJson[$key]['realname'] = $value->userdetail->realname;
            if($value->is_activated==1)
                $listJson[$key]['status'] = '是';
            else
                $listJson[$key]['status'] = '否';
            if($value->userdetail->gender==1)
                $listJson[$key]['gender'] = '男';
            else if($value->userdetail->gender!=null && $value->userdetail->gender==0)
            {$listJson[$key]['gender'] = '女';}
            $listJson[$key]['major_name'] = $value->studyexperience->major_name;

            // switch ($result->num)
            //     {
            //         case 5:
            //             $listJson[$key]['num'] = '其他';
            //             break;
            //         case 4:
            //             $listJson[$key]['num'] = '专科';
            //             break;
            //         case 3:
            //             $listJson[$key]['num'] = '博士';
            //             break;
            //         case 2:
            //             $listJson[$key]['num'] = '硕士';
            //             break;
            //         case 1:
            //             $listJson[$key]['num'] = '本科';
            //             break;
            //     }
            $listJson[$key]['graduate_time'] = substr($value->studyexperience->end_time,0,4);
        }
        $list2='{"list":'.CJSON::encode($listJson).',"dataCount":"'.$recordCount.'"}';
        print $list2;
    }

    public function actionSearch(){
        $keyword = $_POST['keyword'];
        $isNenu = $_POST['isNenu'];
        // $status = $_POST['status'];
        $gender = $_POST['gender'];
        $major_nameKW = $_POST['major_nameKW'];
        $position_degree_id = $_POST['position_degree_id'];
        $graduate_time = $_POST['graduate_time'];
        $cri = new CDbCriteria();
        $conditions = "1=1 ";
        $params = array();
        if ($keyword != null && $keyword!=""){
            $conditions .= " and (userdetail.realname LIKE :keyword OR t.username LIKE :keyword)";
            $params[':keyword']='%'.$keyword.'%';
        }
        if($isNenu!=-1&& $isNenu!="-1"){
            $conditions .= " and t.is_league = :isNenu";
            $params[':isNenu']=$isNenu;
        }
        // if ($status != -1 && $status!="-1"){
        //     $conditions .= " and t.is_activated = :status";
        //     $params[':status']=$status;
        // }
        if ($gender != -1 && $gender!="-1"){
            $conditions .= " and userdetail.gender = :gender";
            $params[':gender']=$gender;
        }
        if ($major_nameKW != null && $major_nameKW!=""){
            $conditions .= " and studyexperience.major_name LIKE :major_nameKW";
            $params[':major_nameKW']='%'.$major_nameKW.'%';
        }
        if ($position_degree_id != -1 && $position_degree_id!="-1"){
            $conditions .= " and studyexperience.position_degree_id = :position_degree_id";
            $params[':position_degree_id']=$position_degree_id;
        }
        if ($graduate_time != -1 && $graduate_time!="-1"){
            $conditions .= " and studyexperience.end_time LIKE :graduate_time";
            $params[':graduate_time']='%'.$graduate_time.'%';
        }
        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = User::model()->with('userdetail','studyexperience')->count($cri);
        $currentYear = intval(date('Y',time()));
        $this->smarty->assign('currentYear', $currentYear);
        $this->smarty->assign('keyword',$keyword);
        $this->smarty->assign('isNenu',$isNenu);
        $this->smarty->assign('status',$status);
        $this->smarty->assign('gender',$gender);
        $this->smarty->assign('major_nameKW',$major_nameKW);
        $this->smarty->assign('position_degree_id',$position_degree_id);
        $this->smarty->assign('graduate_time',$graduate_time);
        $this->smarty->assign('recordCount',$recordCount);
        $this->smarty->assign('current','graduate');
        $this->smarty->display('admin/graduate/search.html');
    }

    public function actionSearchJson(){
        $keyword = $_GET['keyword'];
        $isNenu = $_GET['isNenu'];
        // $status = $_GET['status'];
        $gender = $_GET['gender'];
        $major_nameKW = $_GET['major_nameKW'];
        $position_degree_id = $_GET['position_degree_id'];
        $graduate_time = $_GET['graduate_time'];
        $page = $_GET['page'];
        $offset = ($page-1)*10;
        $cri = new CDbCriteria();
        $conditions = "1=1 ";
        $params = array();
        if($keyword!=null&& $isNenu!=""){
            $conditions .= " and (t.username like :keyword OR userdetail.realname like :keyword)";
            $params[':keyword']='%'.$keyword.'%';
        }
        if($isNenu!=-1&& $isNenu!="-1"){
            $conditions .= " and t.is_league = :isNenu";
            $params[':isNenu']=$isNenu;
        }
        // if ($status != -1 && $status!="-1"){
        //     $conditions .= " and t.is_activated = :status";
        //     $params[':status']=$status;
        // }
        if ($gender != -1 && $gender!="-1"){
            $conditions .= " and userdetail.gender = :gender";
            $params[':gender']=$gender;
        }
        if ($major_nameKW != null && $major_nameKW!=""){
            $conditions .= " and studyexperience.major_name LIKE :major_nameKW";
            $params[':major_nameKW']='%'.$major_nameKW.'%';
        }
        if ($position_degree_id != -1 && $position_degree_id!="-1"){
            $conditions .= " and studyexperience.position_degree_id = :position_degree_id";
            $params[':position_degree_id']=$position_degree_id;
        }
        if ($graduate_time != -1 && $graduate_time!="-1"){
            $conditions .= " and studyexperience.end_time LIKE :graduate_time";
            $params[':graduate_time']='%'.$graduate_time.'%';
        }

        $cri->condition=$conditions;
        $cri->params=$params;
        $recordCount = User::model()->with('userdetail','studyexperience')->count($cri);
        $cri->limit =10;
        $cri->offset = $offset;
        $data = User::model()->with('userdetail','studyexperience')->findAll($cri);
        foreach( $data as $key => $value){
            $listJson[$key]['id'] = $value->id;
            if(empty($value->password))
                $listJson[$key]['username'] = $value->username;
            else
                $listJson[$key]['username'] = $value->email;
            $listJson[$key]['realname'] = $value->userdetail->realname;
            if($value->is_activated==1)
                $listJson[$key]['status'] = '是';
            else
                $listJson[$key]['status'] = '否';
            if($value->userdetail->gender==1)
                $listJson[$key]['gender'] = '男';
            else if($value->userdetail->gender!=null && $value->userdetail->gender==0)
                $listJson[$key]['gender'] = '女';
            $listJson[$key]['major_name'] = $value->studyexperience->major_name;

            switch ($value->studyexperience->position_degree_id)
            {
                case 5:
                    $listJson[$key]['position_degree_id'] = '其他';
                    break;
                case 4:
                    $listJson[$key]['position_degree_id'] = '专科';
                    break;
                case 3:
                    $listJson[$key]['position_degree_id'] = '博士';
                    break;
                case 2:
                    $listJson[$key]['position_degree_id'] = '硕士';
                    break;
                case 1:
                    $listJson[$key]['position_degree_id'] = '本科';
                    break;
            }
            $listJson[$key]['graduate_time'] = substr($value->studyexperience->end_time,0,4);
        }
        $SearchJson = '{"list":'.CJSON::encode($listJson).',"dataCount":"'.$recordCount.'"}';
        print  $SearchJson;
    }
}


