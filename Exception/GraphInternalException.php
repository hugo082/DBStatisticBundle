<?php

namespace DB\StatisticBundle\Exception;

use Throwable;

class GraphInternalException extends ApiException
{
    private const TITLE = "Graph Internal";
    private const CODE = 500;

    public function __construct(string $message, string $graphID = null, Throwable $previous = null)
    {
        parent::__construct(self::TITLE, $message, self::CODE, $graphID, $previous);
    }
}