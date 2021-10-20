<?php

namespace supercrafter333\theRankShop\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theRankShop\Forms\theRankShopDefaultForms;
use supercrafter333\theRankShop\Manager\CommandMgr;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class theRankShopCommand extends Command implements PluginIdentifiableCommand
{

    /**
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param array|string[] $aliases
     */
    public function __construct(string $name = "therankshop", string $description = "Manage/Open the rank shop.", string $usageMessage = "§4Usage:§r /rankshop <subcommand>", array $aliases = ["rankshop", "rs"])
    {
        $cmdInfo = CommandMgr::getCommandInfo($name);

        $description = $cmdInfo->getDescription() == null ? $description : $cmdInfo->getDescription();
        $usageMessage = $cmdInfo->getUsage() !== null ? $cmdInfo->getUsage() : $usageMessage;
        $aliases = !is_array($cmdInfo->getAliases()) ? $cmdInfo->getAliases() : $aliases;

        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender $s
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        //Default part
        if (!isset($args[0])) {
            if (!$s instanceof Player) {
                $s->sendMessage("Only In-Game!");
                return;
            }
            $forms = new theRankShopDefaultForms($s);
            $forms->openMenuForm();
            return;
        }
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return theRankShop::getInstance();
    }
}