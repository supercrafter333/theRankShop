<?php

namespace supercrafter333\theRankShop\Manager;

use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use supercrafter333\theRankShop\Events\RankBoughtEvent;
use supercrafter333\theRankShop\Events\RankBuyEvent;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Lang\Messages;
use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class PlayerMgr
{

    /**
     * @param Player $player
     */
    public function __construct(private Player $player) {}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return string|null
     */
    public function getRank()
    {
        return RankManagementPluginMgr::getRankPlugin()->getRankOfPlayer($this->player);
    }

    /**
     * @param string $rankName
     * @return bool
     */
    public function setRank(string $rankName): bool
    {
        return RankManagementPluginMgr::getRankPlugin()->setRankOfPlayer($this->player, $rankName);
    }

    public function havingHigherRank(string $rankName): bool
    {
        $now = $this->getRank();

        if ($now === null) return false;

        $allRanks = RankMgr::getAllRanksInConfig();

        if (!in_array($now, $allRanks) || !in_array($rankName, $allRanks)) return false;

        if (array_search($now, $allRanks) > array_search($rankName, $allRanks)) return true;
        return false;
    }

    /**
     * @param RankInfo $rankInfo
     * @return int - 0 = event cancelled, 1 = successfully, 2 = same or higher rank
     */
    public function buyRank(RankInfo $rankInfo): int
    {
        $name = $rankInfo->getRankName();
        $price = $rankInfo->getPrice();

        $playerRank = RankManagementPluginMgr::getRankPlugin()->getRankOfPlayer($this->player);
        if ($playerRank !== null) {
            if (($newPrice = RankMgr::calculateRankPrices($name, $playerRank)) !== false && $newPrice !== null) $price = $newPrice;
        }

        if ($name == null || $price == null) return throw new AssumptionFailedError("[theRankShop] -> Rank-Name and/or Rank-Price is null!");

        if ($playerRank == $name || $this->havingHigherRank($name)) return 2;

        $ev = new RankBuyEvent($this->player, $name, LanguageMgr::getMsg(Messages::MSG_RANKBUY_CANCELLED));
        $ev->call();
        if ($ev->isCancelled()) return 0;

        // INFO: Money-Check was removed from this function because of compatibility problems with BedrockEconomy!!

        theRankShop::getEconomyProvider()->takeMoney($this->player, $price);
        $setRank = $this->setRank($name);
        if (!$setRank) return throw new AssumptionFailedError("[theRankShop] -> Can't set rank ($name) for player (" . $this->player->getName() . ")!");
        $ev = new RankBoughtEvent($this->player, $name);
        $ev->call();
        return 1;
    }
}