<?php
	include '../UploadFile.class.php';


	$upload=new UploadFile();
	$upload->maxSize  = 3*pow(2,20) ;// ���ø����ϴ���С  3M    Ĭ��Ϊ2M
	$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// ���ø����ϴ�����   Ĭ��Ϊ�ղ������չ
	$upload->savePath =  './pictures/';// ���ø����ϴ�Ŀ¼   Ĭ���ϴ�Ŀ¼Ϊ ./uploads/
	
	if(!$upload->upload()) {
		// �ϴ�������ʾ������Ϣ
		$this->error($upload->getErrorMsg());
	}else{
		// �ϴ��ɹ� ��ȡ�ϴ��ļ���Ϣ
		$info =  $upload->getUploadFileInfo();
		echo json_encode($info);
	}	

?>
