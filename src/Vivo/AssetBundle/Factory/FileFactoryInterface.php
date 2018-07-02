<?php

namespace Vivo\AssetBundle\Factory;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

interface FileFactoryInterface
{
    /**
     * @param SymfonyFile $file
     * @param bool        $copy Copy the file rather than moving it
     *
     * @return null|\Vivo\AssetBundle\Model\FileInterface
     *
     * @throws \Vivo\AssetBundle\Exception\NonReadableFileException
     */
    public function findOneFileOrCreate(SymfonyFile $file, $copy = false);
}
