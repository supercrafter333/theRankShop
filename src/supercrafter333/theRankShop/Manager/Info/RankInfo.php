<?php

namespace supercrafter333\theRankShop\Manager\Info;

use pocketmine\utils\Config;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class RankInfo
{

    /**
     * @var string
     */
    private string $rankName;

    /**
     * @var Config
     */
    private Config $list;

    /**
     * @param string $rankName
     */
    public function __construct(string $rankName)
    {
        $this->rankName = $rankName;
        $this->list = theRankShop::getRankCfg();
    }

    /**
     * @return string
     */
    public function getRankName(): string
    {
        return $this->rankName;
    }

    /**
     * @return string|null
     */
    public function getUiTitle(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["uiTitle"])) return null;
        return $list->get($this->rankName)["uiTitle"];
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["desc"])) return null;
        return $list->get($this->rankName)["desc"];
    }

    /**
     * @return int|float|null
     */
    public function getPrice(): int|float|null
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["price"])) return null;
        return $list->get($this->rankName)["price"];
    }
}