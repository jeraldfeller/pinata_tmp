<?php

namespace Vivo\UtilBundle\Doctrine;

class InheritanceManager
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->classes = array();
    }

    /**
     * @param $parentClass
     * @param $childClass
     * @param $alias
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addClass($parentClass, $childClass, $alias)
    {
        $childClassReflection = new \ReflectionClass($childClass);
        $childClass = $childClassReflection->getName();
        $parentClassReflection = new \ReflectionClass($parentClass);
        $parentClass = $parentClassReflection->getName();
        $alias = trim($alias);

        if (!isset($this->classes[$parentClass])) {
            $this->classes[$parentClass] = array();
        }

        if (in_array($childClass, $this->classes[$parentClass])) {
            throw new \Exception(sprintf("Class '%s' already exists.", $childClass));
        } elseif (array_key_exists($alias, $this->classes[$parentClass])) {
            throw new \Exception(sprintf("Alias '%s' already exists.", $alias));
        }

        $this->classes[$parentClass][$alias] = $childClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
