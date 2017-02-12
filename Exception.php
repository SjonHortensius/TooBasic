<?php namespace TooBasic;

class Exception extends \Exception
{
	public function __construct($message, $params = [], $code = 500, Exception $cause = null)
	{
		if (!empty($params))
			$message = vsprintf($message, $params);

		parent::__construct($message, $code, $cause);
	}
}