<?php

namespace DB\StatisticBundle\Exception;

use Throwable;

class GraphNotFoundException extends ApiException
{
    private const TITLE = "Graph Not Found";
    private const CODE = 404;

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::TITLE, "Impossible to find graph", self::CODE, null, $previous);
    }

    /**
     * @param string $graphID
     */
    public function setGraphID(string $graphID)
    {
        $this->message = "Impossible to find graph with id : " . $graphID;
        $this->graphID = $graphID;
    }
}