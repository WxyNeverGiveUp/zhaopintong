<?php
/**
 * Created by PhpStorm.
 * User: erdan
 * Date: 2015/7/29
 * Time: 15:34
 */

class TestCommand  extends  CConsoleCommand{
    public  function run(){
        $about = new AboutUs();
        $about->title = 'as';
        $about->content = 'as';
        $about->save();
    }
}