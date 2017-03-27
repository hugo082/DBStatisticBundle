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

use Doctrine\ORM\EntityManager as ORMManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use DB\StatisticBundle\DependencyInjection\Configuration as Conf;

abstract class DataManager
{
    public const SINGLE_LINE_COLOR = array(Conf::TYPE_LINE, Conf::TYPE_BAR, Conf::TYPE_RADAR);
    private $isSingleLineColor = null;

    private $context;
    private $token;
    protected $em;

    private $gInfo = array();
    protected $lines = array();
    protected $labels = array();

    public function __construct(ORMManager $em, AuthorizationChecker $context, TokenStorage $token)
    {
        $this->em = $em;
        $this->context = $context;
        $this->token = $token;
    }

    public function compute() {
        $c = 0;
        foreach ($this->lines as &$data) {
            foreach ($data["items"] as &$item) {
                unset($item["buf"]);
                if (!in_array($item["label"], $this->labels))
                    $this->labels[] = $item["label"];
                $data["dataSets"][] = $item["value"];
                $data["backgroundColor"][] = $item["backgroundColor"];
            }
            if ($this->isSingleLineColor)
                $data["backgroundColor"] = $data["backgroundColor"][$c++];
        }

        return array(
            "information" => $this->gInfo,
            "lines" => $this->lines,
            "labels" => $this->labels
        );
    }

    protected function createLine($label = null) {
        $this->lines[] = array(
            "items" => array(),
            "label" => $label
        );
    }

    protected function valueForLabel($labelId, $value, $line = 0) {
        $data = &$this->lines[$line];
        foreach ($data["items"] as &$item) {
            if ($item["label"] == $labelId) {
                $item["value"] += $value;
                return;
            }
        }
        $data["items"][] = array(
            "value" => $value,
            "label" => $labelId,
            "backgroundColor" => $this->random_color(),
            "buf" => array());
    }

    protected function valueMoyForLabel($labelId, $value, $line = 0) {
        $data = &$this->lines[$line];
        foreach ($data["items"] as &$item) {
            if ($item["label"] == $labelId) {
                $item["buf"]["count"] += 1;
                $item["buf"]["total"] += $value;
                $item["value"] = $item["buf"]["total"] / $item["buf"]["count"];
                return;
            }
        }
        $data["items"][] = array(
            "value" => $value,
            "label" => $labelId,
            "backgroundColor" => $this->random_color(),
            "buf" => array("count" => 1, "total" => $value));
    }
    protected function random_color() {
        if ($this->isSingleLineColor)
            return  'rgba('.mt_rand(0, 255).','. mt_rand(0, 255).','. mt_rand(0, 255).',0.5)';
        else
            return  'rgba('.mt_rand(0, 255).','. mt_rand(0, 255).','. mt_rand(0, 255).',1)';
    }

    public function setGraphInformation(array $gInfo) {
        $this->gInfo = array(
            "id" => $gInfo["id"],
            "type" => $gInfo["type"],
            "title" => $gInfo["title"]
        );
        $this->isSingleLineColor = in_array($this->gInfo["type"], DataManager::SINGLE_LINE_COLOR);
    }
}
