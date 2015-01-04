<?php
namespace meteor\endpoints;

use meteor\core\Endpoint;
use meteor\core\Headers;
use meteor\data\UserProfile;
use meteor\data\Token;
use meteor\database\Backend;
use meteor\exceptions\InvalidClientException;
use meteor\exceptions\AuthorizationException;

abstract class AuthenticatedEndpoint extends Endpoint
{
    /**
     * @var UserProfile
     */
    protected $user;

    public function execute($body, array $params)
    {
        $this->data = $body == "" ? array() : json_decode($body);

        if (!array_key_exists(Headers::CLIENT_ID, $_SERVER)) {
            throw new InvalidClientException();
        }

        if (!isset($_SERVER[Headers::AUTH_USER]) || !isset($_SERVER[Headers::AUTH_TOKEN])) {
            throw new AuthorizationException("Must provide authentication");
        }

        $this->user = Backend::fetch_user_profile($_SERVER[Headers::AUTH_USER]);
        $this->params = $params;
        $this->method = $_SERVER["REQUEST_METHOD"];
        $token = Token::decode($_SERVER[Headers::AUTH_TOKEN]);
        $this->clientid = Token::decode($_SERVER[Headers::CLIENT_ID]);

        // If debugging we ignore auth checks
        if (DEBUG) {
            return parent::execute($body, $params);
        }

        // Validate token
        if ($token->getType() != TOKEN_ACCESS) {
            throw new AuthorizationException("Token provided is not a access token");
        }

        if (!Backend::validate_token($this->clientid, $this->user->getUserId(), $token)) {
            throw new AuthorizationException("Token provided is not a valid access token");
        }

        $payload = $this->handle($this->data);
        $payload["client-id"] = $this->clientid->toString();
        return $payload;
    }

    protected function validate_permission($permission)
    {
        if (!DEBUG && !Backend::check_user_permission($this->user, $permission)) {
            throw new AuthorizationException("You do not have the required permissions to perform this operation", array ("permission" => $permission));
        }

        return true;
    }
} 