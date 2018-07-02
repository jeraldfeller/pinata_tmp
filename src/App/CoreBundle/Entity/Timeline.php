<?php

namespace App\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;

/**
 * Fruit.
 *
 * @ORM\Table(name="app_timeline")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\TimelineRepository")
 */
class Timeline implements AutoFlushCacheInterface
{
    const ICON_GENERAL = 0;
    const ICON_MANGO = 1;
    const ICON_PINEAPPLE = 2;
    const ICON_STRAWBERRY = 3;
    const ICON_BERRYWORLD_STRAWBERRY = 22;
    const ICON_RASPBERRY = 23;
    const ICON_BLACKBERRY = 24;

    public static $icons = array(
        self::ICON_GENERAL => 'General',
        self::ICON_MANGO => 'Mango',
        self::ICON_PINEAPPLE => 'Pineapple',
        self::ICON_STRAWBERRY => 'Strawberry',
        self::ICON_RASPBERRY => 'RaspBerry',
        self::ICON_BLACKBERRY => 'BlackBerry',
        self::ICON_BERRYWORLD_STRAWBERRY => 'BerryWorldStrawberry',
    );

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=10)
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column(name="icon", type="smallint")
     */
    protected $icon;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint")
     */
    protected $rank;

    public function __construct()
    {
        $this->rank = 9999;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Set date.
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set icon.
     *
     * @param int $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return int
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get icon label.
     *
     * @return string
     */
    public function getIconLabel()
    {
        if (array_key_exists($this->icon, self::$icons)) {
            return self::$icons[$this->icon];
        }

        return 'Unknown';
    }

    /**
     * Set rank.
     *
     * @param int $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }
}
