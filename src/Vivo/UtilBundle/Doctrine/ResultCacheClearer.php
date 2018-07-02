<?php

namespace Vivo\UtilBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * Class ResultCacheClearer.
 *
 * Clears the Doctrine Result Cache
 */
class ResultCacheClearer implements CacheClearerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($cacheDir)
    {
        $this->entityManager
            ->getConfiguration()
            ->getResultCacheImpl()
            ->deleteAll();
    }
}
