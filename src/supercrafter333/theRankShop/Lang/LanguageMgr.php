<?php

namespace supercrafter333\theRankShop\Lang;

use pocketmine\utils\Config;

class LanguageMgr
{
/**
     * Language Data (PocketMine-MP Config)
     * @var Config $langData
     */
    public $langData;

    /**
     * Get a message.
     * [auto-replace: {line} to \n]
     *
     * @param string $message
     * @return string
     */
    public static function getMsg(string $message): string
    {
        $lang = Languages::getLanguageData();
        if (!$lang->exists($message)) {
            if (!Languages::getDefaultLanguageData()->exists($message)) return "ERROR! Message not found!";
            return str_replace("{line}", "\n", Languages::getDefaultLanguageData()->get($message));
        }
        return str_replace("{line}", "\n", $lang->get($message));
    }

    /**
     * Get a message without auto-replace.
     * @param string $message
     * @return string
     */
    public static function getMsgWithNoExtras(string $message): string
    {
        $lang = Languages::getLanguageData();
        if (!$lang->exists($message)) {
            if (!Languages::getDefaultLanguageData()->exists($message)) return "ERROR! Message not found!";
            return Languages::getDefaultLanguageData()->get($message);
        }
        return $lang->get($message);
    }

    /**
     * @return string
     */
    public static function getNoPermMsg(): string
    {
        return self::getMsg("no-perm");
    }

    /**
     * @return string
     */
    public static function getOnlyIG(): string
    {
        return self::getMsg("only-In-Game");
    }
}