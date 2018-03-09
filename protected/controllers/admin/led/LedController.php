<?php
/**
 * Created by PhpStorm.
 * User: lix
 * Date: 2015/10/30
 * Time: 10:23
 */

class LedController extends Controller{
    /**
     *跳到修改页面
     */
    public function actionToEdit(){
        $this->smarty->assign('current','system');
        $this->smarty->assign('content',LedNotice::model()->findByPk(1)->content);
        $this->smarty->assign('title',LedNotice::model()->findByPk(1)->title);
        $this->smarty->display('admin/led/edit.html');
    }

    /**
     *修改内容
     */
    public function actionEdit() {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $led = LedNotice::model()->findByPk(1);
        $led->title = $title;
        $led->content = $content;
        $led->save();
        echo "<script>alert('修改成功')</script>";
        $this->actionToEdit();
    }
}