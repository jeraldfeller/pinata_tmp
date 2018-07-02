<?php

namespace Vivo\AssetBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\AssetBundle\Model\FileInterface;

/**
 * FileRepository.
 */
class FileRepository extends EntityRepository implements FileRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneById($id)
    {
        return $this->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneFileByHash($hash)
    {
        return $this->createQueryBuilder('file')
            ->where('file.hash = :hash')
            ->setParameter('hash', $hash)
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createFile()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function touch(FileInterface $file, $andFlush = true)
    {
        $file->touch();

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }
}
