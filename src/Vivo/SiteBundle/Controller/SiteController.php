<?php

namespace Vivo\SiteBundle\Controller;

use Doctrine\ORM\Query\QueryException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Site Controller.
 */
class SiteController extends Controller
{
    /**
     * This route will catch the request if Varnish
     * does not exist on the server.
     *
     * @return Response
     */
    public function catchFlushAction()
    {
        return new Response('');
    }

    /**
     * Flush opcache and varnish on the server.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function flushAction(Request $request)
    {
        if (!$request->query->has('timestamp')) {
            throw $this->createNotFoundException();
        }

        $expiry = new \DateTime('-10 minutes', new \DateTimeZone('UTC'));

        if ($request->query->get('timestamp') < $expiry->getTimestamp()) {
            throw new \Exception(sprintf("IP address '%s' attempted to flush the cache with an expired request.", $request->getClientIp()));
        }

        /** @var \Symfony\Component\HttpKernel\UriSigner $uriSigner */
        $uriSigner = $this->get('uri_signer');
        $uriToSign = $request->getSchemeAndHttpHost().$request->getRequestUri();

        if ($request->isSecure()) {
            // Uri was signed as http:// - replace https:// with http://
            $uriToSign = preg_replace('/^https/', 'http', $uriToSign);
        }

        if (true !== $uriSigner->check($uriToSign)) {
            throw new \Exception(sprintf("IP address '%s' attempted to flush the cache with an invalid token.", $request->getClientIp()));
        }

        if (function_exists('opcache_reset')) {
            // Reset opcache
            opcache_reset();
        }

        /** @var \Vivo\SiteBundle\Repository\SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->get('vivo_site.repository.site');
        $sites = $siteRepository->findAllWithDomains();

        $hosts = array();
        foreach ($sites as $site) {
            foreach ($site->getDomains() as $domain) {
                $hosts[] = $domain->getHost();
            }
        }

        $requestUrl = $this->generateUrl('vivo_site.site.catch_flush', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        // Ensure that the request goes to http as Varnish does not listen on https.
        $requestUrl = preg_replace('/^https/', 'http', $requestUrl);

        try {
            $ch = curl_init();

            curl_setopt_array($ch, array(
                CURLOPT_CUSTOMREQUEST => 'PURGE',
                CURLOPT_HTTPHEADER => array(
                    'X-Purge-Host-Pattern: ^(www.)?('.implode('|', $hosts).')$',
                ),
                CURLOPT_URL => $requestUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
            ));

            curl_exec($ch);

            if (curl_errno($ch)) {
                curl_close($ch);

                return new Response('0');
            }

            curl_close($ch);
        } catch (\Exception $e) {
            return new Response('0');
        }

        return new Response('1');
    }

    /**
     * Display flash messages.
     */
    public function flashAction()
    {
        return $this->render('@VivoSite/Site/flash.html.twig');
    }

    /**
     * Display site password form.
     */
    public function sitePasswordAction()
    {
        $response = $this->render('@VivoSite/Site/site_password.html.twig');

        $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);

        return $response;
    }

    /**
     * Display robots txt.
     */
    public function robotsAction(Request $request)
    {
        $response = new Response();
        $response->setSharedMaxAge(7200);
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('@VivoSite/Site/robots.txt.twig', array(
            'fragment_path' => $this->getParameter('fragment.path'),
        ), $response);
    }

    /**
     * Display sitemap index.
     */
    public function sitemapIndexAction(Request $request)
    {
        $response = new Response();
        $response->setSharedMaxAge(7200);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\SiteBundle\Seo\Sitemap\Manager $sitemapManager */
        $sitemapManager = $this->get('vivo_site.seo.sitemap.manager');

        return $this->render('@VivoSite/Site/sitemap_index.xml.twig', array(
            'sitemaps' => $sitemapManager->getSitemaps(),
        ), $response);
    }

    /**
     * Display sitemap.
     */
    public function sitemapAction(Request $request, $name, $page)
    {
        $response = new Response();
        $response->setSharedMaxAge(7200);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\SiteBundle\Seo\Sitemap\Manager $sitemapManager */
        $sitemapManager = $this->get('vivo_site.seo.sitemap.manager');

        try {
            if (false === $urls = $sitemapManager->getUrls($name, $page)) {
                throw new NotFoundHttpException();
            }
        } catch (QueryException $e) {
            /*
             * @TODO: Remove catch once issue is fixed with paginator
             * https://github.com/KnpLabs/KnpPaginatorBundle/issues/125
             */
            throw new NotFoundHttpException();
        }

        return $this->render('@VivoSite/Site/sitemap.xml.twig', array(
            'urls' => $urls,
        ), $response);
    }
}
