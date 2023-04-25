<?php

namespace supercrafter333\theRankShop\Manager\Rank;

use supercrafter333\theRankShop\Manager\Info\RankInfo;
use supercrafter333\theRankShop\theRankShop;

/**
 *
 */
class RankMgr
{

    /**
     * Get all rank names from the rank-config.
     *
     * @return array
     */
    public static function getAllRanksInConfig(): array
    {
        return theRankShop::getRankCfg()->getAll(true);
    }

    /**
     * Get information about a rank.
     *
     * @param string $rankName
     * @return RankInfo|null
     */
    public static function getRankInfo(string $rankName): ?RankInfo
    {
        if (!theRankShop::getRankCfg()->exists($rankName)) return null;
        return new RankInfo($rankName);
    }

    public static function calculateRankPrices(string $newRankName, string $oldRankName): int|float|null|false
    {
        $new = self::getRankInfo($newRankName);
        $old = self::getRankInfo($oldRankName);

        if ($new === null || $old === null) return false;

        $newPrice = $new->getPrice();
        $oldPrice = $old->getPrice();

        if ($newPrice === $oldPrice) return null;

        return ($newPrice - $oldPrice) < 0 ? null : $newPrice - $oldPrice;
    }
}