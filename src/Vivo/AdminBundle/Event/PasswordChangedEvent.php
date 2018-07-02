<?php

namespace Vivo\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Vivo\AdminBundle\Model\UserInterface;

class PasswordChangedEvent extends Event
{
    /**
     * @var \Vivo\AdminBundle\Model\UserInterface
     */
    protected $affectedUser;

    /**
     * @var \Vivo\AdminBundle\Model\UserInterface
     */
    protected $initiatedBy;

    /**
     * @param UserInterface $affectedUser
     * @param UserInterface $changedByUser
     */
    public function __construct(UserInterface $affectedUser, UserInterface $initiatedBy)
    {
        $this->affectedUser = $affectedUser;
        $this->initiatedBy = $initiatedBy;
    }

    /**
     * @return UserInterface
     */
    public function getAffectedUser()
    {
        return $this->affectedUser;
    }

    /**
     * @return UserInterface
     */
    public function getInitiatedByUser()
    {
        return $this->initiatedBy;
    }
}
