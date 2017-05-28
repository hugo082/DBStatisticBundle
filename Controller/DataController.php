<?php

namespace DB\StatisticBundle\Controller;

use DB\StatisticBundle\Exception\ApiException;
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

        $graph = null;
        try {
            $graph = $graphManager->getGraphWithID($graphID, $request->query->all())->encode();
            $statusResponse = array(
                    'code' => 200,
                    'graph_id' => $graphID
            );
        } catch (ApiException $e) {
            $e->setGraphID($graphID);
            $statusResponse = $e->encode();
        }

        $response->setData(array(
            "status" => $statusResponse,
            "graph" => $graph
        ));

        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    public function multipleAction(Request $request)
    {
        $response = new JsonResponse();
        /** @var GraphManager $graphManager */
        $graphManager = $this->get('db.statistic.manager');

        $statusCode = 200;
        $graphs = array();
        foreach ($request->query->all() as $key => $value) {
            if (substr( $key, 0, 2 ) !== "id")
                continue;
            try {
                $graphs[$key] = array(
                    "status" => array(
                        'code' => 200,
                        'graph_id' => $value
                    ),
                    "graph" => $graphManager->getGraphWithID($value, $request->query->all())->encode()
                );
            } catch (ApiException $e) {
                $statusCode = 201;
                $e->setGraphID($value);
                $graphs[$key] = array(
                    "status" => $e->encode(),
                    "graph" => null
                );
            }
        }


        $dataResponse = array(
            'status' => array(
                'code' => $statusCode
            ),
            'graphs' => $graphs
        );

        $response->setData($dataResponse);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }
}
