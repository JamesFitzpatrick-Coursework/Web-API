<?php
namespace meteor\endpoints\users;

use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserListAssignmentsEndpoint extends AuthenticatedEndpoint
{
    const LIST_ALL = 0;
    const LIST_COMPLETED = 1;
    const LIST_OUTSTANDING = 2;

    private $listType;

    public function __construct($type)
    {
        $this->listType = $type;
    }

    public function handle($data)
    {
        $assignments = [];

        switch ($this->listType) {
            case self::LIST_ALL:
                $assignments = $this->handleAll($data);
                break;
            case self::LIST_COMPLETED:
                $assignments = $this->handleCompleted($data);
                break;
            case self::LIST_OUTSTANDING:
                $assignments = $this->handleOutstanding($data);
                break;
        }

        return ["count" => count($assignments), "assignments" => $assignments];
    }

    private function handleAll($data)
    {
        return UserBackend::fetch_user_assignments_all(UserBackend::fetch_user_profile($this->params['id']));
    }

    private function handleCompleted($data)
    {
        return UserBackend::fetch_user_assignments_complete(UserBackend::fetch_user_profile($this->params['id']));
    }

    private function handleOutstanding($data)
    {
        return UserBackend::fetch_user_assignments_outstanding(UserBackend::fetch_user_profile($this->params['id']));
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}