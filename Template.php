<?php namespace TooBasic;

class Template
{
	protected $_file;

	public function __construct($file = null)
	{
		$this->_file = $file;
	}

	public function getWrapped()
	{
		$wrapped = $this->get('_wrapper');
		$wrapped->content = $this;

		return $wrapped;
	}

	public function get($file)
	{
		$tpl = clone $this;
		$tpl->_file = $file;

		return $tpl;
	}

	public static function show($file, array $variables = array())
	{
		$tpl = new self($file);

		foreach ($variables as $key => $value)
			$tpl->$key = $value;

		print $tpl->getWrapped();
	}

	public function __call($name, $arguments)
	{
		if (isset($this->$name) && $this->$name instanceof \Closure)
			return $this->$name->__invoke(...$arguments);

		throw new Exception('Call to undefined method Template::'.$name.'()');
	}

	public function __toString()
	{
		ob_start();

		require('tpl/'. $this->_file .'.php');

		return str_replace(["\t", "\n"], '', ob_get_clean());
	}
}
