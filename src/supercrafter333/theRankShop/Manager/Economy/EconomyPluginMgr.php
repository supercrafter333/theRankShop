<?php

namespace supercrafter333\theRankShop\Manager\Economy;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use pocketmine\utils\AssumptionFailedError;
use supercrafter333\theRankShop\theRankShop;

class EconomyPluginMgr
{

    protected static EconomyPlugin|null $economyPlugin = null;

    /**
     * @param EconomyPlugin|null $economyPlugin
     */
    public static function setEconomyPlugin(EconomyPlugin|null $economyPlugin): void
    {
        self::$economyPlugin = $economyPlugin;
        theRankShop::getInstance()->getLogger()->debug("Set economy plugin to: " . $economyPlugin::class);
    }

    /**
     * @return EconomyPlugin|null
     */
    public static function getEconomyPlugin(): EconomyPlugin|null
    {
        if (self::$economyPlugin === null) {
            if (!theRankShop::getInstance()->getServer()->getPluginManager()->getPlugin("BedrockEconomy") instanceof BedrockEconomy)
                return throw new AssumptionFailedError("[theRankShop] -> Can't find default economy plugin (BedrockEconomy)!");
            return new BedrockEconomyMgr();
        }
        return self::$economyPlugin;
    }
}