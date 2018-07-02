<?php

namespace App\CoreBundle\Model\Choice;

class FruitChoice
{
    const FRUIT_MANGO = 1;
    const FRUIT_STRAWBERRY = 2;
    const FRUIT_PINEAPPLE = 3;
    const FRUIT_BERRYWORLD_STRAWBERRY = 4;
    const FRUIT_BERRYWORLD_RASPBERRY = 5;
    const FRUIT_BERRYWORLD_BLACKBERRY = 6;

    public static $choices = array(
        self::FRUIT_MANGO => 'Mango',
        self::FRUIT_STRAWBERRY => 'Strawberry',
        self::FRUIT_PINEAPPLE => 'Pineapple',
        self::FRUIT_BERRYWORLD_STRAWBERRY => 'BerryworldStrawberry',
        self::FRUIT_BERRYWORLD_BLACKBERRY => 'BerryworldBlackberry',
        self::FRUIT_BERRYWORLD_RASPBERRY => 'BerryworldRaspberry',
    );

    public static $colours = array(
        self::FRUIT_MANGO => 'mango',
        self::FRUIT_STRAWBERRY => 'strawberry',
        self::FRUIT_PINEAPPLE => 'pineapple',
        self::FRUIT_BERRYWORLD_STRAWBERRY => 'berryworldstrawberry',
        self::FRUIT_BERRYWORLD_BLACKBERRY => 'berryworldblackberry',
        self::FRUIT_BERRYWORLD_RASPBERRY => 'berryworldraspberry',
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

    public static function getColour($choice)
    {
        if (array_key_exists($choice, self::$colours)) {
            return self::$colours[$choice];
        }

        return 'Unknown';
    }
}
