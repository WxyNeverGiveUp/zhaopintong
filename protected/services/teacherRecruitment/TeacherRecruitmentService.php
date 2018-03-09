<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/20
 * Time: 上午10:50
 */

class TeacherRecruitmentService {


    /**
     * @return array
     * 首页的8条教师招考
     */
    public function indexTeacherRecruitment(){
        $conditions ="'1'='1' and c.is_tea_recruitment = 1 ";
        $params = array();

        $command = Yii::app()->db->createCommand()
            ->select('c.* , p.name as city_name ')
            ->from('t_announcement c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->order('c.add_time desc');
        $command2 = Yii::app()->db->createCommand()
            ->select('c.* , p.name as city_name ')
            ->from('t_announcement c')
            ->join('t_param_city p', 'c.city_id=p.id')
            ->order('c.add_time desc');
        $offset = 0;
        return array($command->where($conditions,$params)->limit(8)->offset($offset)->queryAll(),$command2->where($conditions,$params)->limit(8)->offset($offset+8)->queryAll());
    }

    /**
     * @param $keyword
     * @param $type_id
     * @return array
     * 搜索
     */
    public function search($keyword , $city_id){
        $conditions ="'1'='1' and c.city_id = p.id and c.is_tea_recruitment = 1 ";
        $params = array();
        if($keyword != null && $keyword!=""){
            $conditions .= " and c.title LIKE :keyword ";
            $params[":keyword"]='%'.$keyword.'%';
        }
        if($city_id != null && $city_id != 0 ){
            $conditions .= " and c.city_id = :city_id ";
            $params[":city_id"]=$city_id;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.* , p.name as city_name ')
            ->from('t_announcement c , t_param_city p ')
            ->order('c.add_time desc');
        $recordCount = count($command->where($conditions,$params)->queryAll());
        $searchList = array();
        $searchList['list'] = $command->where($conditions,$params)->queryAll();
        $searchList['recordCount'] = $recordCount;

        return $searchList;
    }

    /**
     * @param $page
     * @param $keyword
     * @param $type_id
     * @return array
     * 搜索分页
     */
    public function searchWithPage($page,$keyword,$city_id){
        $conditions ="'1'='1' and c.city_id = p.id and c.is_tea_recruitment = 1 ";
        $params = array();
        if($keyword != null && $keyword!=""){
            $conditions .= " and c.title LIKE :keyword ";
            $params[":keyword"]='%'.$keyword.'%';
        }
        if($city_id != null && $city_id != 0 ){
            $conditions .= " and c.city_id = :city_id ";
            $params[":city_id"]=$city_id;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.* , p.name as city_name ')
            ->from('t_announcement c , t_param_city p ')
            ->order('c.add_time desc');
        $command2 = clone $command;
        $recordCount = count($command->where($conditions,$params)->queryAll());
        $offset = ($page-1)*13;

        $searchListOnePage = array();
        $searchListOnePage['list']=$command2->where($conditions,$params)->limit(13)->offset($offset)->queryAll();
        $searchListOnePage['recordCount']=$recordCount;

        return $searchListOnePage;
    }

    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new TeacherRecruitmentService();

        }
        return self::$instance;
    }
    private function __construct(){

    }

    private function __clone(){

    }
}