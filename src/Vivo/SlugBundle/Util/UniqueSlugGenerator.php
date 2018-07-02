<?php

namespace Vivo\SlugBundle\Util;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Vivo\SlugBundle\Model\SlugInterface;
use Vivo\UtilBundle\Util\Urlizer;

class UniqueSlugGenerator implements UniqueSlugGeneratorInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(SlugInterface $slugEntity, $slugString, $allowSlashes = false)
    {
        $slugString = trim(($allowSlashes ? Urlizer::urlizeWithDirectories($slugString) : Urlizer::urlize($slugString)), '/');

        if (strlen($slugString) < 1) {
            return;
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->objectManager->getRepository(get_class($slugEntity));

        /** @var SlugInterface[] $results */
        $results = $repository->createQueryBuilder('slug')
            ->where('slug.slug like :slug_like')
            ->setParameter('slug_like', $slugString.'%')
            ->getQuery()
            ->getResult();

        /** @var SlugInterface[] $slugs */
        $slugs = array();
        foreach ($results as $result) {
            try {
                if ($result->getSite() === $slugEntity->getSite()) {
                    $slugs[] = $result;
                }
            } catch (EntityNotFoundException $e) {
            }
        }

        $slugTaken = false;
        foreach ($slugs as $slug) {
            if ($slug->getSlug() == $slugString) {
                if (!$slug->isPrimary()) {
                    return $slug;
                }

                $slugTaken = true;
                break;
            }
        }

        if ($slugTaken) {
            foreach (range(1, count($slugs)) as $increment) {
                $slugStringIncremented = $slugString.'-'.$increment;
                $slugTaken = false;

                foreach ($slugs as $slug) {
                    if ($slug->getSlug() == $slugStringIncremented) {
                        if (!$slug->isPrimary()) {
                            return $slug;
                        }

                        $slugTaken = true;
                        break;
                    }
                }

                if (!$slugTaken) {
                    $slugEntity->setSlug($slugStringIncremented);

                    return $slugEntity;
                }
            }
        } else {
            $slugEntity->setSlug($slugString);

            return $slugEntity;
        }

        return;
    }
}
