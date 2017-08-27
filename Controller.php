<?php namespace TooBasic;

class Controller
{
	public static function dispatch(string $path = null)
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

	final private function __construct(string $method, string $action, array $params)
	{
		if ('head' == $method)
			$method = 'get';

		try
		{
			$this->_construct($method, $action, $params);

			if (method_exists($this, $method . ucfirst($action)))
				return $this->{$method . ucfirst($action)}(...$params);

			$this->$method(...array_merge(array($action), $params));
		}
		catch (Exception $e)
		{
			$this->_handle($e);
		}
	}

	protected function _construct()
	{
	}

	public function __call(string $method, array $arguments)
	{
		throw new Exception('404 - Unknown method: '. $method);
	}

	protected function _handle(Exception $e)
	{
		if (!headers_sent())
		{
			header('Content-Type: text/plain');
			http_response_code(500);
		}

		print $e;
	}
}
