<?php

namespace supercrafter333\theRankShop;

use jojoe77777\FormAPI\Form;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use supercrafter333\theRankShop\Commands\theRankShopCommand;
//use SimpleLogger;

/**
 * PluginBase of theRankShop.
 */
class theRankShop extends PluginBase
{

    /**
     * @var $this
     */
    protected static self $instance;

    /**
     * onLoad function.
     */
    public function onLoad(): void
    {
        self::$instance = $this;
        if (!class_exists(Form::class)) { //FormAPI cannot found
            $this->getServer()->getLogger()->error("CANNOT FIND FormAPI LIBRARY!! theRankShop will be unloaded now.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        //Save config files and setup directorys
        @mkdir($this->getDataFolder() . "languages");
        $this->saveResource("config.yml");
        $this->saveResource("commands.yml");
        $this->saveResource("ranks.yml");

        /*---==== Register Permissions ====---*/
        //theRankShop.cmd -> true
        DefaultPermissions::registerPermission(new Permission("theRankShop.cmd", "Command permission", Permission::DEFAULT_TRUE));
        /*---------====================-------*/

    }

    /**
     * onEnable function.
     */
    public function onEnable(): void
    {
        $this->getServer()->getCommandMap()->register("theRankShop", new theRankShopCommand());
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance;
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
}