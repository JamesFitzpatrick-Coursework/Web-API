<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 10/12/2014
 * Time: 10:00
 */

class TestEndpoint extends AuthenticatedEndpoint
{
    public function handle($body)
    {
        return array ();
    }
}