<?php
namespace yangyongxu\upload;

/**
 * 上传接口
 * YYX
 */
interface Uploadinterface{
	/**
	 * 字符串上传
	 */
	function putObject($file_path,$file_con,$acl=1);
	/**
	 * 文件上传
	 */
	function uploadFile($file_path, $file_url,$acl=1);
	/**
	 * 下载
	 */
	function download($file_path);
	/**
	 * 设置权限
	 */
	function setAcl($file_path,$acl);
	/**
	 * 删除
	 */
	function delete($file_path);
	/**
	 * 授权访问
	 */
	function signUrl($file_path,$style='');
	/**
	 * 授权上传
	 */
	function signUpload($upload_dir);
	/**
	 * 错误
	 */
	function error();
}