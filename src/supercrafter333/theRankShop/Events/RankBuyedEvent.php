<?php

namespace supercrafter333\theRankShop\Events;

use pocketmine\event\Event;
use pocketmine\Player;

/**
 *
 */
class RankBuyedEvent extends Event
{

    /**
     * @param Player $player
     * @param mixed $rank
     */
    public function __construct(private Player $player, private mixed $rank) {}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return mixed
     */
    public function getRank(): mixed
    {
        return $this->rank;
    }
}