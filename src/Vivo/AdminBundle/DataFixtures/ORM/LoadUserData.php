<?php

namespace Vivo\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vivo\AdminBundle\Event\Events;
use Vivo\AdminBundle\Event\PasswordChangedEvent;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userRepository */
        $userRepository = $this->container->get('vivo_admin.repository.user');
        /** @var \Vivo\AdminBundle\Repository\GroupRepositoryInterface $groupRepository */
        $groupRepository = $this->container->get('vivo_admin.repository.group');

        $defaultRoles = array(
            'ROLE_USER_GROUP_MANAGEMENT',
            'ROLE_USER_MANAGEMENT',
            'ROLE_SITE_MANAGEMENT',
            'ROLE_DEVELOPER',
        );

        foreach (array_keys($this->container->getParameter('vivo_admin.roles')) as $role) {
            if (!in_array($role, $defaultRoles)) {
                $defaultRoles[] = $role;
            }
        }

        $group = $groupRepository->createGroup();
        $group->setName('Vivo Group')
            ->setRank(1)
            ->setSelfManaged(true)
            ->setRoles($defaultRoles);

        $adminGroupRoles = $defaultRoles;

        foreach ($adminGroupRoles as $k => $v) {
            if ('ROLE_USER_GROUP_MANAGEMENT' === $v || 'ROLE_DEVELOPER' === $v) {
                unset($adminGroupRoles[$k]);
            }
        }
        $adminGroup = $groupRepository->createGroup();
        $adminGroup->setName('Administrator')
            ->setRank(2)
            ->setSelfManaged(true)
            ->setRoles($adminGroupRoles);

        $user = $userRepository->createUser();
        $user->setFirstName('Vivo')
            ->setLastName('Group')
            ->setEmail('cms@vivogroup.com.au')
            ->setPlainPassword('updatepassword')
            ->setPasswordExpiredAt(new \DateTime('now'))
            ->setGroup($group);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($user, $user));

        $manager->persist($group);
        $manager->persist($user);
        $manager->persist($adminGroup);

        $manager->flush();
    }
}
