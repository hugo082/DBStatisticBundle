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

use DB\StatisticBundle\Exception\GraphInternalException;

abstract class Action
{
    public const TYPE_BUTTON = 'button';
    public const TYPE_SELECT = 'select';

    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;

    protected $value;

    public function __construct(string $id, string $type, $value = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->value = $value;
    }

    public function encode(): array {
        return array(
            "id" => $this->id,
            "type" => $this->type,
            "value" => $this->value
        );
    }

    public function computeParameters(array $parameters) {
        if (!key_exists("value", $parameters))
            throw new GraphInternalException("Impossible to compute parameters");
        $this->value = $parameters["value"];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array $data
     * @return ButtonAction|SelectAction
     * @throws GraphInternalException
     */
    public static function decode(array $data) {
        if (!key_exists("type", $data))
            throw new GraphInternalException("Action type not found.");
        switch ($data["type"]) {
            case self::TYPE_BUTTON:
                return ButtonAction::decode($data);
            case self::TYPE_SELECT:
                return SelectAction::decode($data);
        }
        throw new GraphInternalException("Action with type " . $data["type"] . " not supported.");
    }

    /**
     * @param array $data
     * @return array
     */
    public static function arrayToActions(array $data) {
        $res = array();
        foreach ($data as $action) {
            $action = self::decode($action);
            $res[$action->id] = $action;
        }
        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function encodeActions(array $data) {
        $res = array();
        /** @var Action $action */
        foreach ($data as $action)
            $res[] = $action->encode();
        return $res;
    }
}