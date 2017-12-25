<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       $this->display();
    }

    public function upload(){
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('xls','jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
	    $upload->savePath  =     ''; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if(!$info) {// 上传错误提示错误信息
	        $this->error($upload->getError());
	    }
	    dump($info);
	    $file_name =  $upload->rootPath.$info['photo']['savepath'].$info['photo']['savename'];
	    $photo = $this->import_file($file_name);
	    dump($photo);
	    $this->success('导入成功','index.php?s=/Home/Index/index');
	    //读取后的相关处理
	    // 去掉第exl表格中第一行
        // unset($photo[0]);
        // // 清理空数组
        // foreach($exl as $k=>$v){
        //     if(empty($v)){
        //         unset($exl[$k]);
        //     }    
        // };
	}

	public function import_file($file_name = ''){

		$file_name= './Uploads/1.xls';
        import("Common.Util.PHPExcel.PHPExcel.Classes.PHPExcel",'','.php');   // 这里不能漏掉
        import("Common.Util.PHPExcel.Classes.PHPExcel.IOFactory",'','.php');
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $data = array();
       	for($row = 1; $row <= $highestRow; $row ++){
        	for ($column = ord('A'); $column <= ord($highestColumn); $column ++) { 
 				$data[$row][] =  $sheet->getCellByColumnAndRow($column-65 ,$row)->getValue();
           }
        }
       return  $data;

	}

}