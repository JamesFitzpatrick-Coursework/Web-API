<?php

// Setup endpoints
$endpoints = array();

$endpoints[""] = new ServerEndpoint();

$endpoints["handshake"] = new HandshakeEndpoint();
$endpoints["login"] = new LoginEndpoint();
$endpoints["refresh"] = new RefreshEndpoint();
$endpoints["invalidate"] = new InvalidateEndpoint();
$endpoints["validate"] = new ValidateEndpoint();
$endpoints["test"] = new TestEndpoint();

$endpoints["users"] = new UserLookupEndpoint();
$endpoints["users/create"] = new UserCreateEndpoint();
