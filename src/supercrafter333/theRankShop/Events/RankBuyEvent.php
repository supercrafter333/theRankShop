<?php

namespace supercrafter333\theRankShop\Events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\Player;

/**
 *
 */
class RankBuyEvent extends Event implements Cancellable
{

    /**
     * @param Player $player
     * @param string $rankName
     * @param string $messageOnCancel
     */
    public function __construct(private Player $player, private string $rankName, private string $messageOnCancel) {}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return string
     */
    public function getRankName(): string
    {
        return $this->rankName;
    }

    /**
     * @param string $rankName
     */
    public function setRankName(string $rankName): void
    {
        $this->rankName = $rankName;
    }

    /**
     * @param string $messageOnCancel
     */
    public function setMessageOnCancel(string $messageOnCancel): void
    {
        $this->messageOnCancel = $messageOnCancel;
    }

    /**
     * @return string
     */
    public function getMessageOnCancel(): string
    {
        return $this->messageOnCancel;
    }
}