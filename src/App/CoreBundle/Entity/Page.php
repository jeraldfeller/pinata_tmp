<?php

namespace App\CoreBundle\Entity;

use Vivo\PageBundle\Model\Page as BasePage;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page.
 *
 * @ORM\Entity
 * @ORM\Table(name="vivo_page", indexes={
 *     @ORM\Index(name="created_at", columns={"created_at"}),
 *     @ORM\Index(name="updated_at", columns={"updated_at"})
 * })
 */
class Page extends BasePage
{
    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     */
    protected $introduction;

    /**
     * @var PromoBlockGroup
     *
     * @ORM\ManyToOne(targetEntity="PromoBlockGroup")
     * @ORM\JoinColumn(name="promo_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $promoGroup;

    /**
     * Set promoGroup.
     *
     * @param PromoBlockGroup $promoGroup
     *
     * @return $this
     */
    public function setPromoGroup(PromoBlockGroup $promoGroup = null)
    {
        $this->promoGroup = $promoGroup;

        return $this;
    }

    /**
     * Get promoGroup.
     *
     * @return PromoBlockGroup
     */
    public function getPromoGroup()
    {
        return $this->promoGroup;
    }

    /**
     * Set introduction.
     *
     * @param $introduction
     *
     * @return $this
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction.
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }
}
