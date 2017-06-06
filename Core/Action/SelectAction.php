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

class SelectAction extends Action
{
    /**
     * @var array
     */
    private $choices;
    /**
     * @var SelectChoice
     */
    private $default_choice = null;

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

    public function computeParameters(array $parameters) {
        parent::computeParameters($parameters);
        if (!$this->isValidValue($this->value)) {
            $this->value = null;
            throw new GraphInternalException("Impossible to set value " . $parameters["value"] . " for action " . $this->id);
        }
    }

    /**
     * @param bool $force
     * @return SelectChoice
     * @throws GraphInternalException
     */
    public function getDefaultChoice(bool $force = false): SelectChoice {
        if ($this->default_choice == null || $force) {
            $this->computeDefaultChoice();
            if ($this->default_choice == null)
                throw new GraphInternalException("Impossible to load default choice for action " . $this->id);
        }
        return $this->default_choice;
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        if ($this->value == null)
            return $this->getDefaultChoice()->getId();
        return $this->value;
    }

    private function isValidValue($value): bool {
        /** @var SelectChoice $choice */
        foreach ($this->choices as $choice) {
            if ($choice->getId() == $value)
                return true;
        }
        return false;
    }

    private function computeDefaultChoice() {
        /** @var SelectChoice $choice */
        foreach ($this->choices as $choice) {
            if ($choice->isDefault()) {
                $this->default_choice = $choice;
                return;
            }
        }
    }

    public static function decode(array $data) {
        $value = (key_exists("value", $data)) ? $data["value"] : null;
        return new SelectAction($data["id"], $data["choices"], $value);
    }
}