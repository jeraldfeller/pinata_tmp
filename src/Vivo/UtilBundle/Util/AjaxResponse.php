<?php

namespace Vivo\UtilBundle\Util;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class AjaxResponse implements AjaxResponseInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param Request           $request
     * @param RouterInterface   $router
     * @param EngineInterface   $templating
     * @param \Twig_Environment $twig
     */
    public function __construct(RouterInterface $router, EngineInterface $templating, \Twig_Environment $twig)
    {
        $this->router = $router;
        $this->templating = $templating;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(Request $request, $templateName, $templateParams)
    {
        if ($request->isXmlHttpRequest()) {
            $routeName = $request->attributes->get('_route');
            $routeParams = array_merge($request->query->all(), $request->attributes->get('_route_params'));
            $template = $this->twig->loadTemplate($templateName);

            $response = new JsonResponse(array(
                'uri' => $this->router->generate($routeName, $routeParams),
                'payload' => $this->renderBlock($template, 'ajax_response', $templateParams),
            ));

            /*
             * @TODO: Check if Chrome bug is fixed
             *
             * Chrome needs to vary on the response otherwise
             * window.history does not work correctly.
             *
             * https://code.google.com/p/chromium/issues/detail?id=94369
             * http://stackoverflow.com/questions/15394156/back-button-in-browser-not-working-properly-after-using-pushstate-in-chrome
             */
            $vary = $response->getVary();
            $vary[] = 'Accept';

            $response->setVary(array_unique($vary));

            return $response;
        }

        return new Response($this->templating->render($templateName, $templateParams));
    }

    /**
     * Return html for specific block in a template.
     *
     * @param \Twig_Template $template
     * @param string         $block
     * @param array          $context
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function renderBlock(\Twig_Template $template, $block, array $context)
    {
        $context = $this->twig->mergeGlobals($context);
        $level = ob_get_level();
        ob_start();
        try {
            $rendered = $template->renderBlock($block, $context);
            ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }
}
