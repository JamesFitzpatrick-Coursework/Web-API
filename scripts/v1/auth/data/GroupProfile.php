<?php
namespace meteor\data;

use common\data\Token;
use InvalidArgumentException;

/**
 * Represents a group's profile.
 */
class GroupProfile
{

    /**
     * @var Token
     */
    private $groupid;

    /**
     * @var String
     */
    private $groupname;

    /**
     * @var String
     */
    private $displayName;

    /**
     * Instantiate a new Group Profile
     *
     * @param null|Token $groupid
     * @param null $groupname
     * @param null $displayName
     */
    public function __construct(Token $groupid = null, $groupname = null, $displayName = null)
    {
        if ($groupid == null && $groupname = null && $displayName == null) {
            throw new InvalidArgumentException("Both id and name cannot be null");
        }

        $this->groupid = $groupid;
        $this->groupname = $groupname;
        $this->displayName = $displayName;
    }

    /**
     * Gets the group's group id
     *
     * @return Token the group's group id
     */
    public function getGroupId()
    {
        return $this->groupid;
    }

    /**
     * Get this group's name.
     *
     * @return String the group's name
     */
    public function getName()
    {
        return $this->groupname;
    }

    /**
     * Gets the group's display name
     *
     * @return String the group's display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Convert this group profile into a json encodedable form to return from the API
     *
     * @return array this profile as an array
     */
    public function toExternalForm()
    {
        return ["group-id" => $this->groupid->toString(), "group-name" => $this->groupname, "display-name" => $this->displayName];
    }

} 