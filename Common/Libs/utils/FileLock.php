<?php

class FileLock
{
	static function isLocked($fileName)
	{
		return file_exists($fileName . '.lock');
	}

	static function createLock($fileName)
	{
		$fileDir = dirname($fileName);
		if (! is_dir($fileDir)) {
			if (! @mkdir($fileDir, 0755, true)) {
				if (! is_dir($fileDir)) {
					throw new Exception("Could not create file directory");
				}
			}
		}
		@touch($fileName . '.lock');
	}

	static function removeLock($fileName)
	{
		@unlink($fileName . '.lock');
	}

	static function waitForLock($fileName)
	{
		// 20 x 250 = 5 seconds
		$tries = 20;
		$cnt = 0;
		do {
			clearstatcache();
			usleep(250);
			$cnt ++;
		} while ($cnt <= $tries && self::isLocked($fileName));

		if (self::isLocked($fileName)) {
			self::removeLock($fileName);
		}
	}
}
?>