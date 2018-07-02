<?php

namespace Vivo\AdminBundle\Model;

use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * User.
 */
class User implements UserInterface
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
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var \DateTime
     */
    protected $lastLoginAt;

    /**
     * @var string
     */
    protected $lastLoginFrom;

    /**
     * @var \DateTime
     */
    protected $passwordExpiredAt;

    /**
     * @var \DateTime
     */
    protected $passwordResetRequestAt;

    /**
     * @var \DateTime
     */
    protected $disabledAt;

    /**
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @var GroupInterface
     */
    protected $group;

    public function __construct()
    {
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
    public function setFirstName($firstName)
    {
        $this->firstName = null === $firstName ? null : (string) $firstName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = null === $lastName ? null : (string) $lastName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = null === $email ? null : (string) $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = null === $password ? null : (string) $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = null === $plainPassword ? null : (string) $plainPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = null === $salt ? null : (string) $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLoginAt(\DateTime $lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLoginFrom($lastLoginFrom)
    {
        $this->lastLoginFrom = (string) $lastLoginFrom;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLoginFrom()
    {
        return $this->lastLoginFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordExpiredAt(\DateTime $passwordExpiredAt = null)
    {
        $this->passwordExpiredAt = $passwordExpiredAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordExpiredAt()
    {
        return $this->passwordExpiredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordExpired()
    {
        $now = new \DateTime('now');
        if (null !== $this->passwordExpiredAt && $this->passwordExpiredAt <= $now) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordResetRequestAt(\DateTime $passwordResetRequestAt = null)
    {
        $this->passwordResetRequestAt = $passwordResetRequestAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordResetRequestAt()
    {
        return $this->passwordResetRequestAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabledAt(\DateTime $disabledAt = null)
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        $now = new \DateTime('now');
        if (null !== $this->deletedAt && $this->deletedAt <= $now) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(GroupInterface $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordResetRequestNonExpired($ttl)
    {
        if (null === $this->getPasswordResetRequestAt()) {
            return false;
        }

        if ($this->getPasswordResetRequestAt() < new \DateTime('-'.$ttl.' seconds')) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordResetToken()
    {
        $timestamp = $this->getPasswordResetRequestAt() instanceof \DateTime ? $this->getPasswordResetRequestAt()->getTimestamp() : null;

        return hash('sha256',
            $this->getUsername().
            $this->getPassword().
            $this->getSalt().
            $timestamp
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = ['ROLE_ADMIN'];

        if (null !== $this->getGroup() && $this->getGroup()->getRoles()) {
            foreach ($this->getGroup()->getRoles() as $role) {
                $roles[] = $role;
            }
        }

        return array_values(array_unique($roles));
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles(), true) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if ($this->isDeleted()) {
            return false;
        }

        $now = new \DateTime('now');
        if (null !== $this->disabledAt && $this->disabledAt <= $now) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'id' => $this->getId(),
            'email' => $this->getEmail(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->id = $data['id'];
        $this->email = $data['email'];
    }

    /**
     * Fields to ignore when updating the timestamp.
     *
     * @return array
     */
    public function getIgnoredUpdateFields()
    {
        return array('lastLoginAt', 'lastLoginFrom', 'passwordResetRequestAt');
    }
}
