<?php
/**
 * Created by PhpStorm.
 */
class AnnouncementService{

    /**
     * @return array
     * 首页的8条公告
     */
    public function indexAnnouncement(){
        $conditions ="'1'='1' and c.is_tea_recruitment = 0 ";
        $params = array();

        $command = Yii::app()->db->createCommand()
            ->select('c.id,c.title,c.add_time')
            ->from('t_announcement c')
            ->order('c.add_time desc');
        $offset = 0;
        $searchListOnePage=$command->where($conditions,$params)->limit(8)->offset($offset)->queryAll();
        return $searchListOnePage;
    }

    /**
     * @param $keyword
     * @param $type_id
     * @return array
     * 搜索
     */
    public function search($keyword,$type_id){
        $conditions ="'1'='1' and c.type_id = d.id and c.is_tea_recruitment = 0 ";
        $params = array();
        if($keyword != null && $keyword!=""){
            $conditions .= " and c.title LIKE :keyword ";
            $params[":keyword"]='%'.$keyword.'%';
        }
        if($type_id != 0 && $type_id!="0"){
            $conditions .= "and c.type_id = :type_id ";
            $params[":type_id"]=$type_id;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.*,d.name as type_name')
            ->from('t_announcement c,t_announcement_type d ')
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
    public function searchWithPage($page,$keyword,$type_id){
        $conditions ="'1'='1' and c.type_id = d.id and c.is_tea_recruitment = 0 ";
        $params = array();
        if($keyword != null && $keyword!=""){
            $conditions .= " and c.title LIKE :keyword ";
            $params[":keyword"]='%'.$keyword.'%';
        }
        if($type_id != 0 && $type_id!="0"){
            $conditions .= "and c.type_id = :type_id ";
            $params[":type_id"]=$type_id;
        }
        $command = Yii::app()->db->createCommand()
            ->select('c.*,d.name as type_name')
            ->from('t_announcement c,t_announcement_type d ')
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
            self::$instance = new AnnouncementService();

        }
        return self::$instance;
    }
    private function __construct(){

    }

    private function __clone(){

    }

}
