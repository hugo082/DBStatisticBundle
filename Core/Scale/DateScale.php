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

namespace DB\StatisticBundle\Core\Scale;

use DB\StatisticBundle\Exception\GraphInternalException;

class DateScale
{
    public const DEFAULT_FORMATS = array(
        "year" => array("format" => "Y", "up_format" => "+1 year", "down_format" => "-10 years"),
        "year_s" => array("format" => "y", "up_format" => "+1 year", "down_format" => "-10 years"),
        "month" => array("format" => "M y", "up_format" => "+1 month", "down_format" => "-2 years"),
        "month_s" => array("format" => "m", "up_format" => "+1 month", "down_format" => "-1 year"),
        "month_b" => array("format" => "M", "up_format" => "+1 month", "down_format" => "-1 year"),
        "day" => array("format" => "d M", "up_format" => "+30 days", "down_format" => "-3 months"),
        "day_s" => array("format" => "d", "up_format" => "+1 day", "down_format" => "-1 month"),
        "day_b" => array("format" => "D", "up_format" => "+1 day", "down_format" => "-1 month"),
    );

    /**
     * @var array
     */
    private $formats;
    /**
     * @var string
     */
    private $default_action;

    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $up_format;
    /**
     * @var string
     */
    private $down_format;

    public function __construct(string $default_action = "month", array $formats = array())
    {
        $this->formats = array_merge(self::DEFAULT_FORMATS, $formats);
        $this->default_action = $default_action;
    }

    public function computeParameters(array $parameters) {
        $this->format = $this->getWithAction($parameters, "format");
        $this->up_format = $this->getWithAction($parameters, "up_format");
        $this->down_format = $this->getWithAction($parameters, "down_format");
    }

    private function getWithAction(array $parameters, string $key) {
        $action = (key_exists("id", $parameters)) ? $parameters["id"] : $this->default_action;
        if (key_exists($action, $this->formats) && key_exists($key, $this->formats[$action]))
            return $this->formats[$action][$key];
        throw new GraphInternalException("Impossible to load " . $key . " for action " . $action);
    }

    /**
     * @return string
     * @throws GraphInternalException
     */
    public function getFormat(): string
    {
        if (!$this->format)
            throw new GraphInternalException("You must compute with parameters before getting date scale format");
        return $this->format;
    }

    /**
     * @return string
     * @throws GraphInternalException
     */
    public function getUpFormat(): string
    {
        if (!$this->up_format)
            throw new GraphInternalException("You must compute with parameters before getting date scale up format");
        return $this->up_format;
    }

    /**
     * @return string
     * @throws GraphInternalException
     */
    public function getDownFormat(): string
    {
        if (!$this->down_format)
            throw new GraphInternalException("You must compute with parameters before getting date scale down format");
        return $this->down_format;
    }

}