<?php

class HandshakeEndpoint extends Endpoint {

	public function handle($body) {
		return array("test" => "test");
	}
}