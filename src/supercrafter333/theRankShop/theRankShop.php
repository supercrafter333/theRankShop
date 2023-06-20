<?php

namespace supercrafter333\theRankShop;

use arie\yamlcomments\YamlComments;
use IvanCraft623\RankSystem\RankSystem;
use jojoe77777\FormAPI\Form;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use r3pt1s\GroupSystem\GroupSystem;
use supercrafter333\theRankShop\Commands\theRankShopCommand;
use supercrafter333\theRankShop\Lang\Languages;
use supercrafter333\theRankShop\Manager\CommandMgr;
use supercrafter333\theRankShop\Manager\Rank\GroupSystemMgr;
use supercrafter333\theRankShop\Manager\Rank\RankManagementPluginMgr;
use supercrafter333\theRankShop\Manager\RankSystemMgr;
use function var_dump;

/**
 * PluginBase of theRankShop.
 */
class theRankShop extends PluginBase
{
    use SingletonTrait;

    /**
     * onLoad function.
     */
    public function onLoad(): void
    {
        self::setInstance($this);

        //Save config files and setup directorys
        @mkdir($this->getDataFolder() . "languages");
        $this->saveResource("config.yml");
        $this->saveResource("commands.yml");
        $this->saveResource("ranks.yml");

        ##################################
        $this->updateConfigs(true);
        ##################################
    }

    /**
     * onEnable function.
     */
    public function onEnable(): void
    {
        if (!class_exists(Form::class)) { //FormAPI cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND FormAPI LIBRARY!! Please download theRankShop form poggit.pmmp.io! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        if (!class_exists(YamlComments::class)) { //YamlComments cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND YamlComments LIBRARY!! Please download theRankShop form poggit.pmmp.io! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }


        /*if (str_contains($this->getDescription()->getVersion(), '-dev'))
            $this->getLogger()->warning("DEVELOPMENT VERSION!! You're using a development-version of theRankShop. This version can contain bugs. Please only use this version if you are sure of what you are doing.");

        if (mb_strtolower($this->getConfig()->get("economy-plugin")) == "bedrockeconomy")
            EconomyPluginMgr::setEconomyPlugin(new BedrockEconomyMgr());
        elseif (class_exists(BedrockEconomy::class))
            EconomyPluginMgr::setEconomyPlugin(new BedrockEconomyMgr());
        else {
            $this->getLogger()->error("Can't find any supported economy plugin. Disabling theRankShop...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }*/
        # BedrockEconomy is default

        if (mb_strtolower($this->getConfig()->get("rank-management-plugin")) == "groupsystem")
            RankManagementPluginMgr::setRankManagementClass(new GroupSystemMgr());
        elseif (class_exists(GroupSystem::class))
            RankManagementPluginMgr::setRankManagementClass(new GroupSystemMgr());
        elseif (mb_strtolower($this->getConfig()->get("rank-management-plugin")) == "ranksystem")
            RankManagementPluginMgr::setRankManagementClass(new RankSystemMgr());
        elseif (class_exists(RankSystem::class))
            RankManagementPluginMgr::setRankManagementClass(new RankSystemMgr());
        
        # GroupSystem is default


        $cmdInfo = CommandMgr::getCommandInfo("therankshop");

        $description = "Open/Manage the rank shop.";
        $usageMessage = "§4Usage:§r /rankshop <subcommand>";
        $aliases = ["rankshop", "rs"];

        $description = $cmdInfo->getDescription() !== null ? $cmdInfo->getDescription() : $description;
        $usageMessage = $cmdInfo->getUsage() !== null ? $cmdInfo->getUsage() : $usageMessage;
        $aliases = is_array($cmdInfo->getAliases()) ? $cmdInfo->getAliases() : $aliases;
        var_dump($aliases);
        $this->getServer()->getCommandMap()->register("theRankShop", new theRankShopCommand("therankshop", "theRankShop.cmd", $description, $usageMessage, $aliases));
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return parent::getFile();
    }

    /**
     * @return Config
     */
    public static function getCommandCfg(): Config
    {
        return new Config(self::getInstance()->getDataFolder() . "commands.yml", Config::YAML);
    }

    /**
     * @return Config
     */
    public static function getRankCfg(): Config
    {
        return new Config(self::getInstance()->getDataFolder() . "ranks.yml", Config::YAML);
    }

    private function updateConfigs(bool $update): void
    {
        $logger = $this->getLogger();
        $defaultPath = $this->getDataFolder();
        $langPath = $defaultPath . "languages/";
        $version = $this->getDescription()->getVersion();

        $updateCustomLang = function (string|null $oldVersion = null) use ($langPath, $version, $logger): void {
            $logger->warning("Updating '" . Languages::LANG_CUSTOM . ".yml' for theRankShop version " . $version . " ...");
            $verString = $oldVersion !== null ? $oldVersion . "_" : "outdated_";

            if (rename($langPath . Languages::LANG_CUSTOM . ".yml", $langPath . $verString . Languages::LANG_CUSTOM . ".yml")) {
                $logger->warning("Successfully updated '" . Languages::LANG_CUSTOM . ".yml'! Old file can be found in: " . $langPath . $verString . Languages::LANG_CUSTOM . ".yml");
            } else {
                $logger->error("Cannot update language data! (rename failed)");
            }
        };

        $updateConfig = function (string|null $oldVersion = null) use ($defaultPath, $version, $logger): void {
            $logger->warning("Updating 'config.yml' for theRankShop version " . $version . " ...");
            $verString = $oldVersion !== null ? $oldVersion . "_" : "outdated_";

            if (rename($defaultPath . "config.yml", $defaultPath . $verString . "config.yml")) {
                $logger->warning("Successfully updated 'config.yml'! Old file can be found in: " . $defaultPath . $verString . "config.yml");
            } else {
                $logger->error("Cannot update config! (rename failed)");
            }
        };

        /*$updateCmdConfig = function (string|null $oldVersion = null) use ($defaultPath, $version, $logger): void {
            $logger->warning("Updating 'commands.yml' for theRankShop version " . $version . " ...");
            $verString = $oldVersion !== null ? $oldVersion . "_" : "outdated_";

            if (rename($defaultPath . "commands.yml", $defaultPath . $verString . "commands.yml")) {
                $logger->warning("Successfully updated 'commands.yml'! Old file can be found in: " . $defaultPath . $verString . "commands.yml");
            } else {
                $logger->error("Cannot update command config! (rename failed)");
            }
        };*/

        if (Languages::getLanguage() == Languages::LANG_CUSTOM
        && (($lVer = Languages::getLanguageData()->get("version", null)) !== $version)) {
            $logger->notice("Your language file is outdated!");
            if ($update) $updateCustomLang($lVer);
        }

        if (($cVer = $this->getConfig()->get("version", null)) !== $version) {
            $logger->notice("Your configuration (config.yml) file is outdated!");
            if ($update) $updateConfig($cVer);
        }
    }
}