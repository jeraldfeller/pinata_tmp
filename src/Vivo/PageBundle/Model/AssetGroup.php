<?php

namespace Vivo\PageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * AssetGroup.
 */
class AssetGroup implements AssetGroupInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var \Vivo\PageBundle\Model\AssetInterface[]|ArrayCollection
     */
    protected $assets;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->assets = new ArrayCollection();
    }

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
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function addAsset(AssetInterface $asset)
    {
        if (!$this->assets->contains($asset)) {
            $asset->setAssetGroup($this);
            $this->assets->add($asset);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAsset(AssetInterface $asset)
    {
        if ($this->assets->contains($asset)) {
            $asset->setAssetGroup(null);
            $this->assets->removeElement($asset);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        $criteria = Criteria::create()
            ->orderBy(array('rank' => Criteria::ASC, 'id' => Criteria::ASC))
        ;

        return $this->assets->matching($criteria);
    }

    /**3
     * {@inheritdoc}
     */
    public function getActiveAssets()
    {
        $now = new \DateTime('now');

        $criteria = Criteria::create()
            ->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->lte('activeAt', $now),
                    Criteria::expr()->isNull('activeAt')
                )
            )
            ->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->gt('expiresAt', $now),
                    Criteria::expr()->isNull('expiresAt')
                )
            )
            ->orderBy(array('rank' => Criteria::ASC, 'id' => Criteria::ASC))
        ;

        return $this->assets->matching($criteria);
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array($this->getPage());
    }
}
