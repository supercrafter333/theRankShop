<?php

namespace supercrafter333\theRankShop\Manager;

use DateTime;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use r3pt1s\GroupSystem\group\GroupManager;
use r3pt1s\GroupSystem\GroupSystem;
use r3pt1s\groupsystem\session\SessionManager;
use supercrafter333\theRankShop\Events\RankBoughtEvent;
use supercrafter333\theRankShop\Events\RankBuyEvent;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Lang\Messages;
use supercrafter333\theRankShop\Manager\Economy\EconomyPluginMgr;
use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\Manager\Rank\RankManagementPluginMgr;
use supercrafter333\theRankShop\Manager\Rank\RankMgr;
use supercrafter333\theRankShop\theRankShop;
use function class_exists;

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
     * @param DateTime|null $expireAt
     * @return bool
     */
    public function setRank(string $rankName, DateTime|null $expireAt = null): bool
    {
        return RankManagementPluginMgr::getRankPlugin()->setRankOfPlayer($this->player, $rankName, $expireAt);
    }

    /**
     * @param string $rankName
     * @return bool
     */
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
     * @throws \Exception
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

        if ($playerRank == $name || $this->havingHigherRank($name) || $this->havingHigherRank_GroupSystem($name)) return 2;

        $ev = new RankBuyEvent($this->player, $name, LanguageMgr::getMsg(Messages::MSG_RANKBUY_CANCELLED), $rankInfo->getExpireAt());
        $ev->call();
        if ($ev->isCancelled()) return 0;

        // INFO: Money-Check was removed from this function because of compatibility problems with BedrockEconomy!!

        EconomyPluginMgr::getEconomyPlugin()->reduceMoney($this->player, $price);
        $setRank = $this->setRank($name, $ev->getExpireAt());
        if (!$setRank) return throw new AssumptionFailedError("[theRankShop] -> Can't set rank ($name) for player (" . $this->player->getName() . ")!");
        $ev = new RankBoughtEvent($this->player, $name);
        $ev->call();
        return 1;
    }

    protected function havingHigherRank_GroupSystem(string $rankName): bool
    {
        if (!class_exists(GroupSystem::class) ||
            theRankShop::getInstance()->getServer()->getPluginManager()->getPlugin("GroupSystem") === null)
            return false;

        if (($rank = GroupManager::getInstance()->getGroupByName($rankName)) === null) return false;

        $session = SessionManager::getInstance()->get($this->player);
        return $session->hasGroup($rank)
            || $rank->isHigher($session->getGroup()->getGroup());
    }
}