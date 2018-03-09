<?php

class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public $breadcrumbs;
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page1
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
            ),
            // page1 action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page1&view=FileName
            'page1'=>array(
                'class'=>'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */


    /*public function actionIndex()
    {
        $images = IndexImage::model()->findAll();
        $newestAnnouncements = AnnouncementService::getInstance()->indexAnnouncement();
        $companyCount = Company::model()->count();
        $positionCount = Position::model()->count();
        $careerTalkCount = CareerTalk::model()->count();
        $companyList = CompanyService::getInstance()->indexCompany();
        $indexPositionLeft = PositionService::getInstance()->indexList()[0];
        $indexPositionRight = PositionService::getInstance()->indexList()[1];
        $indexTeacherPositionLeft = PositionService::getInstance()->indexTeacherList()[0];
        $indexTeacherPositionRight = PositionService::getInstance()->indexTeacherList()[1];
        $indexTeacherRecruitLeft = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[0];
        $indexTeacherRecruitRight = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[1];
        $indexLiveCT = CTService::getInstance()->indexLiveCT();
        $indexPreach = CTService::getInstance()->indexPreach();
        $this->smarty->assign('newestAnnouncements',$newestAnnouncements);
        $this->smarty->assign('companyCount',$companyCount);
        $this->smarty->assign('positionCount',$positionCount);
        $this->smarty->assign('careerTalkCount',$careerTalkCount);
        $this->smarty->assign('number', mt_rand(10000,100000));
        $this->smarty->assign('images',$images);
        $this->smarty->assign('indexPositionLeft',$indexPositionLeft);
        $this->smarty->assign('indexPositionRight',$indexPositionRight);
        $this->smarty->assign('indexTeacherPositionLeft',$indexTeacherPositionLeft);
        $this->smarty->assign('indexTeacherPositionRight',$indexTeacherPositionRight);
        $this->smarty->assign('indexTeacherRecruitLeft',$indexTeacherRecruitLeft);
        $this->smarty->assign('indexTeacherRecruitRight',$indexTeacherRecruitRight);
        $this->smarty->assign('companyList',$companyList);
        $this->smarty->assign('indexLiveCT',$indexLiveCT);
        $this->smarty->assign('indexPreach',$indexPreach);

        $this->smarty->display('index/index.html');
    }

public function actionNotLogin()
    {
        $images = IndexImage::model()->findAll();
        $newestAnnouncements = AnnouncementService::getInstance()->indexAnnouncement();
        $companyCount = Company::model()->count();
        $positionCount = Position::model()->count();
        $careerTalkCount = CareerTalk::model()->count();
        $companyList = CompanyService::getInstance()->indexCompany();
        $indexPositionLeft = PositionService::getInstance()->indexList()[0];
        $indexPositionRight = PositionService::getInstance()->indexList()[1];
        $indexTeacherPositionLeft = PositionService::getInstance()->indexTeacherList()[0];
        $indexTeacherPositionRight = PositionService::getInstance()->indexTeacherList()[1];
        $indexTeacherRecruitLeft = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[0];
        $indexTeacherRecruitRight = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[1];
        $this->smarty->assign('newestAnnouncements',$newestAnnouncements);
        $this->smarty->assign('companyCount',$companyCount);
        $this->smarty->assign('positionCount',$positionCount);
        $this->smarty->assign('careerTalkCount',$careerTalkCount);
        $this->smarty->assign('number', mt_rand(10000,100000));
        $this->smarty->assign('images',$images);
        $this->smarty->assign('indexPositionLeft',$indexPositionLeft);
        $this->smarty->assign('indexPositionRight',$indexPositionRight);
        $this->smarty->assign('indexTeacherPositionLeft',$indexTeacherPositionLeft);
        $this->smarty->assign('indexTeacherPositionRight',$indexTeacherPositionRight);
        $this->smarty->assign('indexTeacherRecruitLeft',$indexTeacherRecruitLeft);
        $this->smarty->assign('indexTeacherRecruitRight',$indexTeacherRecruitRight);
        $this->smarty->assign('companyList',$companyList);

        $this->smarty->display('index/index1.html');
    }*/



    public function actionIndex()
    {
        $images=Yii::app()->cache->get('$images');
        if($images===false){
        $images = IndexImage::model()->findAll(array('order'=>'number'));
        Yii::app()->cache->set('$images', $images, 60);
       }
        $newestAnnouncements=Yii::app()->cache->get('$newestAnnouncements');
        if($newestAnnouncements===false){
        $newestAnnouncements = AnnouncementService::getInstance()->indexAnnouncement();
        Yii::app()->cache->set('$newestAnnouncements', $newestAnnouncements, 60);
       }
        $companyCount=Yii::app()->cache->get('$companyCount');
        if($companyCount===false){
        $companyCount = Company::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
        Yii::app()->cache->set('$companyCount', $companyCount, 60);
       }
        $positionCount=Yii::app()->cache->get('$positionCount');
        if($positionCount===false){
        $positionCount = Position::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
        Yii::app()->cache->set('$positionCount', $positionCount, 60);
       }
        $careerTalkCount=Yii::app()->cache->get('$careerTalkCount');
        if($careerTalkCount===false){
        $careerTalkCount = CareerTalk::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
        Yii::app()->cache->set('$careerTalkCount', $careerTalkCount, 60);
       }
        $graduateCount = Yii::app()->cache->get('$graduateCount');
        if($graduateCount===false){
        $cri = new CDbCriteria();
            $cri->with = array('studyexperience');
            $conditions = "1=1 ";
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition=$conditions;
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $graduateCount = Resume::model()->count($cri);
        Yii::app()->cache->set('$graduateCount', $graduateCount, 60);
       }
        $companyList=Yii::app()->cache->get('$companyList');
        if($companyList===false){
        $companyList = CompanyService::getInstance()->indexCompany();
        Yii::app()->cache->set('$companyList', $companyList, 60);
       }
        $indexPositionLeft=Yii::app()->cache->get('$indexPositionLeft');
        if($indexPositionLeft===false){
        $indexPositionLeft = PositionService::getInstance()->indexList()[0];
        Yii::app()->cache->set('$indexPositionLeft', $indexPositionLeft, 60);
       }
        $indexPositionRight=Yii::app()->cache->get('$indexPositionRight');
        if($indexPositionRight===false){
        $indexPositionRight = PositionService::getInstance()->indexList()[1];
        Yii::app()->cache->set('$indexPositionRight', $indexPositionRight, 60);
       }
        $indexTeacherPositionLeft=Yii::app()->cache->get('$indexTeacherPositionLeft');
        if($indexTeacherPositionLeft===false){
        $indexTeacherPositionLeft = PositionService::getInstance()->indexTeacherList()[0];
        Yii::app()->cache->set('$indexTeacherPositionLeft', $indexTeacherPositionLeft, 60);
       }
        $indexTeacherPositionRight=Yii::app()->cache->get('$indexTeacherPositionRight');
        if($indexTeacherPositionRight===false){
        $indexTeacherPositionRight = PositionService::getInstance()->indexTeacherList()[1];
        Yii::app()->cache->set('$indexTeacherPositionRight', $indexTeacherPositionRight, 60);
       }
        $indexTeacherRecruitLeft=Yii::app()->cache->get('$indexTeacherRecruitLeft');
        if($indexTeacherRecruitLeft===false){
        $indexTeacherRecruitLeft = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[0];
        Yii::app()->cache->set('$indexTeacherRecruitLeft', $indexTeacherRecruitLeft, 60);
       }
        $indexTeacherRecruitRight=Yii::app()->cache->get('$indexTeacherRecruitRight');
        if($indexTeacherRecruitRight===false){
        $indexTeacherRecruitRight = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[1];
        Yii::app()->cache->set('$indexTeacherRecruitRight', $indexTeacherRecruitRight, 60);
       }
        $indexLiveCT=Yii::app()->cache->get('$indexLiveCT');
        if($indexLiveCT===false){
        $indexLiveCT = CTService::getInstance()->indexLiveCT();
        Yii::app()->cache->set('$indexLiveCT', $indexLiveCT, 60);
       }
        $indexPreach=Yii::app()->cache->get('$indexPreach');
       if($indexPreach===false){
        $indexPreach = CTService::getInstance()->indexPreach();
        Yii::app()->cache->set('$indexPreach', $indexPreach, 60);
       }
        $this->smarty->assign('newestAnnouncements',$newestAnnouncements);
        $this->smarty->assign('companyCount',$companyCount);
        $this->smarty->assign('positionCount',$positionCount);
        $this->smarty->assign('careerTalkCount',$careerTalkCount);
        $this->smarty->assign('graduateCount',$graduateCount);
        $this->smarty->assign('number', mt_rand(1000,2000));
        $this->smarty->assign('images',$images);
        $this->smarty->assign('indexPositionLeft',$indexPositionLeft);
        $this->smarty->assign('indexPositionRight',$indexPositionRight);
        $this->smarty->assign('indexTeacherPositionLeft',$indexTeacherPositionLeft);
        $this->smarty->assign('indexTeacherPositionRight',$indexTeacherPositionRight);
        $this->smarty->assign('indexTeacherRecruitLeft',$indexTeacherRecruitLeft);
        $this->smarty->assign('indexTeacherRecruitRight',$indexTeacherRecruitRight);
        $this->smarty->assign('indexLiveCT',$indexLiveCT);
        $this->smarty->assign('indexPreach',$indexPreach);
        $this->smarty->assign('companyList',$companyList);
        $this->smarty->display('index/index.html');
    }

  public function actionNotLogin()
    {
        $images=Yii::app()->cache->get('$images');
        if($images===false){
            $images = IndexImage::model()->findAll(array('order'=>'number'));
            Yii::app()->cache->set('$images', $images, 60);
        }
        $newestAnnouncements=Yii::app()->cache->get('$newestAnnouncements');
        if($newestAnnouncements===false){
            $newestAnnouncements = AnnouncementService::getInstance()->indexAnnouncement();
            Yii::app()->cache->set('$newestAnnouncements', $newestAnnouncements, 60);
        }
        $companyCount=Yii::app()->cache->get('$companyCount');
        if($companyCount===false){
            $companyCount = Company::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
            Yii::app()->cache->set('$companyCount', $companyCount, 60);
        }
        $positionCount=Yii::app()->cache->get('$positionCount');
        if($positionCount===false){
            $positionCount = Position::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
            Yii::app()->cache->set('$positionCount', $positionCount, 60);
        }
        $careerTalkCount=Yii::app()->cache->get('$careerTalkCount');
        if($careerTalkCount===false){
            $careerTalkCount = CareerTalk::model()->count(array(
                'condition' => 'is_front_input=0 OR is_ok=1'
            ));
            Yii::app()->cache->set('$careerTalkCount', $careerTalkCount, 60);
        }
        $graduateCount = Yii::app()->cache->get('$graduateCount');
        if($graduateCount===false){
            $cri = new CDbCriteria();
            $cri->with = array('studyexperience');
            $conditions = "1=1 ";
            $conditions .= " and studyexperience.school_name != '' and studyexperience.major_name != '' and studyexperience.position_degree_id != ''";
            $cri->condition=$conditions;
            $cri->group = 't.id';
            $cri->order = 't.id DESC';
            $graduateCount = Resume::model()->count($cri);
            Yii::app()->cache->set('$graduateCount', $graduateCount, 60);
        }
        $companyList=Yii::app()->cache->get('$companyList');
        if($companyList===false){
            $companyList = CompanyService::getInstance()->indexCompany();
            Yii::app()->cache->set('$companyList', $companyList, 60);
        }
        $indexPositionLeft=Yii::app()->cache->get('$indexPositionLeft');
        if($indexPositionLeft===false){
            $indexPositionLeft = PositionService::getInstance()->indexList()[0];
            Yii::app()->cache->set('$indexPositionLeft', $indexPositionLeft, 60);
        }
        $indexPositionRight=Yii::app()->cache->get('$indexPositionRight');
        if($indexPositionRight===false){
            $indexPositionRight = PositionService::getInstance()->indexList()[1];
            Yii::app()->cache->set('$indexPositionRight', $indexPositionRight, 60);
        }
        $indexTeacherPositionLeft=Yii::app()->cache->get('$indexTeacherPositionLeft');
        if($indexTeacherPositionLeft===false){
            $indexTeacherPositionLeft = PositionService::getInstance()->indexTeacherList()[0];
            Yii::app()->cache->set('$indexTeacherPositionLeft', $indexTeacherPositionLeft, 60);
        }
        $indexTeacherPositionRight=Yii::app()->cache->get('$indexTeacherPositionRight');
        if($indexTeacherPositionRight===false){
            $indexTeacherPositionRight = PositionService::getInstance()->indexTeacherList()[1];
            Yii::app()->cache->set('$indexTeacherPositionRight', $indexTeacherPositionRight, 60);
        }
        $indexTeacherRecruitLeft=Yii::app()->cache->get('$indexTeacherRecruitLeft');
        if($indexTeacherRecruitLeft===false){
            $indexTeacherRecruitLeft = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[0];
            Yii::app()->cache->set('$indexTeacherRecruitLeft', $indexTeacherRecruitLeft, 60);
        }
        $indexTeacherRecruitRight=Yii::app()->cache->get('$indexTeacherRecruitRight');
        if($indexTeacherRecruitRight===false){
            $indexTeacherRecruitRight = TeacherRecruitmentService::getInstance()->indexTeacherRecruitment()[1];
            Yii::app()->cache->set('$indexTeacherRecruitRight', $indexTeacherRecruitRight, 60);
        }
        $indexLiveCT=Yii::app()->cache->get('$indexLiveCT');
        if($indexLiveCT===false){
            $indexLiveCT = CTService::getInstance()->indexLiveCT();
            Yii::app()->cache->set('$indexLiveCT', $indexLiveCT, 60);
        }
        $indexPreach=Yii::app()->cache->get('$indexPreach');
        if($indexPreach===false){
            $indexPreach = CTService::getInstance()->indexPreach();
            Yii::app()->cache->set('$indexPreach', $indexPreach, 60);
        }
        $this->smarty->assign('newestAnnouncements',$newestAnnouncements);
        $this->smarty->assign('companyCount',$companyCount);
        $this->smarty->assign('positionCount',$positionCount);
        $this->smarty->assign('careerTalkCount',$careerTalkCount);
        $this->smarty->assign('graduateCount',$graduateCount);
        $this->smarty->assign('number', mt_rand(1000,2000));
        $this->smarty->assign('images',$images);
        $this->smarty->assign('indexPositionLeft',$indexPositionLeft);
        $this->smarty->assign('indexPositionRight',$indexPositionRight);
        $this->smarty->assign('indexTeacherPositionLeft',$indexTeacherPositionLeft);
        $this->smarty->assign('indexTeacherPositionRight',$indexTeacherPositionRight);
        $this->smarty->assign('indexTeacherRecruitLeft',$indexTeacherRecruitLeft);
        $this->smarty->assign('indexTeacherRecruitRight',$indexTeacherRecruitRight);
        $this->smarty->assign('indexLiveCT',$indexLiveCT);
        $this->smarty->assign('indexPreach',$indexPreach);
        $this->smarty->assign('companyList',$companyList);
        $this->smarty->display('index/index1.html');
    }
    public function actionStatus($data){
        $sql="select sign from {{study_experience}} where user_id='".$data."'";
        $result=StudyExperience::model()->findBySql($sql);
        if(!$result){
            $sqll = "select sign from {{work_experience}} where user_id='".$data."'";
            $result = WorkExperience::model()->findBySql($sqll);
            if($result){
                return true;
            }
        }else{
            return true;
        }
    }
    public function actionQqlogin(){
        if(isset($_REQUEST['state'])=='dsjyw'){
            if(isset($_REQUEST['code'])){
                Yii::import('ext.oauthLogin.qq.qqConnect',true);
                $keys = array();
                $keys['code'] = $_REQUEST['code'];
                $keys['state'] = Yii::app()->session['qq_state'];
                $keys['redirect_uri'] = QQ_CALLBACK_URL;
                try {
                    $qqConnect = new qqConnectAuthV2(QQ_APPID,QQ_APPKEY);
                    $qqToken = $qqConnect->getAccessToken('code',$keys);
                } catch (CHttpException $e) {
                }

                if (isset($qqToken)) {
                    Yii::app()->session->add('qqToken',$qqToken);
                    Yii::import('ext.oauthLogin.qq.qqConnect',true);
                    $c = new qqConnectAuthV2(QQ_APPID,QQ_APPKEY);
                    $userInfo = $c->getUserInfo(Yii::app()->session['qqToken']);
                    $userShow= array();
                    $userShow['screen_name'] = $userInfo['nickname'];
                    $userShow['profile_image_url'] = $userInfo['figureurl_2'];
                    //Yii::app()->session['username']=$userShow['screen_name'];
                    $model = User::model()->findByAttributes(array('username'=>Yii::app()->session['qqToken']['openid']));
                    if($model)
                    {
                        Yii::app()->session['user_id']= $model->id;
                        $study = $this->actionStatus($model->id);
                        if($study){
                            Yii::app()->session['username']=$userShow['screen_name'];
                            $this->redirect(Yii::app()->webConstants->getSite());
                        }else{
                            $this->redirect(array('user/message/seekMessage'));
                        }
                    }
                    else
                    {
                        $use = new User();
                        $use->username=Yii::app()->session['qqToken']['openid'];  //插入用户名
                        if($use->save())
                        {
                            $attribute = array('username'=>Yii::app()->session['qqToken']['openid']);
                            $id = User::model()->findByAttributes($attribute)->id;   //查询用户id
                            $userDetail= new UserDetail();
                            $userDetail->user_id=$id;
                            Yii::app()->session['user_id']=$id;
                            $userDetail->realname=$userShow['screen_name'];
                            $userDetail->head_url=$userShow['profile_image_url'];
                            $userDetail->save();
                            $resume = new Resume();
                            $resume->user_id = $id;
                            $resume->save();
                            Yii::app()->session['resume_id']=$resume->attributes['id'];
                            $study = $this->actionStatus($id);
                            if($study){
                                $this->redirect(Yii::app()->webConstants->getSite());
                            }else{
                                $this->redirect(array('user/message/seekMessage'));
                            }
                        }
                        else
                        {
                            print '认证失败';
                        }
                    }
                }
                else {
                 echo '认证失败';
                }
            }
        }
    }
    public function actionWblogin(){
        if(isset($_REQUEST['state'])=='dsjyw'){
            if(isset($_REQUEST['code'])){
                Yii::import('ext.oauthLogin.sina.sinaWeibo',true);
                $keys = array();
                $keys['code'] = $_REQUEST['code'];
                $keys['redirect_uri'] = WB_CALLBACK_URL;
                try {
                    $weibo = new SaeTOAuthV2(WB_AKEY,WB_SKEY);
                    $sinaToken = $weibo->getAccessToken('code',$keys);
                } catch (CHttpException $e) {

                }
                //获取认证
                if (isset($sinaToken)) {
                    Yii::app()->session->add('sinaToken',$sinaToken);
                    //查询微博的账号信息
                    $c = new SaeTClientV2( WB_AKEY , WB_SKEY ,Yii::app()->session['sinaToken']['access_token']);
                    $userShow  = $c->getUserShow(Yii::app()->session['sinaToken']); // done
                    //Yii::app()->session['username']=$userShow['screen_name'];
                    $model = User::model()->findByAttributes(array('username'=>Yii::app()->session['sinaToken']['uid']));
                    if($model)
                    {
                        Yii::app()->session['user_id']= $model->id;
                        $study = $this->actionStatus($model->id);
                        if($study){
                            Yii::app()->session['username']=$userShow['screen_name'];
                            $this->redirect(Yii::app()->webConstants->getSite());
                        }else{
                            $this->redirect(array('user/message/seekMessage'));
                        }
                    }
                    else
                    {
                        $use = new User();
                        $use->username=Yii::app()->session['sinaToken']['uid'];  //插入用户名
                        if($use->save())
                        {
                            $attribute = array('username'=>Yii::app()->session['sinaToken']['uid']);
                            $id = User::model()->findByAttributes($attribute)->id;   //查询用户id
                            $userDetail= new UserDetail();
                            $userDetail->user_id=$id;
                            Yii::app()->session['user_id']=$id;
                            $userDetail->realname=$userShow['screen_name'];
                            $userDetail->head_url=$userShow['profile_image_url'];
                            $userDetail->save();
                            $resume = new Resume();
                            $resume->user_id = $id;
                            $resume->save();
                            Yii::app()->session['resume_id']=$resume->attributes['id'];
                            $study = $this->actionStatus($id);
                            if($study){
                                $this->redirect(Yii::app()->webConstants->getSite());
                            }else{
                                $this->redirect(array('user/message/seekMessage'));
                            }
                        }
                        else
                        {
                            print '认证失败';
                        }
                    }
                }  else {
                    echo '认证失败';
                }
            }
        }
    }


    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page1
     */
    public function actionContact()
    {
        $model=new ContactForm;
        if(isset($_POST['ContactForm']))
        {
            $model->attributes=$_POST['ContactForm'];
            if($model->validate())
            {
                $name='=?UTF-8?B?'.base64_encode($model->name).'?=';
                $subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
                $headers="From: $name <{$model->email}>\r\n".
                    "Reply-To: {$model->email}\r\n".
                    "MIME-Version: 1.0\r\n".
                    "Content-type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
                Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact',array('model'=>$model));
    }

    /**
     * Displays the login page1
     */
    public function actionLogin()
    {
        $model=new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page1 if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login',array('model'=>$model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    
}