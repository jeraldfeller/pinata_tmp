<?php

namespace Vivo\UtilBundle\Twig;

class TimeAgoExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('vivo_util_time_ago', array($this, 'timeAgo')),
        );
    }

    /**
     * Convert \DateTime object to "Time ago" string.
     *
     * @param \DateTime $datetime
     * @param string    $dateFormat
     * @param string    $prefix
     *
     * @return string
     */
    public function timeAgo(\DateTime $datetime, $dateFormat = 'g:ia \o\n j M y', $prefix = 'about')
    {
        $now = new \DateTime('now');

        $totalSeconds = $now->getTimestamp() - $datetime->getTimestamp();

        if (null !== $dateFormat && ($totalSeconds / 86400) > 7) {
            // If datetime is > 7 days use the dateFormat
            return $datetime->format($dateFormat);
        }

        $units = array(
            31536000 => 'year',
            2592000 => 'month',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
        );

        foreach ($units as $seconds => $unit) {
            if ($seconds <= $totalSeconds) {
                $value = (int) floor($totalSeconds / $seconds);

                return ($prefix ? $prefix.' ' : '').$value.' '.$unit.(1 === $value ? '' : 's').' ago';
            }
        }

        return 'less than a minute ago';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_util_time_ago';
    }
}
