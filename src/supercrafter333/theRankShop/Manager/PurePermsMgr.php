<?php

namespace supercrafter333\theRankShop\Manager;

use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\PurePerms;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
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
        $this->pureperms = PurePerms::getInstance();
        $this->ppUserMgr = $this->pureperms->getUserDataMgr();
    }


    /**
     * @param string $rankName
     * @return null|PPGroup
     */
    public function getRank(string $rankName): ?PPGroup
    {
        return $this->pureperms->setGroup($rankName);
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
        return $this->ppUserMgr->setGroup($player)->getName();
    }

    /**
     * @return Plugin
     */
    public function getRealPlugin(): Plugin
    {
        return PurePerms::getInstance();
    }
}
