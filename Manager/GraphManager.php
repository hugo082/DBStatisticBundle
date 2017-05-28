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
use DB\StatisticBundle\Exception\GraphNotFoundException;

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
    private function computeGraph(Graph $graph, array $parameters) {
        $service = $this->container->get($graph->getService());
        $methodName = $graph->getMethod();
        $service->$methodName($graph, $parameters);
        return $graph;
    }

    /**
     * @param string $graphID
     * @param array $parameters
     * @return Graph|null
     * @throws GraphNotFoundException
     */
    public function getGraphWithID(string $graphID, array $parameters) : ?Graph {
        if (key_exists($graphID, $this->graphs))
            return $this->computeGraph($this->graphs[$graphID], $parameters);
        throw new GraphNotFoundException();
    }

    private function decodeGraphs(array $graphs) {
        foreach ($graphs as $graph) {
            $g = Graph::fromArray($graph);
            if ($g != null)
                $this->graphs[$graph["id"]] = $g;
        }
    }
}
