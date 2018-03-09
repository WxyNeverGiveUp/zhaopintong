<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/24
 * Time: 下午2:27
 */

class AboutUsController extends Controller{

    /**
     * @param $id
     * 关于我们的详细页显示
     */
    function actionDetail($id){
        $aboutUs = AboutUs::model()->findByPk($id);
        $title = $aboutUs->title;
        $content = $aboutUs->content;

        $this->smarty->assign('title', $title);
        $this->smarty->assign('content',$content);
        $this->smarty->assign('current','aboutMe');
        $this->smarty->display('admin/aboutUs/detail.html');
    }

    /**
     * 列出所有关于我们的子项
     */
    function actionList(){
        $list = AboutUs::model()->findAll();
        $this->smarty->assign('list',$list);
        $this->smarty->assign('current','aboutMe');
        $this->smarty->display('admin/aboutUs/list.html');
    }

    /**
     * @param $id
     * 跳转到编辑某一个关于我们的子项
     */
    function actionToEdit($id){
        $aboutUs = AboutUs::model()->findByPk($id);
        $title = $aboutUs->title;
        $content = $aboutUs->content;
        $id = $aboutUs->id;

        $this->smarty->assign('title', $title);
        $this->smarty->assign('content',$content);
        $this->smarty->assign('id',$id);
        $this->smarty->assign('current','aboutMe');
        $this->smarty->display('admin/aboutUs/edit.html');
    }

    /**
     * @param $id
     * 编辑某一个关于我们的子项
     */
    function actionEdit($id){
        $aboutUs = AboutUs::model()->findByPk($id);
        $aboutUs->title = $_POST['title'];
        $aboutUs->content = $_POST['content'];
        $aboutUs->save();

        $this->redirect($this->createUrl('admin/aboutUs/aboutUs/list'));
    }
}