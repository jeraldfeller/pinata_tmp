<?php

namespace App\CoreBundle\Entity;

use App\CoreBundle\Model\Choice\FruitChoice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vivo\AddressBundle\Behaviour\Model\AustralianAddressTrait;
use Vivo\AddressBundle\Model\AddressInterface;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Doctrine\Common\Collections\Criteria;

/**
 * Farm.
 *
 * @ORM\Table(name="app_farm")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\FarmRepository")
 */
class Farm implements AddressInterface, AutoFlushCacheInterface
{
    use AustralianAddressTrait;
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
     * @ORM\Column(name="location_name", type="string", length=255)
     */
    protected $locationName;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     */
    protected $introduction;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $headOffice;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $thirdPartyFarm;

    /**
     * @var array
     * @ORM\Column(type="simple_array")
     */
    protected $fruits;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $disabledAt;

    /**
     * @var FarmSlug.
     * @ORM\ManyToOne(targetEntity="FarmSlug", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="primary_slug_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $primarySlug;

    /**
     * @var FarmSlug[]|\Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="FarmSlug", mappedBy="farm", cascade={"persist", "remove"})
     */
    protected $slugs;

    /**
     * @var FarmImage
     *
     * @ORM\OneToOne(targetEntity="FarmImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var FarmImage
     *
     * @ORM\OneToOne(targetEntity="FarmImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $contentImage;

    /**
     * @var FarmImage
     *
     * @ORM\OneToOne(targetEntity="FarmImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $bannerImage;

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
     * @return Farm
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
     * Set headOffice.
     *
     * @param bool $headOffice
     */
    public function setHeadOffice($headOffice)
    {
        $this->headOffice = $headOffice;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHeadOffice()
    {
        return $this->headOffice;
    }

    /**
     * Set thirdPartyFarm.
     *
     * @param bool $thirdPartyFarm
     */
    public function setThirdPartyFarm($thirdPartyFarm)
    {
        $this->thirdPartyFarm = $thirdPartyFarm;

        return $this;
    }

    /**
     * @return bool
     */
    public function getThirdPartyFarm()
    {
        return $this->thirdPartyFarm;
    }

    /**
     * Set fruits.
     *
     * @param array $fruits
     */
    public function setFruits(array $fruits = null)
    {
        $this->fruits = $fruits;

        return $this;
    }

    /**
     * @return array
     */
    public function getFruits()
    {
        return $this->fruits;
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
     * {@inheritdoc}
     */
    public function addSlug(FarmSlug $slug)
    {
        if (!$this->getSlugs()->contains($slug)) {
            if ($slug->getFarm()) {
                $slug->getFarm()->removeSlug($slug);
            }

            $slug->setFarm($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(FarmSlug $slug)
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
    public function setPrimarySlug(FarmSlug $primarySlug = null)
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
     * Set image.
     *
     * @param FarmImage $image
     *
     * @return $this
     */
    public function setBannerImage(FarmImage $image = null)
    {
        $this->bannerImage = $image;

        if ($image && $image->getFarm() !== $this) {
            $image->setFarm($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return FarmImage
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * Set image.
     *
     * @param FarmImage $image
     *
     * @return $this
     */
    public function setImage(FarmImage $image = null)
    {
        $this->image = $image;

        if ($image && $image->getFarm() !== $this) {
            $image->setFarm($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return FarmImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set Content image.
     *
     * @param FarmImage $image
     *
     * @return $this
     */
    public function setContentImage(FarmImage $image = null)
    {
        $this->contentImage = $image;

        if ($image && $image->getFarm() !== $this) {
            $image->setFarm($this);
        }

        return $this;
    }

    /**
     * Get image.
     *
     * @return FarmImage
     */
    public function getContentImage()
    {
        return $this->contentImage;
    }

    /**
     * Set latitude.
     *
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getMapIcon()
    {
        if ($this->headOffice) {
            return 'headOffice';
        }

        if (count($this->fruits) > 1) {
            return 'greenStar';
        } else {
            $icon = strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', FruitChoice::getLabel($this->fruits[0])));
            if ($this->thirdPartyFarm) {
                return $icon.'_third_party';
            }

            return $icon;
        }

        return 'icon';
    }

    public function getMapFruits()
    {
        $fruitsList = '';
        foreach ($this->fruits as $fruit) {
            $label = strtolower(FruitChoice::getLabel($fruit));
            $fruitsList .= '<li class="'.$label.'"><i class="pin-'.$label.'-relief"></i></li>';
        }

        return $fruitsList;
    }

    public function getMapListFruitIcon()
    {
        if (is_array($this->fruits)) {
            $fruit = $this->fruits[0];
            $label = strtolower(FruitChoice::getLabel($fruit));
            if ($this->thirdPartyFarm) {
                return '<i class="pin-'.$label.'-circled coloured"></i>';
            }

            return '<i class="pin-'.$label.'-relief coloured"></i>';
        }

        return '';
    }

    public function getMapListFruitColour()
    {
        if (is_array($this->fruits)) {
            $fruit = $this->fruits[0];
            $label = strtolower(FruitChoice::getColour($fruit));

            return $label;
        }

        return '';
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
     * Set locationName.
     *
     * @param string $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /*public function getLinkedData($parentId){
        return array(
            "@context"=> "http://schema.org",
            "@type"=> "LocalBusiness",
            "address"=> array(
                    "@type"=> "PostalAddress",
                    "addressLocality"=> $this->getLocality(),
                    "addressRegion"=> $this->getState(true),
                    "streetAddress"=> $this->getStreetAddress()
            ),
            "name"=> $this->getName(),
            "telephone"=> $this->getPhoneNumber(),
            "parentOrganization"=> array( "@id"=> $parentId )
        );
    }*/
}
