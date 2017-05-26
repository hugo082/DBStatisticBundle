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
use Symfony\Component\Validator\Constraints\DateTime;

class Line extends DesignableItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $items;

    public function __construct(string $id, string $label = null)
    {
        parent::__construct($label);
        $this->id = $id;
        $this->items = array();
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
     * @param string $format
     * @param string $increments
     * @param int $value
     * @throws \Exception
     */
    public function defaultLabelForDate(string $format, string $increments, $value = 0) {
        /** @var \DateTime $firstDate */
        $firstDate = null;
        /** @var \DateTime $lastDate */
        $lastDate = null;
        /** @var DataItem $item */
        foreach ($this->items as $item) {
            if ($item->getDate() == null)
                throw new \Exception("All items must have a date");
            if ($firstDate == null || $firstDate > $item->getDate())
                $firstDate = $item->getDate();
            if ($lastDate == null || $lastDate < $item->getDate())
                $lastDate = $item->getDate();
        }
        $date = clone $firstDate;
        $date->modify($increments);
        if ($date <= $firstDate)
            throw new \Exception($increments . " must increment date");
        $maxSize = 100;
        while ($date < $lastDate) {
            if ($maxSize == 0)
                throw new \Exception("To much iteration");
            $maxSize--;
            $date = $date->modify($increments);
            $this->getItemWithLabel($date->format($format), true, clone $date, $value);
        }
    }

    /**
     * @param string $label
     * @param float $value
     * @param bool $designColor
     * @return DataItem
     */
    public function incrementValueForItemWithLabel(string $label, float $value, bool $designColor = false): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->incrementValue($value);
        if ($designColor)
            $item->designColor();
        return $item;
    }

    /**
     * @param string $label
     * @param float $value
     * @param bool $designColor
     * @return DataItem
     */
    public function setValueForItemWithLabel(string $label, float $value, bool $designColor = false): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->setValue($value);
        if ($designColor)
            $item->designColor();
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
    public function incrementMoyValueForItemWithLabel(string $label, float $value, int $count = 1, bool $designColor = false): DataItem {
        $item = $this->getItemWithLabel($label, true);
        $item->incrementMoyValue($value, $count);
        if ($designColor)
            $item->designColor();
        return $item;
    }

    /**
     * @param string $label
     * @param bool $autoCreate
     * @param \DateTime|null $date
     * @param int $defaultValue
     * @return DataItem|mixed|null
     */
    public function getItemWithLabel(string $label, bool $autoCreate = false, \DateTime $date = null, $defaultValue = 0) {
        if (key_exists($label, $this->items))
            return $this->items[$label];
        if ($autoCreate)
            return $this->items[$label] = new DataItem($label, $defaultValue, $date);
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
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
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

}