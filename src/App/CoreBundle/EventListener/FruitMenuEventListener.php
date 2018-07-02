<?php

namespace App\CoreBundle\EventListener;

use App\CoreBundle\PageType\FruitListPageType;
use App\CoreBundle\Repository\FruitRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Vivo\PageBundle\Menu\Event\ItemEvent;
use Vivo\PageBundle\Menu\Item;
use Vivo\PageBundle\Model\MenuNodeInterface;

class FruitMenuEventListener
{
    /**
     * @var FruitRepository
     */
    protected $fruitRepository;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param FruitRepository $categoryRepository
     * @param Container       $container
     * @param EngineInterface $templating
     */
    public function __construct(FruitRepository $fruitRepository, RouterInterface $router, EngineInterface $templating, Container $container)
    {
        $this->fruitRepository = $fruitRepository;
        $this->container = $container;
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * @param ItemEvent $event
     */
    public function addFruits(ItemEvent $event)
    {
        $parentItem = $event->getItem()->getParent();
        $owner = $event->getItem()->getItemOwner();

        if ($owner instanceof MenuNodeInterface) {
            if ($owner->getPage() && $owner->getPage()->getPageTypeInstance() instanceof FruitListPageType) {
                /** @var \App\CoreBundle\Entity\Fruit[] $fruits */
                $fruits = $this->fruitRepository
                    ->getActiveQueryBuilder()
                    ->setMaxResults(5)
                    ->getQuery()
                    ->getResult();

                if (count($fruits) > 0) {
                    $event->getItem()->setAttribute('class', 'fruit-menu');
                }

                $request = $this->container->get('request');
                $routeName = $request->get('_route');
                $currentSlug = $request->get('slug');

                foreach ($fruits as $fruit) {
                    $fruitSlug = $fruit->getPrimarySlug()->getSlug();
                    $url = $this->router->generate('app_core.fruit.view', array('slug' => $fruitSlug));

                    $item = new Item($fruit);

                    $item->setHref($url)
                        ->setAttribute('class', 'fruit fruit-'.$fruit->getId());

                    /*if($routeName === 'app_core.fruit.view' && $currentSlug === $fruitSlug){
                        $item->setActive(TRUE);
                        $event->getItem()->setActive(FALSE);
                    }*/

                    $item->setRawContent(
                        $this->templating->render('@AppCore/Fruit/_menu_item.html.twig', array(
                            'item' => $item,
                            'fruit' => $fruit,
                        ))
                    );

                    $parentItem->addChild($item);
                }
            }
        }
    }
}
