<?php

namespace Vivo\AdminBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Group.
 */
class Group implements GroupInterface
{
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $roles = array();

    /**
     * @var bool
     */
    protected $selfManaged = false;

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var UserInterface[]|ArrayCollection
     */
    protected $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = null === $name ? null : (string) $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias)
    {
        $this->alias = null === $alias ? null : (string) $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelfManaged($selectManaged)
    {
        $this->selfManaged = (bool) $selectManaged;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSelfManaged()
    {
        return $this->selfManaged;
    }

    /**
     * {@inheritdoc}
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRank($useSelfManagedValue = false)
    {
        if (true === $useSelfManagedValue) {
            if ($this->isSelfManaged()) {
                return $this->rank - 1;
            }
        }

        return $this->rank;
    }

    /**
     * {@inheritdoc}
     */
    public function addUser(UserInterface $user)
    {
        if (!$this->users->contains($user)) {
            $user->setGroup($this);
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeUser(UserInterface $user)
    {
        if ($this->users->contains($user)) {
            $user->setGroup(null);
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsers()
    {
        return $this->users;
    }
}
