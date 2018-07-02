<?php

namespace Vivo\AddressBundle\Model;

use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Suburb.
 */
class Suburb
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $state;

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
     * Formats the suburb.
     *
     * @param string $name
     *
     * @return string
     */
    public static function formatSuburbName($name)
    {
        $name = ucwords(strtolower($name));
        foreach (array('GPO', 'BC', 'DC', 'MC') as $word) {
            $name = preg_replace('/\b('.$word.')\b/i', strtoupper($word), $name);
        }

        return $name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) self::formatSuburbName($name);

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set postcode.
     *
     * @param string $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->postcode = (string) $postcode;

        return $this;
    }

    /**
     * Get postcode.
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = (string) $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getNameAndState()
    {
        return $this->name.', '.$this->state;
    }
}
