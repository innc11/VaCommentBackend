<?php

namespace Exception\Handler;

use \Exception;

class ExceptionHandler
{
	public static function getFormattedTraceback($e)
	{
		$traceback = $e->getTrace();
		$ts = [];
		$lineNumber = count($traceback);
		foreach($traceback as $line)
		{
			$layer = $lineNumber - 1;
			$filepath = $line['file'];
			$linenum = $line['line'];
			$funcname = $line['function'];

			$ts[]= "#$layer  $filepath ($linenum) : $funcname()";
			$lineNumber --;
		}
		
		return '#'.(count($traceback)).'  '.$e->getFile().' ('.$e->getLine().")\n".implode(PHP_EOL, $ts);
	}

	public static function print(\Throwable $e)
	{
		$code = $e->getCode();

		http_response_code($code!=0? $code:403);

		$extraMessage = ($e instanceof \Exception\BaseExceptionWithMessages)? $e->extraMessage:$e->getMessage();
		if($extraMessage != '')
		{
			header('X-Extra-Message-Plain: '.$extraMessage);
			header('X-Extra-Message: '.base64_encode($extraMessage));
		}
	
		$tracebackString = PRINT_TRACEBACK? self::getFormattedTraceback($e):'';
		echo <<<EOF
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<title>Internal Error</title>
			</head>
			<body>
				<div class="container">
					Internal Error: {$e->getMessage()}
					<br/><br/>
					<pre style="font-family: &quot;Microsoft YaHei&quot;;font-size: 12px;">Traceback:\n\n{$tracebackString}</pre>
					<pre style="font-family: &quot;Microsoft YaHei&quot;;font-size: 12px;"></pre>
				</div>
				<style>
					html {
						padding: 50px 10px;
						font-size: 16px;
						line-height: 1.4;
						color: #666;
						background: #F6F6F3;
						-webkit-text-size-adjust: 100%;
						-ms-text-size-adjust: 100%;
					}

					html,
					input { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
					body {
						_width: 500px;
						padding: 30px 20px;
						margin: 0 auto;
						background: #FFF;
					}
					ul {
						padding: 0 0 0 40px;
					}
					.container {
						_width: 380px;
						margin: 0 auto;
					}
				</style>
			</body>
		</html>
		EOF;

		$tracebackString = self::getFormattedTraceback($e);
		
		$shortOuput = $e instanceof \Exception\BaseExceptionWithMessages && !$e->detailForLogging;
		if($e instanceof \Exception\InvalidTokenException || $shortOuput)
			\Log\Log::error("Exception: ".$e->getMessage());
		else
			\Log\Log::error("\n\nException: ".$e->getMessage()."\n\n".$tracebackString."\n\n");
	}
}


?>