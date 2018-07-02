<?php

namespace App\CoreBundle\Entity;

use App\CoreBundle\Model\Choice\IconChoice;
use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * PromoBlock.
 *
 * @ORM\Table(name="app_core_promo_block")
 * @ORM\Entity(repositoryClass="App\CoreBundle\Repository\PromoBlockRepository")
 */
class PromoBlock
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
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255, nullable=true)
     */
    protected $content;

    /**
     * @var int
     *
     * @ORM\Column(name="icon", type="smallint", nullable=true)
     */
    protected $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=100, nullable=true)
     */
    protected $url;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $newWindow;

    /**
     * @var int
     *
     * @ORM\Column(name="icon_position", type="smallint", nullable=true)
     */
    protected $iconPosition;

    public function __construct()
    {
        $this->iconPosition = 1;
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
     * Get icon label.
     *
     * @return string
     */
    public function getIconClass()
    {
        if (array_key_exists($this->icon, IconChoice::$icons)) {
            return IconChoice::$classes[$this->icon];
        }
    }

    /**
     * Set url.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set newWindow.
     *
     * @param bool $newWindow
     */
    public function setNewWindow($newWindow)
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNewWindow()
    {
        return $this->newWindow;
    }

    /**
     * Set iconPosition.
     *
     * @param bool $iconPosition
     */
    public function setIconPosition($iconPosition)
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIconPosition()
    {
        return $this->iconPosition;
    }
}
