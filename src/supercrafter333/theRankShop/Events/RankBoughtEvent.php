<?php

namespace supercrafter333\theRankShop\Events;

use DateTime;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

/**
 *
 */
class RankBoughtEvent extends Event
{

    /**
     * @param Player $player
     * @param mixed $rank
     * @param DateTime|null $expireAt
     */
    public function __construct(private Player $player, private mixed $rank, private DateTime|null $expireAt = null) {}

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

    /**
     * @return DateTime|null
     */
    public function getExpireAt(): ?DateTime
    {
        return $this->expireAt;
    }
}