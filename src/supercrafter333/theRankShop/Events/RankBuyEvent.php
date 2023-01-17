<?php

namespace supercrafter333\theRankShop\Events;

use DateTime;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

/**
 *
 */
class RankBuyEvent extends Event implements Cancellable
{
    use CancellableTrait;

    /**
     * @param Player $player
     * @param string $rankName
     * @param string $messageOnCancel
     * @param DateTime|null $expireAt
     */
    public function __construct(private Player $player, private string $rankName, private string $messageOnCancel, private DateTime|null $expireAt = null) {}

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

    /**
     * @param DateTime|null $expireAt
     */
    public function setExpireAt(?DateTime $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    /**
     * @return DateTime|null
     */
    public function getExpireAt(): ?DateTime
    {
        return $this->expireAt;
    }
}