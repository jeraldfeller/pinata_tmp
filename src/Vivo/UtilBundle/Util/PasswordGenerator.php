<?php

namespace Vivo\UtilBundle\Util;

class PasswordGenerator
{
    /**
     * Generate a random password.
     *
     * @param int $letter_count
     * @param int $number_count
     * @param int $symbol_count
     *
     * @return string
     */
    public static function generatePassword($letter_count = 4, $number_count = 2, $symbol_count = 2)
    {
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()-+?';
        $output = '';

        for ($i = 1; $i <= $letter_count; ++$i) {
            $letter = substr(str_shuffle($letters), 0, 1);
            $output .= 0 == rand(0, 1) ? strtoupper($letter) : $letter;
        }

        for ($i = 1; $i <= $number_count; ++$i) {
            $output .= substr(str_shuffle($numbers), 0, 1);
        }

        for ($i = 1; $i <= $symbol_count; ++$i) {
            $output .= substr(str_shuffle($symbols), 0, 1);
        }

        $shuffle_count = rand(10, 100);
        for ($i = 0; $i <= $shuffle_count; ++$i) {
            $output = str_shuffle($output);
        }

        return $output;
    }
}
