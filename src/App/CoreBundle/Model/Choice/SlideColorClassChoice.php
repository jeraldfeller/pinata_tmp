<?php

namespace App\CoreBundle\Model\Choice;

class SlideColorClassChoice
{
    const COLOUR_ONE = 'cream';
    const COLOUR_TWO = 'green';
    const COLOUR_THREE = 'dark-green';
    const COLOUR_FOUR = 'dark-brown';

    public static $choices = array(
        self::COLOUR_ONE => 'Cream',
        self::COLOUR_TWO => 'Green',
        self::COLOUR_THREE => 'Dark Green',
        self::COLOUR_FOUR => 'Dark Brown',
    );

    /**
     * Get choice label.
     *
     * @param $choice
     *
     * @return string
     */
    public static function getLabel($choice)
    {
        if (array_key_exists($choice, self::$choices)) {
            return self::$choices[$choice];
        }

        return 'Unknown';
    }
}
