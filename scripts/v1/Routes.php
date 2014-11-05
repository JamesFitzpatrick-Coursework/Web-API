<?php

// Setup endpoints
$endpoints = array();

$endpoints[""] = new ServerEndpoint();

$endpoints["handshake"] = new HandshakeEndpoint();
$endpoints["login"] = new LoginEndpoint();

$endpoints["users"] = new UserLookupEndpoint();
$endpoints["users/create"] = new UserCreateEndpoint();
