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

namespace DB\StatisticBundle\Core;

class Graph
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Data
     */
    private $data;

    /**
     * @var string
     */

    private $service;

    /**
     * @var string
     */
    private $method;


    public function __construct(string $id, string $type, string $title, string $service, string $method)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->service = $service;
        $this->method = $method;
        $this->data = new Data();
    }

    /**
     * @return array
     */
    public function encode() : array {
        return array(
            "informations" => $this->encodeGraphInformations(),
            "data" => $this->data->encode()
        );
    }

    private function encodeGraphInformations() : array {
        return array(
            "id" => $this->id,
            "type" => $this->type,
            "title" => $this->title
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param Data $data
     */
    public function setData(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService(string $service)
    {
        $this->service = $service;
    }
}