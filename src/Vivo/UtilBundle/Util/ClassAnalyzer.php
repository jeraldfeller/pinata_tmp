<?php

namespace Vivo\UtilBundle\Util;

class ClassAnalyzer implements ClassAnalyzerInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasTrait(\ReflectionClass $class, $traitName, $isRecursive = true)
    {
        if (in_array($traitName, $class->getTraitNames())) {
            return true;
        }

        if ((false === $isRecursive) || (false === $parentClass = $class->getParentClass())) {
            return false;
        }

        return $this->hasTrait($parentClass, $traitName, $isRecursive);
    }
}
