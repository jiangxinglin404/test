<?php 
/**
 *	$fileName UploadFile.class.php	
 *	$description ֧�ֶ��ļ��ϴ����ļ��ϴ���
 *	$date 2013-02-02 21:15
 *	$author silenceper
 *	$version 1.0
 */
class UploadFile{
	//�������չ��
	public $allowExts=array();
	//������ļ�����
	public $allowTypes=array();
	//�ļ�����·��
	public $savePath='./upload/';
	//����ϴ���С Ĭ������ϴ� 2M =2097152 B
	public $maxSize=2097152;
	//���һ�εĴ��� 
	private $error='';
	//�Զ�����ļ� Ĭ��δ����
	public $autoCheck=true;
	//�Ƿ񸲸�ͬ���ļ�  Ĭ�ϲ�����
	public $uploadReplace=false;
	//�ļ��ϴ���Ϣ
	private $uploadFileInfo;
	
	/**
	 * �ܹ�����
	 * 
	 */
	public function __construct($allowExts='',$maxSize='',$allowTypes=''){
		//�����ļ��ĺ�׺
		if(!empty($allowExts)){
			if(is_array($allowExts)){
				$this->allowExts=array_map('strtolower',$allowExts);
			}else{
				$this->allowExts=explode(',',strtolower($allowExts));
			}
		}
		//���ô�С
		if(!empty($maxSize) && is_numeric($maxSize)){
			$this->maxSize=$maxSize;
		}
		//�������������
		if(!empty($allowTypes)){
			if(is_array($allowTypes)){
				$this->allowTypes=array_map('strtolower',$allowTypes);
			}else{
				$this->allowTypes=explode(',',strtolower($allowTypes));
			}
		}
		
	}
	
	/**
	 * ����һ���ļ�
	 * 
	 */
	private function save($file){
		$filename = $file['savepath'].$file['savename'];
		if(!$this->uploadReplace && is_file($filename)) {
			// ������ͬ���ļ�
			$this->error	=	'�ļ��Ѿ����ڣ�'.$filename;
			return false;
		}
		// �����ͼ���ļ� ����ļ���ʽ
		if( in_array(strtolower($file['extension']),array('gif','jpg','jpeg','bmp','png','swf')) && false === getimagesize($file['tmp_name'])) {
			$this->error = '�Ƿ�ͼ���ļ�';
			return false;
		}
		//�ϴ��ļ�
		if(!move_uploaded_file($file['tmp_name'], $filename)) {
			$this->error = '�ļ��ϴ��������';
			return false;
		}
		return true;
	}
	/**
	 * �ϴ������ļ�
	 * 
	 */
	public function upload($savePath=''){
		if(empty($savePath))
			$savePath=$this->savePath;
		$savePath=rtrim($savePath,'/').'/';
		if(!is_dir($savePath)){
			//Ŀ¼���������Դ���
			if(!mkdir($savePath)){
				$this->error="Ŀ¼$savePath������";
				return false;
			}	
		}else{
			//���Ŀ¼�Ƿ��д
			if(!is_writeable($savePath)){
				$this->error="Ŀ¼$savePath����д";
				return false;
			}
		}
		
		$fileInfo = array();
		$isUpload   = false;
		// ��$_FILES������Ϣ����
		$files	 =	 $this->dealFiles($_FILES);
		foreach ($files as $key=>$file){
			if(!empty($file['name'])){
				//����file��Ϣ
				$file['key']          =  $key;
				$file['extension']  = $this->getExt($file['name']);
				$file['savepath']   = $savePath;
				$file['savename']   = $this->getSaveName($file);
				// �Զ���鸽��
				if($this->autoCheck) {
					if(!$this->check($file))
						return false;
				}
				
				//�����ļ�
				if(!$this->save($file)) return false;
				//�ϴ��ɹ��󱣴��ļ���Ϣ���������ط�����
				unset($file['tmp_name'],$file['error']);
				$fileInfo[] = $file;
				$isUpload   = true;
				
			}
		}
		if($isUpload) {
			$this->uploadFileInfo = $fileInfo;
			return true;
		}else {
			$this->error  =  'û��ѡ���ϴ��ļ�';
			return false;
		}
	}
	
	/**
	 * ͨ��ָ���ļ���$_FILES['name']�ϴ��ļ� 
	 */
	public function uploadOne($file,$savePath=''){
		//�����ָ�������ļ���������ϵͳĬ��
		if(empty($savePath))
			$savePath = $this->savePath;
		$savePath=rtrim($savePath,'/').'/';
		// ����ϴ�Ŀ¼
		if(!is_dir($savePath)) {
			// ���Դ���Ŀ¼
			if(!mk_dir($savePath)){
				$this->error  =  '�ϴ�Ŀ¼'.$savePath.'������';
				return false;
			}
		}else {
			if(!is_writeable($savePath)) {
				$this->error  =  '�ϴ�Ŀ¼'.$savePath.'����д';
				return false;
			}
		}
		//������Ч���ϴ�
		if(!empty($file['name'])) {
			$fileArray = array();
			if(is_array($file['name'])) {
				$keys = array_keys($file);
				$count	 =	 count($file['name']);
				for ($i=0; $i<$count; $i++) {
					foreach ($keys as $key)
						$fileArray[$i][$key] = $file[$key][$i];
				}
			}else{
				$fileArray[] =  $file;
			}
			$fileInfo =  array();
			foreach ($fileArray as $key=>$file){
				//�Ǽ��ϴ��ļ�����չ��Ϣ
				$file['extension']  = $this->getExt($file['name']);
				$file['savepath']   = $savePath;
				$file['savename']   = $this->getSaveName($file);
				// �Զ���鸽��
				if($this->autoCheck) {
					if(!$this->check($file))
						return false;
				}
				//�����ϴ��ļ�
				if(!$this->save($file)) return false;
				unset($file['tmp_name'],$file['error']);
				$fileInfo[] = $file;
			}
			
			$this->uploadFileInfo = $fileInfo;
			// �����ϴ����ļ���Ϣ
			return true;
		}else {
			$this->error  =  'û��ѡ���ϴ��ļ�';
			return false;
		}
	}
	
	/**
	 * ����$_FILES��Ϣ  �����file����
	 */
	private function dealFiles($files){
		$fileArray = array();
		$n = 0;
		foreach($files as $file){
			if(is_array($file['name'])){
				//��������
				$keys = array_keys($file);
				$count = count($file['name']);
				for($i=0;$i<$count;$i++){
					foreach ($keys as $key)
						$fileArray[$n][$key] = $file[$key][$i];
					$n++;
				}
			}else{
				$fileArray[$n]=$file;
				$n++;
			}
		}
		
		return $fileArray;
	}
	
	/**
	 * ��ȡ��չ��
	 */
	private function getExt($filename){
		$pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
	}	
	
	/**
	 * �ļ����� ����
	 */
	private function getSaveName($file){
		$saveName = md5(uniqid()).'.'.$file['extension'];
		return $saveName;
	}
	
	/**
	 * ��������ϴ���Ϣ
	 */
	private function error($errorCode){
		switch($errorCode) {
            case 1:
                $this->error = '�ϴ����ļ������� php.ini �� upload_max_filesize ѡ�����Ƶ�ֵ';
                break;
            case 2:
                $this->error = '�ϴ��ļ��Ĵ�С������ HTML ���� MAX_FILE_SIZE ѡ��ָ����ֵ';
                break;
            case 3:
                $this->error = '�ļ�ֻ�в��ֱ��ϴ�';
                break;
            case 4:
                $this->error = 'û���ļ����ϴ�';
                break;
            case 6:
                $this->error = '�Ҳ�����ʱ�ļ���';
                break;
            case 7:
                $this->error = '�ļ�д��ʧ��';
                break;
            default:
                $this->error = 'δ֪�ϴ�����';
        }
        return ;
	}
	
	/**
	 * ����ļ���С���ļ���չ�����ļ�Mime���ͣ��Ƿ�Ƿ��ϴ�
	 * 
	 */
	private function check($file){
		if($file['error']!==0){
			//�ļ��ϴ�ʧ��
			//����������
			$this->error($file['error']);
			return false;
		}
		//����ļ���С
		if(!$this->checkSize($file['size'])){
			$this->error = '�ϴ��ļ���С������';
            return false;
		}
		//����ļ���չ��
		if(!$this->checkExt($file['extension'])){
			$this->error = '�ϴ��ļ����Ͳ�����';
			return false;
		}
		//����ļ�Mime����
		if(!$this->checkType($file['type'])) {
			$this->error = '�ϴ��ļ�MIME���Ͳ�����';
			return false;
		}
		//����Ƿ�Ƿ��ϴ�
		if(!$this->checkUpload($file['tmp_name'])) {
			$this->error = '�Ƿ��ϴ��ļ���';
			return false;
		}
		return true;
	}
	
	/**
	 * ����ļ���С
	 */
	private function checkSize($size){
		return $size < $this->maxSize;
	}
	
	/**
	 * ����ļ���չ��
	 * 
	 */
	private function checkExt($extension){
		if(!empty($this->allowExts))
            return in_array(strtolower($extension),$this->allowExts,true);
        return true;
	}
	
	/**
	 * ����ļ�Mime����
	 */
	private function checkType($type){
		if(!empty($this->allowTypes))
			return in_array(strtolower($type),$this->allowTypes,true);
		return true;
	}
	
	/**
	 * ����Ƿ�Ƿ��ϴ�
	 */
	private function checkUpload($filename){
		return is_uploaded_file($filename);
	}
	/**
	 * ��ȡ�ļ��ϴ��ɹ�֮�����Ϣ
	 */
	public function getUploadFileInfo(){
        return $this->uploadFileInfo;
    }
    /**
     * ��ȡ���һ�εĴ�����Ϣ
     * 
     */
    public function getErrorMsg(){
    	return $this->error;
    }
}
?>