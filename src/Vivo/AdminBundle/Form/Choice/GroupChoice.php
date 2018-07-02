<?php

namespace Vivo\AdminBundle\Form\Choice;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vivo\AdminBundle\Model\UserInterface;

class GroupChoice extends AbstractType
{
    /**
     * @var string
     */
    protected $groupClass;

    /**
     * @var TokenStorageInterface
     */
    protected $authTokenStorage;

    /**
     * @param string                $groupClass
     * @param TokenStorageInterface $authTokenStorage
     */
    public function __construct($groupClass, TokenStorageInterface $authTokenStorage)
    {
        $this->groupClass = $groupClass;
        $this->authTokenStorage = $authTokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $user = null;

        if ($token = $this->authTokenStorage->getToken()) {
            $user = $token->getUser();
        }

        if (!$user || !is_object($user) || !$user instanceof UserInterface || !$user->getGroup()) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $resolver->setDefaults(array(
            'placeholder' => 'Select one..',
            'choice_label' => 'name',
            'class' => $this->groupClass,
            'query_builder' => function (EntityRepository $repository) use ($user) {
                /* @var \Vivo\AdminBundle\Repository\GroupRepositoryInterface $repository */

                return $repository->getGroupsUnderUserQueryBuilder($user, false);
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Bridge\Doctrine\Form\Type\EntityType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_group_choice_type';
    }

    /**
     * BC - Add alias if Symfony < 3.0.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
