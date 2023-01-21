<?php

namespace supercrafter333\theRankShop\Forms;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\GhastShootSound;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Lang\Messages;
use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\Manager\PlayerMgr;
use supercrafter333\theRankShop\Manager\RankManagementPluginMgr;
use supercrafter333\theRankShop\Manager\RankMgr;
use supercrafter333\theRankShop\theRankShop;

/**
 * Forms of theRankShop.
 */
class theRankShopDefaultForms
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
     * @param Player $player
     */
    public function setPlayer(Player $player): void
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
     * @return SimpleForm
     */
    public function openMenuForm()
    {
        $player = $this->player;
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null || $result === "") {
                $this->closed();
                return;
            }
            $this->openRankBuyForm($result);
            return;
        });
        $form->setTitle(LanguageMgr::getMsgWithNoExtras(Messages::FORMS_MENU_TITLE));
        $form->setContent(LanguageMgr::getMsg(Messages::FORMS_MENU_CONT));
        foreach (RankMgr::getAllRanksInConfig() as $rank) {
            $rankInfo = RankMgr::getRankInfo($rank);
            /*$price = $rankInfo->getPrice();
            $playerRank = RankManagementPluginMgr::getRankPlugin()->getRankOfPlayer($player);
            if ($playerRank !== null) {
                if (($newPrice = RankMgr::calculateRankPrices($rank, $playerRank)) !== null) $price = $newPrice;
            }*/
            $uiTitle = str_replace(["{rank}", "{price}", "{expiryTime}", "{line}"], [$rankInfo->getRankName(), $rankInfo->getPrice(), $rankInfo->getExpireAtRaw() !== null ? $rankInfo->getExpireAtRaw() : "LIFETIME", "\n"], $rankInfo->getUiTitle());
            $form->addButton($uiTitle, -1, "", $rank);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    /**
     * @param string $rankName
     * @return SimpleForm
     */
    public function openRankBuyForm(string $rankName)
    {
        $rank = RankManagementPluginMgr::getRankPlugin()->getRank($rankName);
        if ($rank == null) throw new AssumptionFailedError("[theRankShop] -> Can't find selected rank in RankManagementPlugin!");
        $rankInfo = new RankInfo($rankName);
        $player = $this->player;
        $form = new SimpleForm(function (Player $player, $data = null) use ($rankName, $rankInfo, $rank) {
            $result = $data;
            if ($result === null || $result === "") {
                $this->closed();
                return;
            }
            if ($result == "cancel") {
                $player->sendMessage(str_replace(["{rank}"], [$rankName], LanguageMgr::getMsg(Messages::FORMS_BUYRANK_CANCELBUY_MSG)));
                return;
            }
            if ($result == "submit") {
                $playerMgr = new PlayerMgr($player);
                if ($playerMgr->getRank() == $rankName) {
                    $player->sendMessage(LanguageMgr::getMsg(str_replace("{rank}", $rankName, Messages::MSG_RANKBUY_ALREADY_OWN)));
                    return;
                }

                theRankShop::getEconomyProvider()->getMoney($this->player,
                    function (float|int $amount) use ($player, $playerMgr, $rank, $rankInfo, $rankName): void {

                        $name = $rankInfo->getRankName();
                        ##########################################################################
                        $price = $rankInfo->getPrice();

                        $playerRank = RankManagementPluginMgr::getRankPlugin()->getRankOfPlayer($this->player);
                        if ($playerRank !== null) {
                            if (($newPrice = RankMgr::calculateRankPrices($name, $playerRank)) !== false && $newPrice !== null) $price = $newPrice;
                        }

                        if ($price > $amount) {
                            $player->sendMessage(str_replace(["{rank}", "{price}", "{missing}"], [$rankInfo->getRankName(), $rankInfo->getPrice(), ($rankInfo->getPrice() - $amount)], LanguageMgr::getMsg(Messages::MSG_RANKBUY_NOMONEY)));
                            $player->getWorld()->addSound($player->getPosition()->asVector3(), new AnvilFallSound());
                            return;
                        }
                        ##########################################################################

                        $buyRank = $playerMgr->buyRank($rankInfo);
                        if ($buyRank == 0) return;

                        switch ($buyRank) {
                            case 1:
                                $player->sendMessage(str_replace(["{rank}", "{price}", "{expireAt}"], [$rankInfo->getRankName(), $rankInfo->getPrice(), $rankInfo->getExpireAt() !== null ? $rankInfo->getExpireAt()->format("d.m.Y H:i:s") : "NEVER"], LanguageMgr::getMsg(Messages::MSG_RANKBUY_SUCCESS)));
                                $player->getWorld()->addSound($player->getPosition()->asVector3(), new GhastShootSound());
                                return;
                            case 2:
                                $player->sendMessage(str_replace(["{rank}"], [$rankInfo->getRankName()], LanguageMgr::getMsg(Messages::MSG_RANKBUY_HIGHER_RANK)));
                                $player->getWorld()->addSound($player->getPosition()->asVector3(), new AnvilFallSound());
                                return;
                        }
                    });
            }
        });
        $form->setTitle(LanguageMgr::getMsgWithNoExtras(Messages::FORMS_BUYRANK_TITLE));
        $form->setContent(str_replace(["{description}", "{price}", "{expiryTime}"], [$rankInfo->getDescription(), $rankInfo->getPrice(), $rankInfo->getExpireAtRaw() !== null ? $rankInfo->getExpireAtRaw() : "NEVER"], LanguageMgr::getMsg(Messages::FORMS_BUYRANK_CONT)));
        $form->addButton(LanguageMgr::getMsg(Messages::FORMS_BUYRANK_SUBMIT), -1, "", "submit");
        $form->addButton(LanguageMgr::getMsg(Messages::FORMS_BUYRANK_CANCEL), -1, "", "cancel");
        $form->sendToPlayer($player);
        return $form;
    }

    /**
     * Send the close-message for forms to the player.
     */
    public function closed(): void
    {
        $this->player->sendMessage(LanguageMgr::getMsg(Messages::FORMS_CLOSEFORM_MSG));
    }
}