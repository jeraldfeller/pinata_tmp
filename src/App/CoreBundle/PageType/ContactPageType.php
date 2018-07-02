<?php

namespace App\CoreBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class ContactPageType extends AbstractPageType
{
    /**
     * @var ArrayCollection
     */
    private $blocks;

    /**
     * @var string
     */
    protected $contentClass;

    /**
     * @var string
     */
    protected $assetGroupClass;

    /**
     * Constructor.
     *
     * @param string $contentClass
     * @param string $assetGroupClass
     */
    public function __construct($contentClass, $assetGroupClass)
    {
        $this->contentClass = $contentClass;
        $this->assetGroupClass = $assetGroupClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Contact Page Layout';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'AppCoreBundle:Contact:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        if (null === $this->blocks) {
            $this->blocks = new ArrayCollection(array(
                new ContentBlock($this->contentClass, 'main', 'Main Content'),
                new ContentBlock($this->contentClass, 'thankyou', 'Thank you page content'),
            ));
        }

        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('app_core.contact.index', $this->getRoute()->setMethods(array('POST')));
        $collection->add('app_core.contact.thankyou', $this->getRoute(
            array(
                '_controller' => 'AppCoreBundle:Contact:thankyou',
            ), '/thankyou'
        ));

        return $collection;
    }
}
