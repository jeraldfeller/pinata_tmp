<?php

namespace App\CoreBundle\Model\Choice;

class ColorClassChoice
{
    const FRUIT_MANGO = 'mango';
    const FRUIT_STRAWBERRY = 'strawberry';
    const FRUIT_PINEAPPLE = 'pineapple';
    const FRUIT_BERRYWORLD_STRAWBERRY = 'berryworldstrawberry';
    const FRUIT_BERRYWORLD_BLACKBERRY = 'berryworldblackberry';
    const FRUIT_BERRYWORLD_RASPBERRY = 'berryworldraspberry';

    public static $choices = array(
        self::FRUIT_MANGO => 'Mango',
        self::FRUIT_STRAWBERRY => 'Strawberry',
        self::FRUIT_PINEAPPLE => 'Pineapple',
        self::FRUIT_BERRYWORLD_STRAWBERRY => 'BerryWorldStrawberry',
        self::FRUIT_BERRYWORLD_BLACKBERRY => 'BerryWorldBlackberry',
        self::FRUIT_BERRYWORLD_RASPBERRY => 'BerryWorldRaspberry',
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
