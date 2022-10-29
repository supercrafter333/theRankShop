<?php

namespace supercrafter333\theRankShop\Manager;

use DateTime;
use IvanCraft623\RankSystem\rank\Rank;
use IvanCraft623\RankSystem\RankSystem;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class RankSystemMgr implements RankManagementPlugin
{

    /**
     * @param string $rankName
     * @return Rank|null
     */
    public function getRank(string $rankName): Rank|null
    {
        return RankSystem::getInstance()->getRankManager()->getRank($rankName);
    }

    /**
     * @param Player $player
     * @param string $rankName
     * @param DateTime|null $expireAt
     * @return bool
     */
    public function setRankOfPlayer(Player $player, string $rankName, ?DateTime $expireAt = null): bool
    {
        $rs = RankSystem::getInstance();
        $session = $rs->getSessionManager()->get($player);

        if (!($rank = $this->getRank($rankName)) instanceof Rank) return false;

        return $session->setRank($rank, $expireAt?->getTimestamp());
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getRankOfPlayer(Player $player): string
    {
        return RankSystem::getInstance()->getSessionManager()->get($player)->getHighestRank()->getName();
    }

    /**
     * @return Plugin
     */
    public function getRealPlugin(): Plugin
    {
        return RankSystem::getInstance();
    }
}