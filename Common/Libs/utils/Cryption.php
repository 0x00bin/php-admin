<?php

// 两层加密
// 防修改带校验机制
class Cryption
{
	// 数据加密种子
	private static $_codeseed = 'a&^#%9ow0f*(';

    // Md5数据验证种子
	private static $_md5seed  = '7f6e542dd60fabab976bd6c8f3c02dc7b4865309';

    private static $_separator = "\t";
    /**
	 * get timestamp for check timeout
	 */
	private static function gettime()
	{
		$begin 	= '2014-09-01'; // 时间起点
		$newTz 	= 'UTC';		// timezone
		$save = date_default_timezone_get();
		if($save != $newTz) {
			date_default_timezone_set($newTz);
		}
		$time = time() - strtotime($begin);
		if($save != $newTz) {
			date_default_timezone_set($save);
		}
		return $time;
	}

    // 加密数据，生成安全信忿
	public static function encode($str)
	{
	    if (!is_string($str)) {
	        return false;
	    }
		$time = self::gettime();

		// gen codev
		$codev 	= md5(self::$_md5seed.self::$_separator.$time.self::$_separator.$str);
		$value 	= $codev . self::$_separator . $time . self::$_separator . $str;
		$code 	= self::_encode($value);

		return $code;
	}

	// 解密数据，检验数据有效使
	public static function decode($str)
	{
		if (!is_string($str)) {
			return false;
		}

		$value 	= self::_decode($str);

		$data  = explode(self::$_separator, $value);
        $str   = $data[2];
        $codev = array_shift($data);

		$data  = implode(self::$_separator, $data);
		$codev_1 = md5(self::$_md5seed.self::$_separator.$data);

		if ($codev == $codev_1) { // 加密验证通过
		    return $str; // 解码正确，返回数拿
		}

		return false; // 数据错误
	}

	/**
	 * data encode and decode funciton
	 */
	private static function _encode($str, $decode=false)
	{
		if ($decode) {
			$str = base64_decode($str);
		} else {
			$str = $str;
		}

		$key = substr(md5(self::$_codeseed), 8, 18);
		$len_key = strlen($key);
		$len_str = strlen($str);
		$code = '';
		for($i=0; $i < $len_str; $i++) {
		    $k = $i % $len_key;
		    $code .= $str[$i] ^ $key[$k];
		}

		if (!$decode) {
			$code = base64_encode($code);
		}
		return $code;
	}

	private static function _decode($str)
	{
		return self::_encode($str, true);
	}
}
?>