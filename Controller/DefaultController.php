<?php

namespace DB\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DBStatisticBundle:Default:index.html.twig');
    }
}
