<?php

namespace Vivo\SlugBundle\Util;

use Vivo\SlugBundle\Model\SlugInterface;

interface UniqueSlugGeneratorInterface
{
    /**
     * Generate a unique slug.
     *
     * @param SlugInterface $slugEntity
     * @param string        $slugString
     * @param bool          $allowSlashes
     *
     * @return null|SlugInterface
     */
    public function generate(SlugInterface $slugEntity, $slugString, $allowSlashes = false);
}
