<?php namespace TooBasic;

class Pdo extends \Pdo
{
	public function __construct(string $dsn, string $username = null, string $password = null, array $options = array())
	{
		parent::__construct($dsn, $username, $password, $options);

		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function fetchObject(string $query, array $params = [], string $class = 'StdClass')
	{
		$r = $this->fetchObjects($query, $params, $class);

		if (1 != count($r))
			throw new Exception('Query returned '. count($r) .' rows, no single result');

		return $r[0];
	}

	public function fetchObjects(string $query, array $params, string $class = 'StdClass')
	{
		$s = $this->preparedQuery($query, $params);
		return $s->fetchAll(PDO::FETCH_CLASS, $class);
	}

	public function preparedExec(string $query, array $params)
	{
		$s = $this->prepare($query);
		$s->execute($params);

		return $s->rowCount();
	}

	public function preparedQuery(string $query, array $params)
	{
		$s = $this->prepare($query);
		$s->execute($params);

		return $s;
	}
}
