<?php

namespace supercrafter333\theRankShop;

use _64FF00\PurePerms\PurePerms;
use arie\yamlcomments\YamlComments;
use cooldogedev\BedrockEconomy\BedrockEconomy;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use jojoe77777\FormAPI\Form;
use onebone\economyapi\EconomyAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use supercrafter333\theRankShop\Commands\theRankShopCommand;
use supercrafter333\theRankShop\Lang\Languages;
use supercrafter333\theRankShop\Manager\CommandMgr;
use supercrafter333\theRankShop\Manager\PurePermsMgr;
use supercrafter333\theRankShop\Manager\RankManagementPluginMgr;

/**
 * PluginBase of theRankShop.
 */
class theRankShop extends PluginBase
{

    /**
     * @var self
     */
    protected static self $instance;

    protected static EconomyProvider $economyProvider;

    /**
     * onLoad function.
     */
    public function onLoad(): void
    {
        self::$instance = $this;

        if (!class_exists(Form::class)) { //FormAPI cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND FormAPI LIBRARY!! Please download theRankShop form poggit.pmmp.io! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        if (!class_exists(libPiggyEconomy::class)) { //libPiggyEconomy cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND libPiggyEconomy LIBRARY!! Please download theRankShop form poggit.pmmp.io! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        if (!class_exists(YamlComments::class)) { //YamlComments cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND YamlComments LIBRARY!! Please download theRankShop form poggit.pmmp.io! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

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
        libPiggyEconomy::init();
        if (mb_strtolower($this->getConfig()->get("economy-plugin")) == "economyapi") {
            self::$economyProvider = libPiggyEconomy::getProvider(["provider" => "economyapi"]);
        } elseif (mb_strtolower($this->getConfig()->get("economy-plugin")) == "bedrockeconomy") {
            self::$economyProvider = libPiggyEconomy::getProvider(["provider" => "bedrockeconomy"]);
        } elseif (class_exists(EconomyAPI::class)) {
            self::$economyProvider = libPiggyEconomy::getProvider(["provider" => "economyapi"]);
        } elseif (class_exists(BedrockEconomy::class)) {
            self::$economyProvider = libPiggyEconomy::getProvider(["provider" => "bedrockeconomy"]);
        } else {
            $this->getLogger()->error("Can't find any supported economy plugin. Disabling theRankShop...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        if (mb_strtolower($this->getConfig()->get("rank-management-plugin")) == "pureperms") {
            RankManagementPluginMgr::setRankManagementClass(new PurePermsMgr());
        } elseif (class_exists(PurePerms::class)) {
            RankManagementPluginMgr::setRankManagementClass(new PurePermsMgr());
        } # GroupsAPI is default

        $cmdInfo = CommandMgr::getCommandInfo("therankshop");

        $description = "Open/Manage the rank shop.";
        $usageMessage = "§4Usage:§r /rankshop <subcommand>";
        $aliases = ["rankshop", "rs"];

        $description = $cmdInfo->getDescription() == null ? $description : $cmdInfo->getDescription();
        $usageMessage = $cmdInfo->getUsage() !== null ? $cmdInfo->getUsage() : $usageMessage;
        $aliases = !is_array($cmdInfo->getAliases()) ? $cmdInfo->getAliases() : $aliases;
        $this->getServer()->getCommandMap()->register("theRankShop", new theRankShopCommand("therankshop", $description, $usageMessage, $aliases));
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    public static function getEconomyProvider(): EconomyProvider
    {
        return self::$economyProvider;
    }

    /**
     * @return string
     */
    public function getFile2(): string
    {
        return $this->getFile();
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