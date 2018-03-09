<?php

class UploadPicService {
	/**
	 * 文件上传
	 */
	public function upload(){
		//url为文件上传时input的name
		$file = CUploadedFile::getInstanceByName('url');
		if(!$file){
			echo "文件不存在";
			$fileBUrl=null;
		}else {
			//文件保存路径
			$uploadPath ="assets/uploadFile/head/";
			//获取文件后缀名
			$extName = $file->getExtensionName();
			//给文件重命名
			$fileName = time().'.'.$extName;
			//保存文件
			$fileUrl = $uploadPath.$fileName;
			$file->saveAs($fileUrl);
		}
		return $fileName;
	}

    /**
     * 两个文件上传
     */
    public function uploadTwo(){
        //url为文件上传时input的name
        $file = CUploadedFile::getInstanceByName('daima');
        $file2 = CUploadedFile::getInstanceByName('zhizhao');
        if(!$file){
            echo "文件不存在";
            $fileBUrl1=null;
        }
        elseif(!$file2){
            echo "文件不存在";
            $fileBUrl2=null;
        }
        else {
            //文件保存路径
            $uploadPath ="assets/uploadFile/recruitEntrance/";
            //获取文件后缀名
            $extName = $file->getExtensionName();
            $extName2 = $file2->getExtensionName();
            //给文件重命名
            $fileName = time().'.'.$extName;
            $fileName2= (time()+1).'.'.$extName2;

            //保存文件
            $fileUrl = $uploadPath.$fileName;
            $fileUrl2 = $uploadPath.$fileName2;
            $file->saveAs($fileUrl);
            $file2->saveAs($fileUrl2);
            return array($fileUrl,$fileUrl2);
        }

    }
    /**
     * 三个图片上传
     */
    public function uploadThree(){
        //url为文件上传时input的name
        $file = CUploadedFile::getInstanceByName('companyImg1');
        $file2 = CUploadedFile::getInstanceByName('companyImg2');
        $file3 = CUploadedFile::getInstanceByName('companyImg3');
        //文件保存路径
        $uploadPath ="assets/uploadFile/companyImg/";
        $fileUrl=null;
        $fileUrl2=null;
        $fileUrl3=null;
        //获取文件后缀名
        if($file!=null){
            $fileBUrl1=null;
            $extName = $file->getExtensionName();
            //给文件重命名
            $fileName = (time()+5).'.'.$extName;
            //保存文件
            $fileUrl = $uploadPath.$fileName;
            $file->saveAs($fileUrl);
        }
        if($file2!=null){
            $fileBUrl2=null;
            $extName2 = $file2->getExtensionName();
            $fileName2= (time()+10).'.'.$extName2;
            $fileUrl2 = $uploadPath.$fileName2;
            $file2->saveAs($fileUrl2);
        }
        if($file3!=null){
            $fileBUrl3=null;
            $extName3 = $file3->getExtensionName();
            $fileName3= (time()+15).'.'.$extName3;
            $fileUrl3 = $uploadPath.$fileName3;
            $file3->saveAs($fileUrl3);
        }
            return array($fileUrl,$fileUrl2,$fileUrl3);
    }
    static private $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new UploadPicService();

        }
        return self::$instance;
    }
    private function __construct(){

    }

    private function __clone(){

    }
}
