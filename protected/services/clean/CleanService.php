<?php
/**
 * Created by PhpStorm.
 * User: lixiang
 * Date: 2015/9/29
 * Time: 0:58
 */
class CleanService {

    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new CleanService();
        }
        return self::$instance;
    }
    private static $keyword = array();

    /*public function getBadWords($filename){
        return self::$keyword;
    }*/
    //static private $badword1 = null;
    public  function clean($content){
        $str = strtr($content, self::$keyword);
        return $str;
    }

    private function __construct(){
        //require_once(YII::app()->basePath.'/components/badword.php');
        //self::$keyword = $badword;
        $file_handle = fopen(YII::app()->basePath.'/components/word.txt', "r");
        while (!feof($file_handle)) {
            $line = str_replace('={MOD}','',trim(fgets($file_handle)));
            $line = str_replace('={BANNED}','',$line);
            $line = str_replace('{2}','',$line);
            array_push(self::$keyword,$line);
        }
        fclose($file_handle);
        self::$keyword = array_combine(self::$keyword,array_fill(0,count(self::$keyword),'*'));
    }
    private function __clone(){
    }
}