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

class ScaleItem
{
    /**
     * @var string
     */
    protected $action_id;

    public function __construct(string $action_id)
    {
        $this->action_id = $action_id;
    }

    /**
     * Compute Data with this scale item
     * @param Data $data
     */
    public function compute(Data $data) {}

    /**
     * @return string
     */
    public function getActionId(): string
    {
        return $this->action_id;
    }
}