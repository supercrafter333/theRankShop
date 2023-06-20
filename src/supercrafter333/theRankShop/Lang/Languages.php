<?php

namespace supercrafter333\theRankShop\Lang;

use pocketmine\utils\Config;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class Languages
{

    public const LANG_en_BE = "en_BE"; # Britisch English

    //TODO: public const LANG_en_AE = "en_AE"; # American English

    public const LANG_tr_TR = "tr_TR";

    public const LANG_zh_CN = "zh_CN";

    public const LANG_zh_TW = "zh_TW";

    public const LANG_ger_DE = "ger_DE";

    public const LANG_CUSTOM = "messages";


    /**
     * List of languages. (As array)
     * @var array|string[]
     */
    public static array $languages = [
        self::LANG_en_BE => self::LANG_en_BE,
        self::LANG_tr_TR => self::LANG_tr_TR,
        self::LANG_zh_CN => self::LANG_zh_CN,
        self::LANG_zh_TW => self::LANG_zh_TW,
        self::LANG_ger_DE, self::LANG_ger_DE,
        self::LANG_CUSTOM => self::LANG_CUSTOM
    ];

    /**
     * Get the language Data. (PocketMine-MP Config)
     * @return Config
     */
    public static function getLanguageData(): Config
    {
        $rawLang = theRankShop::getInstance()->getConfig()->get("language");
        if (strtolower($rawLang) == "custom") {
            theRankShop::getInstance()->saveResource("languages/messages.yml");
            return new Config(theRankShop::getInstance()->getDataFolder() . "languages/messages.yml", Config::YAML);
        }
        if (isset(self::$languages[$rawLang]) && file_exists(theRankShop::getInstance()->getFile() . "resources/languages/" . $rawLang . ".yml")) return new Config(theRankShop::getInstance()->getFile() . "resources/languages/" . $rawLang . ".yml", Config::YAML);
        return self::getDefaultLanguageData();
    }

    /**
     * @return string
     */
    public static function getLanguage(): string
    {
        $rawLang = theRankShop::getInstance()->getConfig()->get("language");
        if (strtolower($rawLang) == "custom") {
            return self::LANG_CUSTOM;
        }
        if (isset(self::$languages[$rawLang]) && file_exists(theRankShop::getInstance()->getFile() . "resources/languages/" . $rawLang . ".yml")) return $rawLang;
        return self::LANG_en_BE;
    }

    /**
     * @return Config
     */
    public static function getDefaultLanguageData(): Config
    {
        return new Config(theRankShop::getInstance()->getFile() . "resources/languages/" . self::LANG_en_BE . ".yml", Config::YAML);
    }

    /**
     * @return Config
     */
    public static function getCustomLanguageData(): Config
    {
        return new Config(theRankShop::getInstance()->getDataFolder() . "languages/messages.yml", Config::YAML);
    }
}