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

class Color
{
    /**
     * @var int
     */
    private $red;

    /**
     * @var int
     */
    private $green;

    /**
     * @var int
     */
    private $blue;

    /**
     * @var float
     */
    private $alpha;

    public function __construct(int $red, int $green, int $blue, float $alpha)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    /**
     * Returns whether or not given color is considered "light"
     * @param int $lighterThan
     * @return bool
     * @internal param bool|string $color
     */
    public function isLight(int $lighterThan = 130){
        return $this->getLuma() > $lighterThan;
    }

    /**
     * Returns whether or not given color is considered "dark"
     * @param int $lighterThan
     * @return bool
     * @internal param bool|string $color
     */
    public function isDark(int $lighterThan = 130){
        return $this->getLuma() <= $lighterThan;
    }

    /**
     * Darkens component
     * @param float $delta
     * @return Color
     */
    public function darken(float $delta = 0.7): Color {
        $this->red *= $delta;
        $this->green *= $delta;
        $this->blue *= $delta;
        return $this;
    }

    /**
     * Lightens component
     * @param float $delta
     * @return Color
     */
    public function lighten(float $delta = 0.7): Color {
        $this->red += (255 - $this->red) * $delta;
        $this->green += (255 - $this->green) * $delta;
        $this->blue += (255 - $this->blue) * $delta;
        return $this;
    }

    /**
     * @param Color $color
     * @param int $delta
     * @param float|null $alpha
     * @return Color
     */
    public static function randomFrom(Color $color, int $delta = 20, float $alpha = null): Color {
        $alpha = ($alpha) ? $alpha : $color->getAlpha();
        return new Color(self::rdmDelta($color->getRed(), $delta), self::rdmDelta($color->getGreen(), $delta),
            self::rdmDelta($color->getBlue(), $delta), $alpha);
    }

    /**
     * @param float|null $alpha
     * @return Color
     */
    public static function random(float $alpha = null): Color {
        $alpha = ($alpha) ? $alpha : mt_rand(0, 255) / 255;
        return new Color(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $alpha);
    }

    /**
     * @param float|null $alpha
     * @return Color
     */
    public static function randomDarken(float $alpha = null, int $luma = 80): Color {
        $color = self::random();
        while (!$color->isDark($luma))
            $color->darken(0.9);
        return $color;
    }

    /**
     * @param Color $color
     * @param float $delta
     * @return Color
     */
    public static function darkenFrom(Color $color, float $delta = 0.7): Color {
        $color = self::dublicate($color);
        return $color->darken($delta);
    }

    /**
     * @param Color $color
     * @param float $delta
     * @return Color
     */
    public static function lightenFrom(Color $color, float $delta = 0.7): Color {
        $color = self::dublicate($color);
        return $color->lighten($delta);
    }

    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * @param int $red
     */
    public function setRed(int $red)
    {
        $this->red = $red;
    }

    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * @param int $green
     */
    public function setGreen(int $green)
    {
        $this->green = $green;
    }

    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * @param int $blue
     */
    public function setBlue(int $blue)
    {
        $this->blue = $blue;
    }

    /**
     * @return float
     */
    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * @param float $alpha
     */
    public function setAlpha(float $alpha)
    {
        $this->alpha = $alpha;
    }

    /**
     * @return Int
     */
    public function getLuma(): Int {
        return ( $this->red * 299 + $this->green * 587 + $this->blue * 114 ) / 1000;
    }

    public function getRGBA(): string {
        return  'rgba(' . round($this->red) . ',' . round($this->blue) . ',' . round($this->green) . ',' . round($this->alpha, 3) . ')';
    }

    /**
     * Random int between bounds
     * @param int $min
     * @param int $max
     * @param int $cutMin
     * @param int $cutMax
     * @return int
     */
    private static function rdmBtw(int $min, int $max, int $cutMin = 0, int $cutMax = 255): int {
        return mt_rand(max($min, $cutMin), min($max, $cutMax));
    }

    /**
     * @param int $value
     * @param int $delta
     * @param int $min
     * @param int $max
     * @return int
     */
    private static function rdmDelta(int $value, int $delta, int $min = 0, int $max = 255): int {
        return self::rdmBtw($value - $delta, $value + $delta, $min, $max);
    }

    /**
     * @param Color $color
     * @return Color
     */
    private static function dublicate(Color $color): Color {
        return new Color($color->red, $color->blue, $color->green, $color->alpha);
    }
}