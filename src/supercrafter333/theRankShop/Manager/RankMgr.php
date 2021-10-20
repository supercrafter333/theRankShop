<?php

namespace supercrafter333\theRankShop\Manager;

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
        print_r($rankName);
        if (!theRankShop::getRankCfg()->exists($rankName)) return null;
        return new RankInfo($rankName);
    }
}