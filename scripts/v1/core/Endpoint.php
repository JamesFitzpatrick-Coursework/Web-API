<?php
checkEnv();

abstract class Endpoint {

	public abstract function handle($body);
	
}