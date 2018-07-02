<?php

namespace Vivo\AdminBundle\Form\Choice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vivo\AdminBundle\Model\UserInterface;

class RolesChoice extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    protected $authTokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authChecker;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @param TokenStorageInterface         $authTokenStorage
     * @param AuthorizationCheckerInterface $authChecker
     * @param array                         $roles
     */
    public function __construct(TokenStorageInterface $authTokenStorage, AuthorizationCheckerInterface $authChecker, array $roles = array())
    {
        $this->authTokenStorage = $authTokenStorage;
        $this->authChecker = $authChecker;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => $this->getRoles(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_roles_choice';
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

    /**
     * @return array
     */
    protected function getRoles()
    {
        if (null === $token = $this->authTokenStorage->getToken()) {
            throw new AccessDeniedException();
        }

        if (!is_object($user = $token->getUser())) {
            throw new AccessDeniedException();
        }

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        if ($this->authChecker->isGranted('ROLE_DEVELOPER')) {
            // Developers should see all roles
            return $this->roles;
        }

        return array_intersect_key($this->roles, array_flip($user->getRoles()));
    }
}
