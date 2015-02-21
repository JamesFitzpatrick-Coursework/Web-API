<?php
namespace meteor\data\profiles;

use common\data\Token;
use InvalidArgumentException;

class AssessmentProfile
{
    /**
     * @var Token
     */
    private $assessmentId;

    /**
     * @var String
     */
    private $assessmentName;

    /**
     * @var String
     */
    private $displayName;

    /**
     * Instantiate a new Assessment Profile
     *
     * @param null|Token $assessmentId
     * @param null $assessmentName
     * @param null $displayName
     */
    public function __construct(Token $assessmentId = null, $assessmentName = null, $displayName = null)
    {
        if ($assessmentId == null && $assessmentName = null && $displayName == null) {
            throw new InvalidArgumentException("Both id and name cannot be null");
        }

        $this->assessmentId = $assessmentId;
        $this->assessmentName = $assessmentName;
        $this->displayName = $displayName;
    }

    /**
     * Gets the assessment's group id
     *
     * @return Token the assessment's group id
     */
    public function getAssessmentId()
    {
        return $this->assessmentId;
    }

    /**
     * Get this assessment's name.
     *
     * @return String the assessment's name
     */
    public function getName()
    {
        return $this->assessmentName;
    }

    /**
     * Gets the assessment's display name
     *
     * @return String the assessment's display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Convert this assessment profile into a json encodedable form to return from the API
     *
     * @return array this profile as an array
     */
    public function toExternalForm()
    {
        return ["assessment-id"   => $this->assessmentId->toString(),
                "assessment-name" => $this->assessmentName,
                "display-name"    => $this->displayName
        ];
    }
} 