<?php

namespace App\CoreBundle\Model\Choice;

class IconChoice
{
    const ICON_GENERAL = 0;
    const ICON_MANGO = 1;
    const ICON_PINEAPPLE = 2;
    const ICON_STRAWBERRY = 3;
    const ICON_MAP = 4;
    const ICON_MAIL = 5;
    const ICON_PHONE = 6;
    const ICON_FACEBOOK = 7;
    const ICON_YOUTUBE = 8;
    const ICON_QUOTES = 9;
    const ICON_PLAY = 10;
    const ICON_MARKER_LINE = 11;
    const ICON_HEARTS = 12;
    const ICON_PIN = 13;
    const ICON_SQUIRT = 14;
    const ICON_SIXTYTHREE = 15;
    const ICON_PIN_ARROW_RIGHT = 16;
    const ICON_PIN_ARROW_LEFT = 17;
    const ICON_TRUCK = 18;
    const ICON_STARS = 19;
    const ICON_DOWNLOAD = 20;
    const ICON_UPLOAD = 21;
    const ICON_BERRYWORLD_STRAWBERRY = 22;
    const ICON_BERRYWORLD_RASPBERRY = 23;
    const ICON_BERRYWORLD_BLACKBERRY = 24;

    public static $icons = array(
        self::ICON_GENERAL => 'General',
        self::ICON_MANGO => 'Mango',
        self::ICON_PINEAPPLE => 'Pineapple',
        self::ICON_STRAWBERRY => 'Strawberry',
        self::ICON_MAP => 'Map of Australia',
        self::ICON_MAIL => 'Mail',
        self::ICON_PHONE => 'Phone',
        self::ICON_FACEBOOK => 'Facebook',
        self::ICON_YOUTUBE => 'Youtube',
        self::ICON_QUOTES => 'Quotes',
        self::ICON_PLAY => 'Play Circle',
        self::ICON_MARKER_LINE => 'Pin Marker with Line',
        self::ICON_HEARTS => 'Hearts',
        self::ICON_PIN => 'Location Pin',
        self::ICON_SQUIRT => 'Squirt',
        self::ICON_SIXTYTHREE => '1964',
        self::ICON_PIN_ARROW_RIGHT => 'Arrow Right',
        self::ICON_PIN_ARROW_LEFT => 'Arrow Left',
        self::ICON_TRUCK => 'Truck',
        self::ICON_STARS => 'Stars',
        self::ICON_DOWNLOAD => 'Download',
        self::ICON_UPLOAD => 'Upload',
        self::ICON_BERRYWORLD_STRAWBERRY => 'BerryWorld Strawberry',
        self::ICON_BERRYWORLD_BLACKBERRY => 'BerryWorld Blackberry',
        self::ICON_BERRYWORLD_RASPBERRY => 'BerryWorld Raspberry',
    );

    public static $classes = array(
        self::ICON_GENERAL => 'pin-tild-circle',
        self::ICON_MANGO => 'pin-mango-circled',
        self::ICON_PINEAPPLE => 'pin-pineapple-circled',
        self::ICON_STRAWBERRY => 'pin-strawberry-circled',
        self::ICON_MAP => 'pin-australia',
        self::ICON_MAIL => 'pin-mail',
        self::ICON_PHONE => 'pin-phone',
        self::ICON_FACEBOOK => 'pin-facebook',
        self::ICON_YOUTUBE => 'pin-youtube',
        self::ICON_QUOTES => 'pin-quotes',
        self::ICON_PLAY => 'pin-play-circled',
        self::ICON_MARKER_LINE => 'pin-marker-line',
        self::ICON_HEARTS => 'pin-hearts',
        self::ICON_PIN => 'pin-pin',
        self::ICON_SQUIRT => 'pin-squirt',
        self::ICON_SIXTYTHREE => 'pin-1963',
        self::ICON_PIN_ARROW_RIGHT => 'pin-arrow-right',
        self::ICON_PIN_ARROW_RIGHT => 'pin-arrow-left',
        self::ICON_TRUCK => 'pin-truck',
        self::ICON_STARS => 'pin-stars',
        self::ICON_DOWNLOAD => 'pin-download',
        self::ICON_UPLOAD => 'pin-upload',
        self::ICON_BERRYWORLD_STRAWBERRY => 'pin-berryworldstrawberry-circled',
        self::ICON_BERRYWORLD_BLACKBERRY => 'pin-berryworldblackberry-circled',
        self::ICON_BERRYWORLD_RASPBERRY => 'pin-berryworldraspberry-circled',
    );

    const POSITION_BEFORE = 1;
    const POSITION_AFTER = 2;

    public static $positions = array(
        self::POSITION_BEFORE => 'Before Text',
        self::POSITION_AFTER => 'After Text',
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
        if (array_key_exists($choice, self::$icons)) {
            return self::$icons[$choice];
        }

        return 'Unknown';
    }

    /**
     * Get position label.
     *
     * @param $choice
     *
     * @return string
     */
    public static function getPositionLabel($choice)
    {
        if (array_key_exists($choice, self::$positions)) {
            return self::$positions[$choice];
        }

        return 'Unknown';
    }

    /**
     * Get choice label.
     *
     * @param $choice
     *
     * @return string
     */
    public static function getClass($choice)
    {
        if (array_key_exists($choice, self::$classes)) {
            return self::$classes[$choice];
        }

        return '';
    }
}
