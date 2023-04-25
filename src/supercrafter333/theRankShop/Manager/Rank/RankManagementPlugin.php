<?php

namespace supercrafter333\theRankShop\Manager\Rank;

use DateTime;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

interface RankManagementPlugin
{

    /**
     * Will return the rank-class, like Group in PurePerms.
     * @param string $rankName
     */
    public function getRank(string $rankName);


    /**
     * Will set the rank of a player.
     * @param Player $player
     * @param string $rankName
     * @param DateTime|null $expireAt
     * @return bool
     */
    public function setRankOfPlayer(Player $player, string $rankName, DateTime|null $expireAt = null): bool;

    /**
     * Will return the rank name of a player.
     * @param Player $player
     * @return null|string
     */
    public function getRankOfPlayer(Player $player): ?string;

    /**
     * Will return the real plugin of the interface (Like PurePerms's PluginBase)
     * @return Plugin
     */
    public function getRealPlugin(): Plugin;
}