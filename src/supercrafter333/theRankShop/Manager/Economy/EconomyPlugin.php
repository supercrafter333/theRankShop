<?php

namespace supercrafter333\theRankShop\Manager\Economy;

use Closure;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

interface EconomyPlugin
{

    public function getMoney(Player $player, Closure $callback): void;

    public function addMoney(Player $player, int|float $amount): void;

    public function reduceMoney(Player $player, int|float $amount): void;

    public function getRealPlugin(): Plugin|null;
}