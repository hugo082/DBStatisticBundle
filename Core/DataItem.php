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

class DataItem
{
    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $moyCount = 0;

    /**
     * @var int
     */
    private $moyBuffer = 0;

    /**
     * @var array
     */
    private $options;

    public function __construct(string $label, float $value)
    {
        $this->label = $label;
        $this->value = $value;
        $this->options = array();
    }

    public function encode(array &$data) {
        $data["data"][] = $this->value;
        $data["labels"][] = $this->label;
        foreach ($this->options as $key => $value)
            $data[$key][] = $value;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setOption(string $key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key)
    {
        if (key_exists($key, $this->options))
            return $this->options[$key];
        return null;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
    }

    /**
     * @param float $value
     */
    public function incrementValue(float $value) {
        $this->value += $value;
    }

    /**
     * @param float $value
     * @param int $count
     */
    public function incrementMoyValue(float $value, int $count = 1) {
        $this->moyCount += $count;
        $this->moyBuffer += $value;
        $this->value = $this->moyBuffer / $this->moyCount;
    }
}
