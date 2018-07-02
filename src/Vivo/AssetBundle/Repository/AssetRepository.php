<?php

namespace Vivo\AssetBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\AssetBundle\Model\AssetInterface;

/**
 * AssetRepository.
 */
class AssetRepository extends EntityRepository implements AssetRepositoryInterface
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
    public function findDuplicateAssetByFilename(AssetInterface $asset)
    {
        return $this->createQueryBuilder('asset')
            ->addSelect('file')
            ->innerJoin('asset.file', 'file')
            ->where('file.hash = :hash and asset.filename = :filename')
            ->setParameter('hash', $asset->getFile()->getHash())
            ->setParameter('filename', $asset->getFilename())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createAsset()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
