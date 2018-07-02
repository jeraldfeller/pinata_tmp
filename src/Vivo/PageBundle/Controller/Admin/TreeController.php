<?php

namespace Vivo\PageBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\PageBundle\Exception\InvalidMenuNodeTreeException;
use Vivo\PageBundle\Exception\MissingMenuNodeException;
use Vivo\PageBundle\Model\MenuNodeInterface;

/**
 * Tree controller.
 */
class TreeController extends BaseController
{
    /**
     * Display Tree.
     */
    public function indexAction()
    {
        $pageRepository = $this->get('vivo_page.repository.page');
        $menuNodeRepository = $this->get('vivo_page.repository.menu_node');

        $qb = $menuNodeRepository->createQueryBuilder('e')
            ->addSelect('page')
            ->leftJoin('e.page', 'page')
            ->addOrderBy('e.rank', 'ASC')
            ->addOrderBy('e.id', 'ASC');

        $menuNodes = $menuNodeRepository->findChildren(null, null, $qb);

        return $this->render('@VivoPage/Admin/Tree/index.html.twig', array(
            'menu_nodes' => $menuNodes,
            'orphan_pages' => $pageRepository->findNonNavigationalPages(),
        ));
    }

    /**
     * Update the Tree.
     */
    public function updateAction(Request $request)
    {
        if (!$request->request->get('tree')) {
            $this->addFlash('info', 'admin.flash.tree.none');
        } else {
            $menuNodeRepository = $this->get('vivo_page.repository.menu_node');
            /** @var \Vivo\PageBundle\Model\MenuNodeInterface[] $menuNodes */
            $menuNodes = $menuNodeRepository->findChildren();

            parse_str($request->request->get('tree'), $tree);

            try {
                if (!isset($tree['vivo_page_tree']) || !is_array($tree['vivo_page_tree'])) {
                    throw new InvalidMenuNodeTreeException();
                }

                $em = $this->getDoctrine()->getManager();
                $map = array();
                $rank = 0;

                foreach ($tree['vivo_page_tree'] as $nodeId => $parentId) {
                    if (isset($map[$nodeId])) {
                        $node = $map[$nodeId];
                    } else {
                        $map[$nodeId] = $node = $this->determineEntity($nodeId);
                    }

                    if (isset($map[$parentId])) {
                        $parent = $map[$parentId];
                    } else {
                        $map[$parentId] = $parent = $this->determineEntity($parentId);
                    }

                    if (!$node) {
                        continue;
                    } elseif (!$parent) {
                        if (!$node->getId() || $node->getParent()) {
                            continue;
                        }
                    }

                    if (!$node->getId()) {
                        $em->persist($node);
                    }

                    if ($parent && $parent->getId()) {
                        $em->persist($parent);
                    }

                    if ($parent) {
                        $parent->addChild($node);
                    } else {
                        $node->setParent(null);
                    }

                    $node->setRank(++$rank);
                }

                $em->flush();
                $this->addFlash('success', 'admin.flash.tree.success');
            } catch (InvalidMenuNodeTreeException $e) {
                $this->addFlash('error', 'admin.flash.tree.invalid');
            } catch (MissingMenuNodeException $e) {
                $this->addFlash('error', 'admin.flash.tree.missing');
            }
        }

        return $this->redirectToRoute('vivo_page.admin.tree.index');
    }

    /**
     * Deletes a Menu Node entity.
     */
    public function deleteNodeAction($id, $token)
    {
        /** @var \Vivo\PageBundle\Repository\MenuNodeRepository $menuNodeRepository */
        $menuNodeRepository = $this->get('vivo_page.repository.menu_node');
        $menuNode = $menuNodeRepository->find($id);

        if (!$menuNode || !$menuNode->getParent()) {
            throw $this->createNotFoundException('Unable to find Menu Node entity.');
        }

        if (true !== $this->isCsrfTokenValid($menuNode->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $menuNodeRepository->removeFromTree($menuNode);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'admin.flash.tree.delete_menu_node');
        }

        return $this->redirectToRoute('vivo_page.admin.tree.index');
    }

    /**
     * @param \Vivo\PageBundle\Model\MenuNodeInterface[] $menuNodes
     *
     * @return int
     */
    protected function countAllMenuNodes($menuNodes)
    {
        $count = count($menuNodes);

        foreach ($menuNodes as $menuNode) {
            $count += $this->countAllMenuNodes($menuNode->getChildren());
        }

        return $count;
    }

    /**
     * @param string $value
     *
     * @return MenuNodeInterface
     *
     * @throws \Vivo\PageBundle\Exception\InvalidMenuNodeTreeException
     */
    protected function determineEntity($value)
    {
        if ('null' === strtolower($value) || '0' === (string) $value) {
            return;
        }

        $values = explode('/', $value);
        /** @var \Vivo\PageBundle\Repository\MenuNodeRepositoryInterface|null $menuNodeRepository */
        $menuNodeRepository = $this->get('vivo_page.repository.menu_node');

        if (1 == count($values)) {
            list($nodeId) = $values;

            if ($menuNode = $menuNodeRepository->find($nodeId)) {
                return $menuNode;
            }
        } elseif (3 === count($values)) {
            list($nodeId, $type, $entityId) = $values;

            switch ($type) {
                case 'page':
                    if ($page = $this->get('vivo_page.repository.page')->find($entityId)) {
                        $menuNode = $menuNodeRepository->createMenuNode();
                        $menuNode->setTitle($page->getPageTitle());
                        $menuNode->setPage($page);

                        return $menuNode;
                    }

                    break;
            }
        }

        throw new InvalidMenuNodeTreeException();
    }
}
