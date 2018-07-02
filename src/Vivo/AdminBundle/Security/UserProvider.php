<?php

namespace Vivo\AdminBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Vivo\AdminBundle\Repository\UserRepositoryInterface;

/**
 * Class UserProvider.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var \Vivo\AdminBundle\Repository\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->findOneByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        $class = $this->getClass();
        if (!$user instanceof $class) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        /* @var \Vivo\AdminBundle\Model\UserInterface $user */

        return $this->userRepository->findOneByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }

    /**
     * Get the class name of the User entity.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->userRepository->getClassName();
    }
}
