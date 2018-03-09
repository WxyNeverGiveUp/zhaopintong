<?php
/**
 * Created by PhpStorm.
 * User: 冬阳
 * Date: 2015/4/29
 * Time: 20:02
 */
class CTService{
    public function search($keyword,$time,$industryId,$type,$page){
        if($page==0){
            $offset = '';
        }else{
            $offset = ' limit 10 offset '.($page-1)*10;
        }

        if($time==1){
            $tSql = ' to_days(time) = to_days(now())';
            //to_days()是一个sql函数，根据括号内值返回一个日期（YMD格式）
        }elseif($time==2){
            $tSql = ' to_days(t_career_talk.time) - to_days(now()) <= 7
                and to_days(t_career_talk.time) - to_days(now()) >= 0';
        }elseif($time==3){
            $tSql = ' to_days(t_career_talk.time) - to_days(now()) > 7
                and to_days(t_career_talk.time) - to_days(now()) >= 0';
        }else{
            $tSql = '';
        }

        if($keyword==null && $type==0 && $time == 0 && $industryId == 0){
            $searchSql = '';
        }elseif($keyword==null && $time==0 && $industryId==0){
            $searchSql = ' where t_career_talk.type = '.$type;
        }elseif($keyword==null && $time==0 && $type==0) {
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ')';
        }elseif($keyword==null && $industryId==0 && $type==0) {
            $searchSql = ' where '. $tSql;
        }elseif($type==0 && $time==0 && $industryId==0){
            $searchSql = ' where t_career_talk.name LIKE "%'.$keyword.'%"';
        }elseif($keyword==null && $type==0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and '.$tSql;
        }elseif($keyword==null && $time == 0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and t_career_talk.type = '.$type;
        }elseif($keyword==null && $industryId == 0){
            $searchSql = ' where t_career_talk.type = '.$type.' and '.$tSql;
        }elseif($type==0 && $time == 0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and
            t_career_talk.name LIKE '.'"%'.$keyword.'%"';
        }elseif($type==0 && $industryId == 0){
            $searchSql = ' where t_career_talk.name LIKE '.'"%'.$keyword.'%" and '.$tSql;
        }elseif($time == 0 && $industryId == 0){
            $searchSql = ' where t_career_talk.type = '.$type.' and
            t_career_talk.name LIKE '.'"%'.$keyword.'%"';
        }elseif($keyword==null){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id in ' . $industryId . ') and t_career_talk.type = '.$type.
                ' and '.$tSql;
        }elseif($type==0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and
             t_career_talk.name LIKE '.'"%'.$keyword.'%" and '.$tSql;
        }elseif($time == 0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and t_career_talk.type = '.$type.
                ' and t_career_talk.name LIKE '.'"%'.$keyword.'%"';
        }elseif($industryId == 0){
            $searchSql = ' where t_career_talk.type = '.$type.' and '.$tSql.
                ' and t_career_talk.name LIKE '.'"%'.$keyword.'%"';
        }

        $sql = '
        select
            t_career_talk.id,
            t_career_talk.name,
            t_career_talk.time,
            t_career_talk.place,
            t_career_talk.type,
            t_career_talk.company_id,
            t_career_talk.url,
            t_career_talk.last_update
        from
          t_career_talk
        '.$searchSql.$offset;
        $searchList = Yii::app()->db->createCommand($sql)->queryAll();
        return $searchList;
    }

    //智能分析
    public function intelligentAnalytic($CTId){
        $j = $CTId;
        $sql0 = 'select count(id) from t_study_specialty';
        $countPT = Yii::app()->db->createCommand($sql0)->queryRow();
        $PTC = intval($countPT['count(id)']);
        $sql1 = 'select name from t_study_specialty';
        $listPT = Yii::app()->db->createCommand($sql1)->queryAll();
        for($i = 0;$i<$PTC;$i++){
            $professionalType[$i] = $listPT[$i]['name'];
        }
        $PTData = array();                                      //用于存储每个专业类别对应的报名人数
        for($i=0;$i<$PTC;$i++){
            $PTData[$i] = 0;
        }
        $a = array($professionalType,$PTData);                               //用于存储专业类型分布情况的数组
        $degree = array('本科','硕士','博士','专科','其它');
        $DData = array(0,0,0,0,0);
        $b = array($degree,$DData);                               //用于存储学历分布情况的数组

        $sql2 = 'select count(user_id) from t_career_talk_user
              where career_talk_id = '.$j;                                      //计算该宣讲会一共有多少注册用户报名
        $countU = Yii::app()->db->createCommand($sql2)->queryRow();
        $countUC = intval($countU['count(user_id)']);//不要问我为什么下标号刚好是count(user_id)
        //echo '有这么多个用户报名该宣讲会  '.$countUC.'<br>';

        $sql3 = 'select user_id from t_career_talk_user
              where career_talk_id ='.$j;                                  //一列已经报名ID为j的宣讲会的用户ID
        $userIdList2 = Yii::app()->db->createCommand($sql3)->queryAll();
        //$cc = count($userIdList2);
        for($i = 0;$i<$countUC;$i++){
            $userIdList4[$i] = intval($userIdList2[$i]['user_id']);
        }

        //echo('开始智能分析了<br>');
        //$cacheP = 0;
        //$cacheD = 0;
        for($l=0;$l<$countUC;$l++){                      //将报名这个宣讲会的每个用户ID对应的专业类别、学历进行统计
            $sql4 = 'select
                      study_specialty_id
                      from t_study_experience
                        where t_study_experience.user_id = '.$userIdList4[$l] ;  //通过宣讲会的报名人ID获得其专业类别ID
            $PTypeId = Yii::app()->db->createCommand($sql4)->queryRow();
            $PTypeIdN = intval($PTypeId['study_specialty_id'])-1;

            //if($PTypeIdN){
            //    $cacheP++;
            $a[1][$PTypeIdN] ++;
            //}
            $sql5 = 'select
                      position_degree_id
                      from t_study_experience
                        where t_study_experience.user_id = '.$userIdList4[$l];//获得学历ID
            $degreeId = Yii::app()->db->createCommand($sql5)->queryRow();
            $degreeIdI = intval($degreeId['position_degree_id'])-1;
            //if($degreeIdI){
                //$cacheD++;
            //}
            $b[1][$degreeIdI] ++;
        }

        for($l = 0;$l<47;$l++){                                         //专业类别排序，最大的放到前面
            for($m = $l+1;$m<120;$m++){
                if($a[1][$l]<=$a[1][$m]){
                    $cache1 = $a[0][$m];
                    $a[0][$m] = $a[0][$l];
                    $a[0][$l] = $cache1;
                    $cache2 = $a[1][$m];
                    $a[1][$m] = $a[1][$l];
                    $a[1][$l] = $cache2;
                }
            }
        }
        for($l = 0;$l<5;$l++){
            for($m = $l+1;$m<5;$m++){
                if($b[1][$l]<=$b[1][$m]){
                    $cache3 = $b[1][$m];
                    $b[1][$m] = $b[1][$l];
                    $b[1][$l] = $cache3;
                    $cache4 = $b[0][$m];
                    $b[0][$m] = $b[0][$l];
                    $b[0][$l] = $cache4;
                }
            }
        }

        if($countUC == 0){//有的宣讲会没人报名，则返回一个固定值'dong'
            $e = 'dong';
        }
        else{
            $e = array($a,$b);   //最后结果，$e[0][0]是专业类型排序好的一列，$e[0][1]是专业类型排序好的具体数字;
            //$e[1][0]是排序好的学历列，$e[1][1]是排序好的学历列的具体数字
        }
        return $e;
    }

    /*public function calendarList($date){//这里就不用service了，毕竟就一个查询，没啥复杂的功能，直接用criteria快些
        $sql = 'select name,type,place
        from t_career_talk
        where time = '.$date;
    }*/

    public function diffBetweenTwoDays ($day1, $day2)//计算两个日期隔了几天，下面的searchForFront的必备函数，勿删！
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        return ($second1 - $second2) / 86400;
    }
    public function searchForFront($keyword,$time,$industryId,$type,$page,$id,$userId,$isFollow){
        if($page==0){
            $offset = '';
        }else{
            $offset = ' limit 10 offset '.($page-1)*10;
        }
        if($type==3) {
            $typeSearch = ' where (t_career_talk.type = 3 or t_career_talk.type=4 or t_career_talk.type=5 or t_career_talk.type=6 or t_career_talk.type=7 or t_career_talk.type=8)';
            $typeSearch2 = ' and (t_career_talk.type = 3 or t_career_talk.type=4 or t_career_talk.type=5 or t_career_talk.type=6 or t_career_talk.type=7 or t_career_talk.type=8)';
        }
        elseif($type==1 or $type==2){
            $typeSearch = ' where t_career_talk.type = ' .$type;
            $typeSearch2 = ' and t_career_talk.type = '.$type;
        }
        if($time==1){
            $tSql = ' to_days(t_career_talk.time) = to_days(now())';
            //to_days()是一个sql函数，根据括号内值返回一个日期（YMD格式）
        }elseif($time==2){
            $tSql = ' to_days(t_career_talk.time) - to_days(now()) <= 7
                and to_days(t_career_talk.time) - to_days(now()) >= -7';
        }elseif($time==3){
            $tSql = ' to_days(t_career_talk.time) - to_days(now()) > 7';
        }elseif($time==4) {
            $tSql = ' 1=1';
        }
        elseif($time==5){
                $tSql = ' to_days(t_career_talk.time) - to_days(now()) <= 7
                and to_days(t_career_talk.time) - to_days(now()) >= 0';
        }else
            $tSql = ' to_days(t_career_talk.time) - to_days(now()) >= 0';
        if($keyword==null && $type==0 && $industryId == 0){
            $searchSql = ' where '. $tSql;
        }elseif($keyword==null && $type==0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and '.$tSql;
        }elseif($keyword==null && $industryId == 0){
            $searchSql = $typeSearch.' and '.$tSql;
        }elseif($type==0 && $industryId == 0){
            $searchSql = ' where t_career_talk.name LIKE '.'"%'.$keyword.'%" and '.$tSql;
        }elseif($keyword==null){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ')'.$typeSearch2.
                ' and '.$tSql;
        }elseif($type==0){
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ') and
             t_career_talk.name LIKE "%'.$keyword.'%" and '.$tSql;
        }elseif($industryId == 0){
            $searchSql = $typeSearch.' and '.$tSql.
                ' and t_career_talk.name LIKE "%'.$keyword.'%"';
        }else{
            $searchSql = ' where t_career_talk.company_id in (select t_company.id
            from t_company where trade_id = ' . $industryId . ')'.$typeSearch2.
                ' and t_career_talk.name LIKE "%'.$keyword.'%"'.$tSql;
        }


        if($id!=0)
            $searchSql .= ' and t_career_talk.company_id='.$id;
        if($userId!=0)
            $searchSql .= ' and t_career_talk.id in (select t_career_talk_user.career_talk_id
            from t_career_talk_user where user_id = ' . $userId. ')';
        if($isFollow==1)
            $searchSql .= ' and t_career_talk.company_id in (select t_company_user.company_id
            from t_company_user where user_id = '.Yii::app()->session['user_id'].')';
        $searchSql .= ' and (t_career_talk.is_front_input=0 OR t_career_talk.is_ok=1)';

        $sql = '
        select
            t_career_talk.id,
            t_career_talk.time,
            t_career_talk.name,
            t_career_talk.place as location,
            t_career_talk.type as preachType,
            t_career_talk.company_id,
            t_career_talk.description,
            t_company.name as company
        from
          t_career_talk left outer join t_company on t_career_talk.company_id =t_company.id
        '.$searchSql.' order by t_career_talk.time'.$offset;
        $searchList = Yii::app()->db->createCommand($sql)->queryAll();
        $countSearchResult = count($searchList);
        for($i = 0;$i<$countSearchResult;$i++){
            $today = Date('y-m-d');//注意这里的日期是XX-XX-XX，不是常见的XXXX-XX-XX
            $CTTimeArr = str_split($searchList[$i]['time']);
            $CTDate = $CTTimeArr[2].$CTTimeArr[3].$CTTimeArr[4].$CTTimeArr[5].$CTTimeArr[6].$CTTimeArr[7].$CTTimeArr[8].
                $CTTimeArr[9];
            $CTDateD = Date($CTDate);
            $differential = $this->diffBetweenTwoDays($CTDateD,$today);
            if($differential>-1){
                $searchList[$i]['isOverdue'] = 0;
            }else{
                $searchList[$i]['isOverdue'] = 1;
            }
        }
        return $searchList;
    }

    public function recommendPreach(){
        $list = $this->searchForFront(null,3,0,0,0,0,0,0);
        return $list;
    }

    public function indexLiveCT(){
        /*$list = CareerTalk::model()->findAll(array(
            'condition' => 'type=1',
            'limit' => 7,
        ));*/
     $list = $this->searchForFront(null,0,0,1,0,0,0,0);
     return $list;
    }

    public function indexPreach(){
        $list = Company::model()->with('careertalk')->findAll(array('order' => 'entering_time DESC'));
        foreach ($list as $key => $value) {
            if(isset($value->id))
                $preachs[$key]['cid'] = $value->id;
            else
                $preachs[$key]['cid'] = 0;
            if(isset($value->name))
                $preachs[$key]['name'] = $value->name;
            else
                $preachs[$key]['name'] = null;
            if(isset($value->entering_time))
                $preachs[$key]['time'] = $value->entering_time;
            else
                $preachs[$key]['time'] = null;
        }
        return $preachs;
    }

    static private $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new CTService();
        }
        return self::$instance;
    }
    private function __construct(){
    }

    private function __clone(){
    }
}