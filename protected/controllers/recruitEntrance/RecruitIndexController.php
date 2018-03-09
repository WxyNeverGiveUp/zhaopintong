<?php
/**
 * 用人单位登录之后跳转到的 首页 页面
 * User: 王超
 * Date: 2017/7/18-->2017/11/7
 */
class RecruitIndexController extends Controller{
    function actionIndex()
    {
        $username = $_SESSION['com_phone'];

        //报名人数 统计查询
        $id =  Yii::app()->session['contact_id'];//用户的id
////        $company_id = 296;//注释下一行，写死查看结果
        $company_id =CompanyLoginUser::model()->findByPk($id)->company_id;//根据用户id查询公司id
        $company = Company::model()->findByAttributes(array('id'=>$company_id));
//
//        $conn = Yii::app()->db;
//        $sql = 'SELECT count(*) FROM {{career_talk_user}} WHERE career_talk_id IN (SELECT id FROM t_career_talk WHERE company_id =
//'.$company_id.')';
//        $command = $conn->createCommand($sql);
//        $sign_up_num = $command->queryScalar();
//        $this->smarty->assign('sign_up_num',$sign_up_num);

        //查询拟招聘人数
//        $sql2='select `num_require` from {{career_talk}} WHERE company_id='.$company_id;
//        $command=$conn->createCommand($sql2);
//        $require_num = $command->queryScalar();
//        $this->smarty->assign('require_num',$require_num);

        //查询招聘职位数
//        $sql3='select `jobs_require` from {{career_talk}} WHERE company_id='.$company_id;
//        $command=$conn->createCommand($sql3);
//        $jobs_require = $command->queryScalar();
//        $this->smarty->assign('jobs_require',$jobs_require);

        Yii::app()->session['companyName']=$company->name;
        Yii::app()->session['concern_num'] = $company->concern_num;//当前公司关注数量
        $this->smarty->assign('username',$username);
        $this->smarty->display('recruitEntrance/index.html');
    }


}