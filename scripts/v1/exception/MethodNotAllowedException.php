<?php

class MethodNotAllowedException extends Exception
{
	private $method;

	public function __construct($method)
	{
		parent::__construct("Method not allowed");
		$this->method = method;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
}