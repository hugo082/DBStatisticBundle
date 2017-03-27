<?php

namespace DB\StatisticBundle\Controller;

use DB\StatisticBundle\Core\DataManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends Controller
{
    public function indexAction($graphId)
    {
        $response = new JsonResponse();
        $graphs = $this->container->getParameter('db_statistic.graphs');
        /** @var $dm DataManager */
        $dm = $this->get('db.statistic.dataManager');


        $dataResponse = array(
            'response' => array(
                'status' => 'Displayed',
                'statusCode' => 200,
            ),
            'graph' => null
        );

        if (key_exists($graphId, $graphs)) {
            $gInfo = $graphs[$graphId];
            $mName = $gInfo['dataMethod'];
            $dm->setGraphInformation($gInfo);
            $dm->$mName();
            $dataResponse['graph'] = $dm->compute();
        } else {
            $dataResponse['response']['status'] = $graphId . ' Not Found';
            $dataResponse['response']['statusCode'] = 404;
        }
        $response->setData($dataResponse);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }
}
