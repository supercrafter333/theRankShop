<?php

namespace supercrafter333\theRankShop\Manager;

use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\utils\AssumptionFailedError;
use supercrafter333\theRankShop\Events\RankBuyedEvent;
use supercrafter333\theRankShop\Events\RankBuyEvent;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Lang\Messages;
use supercrafter333\theRankShop\Manager\Info\RankInfo;

/**
 *
 */
class PlayerMgr
{

    /**
     * @var Player
     */
    private Player $player;

    /**
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

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

    /**
     * @param RankInfo $rankInfo
     * @return bool
     */
    public function buyRank(RankInfo $rankInfo): bool
    {
        $name = $rankInfo->getRankName();
        $price = $rankInfo->getPrice();
        if ($name == null || $price == null) return throw new AssumptionFailedError("[theRankShop] -> Rank-Name and/or Rank-Price is null!");
        $ev = new RankBuyEvent($this->player, $name, LanguageMgr::getMsg(Messages::MSG_RANKBUY_CANCELLED));
        $ev->call();
        if ($ev->isCancelled()) return false;
        if (EconomyAPI::getInstance()->myMoney($this->player) < $price) {
            return false;
        }
        EconomyAPI::getInstance()->reduceMoney($this->player, $price);
        $setRank = $this->setRank($name);
        if (!$setRank) return throw new AssumptionFailedError("[theRankShop] -> Can't set rank ($name) for player (" . $this->player->getName() . ")!");
        $ev = new RankBuyedEvent($this->player, $name);
        $ev->call();
        return true;
    }
}