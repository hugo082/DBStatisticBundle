<?php

/*
 * This file is part of the DBStatisticBundle package.
 *
 * (c) FOUQUET <https://github.com/hugo082/DBStatisticBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hugo Fouquet <hugo.fouquet@epita.fr>
 */

namespace DB\StatisticBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use DB\StatisticBundle\Core\Graph;

class GraphManager
{
    /**
     * @var array
     */
    private $graphs;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, array $graphs)
    {
        $this->container = $container;
        $this->graphs = array();
        $this->decodeGraphs($graphs);
    }

    /**
     * @param Graph $graph
     * @return Graph
     */
    private function computeGraph(Graph $graph) {
        $service = $this->container->get($graph->getService());
        $methodName = $graph->getMethod();
        $service->$methodName($graph);
        return $graph;
    }

    /**
     * @param string $graphID
     * @return Graph
     */
    public function getGraphWithID(string $graphID) : Graph {
        if (key_exists($graphID, $this->graphs))
            return $this->computeGraph($this->graphs[$graphID]);
        return null;
    }

    private function decodeGraphs(array $graphs) {
        foreach ($graphs as $graph)
            $this->graphs[$graph["id"]] = new Graph($graph["id"], $graph["type"], $graph["title"], $graph["service"], $graph["method"]);
    }
}
