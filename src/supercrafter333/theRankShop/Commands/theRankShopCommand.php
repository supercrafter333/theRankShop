<?php

namespace supercrafter333\theRankShop\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use supercrafter333\theRankShop\Forms\theRankShopDefaultForms;
use supercrafter333\theRankShop\Manager\CommandMgr;
use supercrafter333\theRankShop\theRankShop;

/**
 * @theRankShopCommand
 */
class theRankShopCommand extends Command implements PluginOwned
{

    /**
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param array|string[] $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
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
    public function getOwningPlugin(): Plugin
    {
        return theRankShop::getInstance();
    }
}