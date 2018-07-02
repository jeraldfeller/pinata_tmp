<?php

namespace Vivo\PageBundle\EventListener;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\PageBundle\Routing\PageReference;

class PageReferenceRouteListener
{
    /**
     * @var PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var string
     */
    protected $pageModelClass;

    /**
     * @var string
     */
    protected $proxyCacheDir;

    /**
     * PageReferenceRouteListener constructor.
     *
     * @param PageTypeManagerInterface $pageTypeManager
     * @param PageRepositoryInterface  $pageRepository
     * @param string                   $pageModelClass
     * @param string                   $proxyCacheDir
     */
    public function __construct(
        PageTypeManagerInterface $pageTypeManager,
        PageRepositoryInterface $pageRepository,
        $pageModelClass,
        $proxyCacheDir
    ) {
        $this->pageTypeManager = $pageTypeManager;
        $this->pageRepository = $pageRepository;
        $this->pageModelClass = $pageModelClass;
        $this->proxyCacheDir = $proxyCacheDir;
    }

    /**
     * Intercept PageReference and find the Page.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        foreach ($request->attributes as $attrName => $attribute) {
            if ($attribute instanceof PageReference) {
                if (!$attribute->getId() || !$attribute->getPageTypeAlias()) {
                    continue;
                }

                if (!is_dir($this->proxyCacheDir)) {
                    @mkdir($this->proxyCacheDir, 0755, true);
                }

                $config = new \ProxyManager\Configuration();
                $config->setProxiesTargetDir($this->proxyCacheDir);
                spl_autoload_register($config->getProxyAutoloader());

                $factory = new LazyLoadingValueHolderFactory($config);

                $pageRepository = $this->pageRepository;
                $pageTypeManager = $this->pageTypeManager;

                $proxy = $factory->createProxy(
                    $this->pageModelClass,
                    function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($attribute, $pageRepository, $pageTypeManager) {
                        $initializer = null; // turning off further lazy initialization

                        $qb = $pageRepository->getPageWithPrimarySlugQueryBuilder()
                            ->addSelect('menu_node')
                            ->leftJoin('page.menuNodes', 'menu_node')
                            ->andWhere('page.id = :id')
                            ->setParameter('id', $attribute->getId());

                        if ($pageType = $pageTypeManager->getPageTypeInstanceByAlias($attribute->getPageTypeAlias())) {
                            if (count($pageType->getContentBlocks()) > 0) {
                                $qb->addSelect('content')
                                    ->leftJoin('page.content', 'content');
                            }

                            if (count($pageType->getAssetGroupBlocks()) > 0) {
                                $qb->addSelect('asset_group, asset, file')
                                    ->leftJoin('page.assetGroups', 'asset_group')
                                    ->leftJoin('asset_group.assets', 'asset')
                                    ->leftJoin('asset.file', 'file');
                            }
                        }

                        $page = $qb->getQuery()
                            ->useQueryCache(true)
                            ->useResultCache(true, 2)
                            ->getOneOrNullResult();

                        if (!$page) {
                            throw new NotFoundHttpException();
                        }

                        $wrappedObject = $page;

                        return true; // confirm that initialization occurred correctly
                    }
                );

                $request->attributes->set($attrName, $proxy);
            }
        }
    }
}
