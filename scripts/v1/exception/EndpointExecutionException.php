<?php

class EndpointExecutionException extends Exception
{
	private $data;
	
	public function __construct($error, $data = array())
	{
		parent::__construct($error);
		$this->data = $data;
	}
	
	public function getData()
	{
		return $this->data;
	}
}