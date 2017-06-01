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

namespace DB\StatisticBundle\Core\Scale\Item;

use DB\StatisticBundle\Core\Data;
use DB\StatisticBundle\Exception\GraphInternalException;

class DateScaleItem extends ScaleItem
{
    /**
     * @var string
     */
    private $label_format;
    /**
     * @var string|null
     */
    private $label_increment;
    /**
     * @var string|null
     */
    private $decrement_min_value;

    public function __construct(string $action_id, string $label_format, string $label_increment = null, string $decrement_min_value = null)
    {
        parent::__construct($action_id);
        $this->label_format = $label_format;
        $this->label_increment = $label_increment;
        $this->decrement_min_value = $decrement_min_value;
    }

    /**
     * Compute Data with this scale item
     * @param Data $data
     */
    public function compute(Data $data) {
        if ($this->label_increment)
            $data->defaultLabelForDate($this->label_format, $this->label_increment);
        $data->sortItemsByDate($this->label_format);
    }

    /**
     * Validate if date is displayable
     * @param \DateTime $date
     * @return bool
     */
    public function validate(\DateTime $date) {
        if ($this->decrement_min_value == null)
            return true;
        $buff = (new \DateTime())->modify($this->decrement_min_value);
        return $date > $buff;
    }

    /**
     * @return string
     */
    public function getLabelFormat(): string
    {
        return $this->label_format;
    }

    /**
     * @return string
     */
    public function getDecrementMinValue(): string
    {
        return $this->decrement_min_value;
    }

    /**
     * @return string
     */
    public function getLabelIncrement(): string
    {
        return $this->label_increment;
    }
}