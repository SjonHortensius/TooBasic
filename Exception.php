<?php namespace TooBasic;

class Exception extends \Exception
{
	public function __construct(string $message, array $params = [], int $code = 500, Exception $cause = null)
	{
		if (!empty($params))
			$message = vsprintf($message, $params);

		parent::__construct($message, $code, $cause);
	}

	public static function errorHandler($number, $string, $file, $line): void
	{
		if (ini_get('error_reporting') > 0)
			error_log($string .' in '. $file .' on line '. $line);

		throw new Exception('An unexpected error has occurred');
	}
}