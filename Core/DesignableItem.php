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

abstract class DesignableItem
{
    /**
     * @var null|string
     */
    protected $label;

    /**
     * @var array
     */
    protected $options;

    public function __construct(string $label = null)
    {
        $this->label = $label;
        $this->options = array();
    }

    public function designColor(string $optionKey = "backgroundColor", string $color = null) {
        if ($color != null)
            $color = Color::fromString($color);
        else if ($this->label != null)
            $color = Color::fromString($this->label);
        else
            return;
        $this->setOption($optionKey, $color->getRGBA());
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
}