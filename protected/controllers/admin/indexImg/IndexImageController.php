<?php
/**
 * Created by PhpStorm.
 * User: lishicao
 * Date: 15/7/28
 * Time: 下午6:30
 */

/**
 * Class IndexImageController
 * 首页滚动图片的Controller
 */
class IndexImageController extends Controller {

    /**
     * 跳转到增加页面
     */
    public function actionToAdd(){
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/indexImg/add.html');
    }

    /**
     * 增加首页图片
     */
    public function actionAdd(){
        if( empty($_FILES['file']['tmp_name']) ){
            $this->smarty->assign('message','请选择文件再上传');
            $this->smarty->display('admin/user/error.html');
        }

        if( $_FILES['file']['error'] > 0 ) {
            $this->smarty->assign('message','上传文件出错');
            $this->smarty->display('admin/user/error.html');
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $file_name = '';
        for ($i = 1; $i <= 15; $i++) {
            $file_name .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $file_name .= '.';
        $file_name .=pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $indexImage = new IndexImage();
        $indexImage->name = $_POST['name'];
        $indexImage->url = $_POST['url'];
        $indexImage->number = $_POST['number'];
        $indexImage->path = 'assets/uploadFile/indexImg/'.$file_name;

        $Image = IndexImage::model()->find('path=:path',array(':path'=>$indexImage->path));
        while( $Image != null ){
            $file_name = '';
            for ($i = 1; $i <= 15; $i++) {
                $file_name .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            $file_name .= '.';
            $file_name .=pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $indexImage->path = 'assets/uploadFile/indexImg/'.$file_name;
            $Image = IndexImage::model()->find('path=:path',array(':path'=>$indexImage->path));
        }
        move_uploaded_file( $_FILES['file']['tmp_name'],$indexImage->path);
        $indexImage->save();
        $this->redirect($this->createUrl('admin/indexImg/indexImage/list'));
    }

    /**
     * 列出图片
     */
    public function actionList(){
        $list = IndexImage::model()->findAll();
        $this->smarty->assign('list',$list);
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/indexImg/list.html');
    }

    /**
     * @param $id
     * 跳转到删除
     */
    public function actionToDelete($id){
        $image = IndexImage::model()->findByPk($id);
        $this->smarty->assign('image',$image);
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/indexImg/delete.html');
    }

    /**
     * @param $id
     * 删除图片
     */
    public function actionDelete($id){
        $image = IndexImage::model()->findByPk($id);

        if( file_exists($image->path)) {
            if (unlink($image->path)) {
                $image->delete();
                $this->redirect($this->createUrl('admin/indexImg/indexImage/list'));
            } else {
                $this->smarty->assign('message', '删除失败');
                $this->smarty->display('admin/user/error.html');
            }
        }
        else {
            $image->delete();
            $this->redirect($this->createUrl('admin/indexImg/indexImage/list'));
        }
    }

    /**
     * @param $id
     * 显示详情
     */
    public function actionDetail($id){
        $image = IndexImage::model()->findByPk($id);
        $this->smarty->assign('image',$image);
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/indexImg/detail.html');
    }

    /**
     * @param $id
     * 跳转到编辑页面
     */
    public function actionToEdit($id){
        $image = IndexImage::model()->findByPk($id);
        $this->smarty->assign('image',$image);
        $this->smarty->assign('current','system');
        $this->smarty->display('admin/indexImg/edit.html');
    }

    /**
     * @param $id
     * 编辑
     */
    public function actionEdit($id)
    {
        if ($_FILES['file']['error'] > 0 && $_FILES['file']['error'] != 4) {
            $this->smarty->assign('message', '上传文件出错' . $_FILES['file']['error']);
            $this->smarty->display('admin/user/error.html');
        } else {
            $indexImage = IndexImage::model()->findByPk($id);
            if($_FILES['file']['error'] == 4){
                $indexImage->name = $_POST['name'];
                $indexImage->url = $_POST['url'];
                $indexImage->number = $_POST['number'];
                $indexImage->update();
                $this->redirect($this->createUrl('admin/indexImg/indexImage/list'));
            }
            else{
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $file_name = '';
                for ($i = 1; $i <= 15; $i++) {
                    $file_name .= $chars[mt_rand(0, strlen($chars) - 1)];
                }
                $file_name .= '.';
                $file_name .= pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                if(is_file($indexImage->path)) unlink($indexImage->path);

                $indexImage->name = $_POST['name'];
                $indexImage->url = $_POST['url'];
                $indexImage->number = $_POST['number'];
                $indexImage->path = 'assets/uploadFile/indexImg/' . $file_name;

                $Image = IndexImage::model()->find('path=:path', array(':path' => $indexImage->path));
                while ($Image != null) {
                    $file_name = '';
                    for ($i = 1; $i <= 15; $i++) {
                        $file_name .= $chars[mt_rand(0, strlen($chars) - 1)];
                    }
                    $file_name .= '.';
                    $file_name .= pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $indexImage->path = 'assets/uploadFile/indexImg/' . $file_name;
                    $Image = IndexImage::model()->find('path=:path', array(':path' => $indexImage->path));
                }
                move_uploaded_file($_FILES['file']['tmp_name'], $indexImage->path);
                $indexImage->update();
                $this->redirect($this->createUrl('admin/indexImg/indexImage/list'));
            }
        }
    }
}