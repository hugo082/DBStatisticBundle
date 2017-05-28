<?php

namespace DB\StatisticBundle\Exception;


use Throwable;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $graphID;

    public function __construct(string $title, string $message, int $code, string $graphID = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->title = $title;
        $this->graphID = $graphID;
    }

    public function encode(): array {
        return array(
            "code" => $this->code,
            "title" => $this->title,
            "message" => $this->message,
            "graph_id" => $this->graphID
        );
    }

    /**
     * @return string
     */
    public function getGraphID(): string
    {
        return $this->graphID;
    }

    /**
     * @param string $graphID
     */
    public function setGraphID(string $graphID)
    {
        $this->graphID = $graphID;
    }
}