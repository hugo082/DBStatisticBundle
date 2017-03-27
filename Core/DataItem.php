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
    private $data;
    private $computed;

    private $values = "";
    private $labels = "";
    private $backgroundColors = "";

    public function __construct(array $data) {
        $this->data = $data;
        $this->computed = false;
    }



    public function getValues() {
        $this->computeData();
        return $this->values;
    }

    public function getLabels() {
        $this->computeData();
        return $this->labels;
    }

    public function getBackgroundColors() {
        $this->computeData();
        return $this->backgroundColors;
    }

    private function computeData() {
        if ($this->computed)
            return;
        foreach ($this->data as $item) {
            $this->values .= $item["value"] . ",";
            $this->backgroundColors .= "'" . $item["backgroundColor"] . "',";
            $this->labels .= "'" . $item["label"] . "',";
        }
        $this->computed = true;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data) {
        $this->data = $data;
        $this->computed = false;
        return $this;
    }

}
