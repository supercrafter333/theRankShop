<?php

namespace supercrafter333\theRankShop\Manager\Economy;

use Closure;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\BedrockEconomy;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class BedrockEconomyMgr implements EconomyPlugin
{

    private readonly BedrockEconomyAPI $bedrockEconomy;

    public function __construct()
    { $this->bedrockEconomy = BedrockEconomyAPI::getInstance(); }

    /**
     * @param Player $player
     * @param Closure $callback function(?int $balance)
     * @return void
     */
    public function getMoney(Player $player, Closure $callback): void
    {
        $this->bedrockEconomy->getPlayerBalance($player->getName(),
            ClosureContext::create(fn(?int $balance) =>
            $callback($balance ?? BedrockEconomy::getInstance()->getCurrencyManager()->getDefaultBalance())));
    }

    /**
     * @param Player $player
     * @param int|float $amount
     * @return void
     */
    public function addMoney(Player $player, float|int $amount): void
    {
        $this->bedrockEconomy->addToPlayerBalance($player->getName(), $amount);
    }

    /**
     * @param Player $player
     * @param int|float $amount
     * @return void
     */
    public function reduceMoney(Player $player, float|int $amount): void
    {
        $this->bedrockEconomy->subtractFromPlayerBalance($player->getName(), $amount);
    }

    /**
     * @return BedrockEconomy
     */
    public function getRealPlugin(): Plugin|null
    {
        return BedrockEconomy::getInstance();
    }
}