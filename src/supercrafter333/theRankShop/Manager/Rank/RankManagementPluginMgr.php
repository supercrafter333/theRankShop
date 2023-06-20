<?php

namespace supercrafter333\theRankShop\Manager\Rank;

use pocketmine\utils\AssumptionFailedError;
use r3pt1s\groupsystem\GroupSystem;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class RankManagementPluginMgr
{

    /**
     * The RankManagementPlugin class.
     * Default manager is PurePerms.
     *
     * @var null
     */
    public static $pluginClass = null;

    /**
     * Set the RankManagementPlugin class.
     *
     * @param RankManagementPlugin $pluginClass
     */
    public static function setRankManagementClass(RankManagementPlugin $pluginClass): void
    {
        self::$pluginClass = $pluginClass;
        theRankShop::getInstance()->getLogger()->debug("Set rank-management plugin to: " . $pluginClass::class);
    }

    /**
     * Get the RankManagementPlugin class of the selected Plugin.
     *
     * @return RankManagementPlugin
     */
    public static function getRankPlugin(): RankManagementPlugin
    {
        if (self::$pluginClass == null) {
            $gApi = theRankShop::getInstance()->getServer()->getPluginManager()->getPlugin("GroupsAPI");
            if (!$gApi instanceof GroupSystem) {
                return throw new AssumptionFailedError("[theRankShop] -> Can't find default rank-management plugin (GroupSystem)!");
            }
            return new GroupSystemMgr();
        }
        return self::$pluginClass;
    }
}