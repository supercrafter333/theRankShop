<?php

namespace supercrafter333\theRankShop\Manager;

use _64FF00\PurePerms\PurePerms;
use alvin0319\GroupsAPI\GroupsAPI;
use pocketmine\utils\AssumptionFailedError;
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
            if (!$gApi instanceof GroupsAPI) {
                return throw new AssumptionFailedError("[theRankShop] -> Can't find default rank-management plugin (GroupsAPI)!");
            }
            return new GroupsAPIMgr();
        }
        return self::$pluginClass;
    }
}