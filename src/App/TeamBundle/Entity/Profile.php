<?php

namespace App\TeamBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use App\CoreBundle\Entity\PromoBlockGroup;

/**
 * Profile.
 *
 * @ORM\Table(name="app_team_profile", indexes={
 *     @ORM\Index(name="disabled_at", columns={"disabled_at"}),
 *     @ORM\Index(name="rank", columns={"rank"})
 * })
 * @ORM\Entity(repositoryClass="App\TeamBundle\Repository\ProfileRepository")
 */
class Profile
{
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $quote;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     */
    protected $introduction;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200)
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $disabledAt;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    protected $rank = 9999;

    /**
     * @var ProfileImage
     *
     * @ORM\OneToOne(targetEntity="ProfileImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var ProfileImage
     *
     * @ORM\OneToOne(targetEntity="ProfileImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $contentImage;

    /**
     * @var ProfileImage
     *
     * @ORM\OneToOne(targetEntity="ProfileImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $bannerImage;

    /**
     * @var ProfileSlug.
     * @ORM\ManyToOne(targetEntity="ProfileSlug", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="primary_slug_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $primarySlug;

    /**
     * @var ProfileSlug[]|\Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="ProfileSlug", mappedBy="profile", cascade={"persist", "remove"})
     */
    protected $slugs;

    /**
     * @var PromoBlockGroup
     *
     * @ORM\ManyToOne(targetEntity="\App\CoreBundle\Entity\PromoBlockGroup")
     * @ORM\JoinColumn(name="promo_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $promoGroup;

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
     * Set quote.
     *
     * @param bool $quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return bool
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set introduction.
     *
     * @param string $introduction
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
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
     * Set position.
     *
     * @param string $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set disabledAt.
     *
     * @param \DateTime $disabledAt
     *
     * @return $this
     */
    public function setDisabledAt(\DateTime $disabledAt = null)
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * Get disabledAt.
     *
     * @return \DateTime
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * Is disabled?
     *
     * @return bool
     */
    public function isDisabled()
    {
        return null === $this->disabledAt ? false : true;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return $this
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set image.
     *
     * @param ProfileImage $image
     *
     * @return $this
     */
    public function setBannerImage(ProfileImage $image = null)
    {
        $this->bannerImage = $image;

        if ($image && $image->getProfile() !== $this) {
            $image->setProfile($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return ProfileImage
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * Set image.
     *
     * @param ProfileImage $image
     *
     * @return $this
     */
    public function setImage(ProfileImage $image = null)
    {
        $this->image = $image;

        if ($image && $image->getProfile() !== $this) {
            $image->setProfile($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return ProfileImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set Content image.
     *
     * @param ProfileImage $image
     *
     * @return $this
     */
    public function setContentImage(ProfileImage $image = null)
    {
        $this->contentImage = $image;

        if ($image && $image->getProfile() !== $this) {
            $image->setProfile($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return ProfileImage
     */
    public function getContentImage()
    {
        return $this->contentImage;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlug(ProfileSlug $slug)
    {
        if (!$this->getSlugs()->contains($slug)) {
            if ($slug->getProfile()) {
                $slug->getProfile()->removeSlug($slug);
            }

            $slug->setProfile($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(ProfileSlug $slug)
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
    public function setPrimarySlug(ProfileSlug $primarySlug = null)
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
}
