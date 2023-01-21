<?php

namespace supercrafter333\theRankShop\Manager;

use alvin0319\GroupsAPI\group\Group;
use alvin0319\GroupsAPI\GroupsAPI;
use DateTime;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class GroupsAPIMgr implements RankManagementPlugin
{

    /**
     * @param string $rankName
     * @return null|Group
     */
    public function getRank(string $rankName): null|Group
    {
        return GroupsAPI::getInstance()->getGroupManager()->getGroup($rankName);
    }

    /**
     * @param Player $player
     * @param string $rankName
     * @param DateTime|null $expireAt
     * @return bool
     */
    public function setRankOfPlayer(Player $player, string $rankName, DateTime|null $expireAt = null): bool
    {
        if (($rank = $this->getRank($rankName)) === null) return false;

        $member = GroupsAPI::getInstance()->getMemberManager()->getMember($player->getName());

        if ($member->hasGroup($rank)) return true;
        //if (!Util::canInteractTo($this->getRank($this->getRankOfPlayer($player)), $rank)) return false;

        $member->addGroup($rank, $expireAt);
        return true;
    }

    /**
     * @param Player $player
     * @return string|null
     */
    public function getRankOfPlayer(Player $player): ?string
    {
        return GroupsAPI::getInstance()->getMemberManager()->getMember($player->getName())->getHighestGroup()->getName();
    }

    /**
     * @return GroupsAPI
     */
    public function getRealPlugin(): Plugin
    {
        return GroupsAPI::getInstance();
    }
}