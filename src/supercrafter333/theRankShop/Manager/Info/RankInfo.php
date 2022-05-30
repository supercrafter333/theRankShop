<?php

namespace supercrafter333\theRankShop\Manager\Info;

use DateInterval;
use DateTime;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Config;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class RankInfo
{

    /**
     * @var Config
     */
    private Config $list;

    /**
     * @param string $rankName
     */
    public function __construct(private string $rankName)
    {
        $this->list = theRankShop::getRankCfg();
    }

    /**
     * @return string
     */
    public function getRankName(): string
    {
        return $this->rankName;
    }

    /**
     * @return string|null
     */
    public function getUiTitle(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["uiTitle"])) return null;
        return $list->get($this->rankName)["uiTitle"];
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["desc"])) return null;
        return $list->get($this->rankName)["desc"];
    }

    /**
     * @return int|float|null
     */
    public function getPrice(): int|float|null
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["price"])) return null;
        return $list->get($this->rankName)["price"];
    }

    /**
     * @return bool
     */
    public function canExpire(): bool
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["expiryTime"])) return false;
        return true;
    }

    /**
     * @throws \Exception
     */
    public function getExpireAt(): DateTime|null
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["expiryTime"])) return null;
        $expDt = self::stringToTimestamp($list->get($this->rankName)["expiryTime"]);

        if ($expDt === null) throw new AssumptionFailedError("[theRankShop] -> Your config setting of 'expiryTime' for the rank " . $this->getRankName() . " isn't right!");

        return $expDt[0];
    }

    /**
     * @return string|null
     */
    public function getExpireAtRaw(): string|null
    {
        $list = $this->list;
        if (!$list->exists($this->rankName) || !isset($list->get($this->rankName)["expiryTime"])) return null;
        return $list->get($this->rankName)["expiryTime"];
    }

    /**
     * @throws \Exception
     */
    public static function stringToTimestamp(string $string): ?array
    {
        /**
         * Rules:
         * Integers without suffix are considered as seconds
         * "s" is for seconds
         * "m" is for minutes
         * "h" is for hours
         * "d" is for days
         * "w" is for weeks
         * "mo" is for months
         * "y" is for years
         */
        if (trim($string) === "") {
            return null;
        }
        $t = new DateTime();
        preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
        if (count($found[0]) < 1) {
            return null;
        }
        $found[2] = preg_replace("/[^0-9]/", "", $found[0]);
        foreach ($found[2] as $k => $i) {
            switch ($c = $found[1][$k]) {
                case "y":
                case "w":
                case "d":
                    $t->add(new DateInterval("P" . $i . strtoupper($c)));
                    break;
                case "mo":
                    $t->add(new DateInterval("P" . $i . strtoupper(substr($c, 0, strlen($c) - 1))));
                    break;
                case "h":
                case "m":
                case "s":
                    $t->add(new DateInterval("PT" . $i . strtoupper($c)));
                    break;
                default:
                    $t->add(new DateInterval("PT" . $i . "S"));
                    break;
            }
            $string = str_replace($found[0][$k], "", $string);
        }
        return [$t, ltrim(str_replace($found[0], "", $string))];
    }
}