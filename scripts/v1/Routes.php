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

// User management
$endpoints["users"] = new UserLookupEndpoint();
$endpoints["users/create"] = new UserCreateEndpoint();

// Group management
$endpoints["groups"] = new GroupLookupEndpoint();
$endpoints["groups/create"] = new GroupCreateEndpoint();
