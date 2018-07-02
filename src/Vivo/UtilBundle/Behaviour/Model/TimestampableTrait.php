<?php

namespace Vivo\UtilBundle\Behaviour\Model;

/**
 * Class TimestampableTrait.
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        if (null !== $this->updatedAt && spl_object_hash($updatedAt) === spl_object_hash($this->updatedAt)) {
            // Hash is the same. Clone the object so Doctrine doesn't reset the updatedAt timestamp
            $updatedAt = clone $updatedAt;
        }

        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Fields to ignore when updating the timestamp.
     *
     * @return array
     */
    public function getIgnoredUpdateFields()
    {
        return array();
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array();
    }
}
