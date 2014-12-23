<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 23/12/2014
 * Time: 14:04
 */

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
    private $displayName;

    /**
     * Instantiate a new Group Profile
     *
     * @param null|Token $groupid
     * @param null|Token $displayName
     */
    public function __construct(Token $groupid = null, $displayName = null)
    {
        if ($groupid == null && $displayName == null) {
            throw new InvalidArgumentException("Both id and display name cannot be null");
        }

        $this->groupid = $groupid;
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
        return ["group-id" => $this->groupid->toString(), "display-name" => $this->displayName];
    }

} 