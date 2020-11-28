<?php
namespace yangyongxu\upload\local;

use yangyongxu\upload\File;
/**
 * 本地文件接口
 * @author YYX
 */
class Local implements File{
	
	private $config;
	private $error;
	private function __construct($config){
		$con = [
			'ACCESS_ID' => '',
			'signUploadHost' => ''
		];
		$site_id = isset($config['site_id'])?$config['site_id']:0;
		$user_id = isset($config['user_id'])?$config['user_id']:0;
		$con['uploadCallbackUrl'] .='?site_id='.$site_id.'&user_id='.$user_id;
		$this->config = array_merge($con,$config);
	}
	public function putObject($file_path,$file_con,$acl=1){
		$img_content = "data:image/jpeg;base64," . base64_encode($file_con);
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result))
		{
			try {
				return file_put_contents($file_path, base64_decode(str_replace($result[1], '', $img_content)));
			} catch (\Exception $e) {
				$this->error = '文件上传失败';
				return false;
			}
		}
		$this->error = '文件内容错误';
		return false;
	}
	
	public function uploadFile($file_path, $file_url,$acl=1){
		try {
			$path_info = pathinfo($file_path);
			if(!is_dir($path_info['dirname'])){
				mkdir($path_info['dirname'],0777,true);
			}
			return move_uploaded_file($file_url,$file_path);
		} catch (\Exception $e) {
			$this->error = '文件上传失败';
			return false;
		}
	}
	
	public function download($file_path){
		header( "Content-Disposition:  attachment;  filename=".$file_path); //告诉浏览器通过附件形式来处理文件
		header('Content-Length: ' . filesize($file_path)); //下载文件大小
		readfile($file_path);  //读取文件内容
	}
	
	public function setAcl($file_path,$acl){
		$this->error = '本地文件无法设置权限';
		return false;
	}
	
	public function delete($file_path){
		if(file_exists(APP_PATH.'../public'.$file_path))
		{
			return unlink(APP_PATH.'../public'.$file_path);
		}else{
			$this->error = '文件不存在';
		}
		return true;
	}
	
	public function signUrl($file_path,$style=''){
		return $file_path;
	}
	
	public function signUpload($upload_dir){
		$time = time()+600;
		$response = [
			'accessid' => $this->config['ACCESS_ID'],
			'host' => $this->config['signUploadHost'],
			'signature' => md5(md5($this->config['ACCESS_ID']).$this->config['signUploadHost'].md5($time).$this->config['uploadCallbackUrl']),
			'expire' => $time,
			'callback' => $this->config['uploadCallbackUrl'],
			'dir' => $upload_dir, // 这个参数是设置用户上传文件时指定的前缀。
		];
		return $response;
	}
	
	public function error(){
		return $this->error;
	}
}