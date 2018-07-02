<?php

namespace Vivo\SlugBundle\Model;

use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Slug.
 */
abstract class Slug implements SlugInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $slug;

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
    public function setSlug($slug)
    {
        $this->slug = null === $slug ? null : (string) $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
