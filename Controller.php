<?php

class TooBasic_Controller
{
	public static function dispatch($path = null)
	{
		if (!isset($path))
			$path = $_SERVER['REQUEST_URI'];

		if (false !== strpos($path, '?'))
			$path = strstr($path, '?', true);

		$params = explode('/', substr($path, 1));
		$params = array_map('rawurldecode', $params);
		$action = array_shift($params);

		if ('' === $action)
			$action = 'index';

		new static(strtolower($_SERVER['REQUEST_METHOD']), $action, $params);
	}

	final private function __construct($method, $action, array $params)
	{
		try
		{
			$this->_construct($method, $action, $params);

			if (method_exists($this, $method . ucfirst($action)))
				return call_user_func_array(array($this, $method . ucfirst($action)), $params);

			return call_user_func_array(array($this, $method), array_merge([$action], $params));
		}
		catch (Exception $e)
		{
			return $this->_handle($e);
		}
	}

	protected function _construct()
	{
	}

	public function __call($method, array $arguments)
	{
		throw new Exception('404 - Unknown method: '. $method);
	}

	protected function _handle(Exception $e)
	{
		if (!headers_sent())
			http_response_code(500);

		echo $e;
	}
}
