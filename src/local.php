<?php
namespace yangyongxu\upload;

use Qiniu\Auth;
/**
 * 七牛云文件接口
 * @author YYX
 */
class Qiniu implements Uploadinterface{
	
	private $config;
	private $error;
	public function __construct($config){
		$con = [
				'ACCESS_KEY' => '24524',//accessKeyId
				'SECRET_KEY' => '242',//accessKeySecret
				'ENDPOINT' => 'oss-cn-chengdu-422.aliyuncs.com',//endpoint
				'signUploadHost' => 'https://757.oss-cn-chengdu.aliyuncs.com',
				'uploadCallbackUrl' => 'https://api.72575.com/575/57/575.7html',
				'BUCKET' => '4574'
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