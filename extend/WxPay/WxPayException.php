<?php

/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
use think\Exception;
class WxPayException extends Exception {
	public function errorMessage(){
		return $this->getMessage();
	}
}
