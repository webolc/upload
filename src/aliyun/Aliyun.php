<?php
namespace yangyongxu\upload\aliyun;

use OSS\OssClient;
use OSS\Core\OssException;
use yangyongxu\upload\File;
/**
 * 阿里云文件接口
 * @author YYX
 */
class Aliyun implements File{
	
	private $config;
	private $error;
	public function __construct($config){
		$con = [
				'ACCESS_ID' => '',//accessKeyId
				'ACCESS_KEY' => '',//accessKeySecret
				'ENDPOINT' => 'oss-cn-chengdu-internal.aliyuncs.com',//endpoint
				'signUploadHost' => 'https://xxx.oss-cn-chengdu.aliyuncs.com',
				'uploadCallbackUrl' => '',
				'BUCKET' => ''
		];
		$site_id = isset($config['site_id'])?$config['site_id']:0;
		$user_id = isset($config['user_id'])?$config['user_id']:0;
		$con['uploadCallbackUrl'] .='?site_id='.$site_id.'&user_id='.$user_id;
		
		$this->config = array_merge($con,$config);
		try {
			$this->client = new OssClient($this->config['ACCESS_ID'],$this->config['ACCESS_KEY'],$this->config['ENDPOINT']);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ . "creating OssClient instance: FAILED";
			return null;
		}
	}
	public function putObject($file_path,$file_con,$acl=1){
		try {
			if ($acl){$acl = 'private';}else{$acl = 'public-read';}
			$options = array(OssClient::OSS_HEADERS => array('x-oss-object-acl' => $acl));
			$result = $this->client->putObject($this->config['BUCKET'],$file_path, $file_con,$options);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
	}
	
	public function uploadFile($file_path, $file_url,$acl=1){
		try {
			if ($acl){$acl = 'private';}else{$acl = 'public-read';}
			$options = array(OssClient::OSS_HEADERS => array('x-oss-object-acl' => $acl));
			$result = $this->client->uploadFile($this->config['BUCKET'],$file_path, $file_url,$options);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
	}
	
	public function download($file_path){
		try {
			$result = $this->client->getObject($this->config['BUCKET'],$file_path);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
	}
	
	public function setAcl($file_path,$acl){
		if ($acl){
			$acl = 'private';
		}else{
			$acl = 'public-read';
		}
		try {
			$result = $this->client->putObjectAcl($this->config['BUCKET'],$file_path,$acl);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
	}
	
	public function delete($file_path){
		try {
			$result = $this->client->deleteObject($this->config['BUCKET'],$file_path);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
		
	}
	
	public function signUrl($file_path,$style=''){
		try {
			$options = [];
			if ($style){$options = ['x-oss-process' => 'style/'.$style];}
			$result = $this->client->signUrl($this->config['BUCKET'],$file_path,600,'GET',$options);
		} catch (OssException $e) {
			$this->error = __FUNCTION__ .$e->getMessage();
			return null;
		}
		return $result;
	}
	
	public function signUpload($upload_dir){
		$callback_param = [
			'callbackUrl' => $this->config['uploadCallbackUrl'],
			'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
			'callbackBodyType'=>"application/x-www-form-urlencoded"
		];
		$base64_callback_body = base64_encode(json_encode($callback_param));
		
		$end = time() + 60;//设置该policy超时时间是20s. 即这个policy过了这个有效时间，将不能访问。
		$expiration = $this->gmt_iso8601($end);
		
		$conditions = [
			//最大文件大小.用户可以自己设置
			['content-length-range',0,1048576000],
			// 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
			['starts-with','$key',$upload_dir]
		];
		$base64_policy = base64_encode(json_encode(['expiration'=>$expiration,'conditions'=>$conditions]));
		$signature = base64_encode(hash_hmac('sha1', $base64_policy, $this->config['ACCESS_KEY'], true));
		$response = [
			'accessid' => $this->config['ACCESS_ID'],
			'host' => $this->config['signUploadHost'],
			'policy' => $base64_policy,
			'signature' => $signature,
			'expire' => $end,
			'callback' => $base64_callback_body,
			'dir' => $upload_dir, // 这个参数是设置用户上传文件时指定的前缀。
		];
		return $response;
	}
	public function error(){
		return $this->error;
	}
	protected function gmt_iso8601($time) {
		$dtStr = date("c", $time);
		$mydatetime = new \DateTime($dtStr);
		$expiration = $mydatetime->format(\DateTime::ISO8601);
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		return $expiration."Z";
	}
}