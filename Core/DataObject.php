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

use DB\StatisticBundle\Core\DataItem as Item;

class DataObject
{
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }


    public function getAllLabels(){
        $str = "";
        foreach ($this->data as $d) {
            $str .= $d->getLabels();
        }
        return $str;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    public function insertData(Item $data) {
        $this->data[] = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

}
