<?php

namespace supercrafter333\theRankShop\Manager\Info;

use pocketmine\utils\Config;
use supercrafter333\theRankShop\theRankShop;

class CommandInfo
{

    /**
     * @var string
     */
    private string $cmdName;

    /**
     * @var Config
     */
    private Config $list;

    /**
     * @param string $cmdName
     */
    public function __construct(string $cmdName)
    {
        $this->cmdName = $cmdName;
        $this->list = theRankShop::getCommandCfg();
    }

    /**
     * @return string
     */
    public function getCmdName(): string
    {
        return $this->cmdName;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->cmdName) || !isset($list->get($this->cmdName)["description"])) return null;
        return $list->get($this->cmdName)["description"];
    }

    /**
     * @return string|null
     */
    public function getUsage(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->cmdName) || !isset($list->get($this->cmdName)["usage"])) return null;
        return $list->get($this->cmdName)["usage"];
    }

    /**
     * @return array|null
     */
    public function getAliases(): ?array
    {
        $list = $this->list;
        if (!$list->exists($this->cmdName) || !isset($list->get($this->cmdName)["aliases"])) return null;
        return $list->get($this->cmdName, ["aliases"]);
    }
}