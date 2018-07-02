<?php

namespace Vivo\AddressBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vivo\AddressBundle\Model\Suburb;

class LoadAustralianSuburbs extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load Australian Suburbs data.
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $zipFile = dirname(__FILE__).'/../../Resources/data/pc-book.zip';
        $zip = zip_open($zipFile);

        if (!is_resource($zip)) {
            throw new \Exception('Could not open postcode zip file.');
        }

        $csvFiles = array();
        while ($zip_entry = zip_read($zip)) {
            $filename = zip_entry_name($zip_entry);
            if (!preg_match('/\.csv$/i', $filename)) {
                continue;
            }

            if (zip_entry_open($zip, $zip_entry, 'r')) {
                $outputFilename = tempnam($this->getCacheDir(), 'pcbook');
                $fp = fopen($outputFilename, 'w');
                $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                fwrite($fp, $buf);
                zip_entry_close($zip_entry);
                fclose($fp);

                $csvFiles[] = $outputFilename;
            }
        }
        zip_close($zip);

        foreach ($csvFiles as $file) {
            $this->importCSV($manager, $file);
        }
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        /** @var \Symfony\Component\HttpKernel\Kernel $kernel */
        $kernel = $this->container->get('kernel');

        return $kernel->getCacheDir();
    }

    /**
     * Returns number of records imported.
     *
     * @param EntityManager $manager
     * @param string        $path
     *
     * @return int
     */
    protected function importCSV(EntityManager $manager, $path)
    {
        $map = array(
            'postcode', 'name', 'state',
        );

        $rowNumber = 0;
        $numRecords = 0;
        $batchSize = 20;

        $handle = fopen($path, 'r');
        while (false !== ($r = fgetcsv($handle, 0, ','))) {
            ++$rowNumber;

            if (!preg_match('/[0-9]/', $r[0])) {
                // Only process if the row contains a valid postcode
                continue;
            }

            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            $suburb = new Suburb();

            foreach ($map as $mapKey => $entityProperty) {
                $csvValue = $propertyAccessor->getValue($r, '['.$mapKey.']');

                $propertyAccessor->setValue($suburb, $entityProperty, $csvValue);
            }

            $manager->persist($suburb);
            if (($rowNumber % $batchSize) == 0) {
                $manager->flush();
                $manager->clear(); // Detaches all objects from Doctrine!
            }

            ++$numRecords;
        }

        $manager->flush();

        unlink($path);

        return $numRecords;
    }
}
