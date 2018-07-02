<?php

namespace Vivo\AssetBundle\Factory;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\File;
use Vivo\AssetBundle\Exception\NonReadableFileException;
use Vivo\AssetBundle\Model\FileInterface;
use Vivo\AssetBundle\Repository\FileRepositoryInterface;

class FileFactory implements FileFactoryInterface
{
    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FileInterface[]
     */
    protected $activeFiles = array();

    /**
     * @param FileRepositoryInterface $fileRepository
     * @param Filesystem              $filesystem
     */
    public function __construct(FileRepositoryInterface $fileRepository, Filesystem $filesystem)
    {
        $this->fileRepository = $fileRepository;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneFileOrCreate(SymfonyFile $symfonyFile, $andCopy = false)
    {
        if (!$symfonyFile->isReadable()) {
            throw new NonReadableFileException();
        }

        if (true === $andCopy) {
            $tmpName = sys_get_temp_dir().'/'.substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 16).'.'.$symfonyFile->getExtension();

            $this->filesystem->copy($symfonyFile, $tmpName);

            $symfonyFile = new File($tmpName);
        }

        $newFile = $this->fileRepository->createFile();
        $newFile->setFile($symfonyFile);
        $fileHash = $newFile->getHash();

        if (!$fileHash) {
            throw new \Exception('Could not determine file hash.');
        }

        if (isset($this->activeFiles[$fileHash])) {
            return $this->activeFiles[$fileHash];
        }

        if ($file = $this->fileRepository->findOneFileByHash($fileHash)) {
            $file->setFile($symfonyFile);
        } else {
            $file = $newFile;
        }

        $this->activeFiles[$file->getHash()] = $file;

        return $file;
    }
}
