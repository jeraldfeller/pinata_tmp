<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\PageBundle\Routing\PageReference;
use Vivo\SlugBundle\Model\SlugInterface;

class RouteValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @param RouterInterface         $router
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(RouterInterface $router, PageRepositoryInterface $pageRepository)
    {
        $this->router = $router;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param PageInterface    $value
     * @param Constraint|Route $constraint
     *
     * @return bool|void
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }

        if (!$value instanceof PageInterface) {
            throw new UnexpectedTypeException($value, 'Vivo\\PageBundle\\Entity\\PageInterface');
        }

        if (!$primarySlug = $value->getPrimarySlug()) {
            return true;
        }

        $currentPage = $this->pageRepository->findOneBy(array(
            'site' => $value->getSite(),
            'primarySlug' => $primarySlug,
        ));

        $isTaken = null;

        if ($currentPage) {
            if ($currentPage === $value) {
                // Same slug - allow it
                return true;
            } else {
                // Another page is using this slug
                $isTaken = true;
            }
        }

        $routePath = '/'.ltrim($primarySlug->getSlug(), '/');

        if (null === $isTaken) {
            // Only check if another page isn't using the uri.

            $isTaken = true;
            $route = null;

            try {
                // We need to buiold our own request so the request method is
                // is GET instead of a POST from the posted data
                $request = new Request(
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(
                        'REQUEST_URI' => $routePath,
                    )
                );

                if ($this->router instanceof ChainRouter) {
                    $route = $this->router->matchRequest($request);
                } elseif ($this->router instanceof Router) {
                    $route = $this->router->matchRequest($request);
                } else {
                    $route = $this->router->match($routePath);
                }
            } catch (ResourceNotFoundException $e) {
                $isTaken = false;
            } catch (\Exception $e) {
            }

            if ($route) {
                if (isset($route['cmsPage'])) {
                    $cmsPageRef = $route['cmsPage'];

                    if ($cmsPageRef instanceof PageReference) {
                        // If the slug object is modified on the same page, the RouteProvider
                        // may provide a result. If it does, let's just checked the id's.

                        if ($cmsPageRef->getId() === $value->getId()) {
                            return true;
                        }
                    }
                }

                if (0 === strpos($route['_route'], SlugInterface::TEMP_SLUG_PREFIX) || (isset($route['vivo_page_fallback']) && $route['vivo_page_fallback'])) {
                    // This is a temporary route - Overwrite
                    $isTaken = false;
                }
            }
        }

        if (true === $isTaken) {
            $this->context->addViolationAt('primarySlug', $constraint->message, array('{{ value }}' => $routePath));

            return false;
        }

        return true;
    }
}
