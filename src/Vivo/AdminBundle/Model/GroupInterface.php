<?php

namespace Vivo\AdminBundle\Model;

interface GroupInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles);

    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles();

    /**
     * Set selfManaged.
     *
     * @param bool $selfManaged
     *
     * @return $this
     */
    public function setSelfManaged($selfManaged);

    /**
     * Get isSelfManaged.
     *
     * @return bool
     */
    public function isSelfManaged();

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return Group
     */
    public function setRank($rank);

    /**
     * Get rank.
     *
     * @param $useSelfManagedValue - If true, rank will be deducted by 1 if self managed
     *
     * @return int
     */
    public function getRank($useSelfManagedValue = false);

    /**
     * Add users.
     *
     * @param UserInterface $user
     *
     * @return $this
     */
    public function addUser(UserInterface $user);

    /**
     * Remove users.
     *
     * @param UserInterface $user
     *
     * @return $this
     */
    public function removeUser(UserInterface $user);

    /**
     * Get users.
     *
     * @return \Doctrine\Common\Collections\Collection|\Vivo\AdminBundle\Model\UserInterface[]
     */
    public function getUsers();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Return a token based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention);
}
