<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 14:03
 */
class CompanyLoginUserController extends Controller
{
    /**
     * 跳转到新增页面
     */
    public function actionToAddUser()
    {
        $companyUserTypeList = CompanyUserType::model()->findAll();
        $this->smarty->assign('companyUserTypeList',$companyUserTypeList);
    }
    /**
     * 新增公司联系人
     */
    public function actionAddCompanyUser()
    {
        $companyId =  Yii::app()->session['company_id'];//当前公司ID
        $adminId =  Yii::app()->session['contact_id'];//当前用户ID
        $companyUserModel = new CompanyLoginUser();
        if (!empty($_POST))
        {
            $companyUserModel->name =$_POST['name'];//用户名
            $companyUserModel->duty = $_POST['duty'];//职务
            $companyUserModel->telephone = $_POST['telephone'];//固定电话
            $companyUserModel->phone = $_POST['phone'];//手机号
            $companyUserModel->email = $_POST['email'];//邮箱

            if ($_POST)
            {
                $string = "";
                $userType = $_POST['userType'];
                $string = implode(" ",$userType);
            }
            $companyUserModel->type_id = $string;//联系人类别
            $companyUserModel->company_id = $companyId;//当前公司ID
            $companyUserModel->admin_id = $adminId;//当前联系人ID
            if ($companyUserModel->save())
            {
                $this->redirect(array('listAllUser'));
            }
        }
    }

    /**
     * @param $name
     * @param $phone
     * 根据条件查找联系人
     */
    public function actionQuery()
    {
        $keyword = $_POST['keyword'];
        $companyId =  Yii::app()->session['company_id'];//当前公司ID
        $adminId =  Yii::app()->session['contact_id'];//当前用户ID
        if (!empty($keyword)){
            $conditions = "1=1 and (clu.phone =  :phone  or  clu.name = :name) and clu.company_id = :companyId and clu.admin_id = :adminId";
            $params = array(':phone' => $keyword, ':name' => $keyword, ':companyId'=>$companyId , ':adminId'=>$adminId);
        }else{
            $conditions = "1=1 and clu.company_id = :companyId and clu.admin_id = :adminId";
            $params = array(':companyId'=>$companyId , ':adminId'=>$adminId);
        }
        $command = Yii::app()->db->createCommand()
            ->select('clu.phone ,clu.name ,clu.duty,clu.type_id')
            ->from('t_company_login_user clu');
        $companyUserModel = CompanyLoginUser::model();
        $companyUserList = $command->Where($conditions, $params)->queryAll();
        $sql1 = "SELECT phone,name,duty,type_id FROM {{company_login_user}} WHERE admin_id != '$adminId 'AND company_id = '$companyId'";
        $companyUserList1 = $companyUserModel->findAllBySql($sql1);
        $this->smarty->assign('companyUserList1',$companyUserList1);
        $this->smarty->assign('companyUserList',$companyUserList);
        //echo json_encode($userList,JSON_UNESCAPED_UNICODE);
        $this->smarty->display('recruitEntrance/company-info/company-member.html');
    }

    /**
     * 列出我添加的联系人和其他的联系人
     */
    public function actionListAllUser()
    {
        $companyId =  Yii::app()->session['company_id'];//当前公司ID
        $adminId =  Yii::app()->session['contact_id'];//当前用户ID
        $companyUserModel = CompanyLoginUser::model();
        $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE admin_id = '$adminId' AND company_id = '$companyId'";
        $companyUserList = $companyUserModel->findAllBySql($sql);
        $sql1 = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE admin_id != '$adminId' AND company_id = '$companyId'";
        $companyLoginUserTypeList = CompanyLoginUserType::model()->findAll();
        $this->smarty->assign('companyLoginUserTypeList',$companyLoginUserTypeList);
        $companyUserList1 = $companyUserModel->findAllBySql($sql1);
        $this->smarty->assign('companyUserList',$companyUserList);
        $this->smarty->assign('companyUserList1',$companyUserList1);
        $this->smarty->display('recruitEntrance/company-info/company-member.html');
    }

    /**
     * 发送短信
     */
    public function actionSendCode(){

        $tel = $_GET['tel'];
        //生成验证码
        for($i=0;$i<4;$i++){
            $this->code .= rand(0,9);
        }
        $content = '你的验证码是 '.$this->code;
        send_sms($content,$tel);
        //传给前端，由前端进行验证码的验证
        echo "right|".$this->code;

    }
    /**
     * 编辑联系人信息
     */
    public function actionEdit()
    {
        $uid = $_POST['uid'];
        $companyUserModel = CompanyLoginUser::model();
        $companyUserInfo = $companyUserModel->findByPk($uid);
        if (!empty($_POST))
        {
            $companyUserInfo->name = $_POST['name'];
            $companyUserInfo->phone = $_POST['phone'];
            $string = "";
            $userType = $_POST['userType'];
            $string = implode(" ",$userType);
            $companyUserInfo->type_id = $string;//联系人类别;
            if ($companyUserInfo->save())
            {
                $this->redirect(array('listAllUser'));
            }
        }
//        echo json_encode( $companyUserInfo,true);
    }

    /**
     * 获取用户的JSON数据
     */
    public function actionUserJson()
    {
        $id = $_GET['id'];
        $companyLoginUserModel = CompanyLoginUser::model();
        $companyLoginUserInfo = $companyLoginUserModel->findByPk($id);
        $UserJson='{"code":0,"data":'.CJSON::encode($companyLoginUserInfo).'}';
        print  $UserJson;
    }
    /**
     * 删除联系人
     */
    public function actionDel()
    {
        $id = $_GET['id'];
        if ($id)
        {
            CompanyLoginUser::model()->deleteByPk($id);
            $DelJson='{"code":"1"}';
        }else
        {
            $DelJson='{"code":"0"}';
        }
        print  $DelJson;
//        $this->redirect(array('index'));
    }


    /**
     * 根据类别查找用户
     */
    public function actionQueryByType()
    {
        $type = $_GET['id'];
        $companyUserModel = CompanyLoginUser::model();
        $companyId =  Yii::app()->session['company_id'];//当前公司ID
        $adminId =  Yii::app()->session['contact_id'];//当前用户ID
        if ($type == -1)
        {
            $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}}  WHERE admin_id = '$adminId' AND company_id = '$companyId'";
        }else{
            $sql = "SELECT id,phone,name,duty,type_id FROM {{company_login_user}} WHERE type_id LIKE '%{$type}%' AND admin_id = '$adminId' AND company_id = '$companyId'";
        }
        $companyUserList = $companyUserModel->findAllBySql($sql);
        if ($companyUserList){
            $UserJson='{"code":1,"data":'.CJSON::encode($companyUserList).'}';
        }else{
            $UserJson='{"code":0}';
        }
        print  $UserJson;
    }
    /**
     * 根据姓名或手机号码查找联系人
     */
    public function actionQurtyByName()
    {
        $keyword = $_POST['keyword'];
        $companyId =  Yii::app()->session['company_id'];//当前公司ID
        $adminId =  Yii::app()->session['contact_id'];//当前用户ID
        if (!empty($keyword)){
            $conditions = "1=1 and (clu.phone =  :phone  or  clu.name = :name) and clu.company_id = :companyId and clu.admin_id = :adminId";
            $params = array(':phone' => $keyword, ':name' => $keyword, ':companyId'=>$companyId , ':adminId'=>$adminId);
        }else{
            $conditions = "1=1 and clu.company_id = :companyId and clu.admin_id = :adminId";
            $params = array(':companyId'=>$companyId , ':adminId'=>$adminId);
        }
        $command = Yii::app()->db->createCommand()
            ->select('clu.phone ,clu.name ,clu.duty,clu.type_id')
            ->from('t_company_login_user clu');
        $companyUserList = $command->Where($conditions, $params)->queryAll();
        $companyJson = '{"code":1,"data":'.CJSON::encode($companyUserList).'}';
        print $companyJson;
    }
    public function filters()
    {
        return array(
            array(
                'application.filters.RecruitmentFilter',
            ),
        );
    }
}