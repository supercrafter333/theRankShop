<?php

namespace supercrafter333\theRankShop\Manager;

use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\PurePerms;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class PurePermsMgr implements RankManagementPlugin
{

    /**
     * @var PurePerms
     */
    private $pureperms;

    /**
     * @var \_64FF00\PurePerms\data\UserDataManager
     */
    private $ppUserMgr;

    /**
     *
     */
    public function __construct()
    {
        $this->pureperms = theRankShop::getInstance()->getServer()->getPluginManager()->getPlugin("PurePerms");
        $this->ppUserMgr = $this->pureperms->getUserDataMgr();
    }


    /**
     * @param string $rankName
     * @return null|PPGroup
     */
    public function getRank(string $rankName): ?PPGroup
    {
        return $this->pureperms->getGroup($rankName);
    }

    /**
     * @param Player $player
     * @param string $rankName
     * @return bool
     */
    public function setRankOfPlayer(Player $player, string $rankName): bool
    {
        if (!$this->getRank($rankName) instanceof PPGroup) return false;
        $this->ppUserMgr->setGroup($player, $this->getRank($rankName), null);
        return true;
    }

    /**
     * @param Player $player
     * @return string|null
     */
    public function getRankOfPlayer(Player $player): ?string
    {
        return $this->ppUserMgr->getGroup($player)->getName();
    }

    /**
     * @return Plugin|PurePerms|null
     */
    public function getRealPlugin(): Plugin
    {
        return $this->pureperms;
    }
}