<?php

namespace App\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AllocatedPromoBlock.
 *
 * @ORM\Table(name="app_core_promo_block_allocation")
 * @ORM\Entity()
 */
class AllocatedPromoBlock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $rank;

    /**
     * @var PromoBlock
     *
     * @ORM\ManyToOne(targetEntity="App\CoreBundle\Entity\PromoBlock")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $block;

    /**
     * @var PromoBlockGroup
     *
     * @ORM\ManyToOne(targetEntity="App\CoreBundle\Entity\PromoBlockGroup", inversedBy="blocks")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $promoGroup;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rank = 99;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rank.
     *
     * @param string $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set block.
     *
     * @param \App\CoreBundle\Entity\PromoBlock $block
     */
    public function setBlock(\App\CoreBundle\Entity\PromoBlock $block = null)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return \App\CoreBundle\Entity\PromoBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set promoGroup.
     *
     * @param \App\CoreBundle\Entity\PromoBlockGroup $promoGroup
     */
    public function setPromoGroup(\App\CoreBundle\Entity\PromoBlockGroup $promoGroup = null)
    {
        $this->promoGroup = $promoGroup;

        return $this;
    }

    /**
     * @return \App\CoreBundle\Entity\PromoBlockGroup
     */
    public function getPromoGroup()
    {
        return $this->promoGroup;
    }
}
