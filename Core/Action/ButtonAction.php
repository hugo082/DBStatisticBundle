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

class ButtonAction extends Action
{
    /**
     * @var string
     */
    private $title;

    public function __construct(string $id, string $title, $value = null)
    {
        parent::__construct($id, self::TYPE_BUTTON, $value);
        $this->title = $title;
    }

    public function encode(): array
    {
        return array_merge(parent::encode(), array(
            "title" => $this->title
        ));
    }

    public static function decode(array $data) {
        $value = (key_exists("value", $data)) ? $data["value"] : null;
        return new ButtonAction($data["id"], $data["title"], $value);
    }
}