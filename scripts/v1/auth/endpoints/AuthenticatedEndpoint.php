<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use common\core\Headers;
use common\data\Token;
use common\exceptions\InvalidClientException;
use common\exceptions\InvalidTokenException;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;
use meteor\database\backend\UserBackend;
use meteor\exceptions\AuthorizationException;

abstract class AuthenticatedEndpoint extends Endpoint
{
    /**
     * @var \meteor\data\profiles\UserProfile
     */
    protected $user;

    public function execute($body, array $params)
    {
        // Decode the request body
        $this->data = $body == "" ? [] : json_decode($body);

        // Check a client id was provided
        if (!array_key_exists(Headers::CLIENT_ID, $_SERVER)) {
            throw new InvalidClientException();
        }

        // Check the auth details were provided
        if (!isset($_SERVER[Headers::AUTH_USER]) || !isset($_SERVER[Headers::AUTH_TOKEN])) {
            throw new AuthorizationException("Must provide authentication");
        }

        // Collect the request details
        $this->user = UserBackend::fetch_user_profile($_SERVER[Headers::AUTH_USER]);
        $this->params = $params;
        $this->method = $_SERVER["REQUEST_METHOD"];
        $token = Token::decode($_SERVER[Headers::AUTH_TOKEN]);
        $this->clientid = Token::decode($_SERVER[Headers::CLIENT_ID]);

        // If debugging we ignore auth checks
        if (DEBUG) {
            return parent::execute($body, $params);
        }

        // Validate the access token
        if ($token->getType() != TOKEN_ACCESS) {
            throw new AuthorizationException("Token provided is not a access token");
        }

        if (!TokenBackend::validate_token($this->clientid, $this->user->getUserId(), $token)) {
            throw new InvalidTokenException("Token provided is not a valid access token");
        }

        // Handle the request
        $payload = $this->handle($this->data);
        $payload["client-id"] = $this->clientid->toString();

        return $payload;
    }

    protected function validate_permission($permission)
    {
        // Check the permission with the backend
        if (!DEBUG && !UserBackend::check_user_permission($this->user, $permission)) {
            throw new AuthorizationException("You do not have the required permissions to perform this operation", ["permission" => $permission]);
        }

        return true;
    }
} 