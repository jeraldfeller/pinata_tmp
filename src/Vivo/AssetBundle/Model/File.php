<?php

namespace Vivo\AssetBundle\Model;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vivo\AssetBundle\Exception\NonReadableFileException;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * File.
 */
class File implements FileInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var \DateTime
     */
    protected $touchedAt;

    /**
     * @var \Vivo\AssetBundle\Model\AssetInterface[]
     */
    protected $assets;

    /**
     * @var SymfonyFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $uploadDirectory;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->salt = substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 16);
        $this->touchedAt = new \DateTime('now');
        $this->assets = new ArrayCollection();
    }

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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function touch()
    {
        $this->touchedAt = new \DateTime('now');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTouchedAt()
    {
        return $this->touchedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = (string) $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function addAsset(AssetInterface $asset)
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAsset(AssetInterface $asset)
    {
        $this->assets->removeElement($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilenameHash($filename, $context = null)
    {
        $string = $this->hash.$this->salt.$filename;

        if (null !== $context) {
            $string .= $context;
        }

        return substr(sha1($string), 0, 6);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedSize($precision = 2)
    {
        $size = (int) $this->size;

        $base = log($size) / log(1024);
        $suffixes = array(' B', ' KB', ' M', ' G', ' T');

        return round(pow(1024, $base - floor($base)), $precision).$suffixes[intval(floor($base))];
    }

    /**
     * {@inheritdoc}
     */
    public static function getImageMimeTypes()
    {
        return array('image/jpeg', 'image/gif', 'image/png');
    }

    /**
     * {@inheritdoc}
     */
    public function isImage()
    {
        return null !== $this->width && null !== $this->height && in_array($this->mimeType, $this->getImageMimeTypes()) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePreviewPath($filename)
    {
        if (!$this->isImage()) {
            return;
        }

        return ceil($this->getId() % 3).'/'.ceil($this->getId() % 10).'/'.$this->getId().'/'.$this->getFilenameHash($filename).'/'.$filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(AssetInterface $asset, $context)
    {
        return 'vivo_asset.asset.download_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters(AssetInterface $asset, $context)
    {
        $filename = $asset->getFilename(true);

        return array(
            'id' => $this->getId(),
            'hash' => $this->getFilenameHash($filename, $context),
            'name' => $filename,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAbsolutePath()
    {
        return rtrim($this->uploadDirectory, '/').'/'.$this->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $numbers = str_pad(substr(preg_replace('/[^0-9]/', null, $this->hash), 0, 2), 2, '0');

        return substr($numbers, 0, 1).'/'.substr($numbers, 1, 1).'/'.$this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(SymfonyFile $file)
    {
        if (!$file->isReadable()) {
            throw new NonReadableFileException('Could not read file.');
        }

        $mimeType = $file->getMimeType();
        $extension = $this->guessExtension($file);
        $size = $file->getSize();
        $hash = $this->generateHashFromFile($file);

        if ($this->id) {
            /*
             * - The mime type is guessed based on the file contents
             * - The file extension is guessed based on the mime type
             *
             * Therefore, there should never be a conflict
             * This exception is here to warn us if there is somehow.
             */
            if ($mimeType !== $this->mimeType) {
                throw new \Exception(sprintf('Mime type conflict (Id: %d, Current: %s, New: %s', $this->id, $this->mimeType, $mimeType));
            } elseif ($extension !== $this->extension) {
                throw new \Exception(sprintf('Extension conflict (Id: %d, Current: %s, New: %s', $this->id, $this->extension, $extension));
            } elseif ($size !== $this->size) {
                throw new \Exception(sprintf('Size conflict (Id: %d, Current: %s, New: %s', $this->id, $this->size, $size));
            } elseif ($hash !== $this->hash) {
                throw new \Exception(sprintf('Hash conflict (Id: %d, Current: %s, New: %s', $this->id, $this->hash, $hash));
            }
        }

        $this->file = $file;
        $this->hash = $hash;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
        $this->size = $size;

        if (false !== $imageSize = @getimagesize($file->getPathname())) {
            $this->width = (int) $imageSize[0];
            $this->height = (int) $imageSize[1];
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
    public function setUploadDirectory($uploadDirectory)
    {
        $this->uploadDirectory = (string) $uploadDirectory;
    }

    /**
     * Fields to ignore when updating the timestamp.
     *
     * @return array
     */
    public function getIgnoredUpdateFields()
    {
        return array('touchedAt');
    }

    /**
     * @param SymfonyFile $file
     *
     * @return string
     */
    protected function generateHashFromFile(SymfonyFile $file)
    {
        return hash_file('sha256', $file->getPathname());
    }

    /**
     * Guess file extension.
     *
     * @param SymfonyFile $file
     *
     * @return null|string
     */
    protected function guessExtension(SymfonyFile $file)
    {
        $extension = $file->guessExtension();

        switch ($extension) {
            case 'jpeg':
                return 'jpg';

                break;
        }

        if (null === $extension) {
            $extension = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : $file->getExtension();
        }

        return $extension;
    }
}
