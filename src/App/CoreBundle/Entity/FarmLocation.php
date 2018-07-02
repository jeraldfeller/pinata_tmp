<?php

namespace App\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Vivo\AddressBundle\Behaviour\Model\AustralianAddressTrait;
use Vivo\AddressBundle\Model\AddressInterface;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;

/**
 * Farm.
 *
 * @ORM\Table(name="app_farm_locations")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\FarmRepository")
 */
class FarmLocation implements AddressInterface, AutoFlushCacheInterface
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
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint")
     */
    protected $rank;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $disabledAt;

    /**
     * @var Farm[]
     *
     * @ORM\ManyToMany(targetEntity="App\CoreBundle\Entity\Farm")
     * @ORM\JoinTable(name="location_farms",
     *      joinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="farm_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"rank" = "ASC"})
     **/
    protected $farms;

    public function __construct()
    {
        $this->rank = 9999;
        $this->farms = new ArrayCollection();
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
     * Set farms.
     *
     * @param ArrayCollection $farms
     *
     * @return $this
     */
    public function setFarms(ArrayCollection $farms)
    {
        foreach ($farms as $farm) {
            $this->addFarm($farm);
        }

        return $this;
    }

    /**
     * Add farms.
     *
     * @param Farm $farm
     *
     * @return $this
     */
    public function addFarm(Farm $farm)
    {
        if (!$this->farms->contains($farm)) {
            $this->farms->add($farm);
        }

        return $this;
    }

    /**
     * Remove farm.
     *
     * @param $farm $farm
     */
    public function removeFarm(Farm $farm)
    {
        $this->farms->removeElement($farm);
    }

    /**
     * @return ArrayCollection
     */
    public function getFarms()
    {
        return $this->farms;
    }

    /**
     * @return ArrayCollection
     */
    public function getThirdPartyFarms()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('third_party_farm', true))
            ->andWhere(Criteria::expr()->eq('disabled_at', NULL));

        $farms = $this->farms->matching($criteria);

        return $farms;
    }

    /**
     * @return ArrayCollection
     */
    public function getPinataFarms()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('third_party_farm', false))
            ->andWhere(Criteria::expr()->eq('disabled_at', NULL));

        $farms = $this->getFarms()->matching($criteria);

        return $farms;
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
}
