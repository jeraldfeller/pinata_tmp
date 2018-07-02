<?php

namespace Vivo\AssetBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Util\Urlizer;

/**
 * Asset.
 */
abstract class Asset implements AssetInterface
{
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $filename_clean;

    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $subtitle;
    
    /**
     * @var string
     */
    protected $colorclass;
    
    /**
     * @var int
     */
    protected $status;
    
    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $linkTarget;

    /**
     * @var \DateTime
     */
    protected $activeAt;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $alt;

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
    public function setFilename($filename)
    {
        if (null === $this->title) {
            $this->setTitle($filename);
        }

        if (strlen($filename) < 1 && $this->getFile()) {
            $filename = (Urlizer::urlize($this->title) ?: 'untitled').'.'.$this->getFile()->getExtension();
        }

        $this->filename = $filename;
        $filenameParts = explode('.', $this->filename);

        if ($this->getFile()) {
            if (!preg_match('/.'.$this->getFile()->getExtension().'$/i', $this->filename)) {
                // Extension is missing from filename
                $this->filename .= '.'.$this->getFile()->getExtension();
            }

            if (strtolower($this->getFile()->getExtension()) !== strtolower($filenameParts[count($filenameParts) - 1])) {
                $filenameParts[] = $this->getFile()->getExtension();
            }
        }

        foreach ($filenameParts as $partKey => $part) {
            $filenameParts[$partKey] = Urlizer::urlize($part);
        }

        $this->filename_clean = implode('.', $filenameParts);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename($clean = false)
    {
        if ($clean) {
            return $this->filename_clean;
        }

        return $this->filename;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = null === $title ? null : (string) $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSubTitle($subtitle)
    {
        $this->subtitle = null === $subtitle ? null : (string) $subtitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubTitle()
    {
        return $this->subtitle;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setColorClass($colorclass)
    {
        $this->colorclass = null === $colorclass ? null : (string) $colorclass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColorClass()
    {
        return $this->colorclass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setLink($link)
    {
        $this->link = null === $link ? null : (string) $link;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkTarget($linkTarget)
    {
        $this->linkTarget = null === $linkTarget ? null : (string) $linkTarget;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveAt(\DateTime $activeAt = null)
    {
        $this->activeAt = $activeAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveAt()
    {
        return $this->activeAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        $now = new \DateTime('now');

        if (
            (null === $this->activeAt || $this->activeAt <= $now) &&
            (null === $this->expiresAt || $this->expiresAt > $now)
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(FileInterface $file)
    {
        $this->file = $file;

        if (null !== $file = $file->getFile()) {
            if ($this->getFile() && ($file = $this->getFile()->getFile())) {
                $this->setFilename($file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename());
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePreviewPath()
    {
        return $this->getFile()->getImagePreviewPath($this->getFilename(true));
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName($context)
    {
        return $this->getFile()->getRouteName($this, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters($context)
    {
        return $this->getFile()->getRouteParameters($this, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlt($alt)
    {
        $this->alt = null === $alt ? null : (string) $alt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->alt;
    }
}
