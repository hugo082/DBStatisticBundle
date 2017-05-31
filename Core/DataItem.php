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

class DataItem extends DesignableItem
{
    /**
     * @var float
     */
    private $value;

    /**
     * @var null|\DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $_moyCount = 0;

    /**
     * @var int
     */
    private $_moyBuffer = 0;

    public function __construct(string $label, float $value, \DateTime $date = null)
    {
        parent::__construct($label);
        $this->value = $value;
        $this->date = $date;
    }

    public function encode(array &$data) {
        $data["data"][] = $this->value;
        $data["labels"][] = $this->label;
        foreach ($this->options as $key => $value)
            $data[$key][] = $value;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
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
        $this->_moyCount += $count;
        $this->_moyBuffer += $value;
        $this->value = $this->_moyBuffer / $this->_moyCount;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return DataItem
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
}
