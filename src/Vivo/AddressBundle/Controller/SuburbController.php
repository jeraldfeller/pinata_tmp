<?php

namespace Vivo\AddressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SuburbController extends Controller
{
    public function searchAction(Request $request)
    {
        $response = new JsonResponse();
        $response->setSharedMaxAge(900);
        if ($response->isNotModified($request)) {
            return $response;
        }

        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        // Note: $country_code is available for future functionality
        $countryCode = $request->query->get('country_code');
        $postcode = $request->query->get('postcode', null);

        /** @var \Vivo\AddressBundle\Repository\SuburbRepositoryInterface $suburbRepository */
        $suburbRepository = $this->get('vivo_address.repository.suburb');

        $results = $suburbRepository->findAllByPostcode($countryCode, $postcode);
        $suburbs = array();

        foreach ($results as $suburb) {
            $suburbs[] = array(
                'name' => $suburb->getName(),
                'postcode' => $suburb->getPostcode(),
                'state' => $suburb->getState(),
            );
        }

        $response->setData(array(
            'results' => $suburbs,
        ));

        return $response;
    }
}
