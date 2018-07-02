<?php

namespace Vivo\UtilBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

interface AjaxResponseInterface
{
    /**
     * Get response.
     *
     * @param Request $request
     * @param string  $templateName
     * @param string  $templateParams
     *
     * @return JsonResponse|Response
     */
    public function getResponse(Request $request, $templateName, $templateParams);
}
