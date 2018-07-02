<?php

namespace Vivo\AdminBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface UserInterface extends AdvancedUserInterface, \Serializable
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password);

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set plain password.
     *
     * @param string $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword($plainPassword);

    /**
     * Get plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return $this
     */
    public function setSalt($salt);

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt();

    /**
     * Set lastLoginAt.
     *
     * @param string $lastLoginAt
     *
     * @return $this
     */
    public function setLastLoginAt(\DateTime $lastLoginAt);

    /**
     * Get lastLoginAt.
     *
     * @return string
     */
    public function getLastLoginAt();

    /**
     * Set lastLoginFrom.
     *
     * @param string $lastLoginFrom
     *
     * @return $this
     */
    public function setLastLoginFrom($lastLoginFrom);

    /**
     * Get lastLoginFrom.
     *
     * @return string
     */
    public function getLastLoginFrom();

    /**
     * Set passwordExpiredAt.
     *
     * @param \DateTime $passwordExpiredAt
     *
     * @return $this
     */
    public function setPasswordExpiredAt(\DateTime $passwordExpiredAt = null);

    /**
     * Get passwordExpiredAt.
     *
     * @return \DateTime|null
     */
    public function getPasswordExpiredAt();

    /**
     * Check if password has expired.
     *
     * @return bool
     */
    public function isPasswordExpired();

    /**
     * Set passwordResetRequestAt.
     *
     * @param \DateTime $passwordResetRequestAt
     *
     * @return $this
     */
    public function setPasswordResetRequestAt(\DateTime $passwordResetRequestAt = null);

    /**
     * Get passwordResetRequestAt.
     *
     * @return \DateTime|null
     */
    public function getPasswordResetRequestAt();

    /**
     * Set disabledAt.
     *
     * @param \DateTime $disabledAt
     *
     * @return $this
     */
    public function setDisabledAt(\DateTime $disabledAt = null);

    /**
     * Get disabledAt.
     *
     * @return \DateTime|null
     */
    public function getDisabledAt();

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt = null);

    /**
     * Get deletedAt.
     *
     * @return \DateTime|null
     */
    public function getDeletedAt();

    /**
     * Check if account has been deleted.
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * Set group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function setGroup(GroupInterface $group);

    /**
     * Get group.
     *
     * @return GroupInterface
     */
    public function getGroup();

    /**
     * Checks if the password reset request has expired.
     *
     * @param int $ttl
     *
     * @return bool
     */
    public function isPasswordResetRequestNonExpired($ttl);

    /**
     * Returns a password reset token.
     *
     * @return string
     */
    public function getPasswordResetToken();

    /**
     * Check if user has role.
     *
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role);

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
