<?php
namespace yangyongxu\upload;

class Upload{
	
	private static $_this;
	private $k;
	private $error;
	private $client;
	/**
	 * @param array $config 
	 */
	private function __construct($type,$config){
		$type = 'yangyongxu\\\upload\\'.$type.'\\'.ucfirst(strtolower($type));
		try {
			$this->client = new $type($config);
		} catch (\Exception $e) {
			$this->error = __FUNCTION__ . "creating instance: FAILED";
			return null;
		}
	}
	/**
	 * 公有方法，用于获取实例
	 */
	public static function getObj($type,$config=[]){
		if ($type && $config){$k = md5($type.implode('',$config));}else{$k = 0;}
		if(!isset(static::$_this[$k]) || !(static::$_this[$k] instanceof self)){
			static::$_this[$k] = new self($type,$config);
			static::$_this[$k]->k = $k;
		}
		return static::$_this[$k];
	}
	/**
	 * 克隆方法私有化，防止复制实例
	 */
	private function __clone(){}
	/**
	 * 获取错误信息
	 */
	public function error(){
		if (!$this->error){
			$this->error = $this->client->error();
		}
		return $this->error;
	}
}