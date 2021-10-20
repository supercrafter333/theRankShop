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
     * @var Player
     */
    private Player $player;

    /**
     * @var mixed
     */
    private mixed $rank;

    /**
     * @param Player $player
     * @param mixed $rank
     */
    public function __construct(Player $player, mixed $rank)
    {
        $this->player = $player;
        $this->rank = $rank;
    }

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