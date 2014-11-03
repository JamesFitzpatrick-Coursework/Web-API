<?php

class MethodNotFoundException extends Exception
{
	private $request;

	public function __construct($request)
	{
		parent::__construct("Method not found");
		$this->request = request;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
}