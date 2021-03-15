<?php

namespace Log;

class Log
{
	public static $logFile;

	public static function init(string $logFilePath=DATA_DIR.DIRECTORY_SEPARATOR.'logs.txt')
	{
		self::$logFile = $logFilePath;
	}

	public static function _output(string $type, string $tag, string $message, $linebreak = PHP_EOL)
	{
		if(!file_exists(DATA_DIR))
			return;
		// if($tag == '' && get_called_class() !== false)
		// 	$tag = str_replace('\\', '/', get_called_class());
		
		$separator = '';
		if($tag != '')
			$separator = ': ';

		$timestamp = sprintf("[%s] [%s%s%s] [%s]: ", date('Y-m-d H:i:s'), $tag, $separator, $type, $_SERVER['REMOTE_ADDR']);

		// $lines = explode($linebreak, $message);
		// array_walk($lines, function(&$value, $key) use($timestamp) {
		// 	$value = $timestamp.$value;
		// });
		// $content = implode($linebreak, $lines).$linebreak;

		$content = $timestamp.$message.$linebreak;

		file_put_contents(self::$logFile, $content, FILE_APPEND);
		// file_put_contents(self::$logFile, $content);
	}

	public static function error(string $message, string $tag='')
	{
		self::_output('Error', $tag, $message);
	}

	public static function warn(string $message, string $tag='')
	{
		self::_output('Warn', $tag, $message);
	}

	public static function info(string $message, string $tag='')
	{
		self::_output('Info', $tag, $message);
	}

	public static function debug(string $message, string $tag='')
	{
		self::_output('Debug', $tag, $message);
	}
}

?>