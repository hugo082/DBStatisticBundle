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

namespace DB\StatisticBundle\Core\Action;

class SelectChoice
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var boolean
     */
    private $default;

    public function __construct(string $id, string $title, bool $default)
    {
        $this->id = $id;
        $this->title = $title;
        $this->default = $default;
    }

    public function encode(): array {
        return array(
            "id" => $this->id,
            "title" => $this->title,
            "default" => $this->default
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    public static function decode(array $data) {
        return new SelectChoice($data["id"], $data["title"], $data["default"]);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function arrayToSelectChoices(array $data) {
        $res = array();
        foreach ($data as $action)
            $res[] = self::decode($action);
        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function encodeChoices(array $data) {
        $res = array();
        /** @var SelectChoice $action */
        foreach ($data as $choice)
            $res[] = $choice->encode();
        return $res;
    }
}