<?php

namespace supercrafter333\theRankShop\Manager\Rank;

use DateTime;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use r3pt1s\groupsystem\group\Group;
use r3pt1s\groupsystem\GroupSystem;
use r3pt1s\groupsystem\player\PlayerRemainingGroup;
use r3pt1s\groupsystem\session\SessionManager;

class GroupSystemMgr implements RankManagementPlugin
{

    /**
     * @param string $rankName
     * @return Group|null
     */
    public function getRank(string $rankName): Group|null
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

        $session = SessionManager::getInstance()->get($player);
        if ($session->hasGroup($group)
            || $group->isHigher($session->getGroup()->getGroup()))
            return false;

        $session->addGroup(new PlayerRemainingGroup($group, null));
        $session->update();
        return true;
    }

    /**
     * @param Player $player
     * @return string|null
     */
    public function getRankOfPlayer(Player $player): ?string
    {
        return SessionManager::getInstance()->get($player)?->getGroup()?->getGroup()->getName();
    }

    /**
     * @return Plugin
     */
    public function getRealPlugin(): Plugin
    {
        return GroupSystem::getInstance();
    }
}