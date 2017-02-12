<?php namespace TooBasic;

class Pdo extends \Pdo
{
	public function __construct($dsn, $username = null, $password = null, array $options = array())
	{
		parent::__construct($dsn, $username, $password, $options);

		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function fetchObject($query, array $params = [], $class = 'StdClass')
	{
		$r = $this->fetchObjects($query, $params, $class);

		if (1 != count($r))
			throw new Exception('Query returned '. count($r) .' rows, no single result');

		return $r[0];
	}

	public function fetchObjects($query, array $params, $class = 'StdClass')
	{
		$s = $this->preparedQuery($query, $params);
		return $s->fetchAll(PDO::FETCH_CLASS, $class);
	}

	public function preparedExec($query, array $params)
	{
		$s = $this->prepare($query);
		$s->execute($params);

		return $s->rowCount();
	}

	public function preparedQuery($query, array $params)
	{
		$s = $this->prepare($query);
		$s->execute($params);

		return $s;
	}
}
