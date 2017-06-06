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

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function encode(): array {
        return array(
            "id" => $this->id,
            "title" => $this->title
        );
    }

    public static function decode(array $data) {
        return new SelectChoice($data["id"], $data["title"]);
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