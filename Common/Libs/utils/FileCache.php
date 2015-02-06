<?php
/**
 * FileCache class
 * @author xianbin.wang
 * @created 2008-02-22 19:16
 *  Using:
 	 get cache data:
 *   	$cache = new FileCache();
 *	 	$cacheData = $cache->get($key);
 *
 *  set cache data:
 *	   $cache = new FileCache();
 *	   $cache->set($key, $value);
 */
include_once( dirname(__FILE__) . '/ICache.php' );
class FileCache extends ICache
{
	/**
	 * the cache life time
	 *
	 * @var integer
	 */
	private $_expiration = 3600;// 60*60

	/**
	 * the cache base directory
	 *
	 * @var string
	 */
	private $_cacheDir   = "./cache/";

    private $_ignore_readable_check = false;
	public function __construct( $cacheDir="", $expiration=3600 )
	{
		if ( $cacheDir != "" )
			$this->setCacheDir($cacheDir);
		$this->_expiration = $expiration;

		// check is_readable return is Right? if not Right ignore check
		if ( is_readable(__FILE__) ) {
            $this->_ignore_readable_check = false;
		} else {
		    $this->_ignore_readable_check = true;
		}
	}

	public function get( $key, $expiration = false )
	{
		if ( !$expiration ){
			$expiration = $this->_expiration;
		}
		$file = $this->_getCacheFile( $key );
		include_once( dirname(__FILE__) . '/FileLock.php' );
		if (FileLock::isLocked($file)) {
			FileLock::waitForLock($file);
		}

		clearstatcache();
		//var_dump($this->_ignore_readable_check);
		//var_dump($file);
		//var_dump(is_readable($file));
		if ( $this->_ignore_readable_check || is_readable($file) ){
            if ((time() - @filemtime($file)) > $expiration) {
                @unlink($file);
                return false;
            }else{
            	return @unserialize(file_get_contents($file));
        	}
		} else {
		    //echo "$key = not readable!\n";
		}
		return false;
	}

	public function set( $key, $value )
	{
		$file = $this->_getCacheFile( $key );
		$filePath = dirname($file);
		include_once( dirname(__FILE__) . '/FileLock.php' );
		if (FileLock::isLocked($file)) {
			FileLock::waitForLock($file);
		}
		if ( !is_dir($filePath) ){
			$this->_createDir($filePath);
		}

		FileLock::createLock($file);
		if ( !file_put_contents($file, serialize($value), LOCK_EX) ){
			FileLock::removeLock($file);
			throw new Exception("Could not store data in cache file");
		}
		FileLock::removeLock($file);
	}

	public function delete( $key )
	{
		$file = $this->_getCacheFile($key);
        //Log::write("delete key:".$key. " file:{$file}");
		if (!@unlink($file)) {
			//throw new Exception("Cache file could not be deleted");
			return false;
		}
		return true;
	}

	private function _getCacheFile($key)
	{
		return $this->_cacheDir . dirname($key) . "/" . md5($key);
	}

	public function setCacheDir($dir)
	{
		if ( @is_dir($dir) ) {
			if ( $dir[strlen($dir)-1] != "/" ) $dir = $dir."/";
			$this->_cacheDir = $dir;
		}else{
			throw new Exception("The Cache dir is not dir");
		}
	}

	private static function _createDir( $dirName, $mode = 0775 )
	{
         if( file_exists($dirName) ) return true;

         if( substr($dirName, strlen($dirName)-1 ) == "/" ){
             $dirName = substr($dirName, 0,strlen($dirName)-1);
         }
         // for example, we will create dir "/a/b/c"
         // $firstPart = "/a/b"
         $firstPart = substr($dirName,0,strrpos($dirName, "/" ));

         if(file_exists($firstPart)){
         	if(!mkdir($dirName,$mode)) {
         		throw new Exception("Cache dir could not be created");
         	}
		     chmod( $dirName, $mode );
         } else {
             self::_createDir($firstPart,$mode);
             if(!mkdir($dirName,$mode)) {
             	throw new Exception("Cache dir could not be created");
             }
		     chmod( $dirName, $mode );
     	}

		return true;
	}

	public static function createDir($dirName, $mode = 0775 ) {
        return self::_createDir($dirName, $mode);
	}
}

?>