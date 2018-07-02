<?php

namespace App\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Doctrine\Common\Collections\Criteria;

/**
 * Fruit.
 *
 * @ORM\Table(name="app_fruit")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\FruitRepository")
 */
class Fruit implements AutoFlushCacheInterface
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255)
     */
    protected $subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="text")
     */
    protected $intro;

    /**
     * @var string
     *
     * @ORM\Column(name="page_introduction", type="text")
     */
    protected $pageIntroduction;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=25)
     */
    protected $colorClass;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $disabledAt;

    /**
     * @var FruitSlug.
     * @ORM\ManyToOne(targetEntity="FruitSlug", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="primary_slug_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $primarySlug;

    /**
     * @var FruitSlug[]|\Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="FruitSlug", mappedBy="fruit", cascade={"persist", "remove"})
     */
    protected $slugs;

    /**
     * @var FruitImage
     *
     * @ORM\OneToOne(targetEntity="FruitImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $primaryImage;

    /**
     * @var FruitImage
     *
     * @ORM\OneToOne(targetEntity="FruitImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $bannerImage;

    /**
     * @var FruitImage
     *
     * @ORM\OneToOne(targetEntity="FruitImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $contentImage;

    /**
     * @var PromoBlockGroup
     *
     * @ORM\ManyToOne(targetEntity="PromoBlockGroup")
     * @ORM\JoinColumn(name="promo_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $promoGroup;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint")
     */
    protected $rank;

    public function __construct()
    {
        $this->rank = 9999;
        $this->slugs = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Fruit
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set disabledAt.
     *
     * @param \DateTime $disabledAt
     */
    public function setDisabledAt(\DateTime $disabledAt = null)
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set pageIntroduction.
     *
     * @param string $pageIntroduction
     */
    public function setPageIntroduction($pageIntroduction)
    {
        $this->pageIntroduction = $pageIntroduction;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageIntroduction()
    {
        return $this->pageIntroduction;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlug(FruitSlug $slug)
    {
        if (!$this->getSlugs()->contains($slug)) {
            if ($slug->getFruit()) {
                $slug->getFruit()->removeSlug($slug);
            }

            $slug->setFruit($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(FruitSlug $slug)
    {
        $this->slugs->removeElement($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugs()
    {
        if ($this->slugs) {
            return $this->slugs;
        }

        return new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimarySlug(FruitSlug $primarySlug = null)
    {
        if (null !== $primarySlug) {
            $this->addSlug($primarySlug);
        }

        $this->primarySlug = $primarySlug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimarySlug()
    {
        if (null === $this->primarySlug) {
            $criteria = Criteria::create()
                ->orderBy(array('id' => Criteria::DESC))
                ->setMaxResults(1);

            return $this->getSlugs()->matching($criteria)->first() ?: null;
        }

        return $this->primarySlug;
    }

    /**
     * Set primaryImage.
     *
     * @param FruitImage $primaryImage
     *
     * @return $this
     */
    public function setPrimaryImage(FruitImage $primaryImage = null)
    {
        $this->primaryImage = $primaryImage;

        if ($primaryImage && $primaryImage->getFruit() !== $this) {
            $primaryImage->setFruit($this);
        }

        return $this;
    }

    /**
     * Get primaryImage.
     *
     * @return FruitImage
     */
    public function getPrimaryImage()
    {
        return $this->primaryImage;
    }

    /**
     * Set color.
     *
     * @param string $color
     */
    public function setColorClass($colorClass)
    {
        $this->colorClass = $colorClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->colorClass;
    }

    /**
     * Set subtitle.
     *
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set intro.
     *
     * @param string $intro
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntro()
    {
        return $this->intro;
    }

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
     * Set bannerImage.
     *
     * @param \App\CoreBundle\Entity\FruitImage $bannerImage
     */
    public function setBannerImage(FruitImage $bannerImage = null)
    {
        $this->bannerImage = $bannerImage;

        if ($bannerImage && $bannerImage->getFruit() !== $this) {
            $bannerImage->setFruit($this);
        }

        return $this;
    }

    /**
     * @return \App\CoreBundle\Entity\FruitImage
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * Set bannerImage.
     *
     * @param \App\CoreBundle\Entity\FruitImage $bannerImage
     */
    public function setContentImage(FruitImage $bannerImage = null)
    {
        $this->contentImage = $bannerImage;

        if ($bannerImage && $bannerImage->getFruit() !== $this) {
            $bannerImage->setFruit($this);
        }

        return $this;
    }

    /**
     * @return \App\CoreBundle\Entity\FruitImage
     */
    public function getContentImage()
    {
        return $this->contentImage;
    }
}
