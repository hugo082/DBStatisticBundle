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

use DB\StatisticBundle\Core\Scale\Scale;
use DB\StatisticBundle\Exception\GraphInternalException;

class Data
{
    /**
     * @var array
     */
    private $lines;

    /**
     * @var Scale
     */
    private $scale;

    public function __construct()
    {
        $this->lines = array();
        $this->scale = null;
    }

    public function encode(): array {
        $lines = array();
        $labels = array();
        /** @var Line $line */
        foreach ($this->lines as $line) {
            $encode = $line->encode();
            $labels = $encode["labels"];
            $lines[] = $encode;
        }
        return array(
            "datasets" => $lines,
            "labels" => $labels
        );
    }

    public function computeItemWithScale() {
        if ($this->scale == null)
            throw new GraphInternalException("You must set a DateScale before compute them");
        $scale_item = $this->scale->getCurrentItem();
        $scale_item->compute($this);
    }

    public function sortItems(callable $cmp) {
        /** @var Line $line */
        foreach ($this->lines as $line)
            $line->sortItems($cmp);
    }

    public function sortItemsByDate(string $format) {
        /** @var Line $line */
        foreach ($this->lines as $line)
            $line->sortItemsByDate($format);
    }

    public function defaultLabelForDate(string $format, string $increments, $value = 0) {
        /** @var Line $line */
        foreach ($this->lines as $line)
            $line->defaultLabelForDate($format, $increments, $value);
    }

    /**
     * @param string $id
     * @param string|null $label
     * @return Line
     * @throws \Exception
     */
    public function createLine(string $id, string $label = null) {
        if (key_exists($id, $this->lines))
            throw new GraphInternalException("Line with id '" . $id . "' already exist.");
        $scale_item = ($this->scale == null) ? null : $this->scale->getCurrentItem();
        $this->lines[$id] = new Line($id, $label, $scale_item);
        return $this->lines[$id];
    }

    /**
     * @param string $label
     * @param float $value
     * @param string|null $lineID
     * @throws \Exception
     */
    public function incrementValueForItemWithLabel(string $label, float $value, string $lineID = null, bool $designColor = false) {
        $line = $this->getLineWithID($lineID, true);
        $line->incrementValueForItemWithLabel($label, $value, $designColor);
    }

    /**
     * @param string|null $id
     * @param bool $throw
     * @return Line|null
     * @throws GraphInternalException
     */
    public function getLineWithID(string $id = null, bool $throw = false): ?Line {
        if ($id != null) {
            if (key_exists($id, $this->lines))
                return $this->lines[$id];
        } else {
            if (!empty($this->lines))
                return array_values($this->lines)[0];
            $id = "null";
        }
        if ($throw)
            throw new GraphInternalException("Impossible to get the line with id '" . $id . "'. Line not found.");
        return null;
    }

    /**
     * @return Scale
     */
    public function getScale(): Scale
    {
        return $this->scale;
    }

    /**
     * @param Scale $scale
     */
    public function setScale(Scale $scale)
    {
        $this->scale = $scale;
    }
}