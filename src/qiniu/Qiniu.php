<?php
namespace yangyongxu\upload;

use Qiniu\Auth;
use yangyongxu\upload\File;
/**
 * 七牛云文件接口
 * @author YYX
 */
class Qiniu implements File{
	
	private $config;
	private $error;
	public function __construct($config){
		$con = [
			'ACCESS_KEY' => '21745',//accessKeyId
			'SECRET_KEY' => '445',//accessKeySecret
			'ENDPOINT' => 'oss-cn-454-internal.aliyuncs.com',//endpoint
			'signUploadHost' => 'https://4534.oss-cn-chengdu.aliyuncs.com',
			'uploadCallbackUrl' => 'https://5324.4524.com/callback/upload/alioss.html',
			'BUCKET' => '4524'
		];
		$this->config = array_merge($con,$config);
		// 初始化签权对象
		return new Auth($this->config['ACCESS_KEY'],$this->config['SECRET_KEY']);
	}
	
	public function putObject($file_path,$file_con){
		;
	}
	
	public function uploadFile($file_path, $file_url){
		;
	}
	
	public function download($file_path){
		;
	}
	
	public function setAcl($file_path,$acl){
		;
	}
	
	public function delete($file_path){
		;
	}
	
	public function signUrl($file_path){
		;
	}
	
	public function signUpload($upload_dir){
		;
	}
	
	public function error(){
		;
	}
}