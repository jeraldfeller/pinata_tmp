<?php

namespace Vivo\UtilBundle\Util;

interface ClassAnalyzerInterface
{
    /**
     * Return true if the given object use the given trait, false if not.
     *
     * @param \ReflectionClass $class
     * @param string           $traitName
     * @param bool             $isRecursive
     */
    public function hasTrait(\ReflectionClass $class, $traitName, $isRecursive = true);
}
