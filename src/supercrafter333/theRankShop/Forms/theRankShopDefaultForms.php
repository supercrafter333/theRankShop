<?php

namespace supercrafter333\theRankShop\Forms;

use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\GhastShootSound;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Lang\Messages;
use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\Manager\PlayerMgr;
use supercrafter333\theRankShop\Manager\RankManagementPluginMgr;
use supercrafter333\theRankShop\Manager\RankMgr;

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
            $uiTitle = str_replace(["{rank}", "{price}", "{line}"], [$rankInfo->getRankName(), $rankInfo->getPrice(), "\n"], $rankInfo->getUiTitle());
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
                $buyRank = $playerMgr->buyRank($rankInfo);
                if ($buyRank) {
                    $player->sendMessage(str_replace(["{rank}", "{price}"], [$rankInfo->getRankName(), $rankInfo->getPrice()], LanguageMgr::getMsg(Messages::MSG_RANKBUY_SUCCESS)));
                    $player->getWorld()->addSound($player->getPosition()->asVector3(), new GhastShootSound());
                    return;
                } else {
                    $player->sendMessage(str_replace(["{rank}", "{price}", "{missing}"], [$rankInfo->getRankName(), $rankInfo->getPrice(), ($rankInfo->getPrice() - EconomyAPI::getInstance()->myMoney($player))], LanguageMgr::getMsg(Messages::MSG_RANKBUY_NOMONEY)));
                    $player->getWorld()->addSound($player->getPosition()->asVector3(), new AnvilFallSound());
                    return;
                }
            }
        });
        $form->setTitle(LanguageMgr::getMsgWithNoExtras(Messages::FORMS_BUYRANK_TITLE));
        $form->setContent(str_replace("{description}", $rankInfo->getDescription(), LanguageMgr::getMsg(Messages::FORMS_BUYRANK_CONT)));
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