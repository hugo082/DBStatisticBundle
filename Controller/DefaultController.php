<?php

namespace DB\StatisticBundle\Controller;

use DB\StatisticBundle\Core\DataManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $graphs = $this->container->getParameter('db_statistic.graphs');
        return $this->render('DBStatisticBundle:Default:index.html.twig', array(
            'graphs' => $graphs
        ));
    }
}
