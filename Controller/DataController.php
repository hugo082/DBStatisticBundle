<?php

namespace DB\StatisticBundle\Controller;

use DB\StatisticBundle\Manager\GraphManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends Controller
{
    public function indexAction($graphID)
    {
        $response = new JsonResponse();
        /** @var GraphManager $graphManager */
        $graphManager = $this->get('db.statistic.manager');

        $graph = $graphManager->getGraphWithID($graphID);


        $dataResponse = array(
            'response' => array(
                'status' => 'Displayed',
                'statusCode' => 200,
            ),
            'graph' => $graph->encode()
        );



        $response->setData($dataResponse);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }
}
