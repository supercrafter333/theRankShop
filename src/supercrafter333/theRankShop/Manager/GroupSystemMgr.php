<?php

namespace supercrafter333\theRankShop\Manager;

use DateTime;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use r3pt1s\GroupSystem\group\Group;
use r3pt1s\GroupSystem\GroupSystem;
use r3pt1s\GroupSystem\player\GroupPriority;
use r3pt1s\GroupSystem\player\PlayerGroup;
use r3pt1s\GroupSystem\player\PlayerGroupManager;

class GroupSystemMgr implements RankManagementPlugin
{

    /**
     * @param string $rankName
     * @return mixed
     */
    public function getRank(string $rankName)
    {
        return GroupSystem::getInstance()->getGroupManager()->getGroupByName($rankName);
    }

    /**
     * @param Player $player
     * @param string $rankName
     * @param DateTime|null $expireAt
     * @return bool
     */
    public function setRankOfPlayer(Player $player, string $rankName, ?DateTime $expireAt = null): bool
    {
        /**@var Group $group*/
        if (!($group = $this->getRank($rankName)) instanceof Group)
            return false;

        if (PlayerGroupManager::getInstance()->hasGroup($player, $group)
            || $group->isHigher(PlayerGroupManager::getInstance()->getGroup($player)->getGroup()))
            return false;

        return PlayerGroupManager::getInstance()->addGroup($player, new PlayerGroup($group, GroupPriority::HIGH(), $expireAt));
    }

    /**
     * @param Player $player
     * @return string|null
     */
    public function getRankOfPlayer(Player $player): ?string
    {
        return PlayerGroupManager::getInstance()->getGroup($player)->getGroup()->getName();
    }

    /**
     * @return Plugin
     */
    public function getRealPlugin(): Plugin
    {
        return GroupSystem::getInstance();
    }
}