<?php

namespace supercrafter333\theRankShop\Commands;

use arie\yamlcomments\YamlComments;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\TextFormat;
use supercrafter333\theRankShop\Forms\theRankShopDefaultForms;
use supercrafter333\theRankShop\Lang\LanguageMgr;
use supercrafter333\theRankShop\Manager\CommandMgr;
use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\Manager\RankManagementPluginMgr;
use supercrafter333\theRankShop\Manager\RankMgr;
use supercrafter333\theRankShop\theRankShop;
use function count;
use function is_numeric;

/**
 * @theRankShopCommand
 */
class theRankShopCommand extends Command implements PluginOwned
{

    /**
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param array|string[] $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $cmdInfo = CommandMgr::getCommandInfo($name);

        $description = $cmdInfo->getDescription() == null ? $description : $cmdInfo->getDescription();
        $usageMessage = $cmdInfo->getUsage() !== null ? $cmdInfo->getUsage() : $usageMessage;
        $aliases = !is_array($cmdInfo->getAliases()) ? $cmdInfo->getAliases() : $aliases;

        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender $s
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        //Default part
        if (!isset($args[0])) {
            if (!$s instanceof Player) {
                $s->sendMessage("Only In-Game!");
                return;
            }
            $forms = new theRankShopDefaultForms($s);
            $forms->openMenuForm();
            return;
        } elseif ($args[0] == "addrank") {
            if (!$s->hasPermission("theRankShop.cmd.addrank")) {
                $s->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($this->getName() . " removerank")->prefix(TextFormat::RED));
                return;
            }

            if (count($args) < 5) {
                $s->sendMessage("§4Usage: §r/therankshop addrank <rankname: string> <title: string> <description: string> <price: int|float>");
                return;
            }
            $rankname = $args[1];
            $title = $args[2];
            $desc = $args[3];
            $price = $args[4];
            $expTime = $args[5] ?? null;

            if (!is_numeric($price)) {
                $s->sendMessage("§4Usage: §r/therankshop addrank <rankname: string> <title: string> <description: string> <price: int|float> [time: string]");
                return;
            }

            if ($expTime !== null && RankInfo::stringToTimestamp($expTime) === null) {
                $s->sendMessage("§4Usage: §r/therankshop addrank <rankname: string> <title: string> <description: string> <price: int|float> [time: string]");
                return;
            }

            if (RankManagementPluginMgr::getRankPlugin()->getRank($rankname) === null) {
                $s->sendMessage(LanguageMgr::getMsg("addrank-cmd-rank-not-found", ["{rank}" => $rankname]));
                return;
            }

            if (RankMgr::getRankInfo($rankname) !== null) {
                $s->sendMessage(LanguageMgr::getMsg("addrank-cmd-rank-already-added", ["{rank}" => $rankname]));
                return;
            }

            $yaml_cms = new YamlComments(theRankShop::getInstance()->getDataFolder() . "ranks.yml");
            $cfg = theRankShop::getRankCfg();
            $cfg->set($rankname, [
                "uiTitle" => $title,
                "desc" => $desc,
                "price" => $price,
                "expiryTime" => $expTime
            ]);
            $cfg->save();
            $yaml_cms->emitComments();

            $rankInfo = RankMgr::getRankInfo($rankname);
            if ($rankInfo === null) throw new AssumptionFailedError("[theRankShop] -> Something went wrong on adding a rank to ranks.yml!");

            $s->sendMessage(LanguageMgr::getMsg("addrank-cmd-success", [
                "{rank}" => $rankname,
                "{title}" => $rankInfo->getUiTitle(),
                "{desc}" => $rankInfo->getDescription(),
                "{price}" => (string)$rankInfo->getPrice(),
                "{expiryTime}" => $rankInfo->getExpireAtRaw() !== null ? $rankInfo->getExpireAtRaw() : "NEVER"
            ]));
        } elseif ($args[0] == "removerank" || $args[0] == "rmrank") {
            if (!$s->hasPermission("theRankShop.cmd.removerank")) {
                $s->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($this->getName() . " removerank")->prefix(TextFormat::RED));
                return;
            }
            
            if (count($args) < 2) {
                $s->sendMessage("§4Usage: §r/therankshop removerank <rankname: string>");
                return;
            }

            $rankname = $args[1];

            if (RankMgr::getRankInfo($rankname) === null) {
                $s->sendMessage(LanguageMgr::getMsg("removerank-cmd-rank-not-added", ["{rank}" => $rankname]));
                return;
            }

            $yaml_cms = new YamlComments(theRankShop::getInstance()->getDataFolder() . "ranks.yml");
            $cfg = theRankShop::getRankCfg();
            $cfg->remove($rankname);
            $cfg->save();
            $yaml_cms->emitComments();

            $s->sendMessage(LanguageMgr::getMsg("removerank-cmd-success", ["{rank}" => $rankname]));
        }
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin
    {
        return theRankShop::getInstance();
    }
}