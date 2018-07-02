<?php

namespace Vivo\UtilBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeToBooleanTransformer implements DataTransformerInterface
{
    /**
     * @var bool
     */
    protected $inverted;

    /**
     * @var \DateTime
     */
    protected $originalValue;

    /**
     * Constructor.
     *
     * @param bool $inverted
     */
    public function __construct($inverted)
    {
        $this->inverted = (bool) $inverted;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return $this->inverted ? true : false;
        }

        $this->originalValue = $value;

        if ($value instanceof \DateTime && $value <= new \DateTime('now')) {
            return $this->inverted ? false : true;
        }

        return $this->inverted ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (true === $value) {
            if (true === $this->transform($this->originalValue)) {
                // Nothing has changed. Use the original value.
                return $this->originalValue;
            }

            return $this->inverted ? null : new \DateTime('now');
        }

        return $this->inverted ? new \DateTime('now') : null;
    }
}
