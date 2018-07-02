<?php

namespace Vivo\SlugBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Vivo\SiteBundle\Model\SiteInterface;
use Vivo\SlugBundle\Model\SlugInterface;
use Vivo\UtilBundle\Util\Urlizer;

class SlugToStringTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    /**
     * @var \Vivo\SlugBundle\Model\SlugInterface
     */
    protected $defaultSlug;

    /**
     * @var bool
     */
    protected $allowSlashes;

    /**
     * @var \Vivo\SiteBundle\Model\SiteInterface
     */
    protected $site;

    /**
     * @var array
     */
    protected $reservedSlugs = array();

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     * @param SlugInterface $defaultSlug
     * @param bool          $allowSlashes
     * @param SiteInterface $site
     * @param array         $reservedSlugs
     */
    public function __construct(ObjectManager $entityManager, SlugInterface $defaultSlug, $allowSlashes, SiteInterface $site, array $reservedSlugs = array())
    {
        $this->entityManager = $entityManager;
        $this->defaultSlug = $defaultSlug;
        $this->allowSlashes = $allowSlashes;
        $this->site = $site;
        $this->reservedSlugs = $reservedSlugs;
    }

    /**
     * Transforms a Slug entity to a to a string.
     *
     * @param SlugInterface|null $slug
     *
     * @return string
     */
    public function transform($slug)
    {
        if (null === $slug || strlen($slug->getSlug()) < 1) {
            return '';
        }

        return '/'.trim($slug->getSlug(), '/');
    }

    /**
     * Transforms a string to a Slug entity.
     *
     * @param string $slugString
     *
     * @return SlugInterface|null
     */
    public function reverseTransform($slugString)
    {
        if (!$slugString) {
            return;
        }

        $slugString = trim(($this->allowSlashes ? Urlizer::urlizeWithDirectories($slugString) : Urlizer::urlize($slugString)), '/');

        foreach ($this->reservedSlugs as $reservedSlug) {
            $reservedSlug = trim(($this->allowSlashes ? Urlizer::urlizeWithDirectories($reservedSlug) : Urlizer::urlize($reservedSlug)), '/');

            if ($slugString === $reservedSlug) {
                throw new TransformationFailedException();
            }
        }

        /** @var SlugInterface[] $slugs */
        $slugs = $this->entityManager
            ->getRepository(get_class($this->defaultSlug))
            ->findBy(array('slug' => $slugString))
        ;

        foreach ($slugs as $slug) {
            try {
                if (
                    (null === $this->defaultSlug->getSite() && null === $slug->getSite()) // This is a non site specific slug
                    || $slug->getSite() === $this->site
                ) {
                    return $slug;
                }
            } catch (EntityNotFoundException $e) {
            }
        }

        $slug = $this->defaultSlug;
        $slug->setSlug($slugString);

        return $slug;
    }
}
