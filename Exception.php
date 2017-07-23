<?php namespace TooBasic;

class Exception extends \Exception
{
	public function __construct(string $message, array $params = [], int $code = 500, Exception $cause = null)
	{
		if (!empty($params))
			$message = vsprintf($message, $params);

		parent::__construct($message, $code, $cause);
	}
}