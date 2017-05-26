<?php

namespace DB\StatisticBundle\Controller;

use DB\StatisticBundle\Manager\GraphManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends Controller
{
    public function indexAction(Request $request, $graphID)
    {
        $response = new JsonResponse();
        /** @var GraphManager $graphManager */
        $graphManager = $this->get('db.statistic.manager');

        $graph = $graphManager->getGraphWithID($graphID, $request->query->all());

        $code = $graph != null ? $graph->encode() : null;

        $dataResponse = array(
            'response' => array(
                'status' => 'Displayed',
                'statusCode' => 200,
            ),
            'graph' => $code
        );



        $response->setData($dataResponse);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }
}
