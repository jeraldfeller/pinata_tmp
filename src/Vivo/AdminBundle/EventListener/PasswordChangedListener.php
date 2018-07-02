<?php

namespace Vivo\AdminBundle\EventListener;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Vivo\AdminBundle\Event\PasswordChangedEvent;

class PasswordChangedListener
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param PasswordChangedEvent $event
     */
    public function encodePlainPassword(PasswordChangedEvent $event)
    {
        $user = $event->getAffectedUser();

        if ($user->getPlainPassword()) {
            $encoder = $this->encoderFactory->getEncoder($user);

            $user->setSalt(substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 16));
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
            $user->setPasswordResetRequestAt(null);
            $user->eraseCredentials();
        }
    }
}
