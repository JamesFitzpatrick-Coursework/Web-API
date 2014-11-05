<?php

class InvalidTokenException extends Exception
{
	public function __construct($error)
	{
		parent::__construct($error);
	}
}