<?php
namespace meteor\core;

class HTTP
{
    const OK = 200;
    const CREATED = 201;

    const FOUND = 302;
    const NOT_MODIFIED = 304;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;

    const INTERNAL_ERROR = 500;
}