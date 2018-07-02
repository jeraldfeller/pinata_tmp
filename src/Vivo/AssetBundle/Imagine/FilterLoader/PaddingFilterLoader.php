<?php

namespace Vivo\AssetBundle\Imagine\FilterLoader;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

/**
 * PaddingFilterLoader.
 */
class PaddingFilterLoader implements LoaderInterface
{
    /**
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;

    /**
     * @param ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        list($width, $height) = $options['size'];
        $currentImageSize = $image->getSize();

        if (null === $width) {
            $width = $currentImageSize->getWidth();
        }

        if (null === $height) {
            $height = $currentImageSize->getHeight();
        }

        if (null === $width && null === $height) {
            // Width and height are both null - Return original image
            return $image;
        }

        if ($width < $currentImageSize->getWidth() && $height < $currentImageSize->getHeight()) {
            // Image is larger than padded size
            return $image;
        }

        $x = $y = 0;

        if ($width > $currentImageSize->getWidth()) {
            $x = floor(($width - $currentImageSize->getWidth()) / 2);
        }

        if ($height > $currentImageSize->getHeight()) {
            $y = floor(($height - $currentImageSize->getHeight()) / 2);
        }

        $background = $image->palette()->color(
            $options['background'],
            isset($options['alpha']) ? $options['alpha'] : null
        );

        $canvas = $this->imagine->create(new Box($width, $height), $background);

        $canvas->paste($image, new Point($x, $y));

        return $canvas;
    }
}
