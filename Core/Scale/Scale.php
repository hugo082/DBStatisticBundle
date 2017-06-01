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

use DB\StatisticBundle\Core\Scale\Item\DateScaleItem;
use DB\StatisticBundle\Core\Scale\Item\ScaleItem;
use DB\StatisticBundle\Exception\GraphInternalException;

class Scale
{
    public const SCALE_TYPE_DATE = "date";

    /**
     * @var array
     */
    private $items;

    /**
     * @var ScaleItem
     */
    private $current_item;

    /**
     * @var string
     */
    private $default_action;

    public function __construct(string $default_action, array $items)
    {
        $this->default_action = $default_action;
        $this->items = $this->computeItems($items);
        $this->current_item = null;
    }

    /**
     * @param ScaleItem $item
     */
    public function pushItem(ScaleItem $item)
    {
        $this->items[$item->getActionId()] = $item;
    }

    public function computeParameters(array $parameters) {
        $action = (key_exists("id", $parameters)) ? $parameters["id"] : $this->default_action;
        if (key_exists($action, $this->items))
            return $this->current_item = $this->items[$action];
        throw new GraphInternalException("Impossible to load scale item for action " . $action);
    }

    /**
     * @return ScaleItem
     * @throws GraphInternalException
     */
    public function getCurrentItem(): ScaleItem
    {
        if (!$this->current_item)
            throw new GraphInternalException("You must compute with parameters before getting current scale item");
        return $this->current_item;
    }

    private function computeItems(array $items) {
        $res = array();
        /** @var ScaleItem $scale_item */
        foreach ($items as $scale_item)
            $res[$scale_item->getActionId()] = $scale_item;
        return $res;
    }

    /**
     * Create Scale of type $scale_type
     * @param string $scale_type
     * @return Scale
     * @throws GraphInternalException
     */
    public static function fromType(string $scale_type) {
        if ($scale_type == self::SCALE_TYPE_DATE) {
            return new Scale("month", array(
                new DateScaleItem("year", "Y", "+1 year", "-7 years"),
                new DateScaleItem("month", "M y", "+1 month", "-1 years"),
                new DateScaleItem("week", "D", "+1 day", "-7 days"),
                new DateScaleItem("day", "d M", "+15 days", "-1 months"),
            ));
        }
        throw new GraphInternalException("Scale type " . $scale_type . " not found.");
    }
}