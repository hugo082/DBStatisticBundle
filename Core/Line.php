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

use Symfony\Component\Config\Definition\Exception\Exception;

class Line
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var null|string
     */
    private $label;

    /**
     * @var array
     */
    private $items;

    /**
     * @var array
     */
    private $options;

    public function __construct(string $id, string $label = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->items = array();
        $this->options = array();
    }

    /**
     * Item value equal last item value + self value
     */
    public function setItemsInheritance() {
        $buf = null;
        /** @var DataItem $item */
        foreach ($this->items as $item) {
            if ($buf)
                $item->incrementValue($buf);
            $buf = $item->getValue();
        }
    }

    /**
     * Saot all items with callable function $cmp
     * @param callable $cmp
     */
    public function sortItems(callable $cmp) {
        uksort($this->items, $cmp);
    }

    /**
     * Sort all items by ASC date (label must be be a date)
     * @param string $format
     */
    public function sortItemsByDate(string $format) {
        $cmp = function ($a, $b) use ($format) {
            $dateA = \DateTime::createFromFormat($format, $a);
            $dateB = \DateTime::createFromFormat($format, $b);
            if (!$dateA instanceof \DateTime and !$dateB instanceof \DateTime)
                throw new Exception("Impossible to convert key '" . $a . "' or '" .$b . "' to DateTime with format '" . $format . "'.");
            return $dateA > $dateB;
        };
        $this->sortItems($cmp);
    }

    /**
     * @param string $label
     * @param float $value
     * @return DataItem
     */
    public function incrementValueForItemWithLabel(string $label, float $value): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->incrementValue($value);
        return $item;
    }

    /**
     * @param string $label
     * @param float $value
     * @return DataItem
     */
    public function setValueForItemWithLabel(string $label, float $value): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->setValue($value);
        return $item;
    }

    public function encode(): array {
        $res = array(
            "label" => $this->label
        );
        /** @var DataItem $item */
        foreach ($this->items as $item)
            $item->encode($res);
        foreach ($this->options as $key => $value)
            $res[$key] = $value;
        return $res;
    }

    public function compareAllItems($action, bool $blockIdentique = true, $parameter = null) {
        /** @var DataItem $parrent */
        foreach ($this->items as $parrent) {
            /** @var DataItem $child */
            foreach ($this->items as $child) {
                if ($blockIdentique && $parrent->getLabel() == $child->getLabel())
                    continue;
                call_user_func($action, $parrent, $child, $parameter);
            }
        }
    }

    /**
     * @param string $label
     * @param float $value
     * @param int $count
     * @return DataItem
     */
    public function incrementMoyValueForItemWithLabel(string $label, float $value, int $count = 1): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->incrementMoyValue($value, $count);
        return $item;
    }

    /**
     * @param string $label
     * @return DataItem|null
     */
    public function getItemWithLabel(string $label, bool $autoCreate = false) {
        if (key_exists($label, $this->items))
            return $this->items[$label];
        if ($autoCreate)
            return $this->items[$label] = new DataItem($label, 0);
        return null;
    }

    /**
     * @param DataItem $item
     * @param string $label
     */
    public function setItemWithLabel(DataItem $item, string $label) {
        $this->items[$label] = $item;
    }

    /**
     * @param DataItem $item
     */
    public function pushItem(DataItem $item) {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return null|string
     */
    public function getLabel()
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

}