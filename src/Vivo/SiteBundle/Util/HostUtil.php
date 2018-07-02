<?php

namespace Vivo\SiteBundle\Util;

class HostUtil
{
    public static function format($hostname)
    {
        $pathinfo = parse_url($hostname);
        $hostname = isset($pathinfo['host']) ? $pathinfo['host'] : $hostname;

        return strtolower(preg_replace('/^www./i', null, $hostname));
    }
}
