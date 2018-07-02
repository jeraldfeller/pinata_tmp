<?php

namespace Vivo\UtilBundle\Util;

use Doctrine\Common\Cache\PhpFileCache;
use GeoIp2\Database\Reader;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;

class GeoIp
{
    /**
     * @var string
     */
    private $cityDatabase;

    /**
     * @var string
     */
    private $countryDatabase;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var PhpFileCache
     */
    private $cache;

    /**
     * @var Reader[]
     */
    private $readers = [];

    /**
     * @param string $cityDatabase
     * @param string $countryDatabase
     * @param string $cacheDirectory
     */
    public function __construct($cityDatabase, $countryDatabase, $cacheDirectory)
    {
        $this->cityDatabase = $cityDatabase;
        $this->countryDatabase = $countryDatabase;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @param string $ipAddress
     *                          IPv4 or IPv6 address to lookup.
     *
     * @return \GeoIp2\Model\City A City model for the requested IP address.
     */
    public function getCity($ipAddress)
    {
        $cache = $this->getCache();
        $cacheKey = 'city.'.hash('sha256', $this->cityDatabase.$ipAddress);

        if ($cache->contains($cacheKey)) {
            return new City($cache->fetch($cacheKey));
        }

        if (null === $reader = $this->getReader($this->cityDatabase)) {
            return new City(array());
        }

        try {
            $city = $reader->city($ipAddress);
        } catch (\Exception $e) {
            $city = new City(array());
        }

        $cache->save($cacheKey, $city->jsonSerialize(), 86400);

        return $city;
    }

    /**
     * This method returns a GeoIP2 Country model.
     *
     * @param string $ipAddress IPv4 or IPv6 address as a string.
     *
     * @return \GeoIp2\Model\Country
     */
    public function getCountry($ipAddress)
    {
        $cache = $this->getCache();
        $cacheKey = 'country.'.hash('sha256', $this->countryDatabase.$ipAddress);

        if ($cache->contains($cacheKey)) {
            return new Country($cache->fetch($cacheKey));
        }

        if (null === $reader = $this->getReader($this->countryDatabase)) {
            return new Country(array());
        }

        try {
            $city = $reader->country($ipAddress);
        } catch (\Exception $e) {
            $city = new Country(array());
        }

        $cache->save($cacheKey, $city->jsonSerialize(), 86400);

        return $city;
    }

    /**
     * Get cache.
     *
     * @return PhpFileCache
     */
    private function getCache()
    {
        if (null === $this->cache) {
            $this->cache = new PhpFileCache($this->cacheDirectory);
        }

        return $this->cache;
    }

    /**
     * Get reader.
     *
     * @param string $database
     *
     * @return Reader|null
     */
    private function getReader($database)
    {
        if (!array_key_exists($database, $this->readers)) {
            try {
                $this->readers[$database] = new Reader($database);
            } catch (\Exception $e) {
                $this->readers[$database] = null;
            }
        }

        return $this->readers[$database];
    }
}
