<?php

/**
 * Created by PhpStorm.
 * User: wangjf
 * Date: 2017/8/11
 * Time: 13:44
 * 联系人管理控制层
 */
class CompanyMemberController  extends Controller
{

    //列出所有的联系人的信息
    public function actionList(){
        $companyUserList = CompanyLoginUser::model()->findAll();
        //这里应该渲染到前台页面
       var_dump($companyUserList);

    }
  //根据条件查询联系人
    public function  actionSearch(){
        $page = $_POST['currPage'];
        $name = $_POST['name'];
        $companyId = $_POST['companyId'];
        $duty = $_POST['duty'];
        $typeId = $_POST['typeId'];
        $companyTypeId = $_POST['companyTypeId'];
        $companyPropertyId   =  $_POST['property'];
        //初始化条件
        $conditions = "'1'='1' and tclu.company_id  = tc.id";
        $params = array();
        if($name != '' && $name != -1){
            $conditions .= 'and tclu.name  = :name';
            $params[':name'] = $name;
        }
        if($companyId != '' && $companyId != -1){
            $conditions .= 'and tclu.company_id  = :company_id';
            $params[':company_id'] = $companyId;
        }
        if($duty != '' && $duty != -1){
            $conditions .= 'and tclu.duty  = :duty';
            $params[':duty'] = $duty;
        }
        if($typeId != '' && $typeId != -1){
            //这里需要注意一下，是否需要进行和数据库中的字段进行匹配时进行去空
            $conditions .= 'and tclu.type_id  = :type_id';
            $params[':type_id'] = $typeId;
        }
        if( $companyTypeId != '' &&  $companyTypeId != -1){
            $conditions .= 'and tc.type_id = :company_type_id';
            $params[':company_type_id'] = $companyTypeId;

        }
        if($companyPropertyId != '' && $companyPropertyId != -1){
            $conditions .= 'and tc.property_id = :company_property_id';
            $params[':company_property_id'] = $companyPropertyId;

        }


        $command = Yii::app()->db->createCommand()
            ->select('tclu.* , count(*) as recordCount')
            ->from('t_company_login_user tclu,t_company tc')
            ->order('p.id asc');
        $pageSize = 8;
        $currentPage = Yii::app()->request->getParam('currentPage', 1);
        $offset = ($currentPage - 1) * $pageSize;
        $model = $command->where($conditions, $params)->limit($pageSize)->offset($offset)->queryAll();


        //这里需要渲染到前台的页面

    }
    //添加公司成员
    public function add(){

        $companyId = $_POST['company_id'];//当前公司ID
        //这里应该是管理员的Id
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
                //这里跳转的页面应该是联系人管理的列表页


                $this->redirect(array(''));
            }
        }

    }

    public function  actionEdit(){
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
                //这里应该是跳转到修改联系人的页面
                $this->redirect(array('listAllUser'));
            }
        }
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

    //删除联系人
    public function  actionDel(){

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

    }

  //批量删除
    public function actionDelall() {
        if (Yii::app()->request->isPostRequest) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition('id', $_POST['id']);
            CompanyLoginUser::model()->deleteAll($criteria); //News换成你的模型

            if (isset(Yii::app()->request->isAjaxRequest)) {
                echo CJSON::encode(array('success' => true));
            } else
                //这里的路径也是需要重新写的
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
        else
            throw new CHttpException(400, '请求错误');
    }



}