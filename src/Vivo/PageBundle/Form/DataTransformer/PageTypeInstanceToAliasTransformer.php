<?php

namespace Vivo\PageBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;

class PageTypeInstanceToAliasTransformer implements DataTransformerInterface
{
    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @param PageTypeManagerInterface $pageTypeManager
     */
    public function __construct(PageTypeManagerInterface $pageTypeManager)
    {
        $this->pageTypeManager = $pageTypeManager;
    }

    /**
     * Transforms an object (PageTypeInterface) to a string (alias).
     *
     * @param \Vivo\PageBundle\PageType\Type\PageTypeInterface|null $pageType
     *
     * @return string
     */
    public function transform($pageType)
    {
        if (null === $pageType) {
            return '';
        }

        return $pageType->getAlias();
    }

    /**
     * Transforms a string (alias) to an object (PageTypeInterface).
     *
     * @param string $alias
     *
     * @return \Vivo\PageBundle\PageType\Type\PageTypeInterface|null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($alias)
    {
        if (!$alias) {
            return;
        }

        $pageTypeInstance = $this->pageTypeManager
            ->getPageTypeInstanceByAlias($alias);

        if (null === $pageTypeInstance) {
            throw new TransformationFailedException(sprintf(
                'A PageType with alias "%s" does not exist!',
                $alias
            ));
        }

        return $pageTypeInstance;
    }
}
