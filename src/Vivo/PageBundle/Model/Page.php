<?php

namespace Vivo\PageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;
use Vivo\SiteBundle\Behaviour\SiteTrait;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Page.
 */
class Page implements PageInterface, AutoFlushCacheInterface
{
    use TimestampableTrait;
    use SiteTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pageType;

    /**
     * @var \Vivo\PageBundle\PageType\Type\PageTypeInterface
     */
    protected $pageTypeInstance;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $pageTitle;

    /**
     * @var \DateTime
     */
    protected $disabledAt;

    /**
     * @var string
     */
    protected $metaTitle;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var string
     */
    protected $socialTitle;

    /**
     * @var string
     */
    protected $socialDescription;

    /**
     * @var bool
     */
    protected $robotsNoIndex = false;

    /**
     * @var bool
     */
    protected $robotsNoFollow = false;

    /**
     * @var bool
     */
    protected $excludedFromSitemap = false;

    /**
     * @var \Vivo\PageBundle\Model\SlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $slugs;

    /**
     * @var SlugInterface
     */
    protected $primarySlug;

    /**
     * @var \Vivo\PageBundle\Model\ContentInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $content;

    /**
     * @var \Vivo\PageBundle\Model\AssetGroupInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $assetGroups;

    /**
     * @var \Vivo\PageBundle\Model\MenuNodeInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $menuNodes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->slugs = new ArrayCollection();
        $this->content = new ArrayCollection();
        $this->assetGroups = new ArrayCollection();
        $this->menuNodes = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageTypeInstance(PageTypeInterface $pageType = null)
    {
        if (null !== $pageType) {
            $this->pageTypeInstance = $pageType;
            $this->pageType = $pageType->getAlias();
            $pageType->setPage($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTypeInstance()
    {
        return $this->pageTypeInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias)
    {
        $this->alias = null === $alias ? null : (string) $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = null === $pageTitle ? null : (string) $pageTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabledAt(\DateTime $disabledAt = null)
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled()
    {
        if ($this->getPageTypeInstance()) {
            if ($this->getPageTypeInstance()->isAlwaysEnabled() || !$this->getPageTypeInstance()->isEnabled()) {
                return false;
            }
        }

        return null === $this->disabledAt ? false : true;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialTitle($socialTitle)
    {
        $this->socialTitle = $socialTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialTitle()
    {
        return $this->socialTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialDescription($socialDescription)
    {
        $this->socialDescription = $socialDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialDescription()
    {
        return $this->socialDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setRobotsNoIndex($robotsNoIndex)
    {
        $this->robotsNoIndex = (bool) $robotsNoIndex;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRobotsNoIndex()
    {
        return $this->robotsNoIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function setRobotsNoFollow($robotsNoFollow)
    {
        $this->robotsNoFollow = (bool) $robotsNoFollow;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRobotsNoFollow()
    {
        return $this->robotsNoFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function setExcludedFromSitemap($excludedFromSitemap)
    {
        $this->excludedFromSitemap = (bool) $excludedFromSitemap;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isExcludedFromSitemap()
    {
        return $this->excludedFromSitemap;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlug(SlugInterface $slug)
    {
        if (!$this->slugs->contains($slug)) {
            if ($slug->getPage()) {
                $slug->getPage()->removeSlug($slug);
            }

            $slug->setPage($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(SlugInterface $slug)
    {
        $this->slugs->removeElement($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugs()
    {
        return $this->slugs;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimarySlug(SlugInterface $primarySlug = null)
    {
        if (null !== $primarySlug) {
            $this->addSlug($primarySlug);
        }

        $this->primarySlug = $primarySlug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimarySlug($fallBack = false)
    {
        if (true === $fallBack && null === $this->primarySlug) {
            $criteria = Criteria::create()
                ->orderBy(array('id' => Criteria::DESC))
                ->setMaxResults(1);

            return $this->getSlugs()->matching($criteria)->first() ?: null;
        }

        return $this->primarySlug;
    }

    /**
     * {@inheritdoc}
     */
    public function addContent(ContentInterface $content)
    {
        if (!$this->content->containsKey($content->getAlias())) {
            $content->setPage($this);
            $this->content->set($content->getAlias(), $content);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeContent(ContentInterface $content)
    {
        if ($this->content->contains($content->getAlias())) {
            $content->setPage(null);
            $this->content->removeElement($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(Collection $contents)
    {
        $this->content = new ArrayCollection();
        foreach ($contents as $content) {
            $this->addContent($content);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentByAlias($alias)
    {
        if (null !== $this->pageTypeInstance && null === $this->pageTypeInstance->getContentBlockByAlias($alias)) {
            // Config does not support this instance - Don't hit DB
            return;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('alias', $alias))
            ->setMaxResults(1);

        return $this->getContent()->matching($criteria)->first() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function addAssetGroup(AssetGroupInterface $assetGroup)
    {
        if (!$this->assetGroups->containsKey($assetGroup->getAlias())) {
            $assetGroup->setPage($this);
            $this->assetGroups->set($assetGroup->getAlias(), $assetGroup);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssetGroup(AssetGroupInterface $assetGroup)
    {
        if ($this->assetGroups->contains($assetGroup)) {
            $assetGroup->setPage(null);
            $this->assetGroups->removeElement($assetGroup);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetGroups(Collection $assetGroups)
    {
        $this->assetGroups = new ArrayCollection();
        foreach ($assetGroups as $assetGroup) {
            $this->addAssetGroup($assetGroup);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroups()
    {
        return $this->assetGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroupByAlias($alias)
    {
        if (null !== $this->pageTypeInstance && null === $this->pageTypeInstance->getAssetGroupBlockByAlias($alias)) {
            // Config does not support this instance - Don't hit DB
            return;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('alias', $alias))
            ->setMaxResults(1);

        return $this->getAssetGroups()->matching($criteria)->first() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMenuNodes(Collection $menuNodes)
    {
        $this->menuNodes = new ArrayCollection();

        foreach ($menuNodes as $menuNode) {
            $this->addMenuNode($menuNode);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addMenuNode(MenuNodeInterface $menuNode)
    {
        if (!$this->menuNodes->contains($menuNode)) {
            $menuNode->setPage($this);
            $this->menuNodes->add($menuNode);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMenuNode(MenuNodeInterface $menuNode)
    {
        if ($this->menuNodes->contains($menuNode)) {
            $menuNode->setPage(null);
            $this->menuNodes->removeElement($menuNode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuNodes($includeDisabled = true)
    {
        if (false === $includeDisabled) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('disabled', false));

            return $this->getMenuNodes()->matching($criteria);
        }

        return $this->menuNodes;
    }
}
