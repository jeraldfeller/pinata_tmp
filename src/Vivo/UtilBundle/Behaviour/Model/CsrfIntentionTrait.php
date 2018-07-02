<?php

namespace Vivo\UtilBundle\Behaviour\Model;

trait CsrfIntentionTrait
{
    abstract public function getId();

    /**
     * Return a token based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention)
    {
        if ($this instanceof \Doctrine\Common\Persistence\Proxy) {
            $className = get_parent_class($this);
        } else {
            $className = get_class($this);
        }

        return $intention.sha1(trim($className.$intention.$this->getId()));
    }
}
