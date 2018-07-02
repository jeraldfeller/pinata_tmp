<?php

namespace App\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * PromoBlock.
 *
 * @ORM\Table(name="app_core_promo_block_group")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\PromoBlockGroupRepository")
 */
class PromoBlockGroup
{
    use TimestampableTrait;
    use CsrfIntentionTrait;

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
    protected $name;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AllocatedPromoBlock", mappedBy="promoGroup", cascade={"persist", "remove"})
     * @ORM\OrderBy({"rank" = "ASC"})
     */
    private $blocks;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
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
     * Set blocks.
     *
     * @param ArrayCollection $blocks
     *
     * @return $this
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        foreach ($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * Add blocks.
     *
     * @param AllocatedPromoBlock $block
     *
     * @return $this
     */
    public function addBlock(AllocatedPromoBlock $block)
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
        }

        return $this;
    }

    /**
     * Remove block.
     *
     * @param AllocatedPromoBlock $block
     */
    public function removeBlock(AllocatedPromoBlock $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * @return ArrayCollection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
