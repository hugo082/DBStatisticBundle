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

class SelectAction extends Action
{
    /**
     * @var array
     */
    private $choices;

    public function __construct(string $id, array $choices, $value = null)
    {
        parent::__construct($id, self::TYPE_SELECT, $value);
        $this->choices = SelectChoice::arrayToSelectChoices($choices);
    }

    public function encode(): array
    {
        return array_merge(parent::encode(), array(
            "choices" => SelectChoice::encodeChoices($this->choices)
        ));
    }

    public static function decode(array $data) {
        $value = (key_exists("value", $data)) ? $data["value"] : null;
        return new SelectAction($data["id"], $data["choices"], $value);
    }

    public static function isValid(array $data) {
        return key_exists("id", $data) && key_exists("value", $data);
    }
}