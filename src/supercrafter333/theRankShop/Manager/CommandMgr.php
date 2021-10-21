<?php

namespace supercrafter333\theRankShop\Manager;

use supercrafter333\theRankShop\Manager\Info\CommandInfo;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class CommandMgr
{

    /**
     * @param string $commandName
     */
    public function __construct(private string $commandName) {}

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * Get informations about a command.
     *
     * @param string $commandName
     * @return CommandInfo|null
     */
    public static function getCommandInfo(string $commandName): ?CommandInfo
    {
        if (!theRankShop::getCommandCfg()->exists($commandName)) return null;
        return new CommandInfo($commandName);
    }
}